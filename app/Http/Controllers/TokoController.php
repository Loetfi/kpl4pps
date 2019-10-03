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

// backend 
use App\Models\Anggota\OrderListModel AS OrderList;
use DB;

class TokoController extends Controller
{
	// backend
	public function listData(Request $request)
	{
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_kategori'	=> 'required|integer',
				'id_layanan'	=> 'required|integer'
			]);

			$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			$id_layanan = $request->id_layanan ? $request->id_layanan : 0;

			$res = OrderList::where('id_layanan',$id_layanan)->where('id_kategori',$id_kategori)->join('anggota','view_order_list.id_anggota','=','anggota.id')->select('view_order_list.*','anggota.nama')->orderBy('id_order','asc')->get();

			$Message = 'Berhasil';
			$code = 200;
			$data = $res;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	public function detailData(Request $request)
	{
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_kategori'	=> 'required|integer',
				'id_layanan'	=> 'required|integer',
				'id_order'		=> 'required|integer',
			]);

			$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			$id_layanan = $request->id_layanan ? $request->id_layanan : 0;
			$id_order = $request->id_order ? $request->id_order : 0;

			$header = OrderList::where('id_layanan',$id_layanan)->where('id_kategori',$id_kategori)->join('anggota','view_order_list.id_anggota','=','anggota.id')->where('id_order',$id_order)->select('view_order_list.*','anggota.nama')->orderBy('tanggal_order','desc')->get()->first();

			$res =  DB::table('view_order_detail')
			->join('anggota','view_order_detail.id_anggota','=','anggota.id')
			->where('id_layanan',$id_layanan)
			->where('id_kategori',$id_kategori)
			->where('id_order',$id_order)
			->select('view_order_detail.*','anggota')
			->get();

			$Message = 'Berhasil';
			$code = 200;
			$data = array('header' => $header, 'detail' => $res);
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}


	public function ProsesApproval(Request $request)
	{
		try {
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_order'		=> 'required|integer',
				'status'		=> 'required'
			]);

			$id_order = $request->id_order ? $request->id_order : 0;
			$status = $request->status ? $request->status : 0;
			$kategori = !empty($request->kategori) ? @$request->kategori : 'Toko';

			$ubah = array(
				'approval' => $status
			);

			$res = Order::where('id_order',$id_order)->update($ubah);

			if ($res) {
				$code = 200;
				$Message = 'Berhasil di Approve';

				// kirim notifikasi ke pengguna 
				$user = Order::where('id_order',$id_order)->get()->first();
				$anggota = Anggota::where('id',$user->id_anggota)->get()->first();

				// dd($user->id_anggota);
				if ($status == 1) {
					$res_notif = Notif::push($anggota->noanggota , 'Status Order '.$kategori. ' #'.$user->id_order. '- KP Lemigas' , 'Berhasil di Setujui');

				} elseif ($status == 0) {
					$res_notif = Notif::push($anggota->noanggota , 'Status Order '.$kategori. ' #'.$user->id_order. '- KP Lemigas' , 'Tidak Berhasil di Setujui');
				}
				// end 
			} else {
				$code = 400;
				$Message = 'Tidak Berhasil di Approve';
			} 			
			$data = [];
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// end
	public function data(Request $request){
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'offset'          		=> 'required|integer',
				'limit'					=> 'required|integer'
			]);

			$data_res = TokoModel::skip($request->offset)->take($request->limit)->orderby('nama','asc')->get();


			foreach ($data_res as $harga) {
				$data['hargajual'] = round($harga->hargajual);
				$data['id'] = $harga->id;
				$data['nama'] = $harga->nama;
				$data['namasatuan'] = $harga->namasatuan;
				$data['namakategori'] = $harga->namakategori;
				$data['apps_gambar_barang'] = $harga->apps_gambar_barang;

				$res_data[] = $data;
			}

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

			foreach ($data_res as $harga) {
				$data['hargajual'] = round($harga->hargajual);
				$data['id'] = $harga->id;
				$data['nama'] = $harga->nama;
				$data['namasatuan'] = $harga->namasatuan;
				$data['namakategori'] = $harga->namakategori;
				$data['apps_gambar_barang'] = $harga->apps_gambar_barang;

				$res_data[] = $data;
			}
			
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

			foreach ($data_res as $harga) {
				$data['hargajual'] = round($harga->hargajual);
				$data['id'] = $harga->id;
				$data['nama'] = $harga->nama;
				$data['namasatuan'] = $harga->namasatuan;
				$data['namakategori'] = $harga->namakategori;
				$data['apps_gambar_barang'] = $harga->apps_gambar_barang;

				$res_data[] = $data;
			}
			
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

			foreach ($data_res as $harga) {
				$data['hargajual'] = round($harga->hargajual);
				$data['id'] = $harga->id;
				$data['nama'] = $harga->nama;
				$data['namasatuan'] = $harga->namasatuan;
				$data['namakategori'] = $harga->namakategori;
				$data['apps_gambar_barang'] = $harga->apps_gambar_barang;

				$res_data[] = $data;
			}

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
