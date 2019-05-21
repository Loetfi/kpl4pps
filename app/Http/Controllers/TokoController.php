<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\TokoModel AS TokoModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class TokoController extends Controller
{
	public function data(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'offset'          		=> 'required|integer',
				'limit'					=> 'required|integer'
			]);

			$data_res = TokoModel::skip($request->offset)->take($request->limit)->orderby('nama','asc')->get();

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

	// detail barang 
	public function detail(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id'		=> 'required'
			]);

			$id = $request->id ? $request->id : 0;

			$data_res = TokoModel::where('id',$id)->first();
			
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

	// related barang 
	public function related(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'namakategori'		=> 'required',
				'id'				=> 'required',
			]);

			$id = $request->id ? $request->id : 0;
			$namakategori = $request->namakategori ? $request->namakategori : '';

			$data_res = TokoModel::where('namakategori',$namakategori)->where('id','!=',$id)->take(5)->get();
			
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
