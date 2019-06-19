<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\RiwayatModel AS RiwayatModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class RiwayatOrderController extends Controller
{
	public function list(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'anggota_id'     => 'required',
				'offset'         => 'required|integer',
				'limit'			=> 'required|integer'
			]);  

			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;

			$data_res = RiwayatModel::where('id_anggota', $anggota_id)->skip($request->offset)->take($request->limit)->orderby('tanggal_order','desc')->get();

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
