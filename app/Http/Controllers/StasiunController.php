<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\StasiunModel AS StasiunModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class StasiunController extends Controller
{

	public function data(Request $request){
		try { 

			$data_res = StasiunModel::select('singkatan as id','nama')->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_res;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

}


