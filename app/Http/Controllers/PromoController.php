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


}
