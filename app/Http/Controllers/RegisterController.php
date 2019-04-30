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

class RegisterController extends Controller
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
    public function register(Request $request){
        try { 

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'nama'          => 'required',
                'tmplahir'      => 'required',
                'tgllahir'      => 'required',
                'gender'        => 'required',
                'agamaid'       => 'required'
            ]);  

            // insert  
            $data_insert = array(
                'id'            => 'REGONLINE-'.time(),
                'noanggota'     => time(),
                'pin'           => '12345',
                'tanggal'       => date('Y-m-d'),
                'nama'          => $request->nama,
                'alamat'        => 'register online',
                'kabupatenid'   => '20100321-200826278',
                'kelompokid'    => '20180117-155755',
                'tmplahir'      => $request->tmplahir,
                'tgllahir'      => $request->tgllahir,
                'gender'        => $request->gender,
                'pekerjaanid'   => '20090628-000706',
                'agamaid'       => '20130709-191250',
                'anggota'       => 1,
                'aktif'         => 1
            );
            $insert = AnggotaModel::insert($data_insert);

            
            $Message = 'Berhasil';
            $code = 200;
            $res = 1;
            $data = '';
        } catch(Exception $e) {
            $res = 0;
            $Message = $e->getMessage();
            $code = 400;
            $data = '';
        }
        return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
    } 
}
