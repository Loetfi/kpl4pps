<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Hashing\BcryptHasher AS hash;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;
// use App\User;
use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class LoginController extends Controller
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
    public function login(Request $request){
        try{ 

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'nik'       => 'required',
                'pin'       => 'required'

            ]);  

            $data = '';
            $Message = 'Berhasil';
            $code = 200;
            $res = 1;

            $check = AnggotaModel::where('noanggota',$request->nik)->where('pin',$request->pin)->get()->first();
            if ($check) {
                $data = $check;
            } else {
                $Message = 'Anggota tidak ditemukan';
                $code = 400;
            }
            
        } catch(Exception $e) {
            $res = 0;
            $Message = $e->getMessage();
            $code = 400;
            $data = '';
        }
        return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
    } 
}
