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

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $layanan;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
