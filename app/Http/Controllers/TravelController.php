<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
// use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class TravelController extends Controller
{
	public function pesawat(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'dari'          	=> 'required',
				'ke'      			=> 'required',
				'penumpang'      	=> 'required',
				'nama_penumpang'      	=> 'required',
				'waktu_kedatangan'	=> 'required',
				'kursi_kelas'       => 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#pesawat <strong>Order Baru dari Pesanan Pesawat </strong>"."\n";
			$txt  .=" dari ". $request->dari ." ke ".$request->ke."\n";
			$txt .="| Penumpang : ".$request->penumpang." | "."\n";
			$txt .="| Nama Penumpang : ".$request->nama_penumpang." | "."\n";
			$txt .="| Waktu Kedatangan : ".$request->waktu_kedatangan." | "."\n";
			$txt .="| Kursi Kelas : ".$request->kursi_kelas." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

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

	// hotel
	public function hotel(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'kota'          	=> 'required',
				'nama_hotel'      	=> 'required',
				'check_in'      	=> 'required',
				'check_out'			=> 'required',
				'tamu'       		=> 'required',
				'rooms'       		=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt  = "#hotel <strong>Order Baru dari Pesanan Hotel </strong>";
			$txt .= "| Kota ". $request->kota."\n";
			$txt .= "| Nama Hotel ". $request->nama_hotel."\n";
			$txt .="| Check In : ".$request->check_in." | "."\n";
			$txt .="| Check Out : ".$request->check_out." | "."\n";
			$txt .="| Tamu : ".$request->tamu." | "."\n";
			$txt .="| Rooms : ".$request->rooms." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

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

	// kereta api
	public function kereta(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'dari'          	=> 'required',
				'ke'      			=> 'required',
				'penumpang_dewasa'  => 'required',
				'penumpang_balita'  => 'required',
				'waktu_kedatangan'	=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#kereta <strong>Order Baru dari Pesanan Pesawat </strong>"."\n";
			$txt  .=" dari ". $request->dari ." ke ".$request->ke."\n";
			$txt .="| Penumpang : ".$request->penumpang_dewasa." | "."\n";
			$txt .="| Waktu Kedatangan : ".$request->waktu_kedatangan." | "."\n";
			// $txt .="| Kursi Kelas : ".$request->kursi_kelas." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

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

	// Bus
	public function bus(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'dari'          	=> 'required',
				'ke'      			=> 'required',
				'penumpang'  		=> 'required',
				'waktu_kedatangan'	=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#bus <strong>Order Baru dari Pesanan Bus </strong>"."\n";
			$txt  .="| Dari ". $request->dari ." Ke ".$request->ke."\n";
			$txt .="| Penumpang : ".$request->penumpang." | "."\n";
			$txt .="| Waktu Kedatangan : ".$request->waktu_kedatangan." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

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

 

	// Shuttle
	public function shuttle(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'dari'          	=> 'required',
				'ke'      			=> 'required',
				'nama_shuttle'		=> 'required',
				'penumpang'  		=> 'required',
				'waktu_kedatangan'	=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#bus <strong>Order Baru dari Pesanan Bus </strong>"."\n";
			$txt  .="| Dari ". $request->dari ." Ke ".$request->ke."\n";
			$txt .="| Penumpang : ".$request->penumpang." | "."\n";
			$txt .="| Waktu Kedatangan : ".$request->waktu_kedatangan." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

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
