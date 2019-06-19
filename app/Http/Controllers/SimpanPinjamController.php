<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\RiwayatModel AS RiwayatModel;
use App\Models\Anggota\RiwayatOrderDetailModel AS RiwayatDetail;

use App\Helpers\Api;
use App\Helpers\RestCurl;

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

	 // pinjam content
	public function getContentPinjam(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$content = array(
				'content' => [
					'Mengisi dan melengkapi form pinjaman Online maupun Cetak',
					'Persetujuan Admin Koperasi',
					'Persetujuan Kepala Koperasi',
					'Dana diterima oleh anggota'
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
}
