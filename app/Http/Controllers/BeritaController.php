<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\BeritaModel AS BeritaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class BeritaController extends Controller
{

	public function data(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'offset'          		=> 'required|integer',
				'limit'					=> 'required|integer'
			]);

			$data_res = BeritaModel::skip($request->offset)->take($request->limit)->get();

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

	// detail berita

	public function detail(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_berita'          	=> 'required|integer'
			]);
			
			$id_berita = $request->id_berita ? $request->id_berita : 0;

			$data_res = BeritaModel::where('id_berita',$id_berita)->get();

			if (count($data_res)) {
				$Message = 'Berhasil';
				$code = 200;
				$res = 1;
			} else {
				$Message = 'Uuupps, berita tidak ditemukan';
				$code = 400;
				$res = 0;
			}

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


