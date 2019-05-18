<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Helpers\Api;
use App\Helpers\RestCurl;

// model
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;



class TravelController extends Controller
{
	public function pesawat(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'dari'          		=> 'required',
				'ke'      				=> 'required',
				'penumpang'      		=> 'required',
				'nama_penumpang'      	=> 'required',
				'waktu_keberangkatan'	=> 'required',
				'kursi_kelas'       	=> 'required',
				'nama_anggota'			=> 'required',
				// 'id_anggota'			=> 'required'
			]);   
			

			// insert header order
			$insert_order = array(
				'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d'),
				'id_layanan' => 1,
				'id_kategori' => 1
			);
			$id_order = Order::insertGetId($insert_order);


			// insert header order detail
			$insert_order_detail = array(
				'id_order' 		=> $id_order,
				'dari' 			=> $request->dari ? $request->dari : NULL,
				'ke' 			=> $request->ke ? $request->ke : NULL,
				'penumpang' 	=> $request->penumpang ? $request->penumpang : NULL,
				'waktu_keberangkatan' 	=> $request->waktu_keberangkatan ? $request->waktu_keberangkatan : NULL,
				'kursi_kelas' 	=> $request->kursi_kelas ? $request->kursi_kelas : NULL,
				'nama_penumpang' 	=> $request->nama_penumpang ? $request->nama_penumpang : NULL,
				// 'nama_anggota' 	=> $request->nama_anggota ? $request->nama_anggota : NULL,
				// 'id_anggota' 	=> $request->id_anggota ? $request->id_anggota : NULL,
			);
			OrderDetail::insert($insert_order_detail);


			// notif to telegram
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#pesawat #IDORDER-".$id_order." <strong>Order Baru dari Pesanan Pesawat </strong>"."\n";
			$txt  .=" dari ". $request->dari ." ke ".$request->ke."\n";
			$txt .="| Penumpang : ".$request->penumpang." | "."\n";
			$txt .="| Nama Penumpang : ".$request->nama_penumpang." | "."\n";
			$txt .="| Waktu Kedatangan : ".$request->waktu_keberangkatan." | "."\n";
			$txt .="| Kursi Kelas : ".$request->kursi_kelas." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');


			$Message = 'Order Pesawat Berhasil';
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
				// 'id_anggota'		=> 'required'
			]);  


			// insert header order
			// $insert_order = array(
			// 	'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
			// 	'tanggal_order' => date('Y-m-d'),
			// 	'id_layanan' => 1,
			// 	'id_kategori' => 1
			// );
			// $id_order = Order::insertGetId($insert_order);


			// // insert header order detail
			// $insert_order_detail = array(
			// 	'id_order' 		=> $id_order,
			// 	'dari' 			=> $request->dari ? $request->dari : NULL,
			// 	'ke' 			=> $request->ke ? $request->ke : NULL,
			// 	'penumpang' 	=> $request->penumpang ? $request->penumpang : NULL,
			// 	'waktu_keberangkatan' 	=> $request->waktu_keberangkatan ? $request->waktu_keberangkatan : NULL,
			// 	'kursi_kelas' 	=> $request->kursi_kelas ? $request->kursi_kelas : NULL,
			// 	'nama_penumpang' 	=> $request->nama_penumpang ? $request->nama_penumpang : NULL,
			// 	// 'nama_anggota' 	=> $request->nama_anggota ? $request->nama_anggota : NULL,
			// 	// 'id_anggota' 	=> $request->id_anggota ? $request->id_anggota : NULL,
			// );
			// OrderDetail::insert($insert_order_detail);

            // notif instagram
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
				// 'id_anggota'		=> 'required'
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
				// 'id_anggota'		=> 'required'
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
				// 'id_anggota'		=> 'required'
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
