<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
// use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;


// model
use App\Models\Anggota\AnggotaModel AS Anggota;
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;

class ListrikController extends Controller
{
	public function token(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'no_meter'          => 'required',
				'nominal_token'		=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#token-listrik <strong>Order Baru dari Pesanan Token Listrik </strong>"."\n";
			$txt  .="| No Meter : ". $request->no_meter ."\n";
			$txt  .="| Nominal : ". $request->nominal_token ."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			// insert header order
			$insert_order = array(
				'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d'),
				'id_layanan' => 5,
				'id_kategori' => 8
			);
			$id_order = Order::insertGetId($insert_order);


			// insert header order detail
			$insert_order_detail = array(
				'id_order' 				=> $id_order,
				'no_meter'				=> $request->no_meter ? $request->no_meter : NULL,
				'nominal_token'			=> $request->nominal_token ? $request->nominal_token : NULL,
			);
			OrderDetail::insert($insert_order_detail);

			$get_anggota = Anggota::where('id' , $request->id_anggota)->select('noanggota')->get()->first();

			$result = Notif::push($get_anggota->noanggota, 'Order Listrik Token Berhasil' , 'Pesanan akan diproses oleh admin koperasi pegawai lemigas');

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
