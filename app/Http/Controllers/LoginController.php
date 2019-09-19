<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
// use Illuminate\Hashing\BcryptHasher AS Hash;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Hashing\BcryptHasher AS Hash;
// use Tymon\JWTAuth\Facades\JWTAuth;
// use Tymon\JWTAuth\Exceptions\JWTException;
// use App\User;
use App\Models\Anggota\UserBackendModel AS UserBackendModel;
use App\Models\Anggota\AggregateAnggotaModel AS AnggotaModel;
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

            // where('noanggotas',$request->nik)->orWhere('username',$request->nik)->
            $check = AnggotaModel::whereRaw('( noanggota = "'.$request->nik.'" OR username = "'.$request->nik.'" )')
            ->where('pin',$request->pin)->get()->first();
            if ($check) {
                $data = $check;
            } else {
                $res = 0;
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

    // backend login

    public function backend_login(Request $request, hash $hash){
        try{ 

            if(empty($request->json())) throw New \Exception('Params not found', 500);

            $this->validate($request, [
                'username'       => 'required',
                'password'       => 'required'

            ]);  

            $data = '';
            $Message = 'Berhasil';
            $code = 200;
            $res = 1;
 
            $check = UserBackendModel::where('username_backend',$request->username)->where('password_backend',sha1($request->password))->get()->first();
        if ($check) {
            $data = $check;
        } else {
            $res = 0;
            $Message = 'User tidak ditemukan';
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
