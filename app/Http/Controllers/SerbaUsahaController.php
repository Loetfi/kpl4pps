<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\LayananModel AS Layanan;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class SerbaUsahaController extends Controller
{
	public function list(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$data_agama = Layanan::join('apps_kategori_channel','apps_layanan.id_layanan','=','apps_kategori_channel.id_layanan')->select('nama_kategori','gambar_kategori','id_kategori') ->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_agama;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}

 
