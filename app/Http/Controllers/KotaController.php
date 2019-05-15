<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\KotaModel AS KotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class KotaController extends Controller
{

	public function kota(Request $request){
		try { 

			$data_res = KotaModel::select('id','name as nama')->where('name','like','kota%')->get();

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


