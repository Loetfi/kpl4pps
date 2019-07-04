<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\RiwayatModel AS RiwayatModel;
use App\Models\Anggota\RiwayatOrderDetailModel AS RiwayatDetail;
use App\Helpers\Telegram;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;

use App\Models\Anggota\AnggotaModel AS Anggota;
use App\Models\Anggota\OrderModel AS Order;
use App\Models\Anggota\OrderDetailModel AS OrderDetail;

class SimpanPinjamController extends Controller
{
	public function getContentSimpan(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$content = array(
				'content' => [
					'Suku bunga dapat berubah sesuai dengan kebijaksanaan pengurus KPL dan akan berlaku untuk penempatan yang dilakukan mulain tanggal efektif perubahan suku bunga tersebut.',
					'Atas bunga simpanan yang diterima oleh penabung akan dipotong pajak penghasilan sebesar 10% sesuai dengan ketentuan perpajakan yang berlaku',
					'Bunga simpanan berjangka dibayarkan dari rekening bank KPL melalui pemindah bukuan atau transfer bank 
					setiap bulan pada tanggal jatuh tempo bunga ke rekening atas nama penabung yang dicantumkan dalam aplikasi simpanan berjangka KPL',
					'Apabila tanggal pembayaran bunga jatuh pada hari Sabtu atau hari libur, maka pembayaran akan dilakukan pada hari kerja berikutnya.'
				],
				'pdf_form' => 'http://sub7.ce.student.pens.ac.id/lab/dasarpemrogramangolang.pdf'
			); 

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $content;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// submit 
	public function submitSimpanan(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'keterangan'        => 'required',
				'store_ke'			=> 'required',
				'jumlah_simpanan'	=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#simpanan <strong>Order Baru dari Simpanan </strong>"."\n";
			$txt  .="| Keterangan : ". $request->keterangan ."\n";
			$txt  .="| Jumlah Simpanan : ". $request->jumlah_simpanan ."\n";
			$txt  .="| Setor Ke : ". $request->store_ke ."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			// insert header order
			$insert_order = array(
				'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d'),
				'id_layanan' => 3,
				'id_kategori' => 13
			);
			$id_order = Order::insertGetId($insert_order);


			// insert header order detail
			$insert_order_detail = array(
				'id_order' 			=> $id_order,
				'keterangan'	=> $request->keterangan ? $request->keterangan : NULL,
				'store_ke'	=> $request->store_ke ? $request->store_ke : NULL,
				'jumlah_simpanan'	=> $request->jumlah_simpanan ? $request->jumlah_simpanan : NULL,
			);
			OrderDetail::insert($insert_order_detail);

			$get_anggota = Anggota::where('id' , $request->id_anggota)->select('noanggota')->get()->first();

			$result = Notif::push($get_anggota->noanggota, 'Simpanan Berhasil' , 'Data akan diproses oleh admin koperasi pegawai lemigas');

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

	 // pinjam content
	public function getContentPinjam(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$content = array(
				'content' => [
					'Perjanjian pinjaman ini disusun berdasarkan kesepakatan antara Koperasi Pegawai Lemigas (selanjutnya disingkat KPL) dengan Pemohon Pinjaman (selanjutnya disebut Pemohon). Perjanjian ini berlaku sejak ditandatangani dan berakhir setelah Pemohon melunasi seluruh pinjamannya.',
					'Pemohon melunasi/mengembalikan pinjaman kepada KPL sebesar pokok pinjaman dan bunga pinjaman dalam jangka waktu yang disepakati. Cara pelunasan/angsuran pinjaman dilakukan dengan cara memotong uang pada rekening gaji Pemohon.',
					'Apabila Pemohon mengalami mutasi/pindah tugas dari Lemigas, maka Pemohon setuju bahwa Perjanjian ini berlaku sebagai pemberian Kuasa kepada Bendahara/Pengelola Gaji tempat Pemohon bertugas/berdinas untuk memotong uang pada rekening gaji Pemohon sejumlah angsuran yang harus bayar. KPL memberitahukan secara tertulis kepada Bendahara/Pengelola Gaji tempat Pemohon bertugas/berdinas sebesar angsuran yang harus dipotong.',
					'Pemohon menjamin bahwa Bendahara/Pengelola Gaji tempat Pemohon bertugas/berdinas dibebaskan dari segala tuntutan dalam bentuk apapun dan atau gugatan dari pihak manapun.',
					'Apabila setelah jatuh tempo Pemohon tidak dapat melunasi seluruh atau sisa pinjaman, maka Pemohon setuju agar KPL menyerahkan jaminan/agunan (jika ada) Pemohon kepada Kantor Pelayanan Kekayaan Negara dan Lelang (KPKNL) untuk kemudian dilelang menurut ketentuan yang berlaku. ',
					'Segala biaya yang timbul (jika ada) akibat pembayaran angsuran hutang kepada KPL dengan layanan perbankan dan/atau lelang jaminan/agunan menjadi tanggungan Pemohon.',
					'Segala pelanggaran terhadap sebagian atau seluruh ketentuan ini menjadi tanggungan dan tanggung jawab Pemohon dengan segala konsekuensinya.'
					// 'Mengisi dan melengkapi form pinjaman Online maupun Cetak',
					// 'Persetujuan Admin Koperasi',
					// 'Persetujuan Kepala Koperasi',
					// 'Dana diterima oleh anggota'
				],
				'pdf_form' => 'http://kpl.awanesia.com/public/FormulirPinjamanKPL(New).docx'
			); 

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $content;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// submit 
	public function submitPinjaman(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'keterangan'        => 'required',
				'nilai_pinjaman'	=> 'required',
				'tenor'				=> 'required',
				'nama_anggota'		=> 'required',
				'id_anggota'		=> 'required'
			]);  

            // return 'oke';
			$token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
			$chatId = "-384536993";
			$txt   ="#pinjaman <strong>Order Baru dari Pinjaman </strong>"."\n";
			$txt  .="| Keterangan : ". $request->keterangan ."\n";
			$txt  .="| Jumlah Pinjaman : ". $request->nilai_pinjaman ."\n";
			$txt  .="| Tenor : ". $request->tenor ."\n";
			$txt .="| Dari Nama Anggota : ".$request->nama_anggota."\n"; 

			$telegram = new Telegram($token);
			$telegram->sendMessage($chatId, $txt, 'HTML');

			// insert header order
			$insert_order = array(
				'id_anggota' => $request->id_anggota ? $request->id_anggota : 0,
				'tanggal_order' => date('Y-m-d'),
				'id_layanan' => 3,
				'id_kategori' => 14
			);
			$id_order = Order::insertGetId($insert_order);


			// insert header order detail
			$insert_order_detail = array(
				'id_order' 			=> $id_order,
				'keterangan'	=> $request->keterangan ? $request->keterangan : NULL,
				'tenor'	=> $request->tenor ? $request->tenor : NULL,
				'nilai_pinjaman'	=> $request->nilai_pinjaman ? $request->nilai_pinjaman : NULL,
			);
			OrderDetail::insert($insert_order_detail);

			$get_anggota = Anggota::where('id' , $request->id_anggota)->select('noanggota')->get()->first();

			$result = Notif::push($get_anggota->noanggota, 'Pinjaman Berhasil' , 'Data akan diproses oleh admin koperasi pegawai lemigas');

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
