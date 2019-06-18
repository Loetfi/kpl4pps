<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\TokoModel AS TokoModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;
// model
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;
use App\Models\Anggota\AnggotaModel AS Anggota;

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

	// cari barang 
	public function searching(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'nama'		=> 'required',
				'offset'	=> 'required|integer',
				'limit'		=> 'required|integer'
			]);

			$nama = $request->nama ? $request->nama : 0;

			$data_res = TokoModel::where('nama','like','%'.$nama.'%')->orwhere('namakategori','like','%'.$nama.'%')->skip($request->offset)->take($request->limit)->get();
			
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


	// kategori toko barng 
	public function list_kategori(Request $request){
		try { 
			$data_res = TokoModel::select('namakategori as id' , 'namakategori as nama')->groupby('namakategori')->get();

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

	// pilih_kategori
	public function pilih_kategori(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'namakategori'	=> 'required',
				'offset'	=> 'required|integer',
				'limit'		=> 'required|integer'
			]);

			$nama = $request->namakategori ? $request->namakategori : 0;

			$data_res = TokoModel::where('namakategori','like','%'.$nama.'%')->skip($request->offset)->take($request->limit)->get();

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


	// add order
	public function buy(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'cart'          		=> 'required',
				'id_anggota'			=> 'required',
				'telepon'				=> 'required',
				'ekstensi'				=> 'required',
				'total'					=> 'required',
				'nama_anggota'			=> 'required',

			]);   

			// insert header order
			$insert_order = array(
				'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d'),
				'id_layanan' => 2,
				'id_kategori' => 7,
				'telepon'		=> $request->telepon ? $request->telepon : 0,
				'ekstension'	=> $request->ekstensi ? $request->ekstensi : 0,
				'total'			=> $request->total ? $request->total : 0,
			);
			$id_order = Order::insertGetId($insert_order);

			// $nama_penumpang = implode(';', $request->nama_penumpang);

			// insert header order detail
			$cart = $request->cart ? $request->cart : [];
			foreach ($cart as $c) {
				
				$insert_order_detail = array(
					'id_order' 			=> $id_order ? $id_order : 0,
					'nama_barang' 		=> $c['nama_barang'] ? $c['nama_barang'] : NULL,
					'id_barang' 		=> $c['id_barang'] ? $c['id_barang'] : 0,
					'harga_barang' 		=> $c['harga_barang'] ? $c['harga_barang'] : 0,
					'qty' 				=> $c['qty'] ? $c['qty'] : 0
				);

				OrderDetail::insert($insert_order_detail);
			}


			// notif to telegram
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#toko #IDORDER-".$id_order." <strong>Order Baru dari TOKO </strong>"."\n";
			$txt .="----------------------------\n";
			$number = 1;
			foreach ($cart as $c) {
				
				$txt .="| Keranjang (".$number.") | "."\n";
				$txt .="| Nama Barang : ".$c['nama_barang']." | "."\n";
				$txt .="| Harga Barang : ".$c['harga_barang']." | "."\n";
				$txt .="| Qty : ".$c['qty']." | "."\n";
				$number++;
			}
			$txt .="----------------------------\n";

			$txt .="| Total Belanja : ".$request->total." | "."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n"; 
			$txt .="| No Telepon : ".$request->telepon." | "."\n"; 
			$txt .="| Ekstensi: ".$request->ekstensi." | "."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			$get_anggota = Anggota::where('id' , $request->id_anggota)->select('noanggota')->get()->first();

			$result = Notif::push($get_anggota->noanggota, 'Order Toko Berhasil' , 'Pesanan akan diproses oleh admin koperasi pegawai lemigas');


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
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:''),isset($code)?$code:200);
	}

}
