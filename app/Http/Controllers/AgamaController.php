<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\AgamaModel AS AgamaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class AgamaController extends Controller
{
	public function data(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$data_agama = AgamaModel::select('id','nama')->orderby('nama','asc')->get();

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
