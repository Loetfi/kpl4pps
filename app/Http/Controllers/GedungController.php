<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\LayananModel AS Layanan;
use App\Models\Anggota\KategoriLayananModel AS Kategori;
use App\Helpers\Telegram;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;
use Carbon\Carbon;

use App\Models\Anggota\AnggotaModel AS Anggota;
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;
use App\Models\Anggota\PaketModel AS PaketGedung;
use DB;

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
	
	// order yang ada dalam tanggal terpilih 
	public function periodeBooking(Request $request){
		try {
			
			if(empty($request->json())) throw New \Exception('Params not found', 500);
			
			$this->validate($request, [
				'start_date'      => 'required',
				'id_kategori'      => 'required',
				]);
				
				$tanggal_bulan_depan = Carbon::parse($request->start_date)->addMonths(1);
				
				$res = Order::where('id_kategori',$request->id_kategori)->where('tanggal_order','>=',$request->start_date)->where('tanggal_order','<=',$tanggal_bulan_depan)->groupBy(DB::raw("DATE_FORMAT(tanggal_order, '%Y-%m-%d')"))->select('tanggal_order')->get();
				
				foreach	($res as $key => $value ){
					$respon[] = date('Y-m-d', strtotime($value['tanggal_order']));
				}
				
				
				$Message = 'Berhasil';
				$code = 200;
				$data = !empty($respon) ? $respon : [];
				
			} catch(Exception $e) {
				$res = 0;
				$Message = $e->getMessage();
				$code = 400;
				$data = '';
			}
			return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
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
					
					$photo = explode(';',$get_kategori->photo);
					$denah = explode(';',$get_kategori->denah);
					
					$data = array(
						'id_kategori'	=> $get_kategori->id_kategori,
						'nama_kategori'	=> $get_kategori->nama_kategori,
						'gambar_kategori'	=> $get_kategori->gambar_kategori,
						'penjelasan'	=> $get_kategori->penjelasan,
						'video'	=> $get_kategori->video,
						'photo'	=> $photo,
						'paket'	=> PaketGedung::where('id_kategori', $get_kategori->id_kategori)->get(),
						'denah'	=> $denah,
						'sk'		=> $get_kategori->sk,
						'rekanan'	=> [
							[
								'id'		=> 1,
								'nama'		=> 'MUA',
								'gambar'	=> 'https://i.pinimg.com/236x/b6/87/59/b687599d203c2e6a204bd7f022c14f6d--blank-wallpaper-apples.jpg'
							],
							[
								'id'		=> 2,
								'nama'		=> 'Cathering',
								'gambar'	=> 'https://i.pinimg.com/236x/b6/87/59/b687599d203c2e6a204bd7f022c14f6d--blank-wallpaper-apples.jpg'
							],
							[
								'id'		=> 3,
								'nama'		=> 'Florist',
								'gambar'	=> 'https://i.pinimg.com/236x/b6/87/59/b687599d203c2e6a204bd7f022c14f6d--blank-wallpaper-apples.jpg'
							],
							[
								'id'		=> 4,
								'nama'		=> 'Decoration',
								'gambar'	=> 'https://i.pinimg.com/236x/b6/87/59/b687599d203c2e6a204bd7f022c14f6d--blank-wallpaper-apples.jpg'
							],
							[
								'id'		=> 5,
								'nama'		=> 'Favor & Gift',
								'gambar'	=> 'https://i.pinimg.com/236x/b6/87/59/b687599d203c2e6a204bd7f022c14f6d--blank-wallpaper-apples.jpg'
								]
								]
							);
							$get_kategori = $data;
							
							if ($check > 0) {
								
								
							} else {
								if(empty($request->json())) throw New \Exception('Channel tidak diberika otoritas form dinamis', 400);
							} 
							
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
					
					//  submit
					public function submit(Request $request){
						try {
							
							if(empty($request->json())) throw New \Exception('Params not found', 500);
							
							$this->validate($request, [
								'tanggal_book'		=> 'required',
								'paket'				=> 'required',
								'nama_kategori'		=> 'required',
								'nama_anggota'		=> 'required',
								'id_layanan'		=> 'required',
								'id_kategori'		=> 'required',
								'nama_kategori'		=> 'required',
								'telepon'			=> 'required',
								'ekstensi'			=> 'nullable',
								'keterangan'			=> 'nullable'
								]);
								
								$insert_order = array(
									'id_anggota' 	=> $request->id_anggota ? $request->id_anggota : 0,
									'tanggal_order' => date('Y-m-d H:i:s'),
									'id_layanan' 	=> $request->id_layanan ? $request->id_layanan : 0,
									'id_kategori' 	=> $request->id_kategori ? $request->id_kategori : 0,
									'telepon'		=> $request->telepon ? $request->telepon : 0,
									// 'ekstension'	=> !empty($request->ekstensi) ? $request->ekstensi : 0,
									
								);
								$id_order = Order::insertGetId($insert_order);
								
								// $nama_penumpang = implode(';', $request->nama_penumpang);
								
								// insert header order detail
								$insert_order_detail = array(
									'id_order' 		=> $id_order ? $id_order : 0,
									'tanggal_book' 	=> $request->tanggal_book ? $request->tanggal_book : NULL,
									'no_hp' 		=> $request->telepon ? $request->telepon : NULL,
									'pilihan_paket'	=> $request->paket ? $request->paket : 0,
									'keterangan'	=> !empty($request->keterangan) ? $request->keterangan : 0
								);
								OrderDetail::insert($insert_order_detail);
								
								
								
								
								$id_anggota = $request->id_anggota ? $request->id_anggota : 0;
								$get_anggota = Anggota::where('id' , $id_anggota)->select('noanggota')->get()->first();
								$result = Notif::push($get_anggota->noanggota, 'Order '.$request->nama_kategori.' '.$request->nama_kategori. ' Berhasil' , 'Pesanan akan diproses oleh admin koperasi pegawai lemigas');
								
								// notif to telegram
								$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
								$chatId = "-384536993";
								$txt   ="#sewagedungforumtekno ".$request->nama_kategori."  #IDORDER-".$id_order." <strong>Order Baru dari ".$request->nama_kategori." </strong>"."\n";
								$txt  .=" Tanggal Booking ". $request->tanggal_book ."\n"; 
								$txt .="| Dari Nama Anggota : ".$request->nama_anggota." | "."\n";  
								$txt .="| Dari No Anggota : ".$get_anggota->noanggota."\n";
								$telegram = new Telegram($token);
								$telegram->sendMessage($chatId, $txt, 'HTML');
								
								$Message = 'Order ' .$request->nama_kategori. ' Berhasil';
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
						
						public function history(Request $request){
							try { 
								
								if(empty($request->json())) throw New \Exception('Params not found', 500);
								
								$this->validate($request, [
									'id_kategori'          	=> 'required|integer'
									]);
									
									$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
									
									$res = Order::where('id_kategori',$id_kategori)->where('approval',1)->take(10)->select('peruntukan_order')->orderBy('tanggal_order','DESC')->get();
									
									$Message = 'Berhasil';
									$code = 200;
									// $res = 1;
									$data = $res;
								} catch(Exception $e) {
									$res = 0;
									$Message = $e->getMessage();
									$code = 400;
									$data = '';
								}
								return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
							}
							
							
							
						}
						
						
						