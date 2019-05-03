<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
// use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class PulsaPaketController extends Controller
{
	public function pulsa(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'provider'          => 'required',
				'nohp'				=> 'required',
				'nominal'			=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#pulsa <strong>Order Baru dari Pesanan Pulsa </strong>"."\n";
			$txt  .="| No HP : ". $request->nohp ."\n";
			$txt  .="| Nominal : ". $request->nominal ."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = '';
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	public function paketdata(Request $request){
		try {

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'provider'          => 'required',
				'nohp'				=> 'required',
				'paket'			=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#paketdata <strong>Order Baru dari Pesanan Pulsa </strong>"."\n";
			$txt  .="| No HP : ". $request->nohp ."\n";
			$txt  .="| Nominal : ". $request->paket ."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = '';
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
