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
                'kabupatenid'   => '20190101-15174812',
                'kelompokid'    => '20180117-155638',
                'tmplahir'      => $request->tmplahir,
                'tgllahir'      => date('Y-m-d',strtotime($request->tgllahir)),
                'gender'        => $request->gender,
                'pekerjaanid'   => '20130709-123718',
                'agamaid'       => '20090628-000357',
                'anggota'       => 1,
                'aktif'         => 1,
                'kawin'         => 0,
                'jenisid'       => 1,
                'pengurus'      => 0,
                'hutang'        => 0,
                'tglpengurusdiangkat' => date('Y-m-d'),
                'tglpengurusberhenti' => date('Y-m-d'),
                'pengawas'      => 0,
                'tglpengawasdiangkat' => date('Y-m-d'),
                'tglpengawasberhenti' => date('Y-m-d'),
                'kantorid'  => '20130706-192634',
                'user'  => 'admin',
                'jam'   => date('Y-m-d H:i:s')
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
