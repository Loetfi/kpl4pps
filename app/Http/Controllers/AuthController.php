<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher AS hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use App\Models\User\ProfileManagement AS Profile;
use App\Models\User\UserManagement AS UserManage;
use App\Models\User\RegisterMemberFlowManagement AS flow;
use App\Models\Master\RegisterMemberFlowMaster AS mst_flow;
use App\Models\Master\RoleMaster AS role;
use App\Models\Master\WorkflowMaster AS mst_workflow;
use App\Repositories\Finance\DokuRepo AS DokuRepo;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
    * @SWG\Post(
    *     path="/auth",
    *     consumes={"multipart/form-data"},
    *     description="Login Lendtick",
    *     operationId="auth",
    *     consumes={"application/x-www-form-urlencoded"},
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Email for login Lendtick",
    *         in="formData",
    *         name="username",
    *         required=true,
    *         type="string"
    *     ),
    *     @SWG\Parameter(
    *         description="Password bond to that email",
    *         in="formData",
    *         name="password",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Authentication",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function auth(Request $request, hash $hash){
        $credentials = $request->only('username', 'password'); // grab credentials from the request

        try {
            // check user data and grab user data
            $check_pass = ($data = User::where('username',$credentials['username'])->where('deleted_by')->first())?$hash->check($credentials['password'], $data->password):false;
            // check user
            if(!$data) throw New JWTException("User Not Found", 404);
            // check password
            if(!$check_pass) throw New JWTException("Password Invalid", 401);
            // check role
            $check_flow = true;
            $unpaid = false;
            if($data){
                if ($data->id_role_master == role::where('name_role_master', 'member')->get()->first()->id_role_master) {
                    $check_flow = (flow::where('id_user', $data->id_user)->whereNotNull('approve_at')->get()->count() == mst_flow::all()->count());
                    $workflow = mst_workflow::where(function($q) use($request){
                        $q->where('workflow_status_name','like','paid%');
                        $q->orWhere('workflow_status_name','like','active%');
                    })->where('workflow_status_desc','like','%user status%')->get();
                    $stat = [];
                    foreach($workflow AS $i => $row){
                        $stat[] = $row->id_workflow_status;
                    }
                    $unpaid = (!in_array($data->id_workflow_status,$stat));
                }
            }
            // check paid
            if ($unpaid) {
                $pay = DokuRepo::getByParam('id_user',$data->id_user)->first();
                throw new JWTException("Please paid for your account", 401);
            }

            // chekif user migrated
            $data = User::where('username',$credentials['username'])->where('deleted_by')->first();
            if ($data->id_workflow_status == 'MBRSTS06') {
                $data->is_new_user = 2;
            }
            // end
            // check flow
            if(!$check_flow) throw New JWTException("Your account in validation progress", 401);
            // check token
            if(!$token = JWTAuth::attempt($credentials)) throw New JWTException("Invalid Credential", 401);

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $data = [];
            if(isset($pay)) $data = array_merge($data,array('va' => $pay->transidmerchant, 'amount' => $pay->totalamount));

            return response()->json(Api::response(false,$e->getMessage(),$data), $e->getCode());
        }

        // update last login field in [user].[User]

        return response()->json(Api::response(true,'Success Login',['token' => "Bearer $token", "is_new_user" => $data->is_new_user, "id_role_master" => $data->id_role_master]));
    }

    /**
    * @SWG\Get(
    *     path="/auth/check",
    *     description="Check Token Lendtick",
    *     operationId="checkAuth",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Check Authentication",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function check(Request $request){
        try {
            // set request to parser
            JWTAuth::parser()->setRequest($request);
            // validate payload
            if ($user = !(JWTAuth::parseToken()->authenticate())) {
                return response()->json(Api::response(false, 'user_not_found'), 404);
            }
        } catch (JWTException $e) {
            if($e->getMessage()=="Token has expired"){
                try{
                    $token = JWTAuth::refresh($request->header('Authorization'));
                } catch(JWTException $e){
                    return response()->json(Api::response(false, $e->getMessage()), 401);
                }
            }
            return response()->json(Api::response(false,$e->getMessage(),isset($token)?['token'=>'Bearer '.$token]:null), 401);
        }

        $data = JWTAuth::toUser($request->header('Autorization'))->only(["id_user","id_role_master","username","is_new_user"]);
        // the token is valid and we have found the user via the sub claim
        return response()->json(Api::response(true,"Valid Token",$data));
    }

    /**
    * @SWG\Get(
    *     path="/auth/refresh",
    *     description="Refresh Token Lendtick",
    *     operationId="refreshAuth",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Refresh Authentication",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function refresh(Request $request){
        try{
            JWTAuth::parser()->setRequest($request);
            $tmp = JWTAuth::getToken();
            $token = JWTAuth::refresh($tmp);
        } catch(JWTException $e){
            return response()->json(Api::response(false, $e->getMessage()), 401);
        }

        return response()->json(Api::response(false,'Token refresh',['token'=>'Bearer '.$token]), 200);
    }

    /**
    * @SWG\Put(
    *     path="/auth/forgot",
    *     description="Forgot Password",
    *     operationId="forgotAuth",
    *     produces={"application/json"},
    *     @SWG\Parameter(
    *         description="Using NIK to Forgot Password",
    *         in="formData",
    *         name="nik",
    *         required=true,
    *         type="string"
    *     ), 
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Forgot Authentication",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function forgot(Request $request, hash $hash){
        try{
            $this->validate($request,['nik' => 'required']);


            $active = mst_workflow::where('workflow_status_name', "like", "Active%")->where('workflow_status_desc', "like", "%user status%")->get()->first()->id_workflow_status;
            $user_management = UserManage::where('username','=',$request->nik)->get();

            if($user_management->count() > 0){
                $user_management = $user_management->first();
                $user = User::where('id_user','=',$user_management->id_user)->where('id_workflow_status',$active)->get();
                $profile = Profile::where('id_user','=',$user_management->id_user)->get()->first();
                if($user_management->count() > 0){
                    //
                    $user = $user->first();
                    $pass = Api::rstring(8,'alphanumeric');
                    $user->password = $hash->make($pass);
                    // update is_new_user = 1
                    $user->is_new_user = 1;
                    $user->save();


                    $sms = [
                        "name_customer"     => $profile->name,
                        "phone_number"    => $profile->phone_number,
                        "password"          => $pass
                    ];
                    
                    $sms1 = RestCurl::post(env('LINK_NOTIF','https://lentick-api-notification-dev.azurewebsites.net')."/send-sms-forgot-password", $sms);

                    return response()->json(Api::response(true, "Mohon check sms untuk menemukan pasword baru"), 200);
                }
                return response()->json(Api::response(false, "User anda sudah tidak aktif"), 400);
            }
            return response()->json(Api::response(false, "Email anda tidak ditemukan"), 400);
        } catch(JWTException $e){
            return response()->json(Api::response(false, $e->getMessage()), 400);
        }

        return response()->json(Api::response(false,'missing parameter'), 400);
    }

    /**
    * @SWG\Get(
    *     path="/auth/credentials",
    *     description="Get User Lendtick Credentials",
    *     operationId="credentialsAuth",
    *     produces={"application/json"},
    *     security={{"Bearer":{}}},
    *     @SWG\Parameter(
    *         description="ID user",
    *         in="query",
    *         name="id",
    *         required=false,
    *         type="string"
    *     ),
    *     @SWG\Response(
    *         response="200",
    *         description="successful"
    *     ),
    *     summary="Credential user",
    *     tags={
    *         "Authentication"
    *     }
    * )
    * */
    public function credentials(Request $request){
        try{
            $res = [];
            $id = $request->has('id')?$request->id:$request->id_user;
            $user = UserManage::where(['id_user' => $id])->get()->first();
            if($user){
                $role = Role::where(['id_role_master' => $user->id_role_master])->get()->first();
                $res = [
                    'username' => $user->username,
                    'password' => $user->password,
                    'role' => $role->name_role_master
                ];
            }
        } catch(JWTException $e){
            return response()->json(Api::response(false, $e->getMessage()), 401);
        }
        return response()->json(Api::response((count($res)>0),(count($res)>0?'Data tersedia':'Data tidak tersedia'),$res), count($res)>0?200:400);
    }
}
