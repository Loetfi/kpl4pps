<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\LayananModel AS LayananModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class LayananController extends Controller
{
	public function all(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$layanan = LayananModel::orderby('order_id','asc')->get();

			foreach($layanan as $lay){
				$ress['id_layanan'] = (int) $lay->id_layanan;
				$ress['nama_layanan'] = $lay->nama_layanan;
				$ress['icon_layanan'] = $lay->icon_layanan;
				$ress['order_id'] = $lay->order_id;

				$hasil[] = $ress;
			}

			// $res = array(
			// 	'id_layanan' => (int) $layanan->id_layanan,
			// 	'nama_layanan' => $layanan->nama_layanan,
			// 	'icon_layanan' => $layanan->icon_layanan
			// );

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $hasil;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
