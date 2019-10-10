<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\MaskapaiModel AS MaskapaiModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class MaskapaiController extends Controller
{
	public function data(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$data_agama = MaskapaiModel::get();

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
