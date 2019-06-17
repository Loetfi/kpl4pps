<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\PromoModel AS PromoModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\PutImage;

class PromoController extends Controller
{
	public function data(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$res_data = PromoModel::where('status',1)->orderby('position','asc')->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// list for backend 
	public function list(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$res_data = PromoModel::orderby('position','asc')->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// add promo 
	public function add(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'link'     	=> 'required',
				'nama'		=> 'required'
			]);

			$Url = $request->link;
			$ImageName = time().'.jpg';

			$ResultPut = PutImage::save($Url, $ImageName);
			if($ResultPut) $content = url($ImageName);

			$position = PromoModel::max('position');
			$insert = array(
				'url_promo' 	=> $content,
				'nama_promo' 	=> $request->nama,
				'status'		=> 1,
				'position'		=> $position+1
			);

			$proses_insert = PromoModel::insert($insert);
			$res_data = '';

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// update promo
	public function update(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'link'     	=> 'required',
				'nama'		=> 'required',
				'status'	=> 'required|bool',
				'position'	=> 'required',
				'id'		=> 'required'
			]);

			$id = $request->id ? $request->id : 0;

			$Url = $request->link;

			if ($Url == 'no-image') {
				$url_promo = [];
			} else {
				$ImageName = time().'.jpg';

				$ResultPut = PutImage::save($Url, $ImageName);
				if($ResultPut) $content = url($ImageName);

				$url_promo = ['url_promo' 	=> $content];
			}
			

			// $position = PromoModel::max('position');
			$insert = array(
				'nama_promo' 	=> $request->nama,
				'status'		=> $request->status,
				'position'		=> $request->position
			);

			$proses_update = PromoModel::where('id_promo',$id)->update(array_merge($insert,$url_promo));


			$res_data = '';

			$Message = 'Berhasil Ubah Data';
			$code = 200;
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}


	// delete promo 
	public function delete(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_promo'  => 'required'
			]);

			$id = $request->id_promo ? $request->id_promo : 0;  
			$delete = PromoModel::where('id_promo', $id)->update(['status' => 0]);

			$res_data = '';

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}


	// detail promo 
	public function detail(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_promo'  => 'required'
			]);

			$id = $request->id_promo ? $request->id_promo : 0;  
			$res_data = PromoModel::where('id_promo', $id)->first();

			if (count($res_data)>0) {
				$res = $res_data;
				$Message = 'Berhasil';
				$code = 200;
			} else {
				$code = 400;
				$Message = 'Data Tidak Ditemukan';
				$res = '';
			}

			
			$res = 1;
			$data = $res_data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}


}
