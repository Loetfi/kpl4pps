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
                'kabupatenid'   => '20100321-200826278',
                'kelompokid'    => '',
                'tmplahir'      => $request->tmplahir,
                'tgllahir'      => date('Y-m-d',strtotime($request->tgllahir)),
                'gender'        => $request->gender,
                'pekerjaanid'   => '20090628-000706',
                'agamaid'       => '20130709-191250',
                'anggota'       => 1,
                'aktif'         => 1,
                'kawin'         => 0,
                'jenisid'       => 1,
                'pengurus'      => 0,
                'tglpengurusdiangkat' => '2018-01-05',
                'tglpengurusberhenti' => '2018-01-05',
                'pengawas' => '0',
                'tglpengawasdiangkat' => '2018-01-05',
                'tglpengawasberhenti' => '2018-01-05',
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
