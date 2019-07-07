<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\LayananModel AS Layanan;
use App\Models\Anggota\KategoriLayananModel AS Kategori;
use App\Helpers\Telegram;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;

use App\Models\Anggota\AnggotaModel AS Anggota;
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;

class GedungController extends Controller
{
	public function list(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$data_agama = Layanan::join('apps_kategori_channel','apps_layanan.id_layanan','=','apps_kategori_channel.id_layanan')->select('nama_kategori','gambar_kategori','id_kategori','apps_kategori_channel.id_layanan')->where('apps_layanan.id_layanan',6)->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_agama;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	public function submit(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'tanggal_book'		=> 'required',
				'nama_anggota'		=> 'required',
				'id_layanan'		=> 'required',
				'id_kategori'		=> 'required',
				'nama_kategori'		=> 'required',
				'telepon'			=> 'required',
				'ekstensi'		=> 'required',

			]);   
			$insert_order = array(
				'id_anggota' 	=> $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d H:i:s'),
				'id_layanan' 	=> $request->id_layanan ? $request->id_layanan : 0,
				'id_kategori' 	=> $request->id_kategori ? $request->id_kategori : 0,
				'telepon'		=> $request->telepon ? $request->telepon : 0,
				'ekstension'	=> $request->ekstensi ? $request->ekstensi : 0
			);
			$id_order = Order::insertGetId($insert_order);

			// $nama_penumpang = implode(';', $request->nama_penumpang);

			// insert header order detail
			$insert_order_detail = array(
				'id_order' 		=> $id_order ? $id_order : 0,
				'tanggal_book' 	=> $request->tanggal_book ? $request->tanggal_book : NULL,
				'no_hp' 		=> $request->telepon ? $request->telepon : NULL,
			);
			OrderDetail::insert($insert_order_detail);


			// notif to telegram
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#serbausaha".$request->nama_kategori."  #IDORDER-".$id_order." <strong>Order Baru dari Serba Usaha </strong>"."\n";
			$txt  .=" Tanggal Booking ". $request->tanggal_book ."\n"; 
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n";  
			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			$get_anggota = Anggota::where('id' , $request->id_anggota)->select('noanggota')->get()->first();
			$result = Notif::push($get_anggota->noanggota, 'Order Serba Usaha '.$request->nama_kategori. ' Berhasil' , 'Pesanan akan diproses oleh admin koperasi pegawai lemigas');


			$Message = 'Order Serba Usaha ' .$request->nama_kategori. ' Berhasil';
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

	// booking serba usaha
	public function detail(Request $request){
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_kategori'          	=> 'required|integer'
			]);

			$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			$get_kategori = Kategori::where('id_kategori' , $id_kategori)->get()->first();
			$check = $get_kategori->id_layanan ? $get_kategori->id_layanan : 0; 

			if ($check > 0) { 


			} else {
				if(empty($request->json())) throw New \Exception('Channel tidak diberika otoritas form dinamis', 400);
			}

			// dd($get_kategori);


			// $data_agama = Layanan::join('apps_kategori_channel','apps_layanan.id_layanan','=','apps_kategori_channel.id_layanan')->select('nama_kategori','gambar_kategori','id_kategori') ->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $get_kategori;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}


