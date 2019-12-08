<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\AgamaModel AS AgamaModel;
use App\Models\Anggota\SaldoModel AS Saldo;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use DB;

class Jobs_ParsingDataController extends Controller
{
	const TAHUN = 2018;
	// const TANGGAL = date('Y-m-d');

	public function toko(Request $request){
		try { 
			$tanggal = date('Y-m-d');
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$data_jual = DB::select(DB::raw("SELECT d.nama as namaanggota, a.* from penjualan a inner join anggota d on a.pelangganid = d.id where a.tanggal = '$tanggal' and a.id not in (SELECT id from apps_log_parsing_toko where tanggal = '$tanggal') limit 10 "));
			// and a.id = '20191015-080152'
			$jumlah=0;
			$param=[];
			foreach ($data_jual as $key) {
				

				// cari data
				$cari_data = DB::select(DB::raw("
				SELECT c.id as id_barang, cast(c.hargajual as integer) as harga_barang, cast(a.kuantitas as integer) as qty , c.nama as nama_barang from penjualandetail a inner join barang c on a.barangid = c.id 
				where a.id = '".$key->id."' "));

				// $cari_data = DB::select(DB::raw("SELECT d.nama as namaanggota, a.id as idjual, a.tanggal , c.id as idbarang , a.id , hargajual,  a.pelangganid, 
				// c.nama as namabarang, a.tunai, a.tempo, b.kuantitas from penjualan a 
				// inner join penjualandetail b on a.id = b.id
				// inner join barang c on b.barangid = c.id
				// inner join anggota d on a.pelangganid = d.id
				// where a.tanggal = '$tanggal' and a.pelangganid = '".$key->pelangganid."'
				// and a.id not in (SELECT id from apps_log_parsing_toko where tanggal = '$tanggal') "));

				// $cari_data = [];
				$total=0;
				foreach ($cari_data as $detail) {
					$sub = $detail->harga_barang*$detail->qty;
					$total += $sub;
				}

				$param[] = array(
					'idjual' => $key->id,
					'id_anggota' => $key->pelangganid,
					'tanggal'	=> $key->jam ?? $tanggal,
					'nama_anggota' => $key->namaanggota,
					'telepon' => 1234,
					'ekstensi' => 1,
					'istoko'	=> 1,
					'tunai'	=> 1,
					'cart' => $cari_data,
					'total'	=> $total
				);
				
				// dd($cari_data);
			}
			// die;

			// dd($param);

			// hit ke toko 

			foreach ($param as $send) {
				$ress = (object) RestCurl::exec('POST', env('API_KPL').'toko/buy', $send );
				// insert log
				if($ress->status == 200){
					DB::statement("INSERT INTO apps_log_parsing_toko (id,tanggal) values ('".$send['idjual']."' , '".$tanggal."') ");
				}
			}

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $param;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// simpan pinjam
	public function sp(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$date_jasa = date('Y')-1;
			$date_jasa_dua = date('Y')-2;
			$date = self::TAHUN;

			$cari_jasa_pinjaman_all = DB::select(DB::raw("SELECT anggotaid from pinjaman a
				inner join pinjjenis b on a.jenisid = b.id
				inner join apps_jasa_pinjaman f on b.kode = f.kode
				where year(tanggal) = $date_jasa ")); 

			$jumlah_jasa_pinjaman_all = 0;
			foreach ($cari_jasa_pinjaman_all as $jasa_all) {
				$cari_jasa_pinjaman_all_dua = DB::select(DB::raw("SELECT a.anggotaid, a.tanggal, a.jangkawaktu, a.angsuranke, a.plafon, f.jasa
					from pinjaman a 
					inner join pinjjenis b on a.jenisid = b.id
					inner join apps_jasa_pinjaman f on b.kode = f.kode
					where anggotaid = '".$jasa_all->anggotaid."' 
					limit 1 "));

				$res_all = ( ( $cari_jasa_pinjaman_all_dua[0]->jasa * $cari_jasa_pinjaman_all_dua[0]->plafon ) / 100 ) * 12 ;

				$jumlah_jasa_pinjaman_all += $res_all;
			}

			// cari dulu datanya ada atau enggak 
			$cari_data_kolektif_sebelumnya = DB::select(DB::raw("SELECT tahun from apps_kolektif_data where tahun = $date"));

			if(isset($cari_data_kolektif_sebelumnya[0]->tahun)){
				// update
				$update = DB::statement("UPDATE apps_kolektif_data set total_jasa_pinjaman = $jumlah_jasa_pinjaman_all where tahun = $date ");
			} else {
				$update = DB::statement("INSERT INTO apps_kolektif_data (tahun,total_jasa_pinjaman) values  ($date,$jumlah_jasa_pinjaman_all) ");
			}
			// end



			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = ['angka' => $update];
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}


	// simpanan / investasi / modal
	public function modal(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$date = self::TAHUN;
			$total = 1;

			$simpanan_pokok_all = DB::select(DB::raw("SELECT a.norekening, b.nama, c.nama as namaanggota from tabungan a 
				inner join tabjenis b on a.jenisid = b.id
				inner join anggota c on a.anggotaid = c.id
				where 
				jenis in (1,2)")); 
			$total = 0;
			foreach ($simpanan_pokok_all as $simpanan_all) {
				$cari_simpanan_tabungan_all = DB::select(DB::raw("
					SELECT saldo from accjurnal a 
					inner join accjurnaldetail b on a.id = b.id
					inner join tabtransaksi c on a.id = c.jurnalid
					where a.keterangan like '%".$simpanan_all->norekening."%'
					group by a.id, saldo
					order by a.tanggal desc 
					limit 1 ")); 
				if(!empty($cari_simpanan_tabungan_all[0])){
					$total += $cari_simpanan_tabungan_all[0]->saldo;		
				}
			}

			// cari dulu datanya ada atau enggak 
			$cari_data_kolektif_sebelumnya = DB::select(DB::raw("SELECT tahun from apps_kolektif_data where tahun = $date"));

			if(isset($cari_data_kolektif_sebelumnya[0]->tahun)){
				// update
				$update = DB::statement("UPDATE apps_kolektif_data set total_simpanan_pokok = $total where tahun = $date ");
			} else {
				$update = DB::statement("INSERT INTO apps_kolektif_data (tahun,total_simpanan_pokok) values  ($date,$total) ");
			}
			// end

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = ['angka' => $update];
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	// simpanan / investasi / modal
	public function saldo(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$date = self::TAHUN;
			$total = 1;
			$tanggal = '2019-12-01';
			$status = 1;
			$bulan = 12;
			$tahun = 2019;
			$saldo = 500000;
			// INSERT into apps_saldo_monthly
			$saldo = DB::select(DB::raw("
				SELECT id , $saldo as saldo, '$tanggal' as date , $status as status from anggota
				where ( noanggota not like '%P.%' and noanggota not like '%P2.%' )
				and id not in (SELECT id from apps_saldo_monthly where MONTH(date) = '$bulan' 
				and YEAR(date) = $tahun) ")); 
			$saldos = json_decode(json_encode($saldo), true);
			// dd($saldos = json_decode(json_encode($saldo), true));
			// dd($saldos);
// 			$data = array(
//     array('name'=>'Coder 1', 'rep'=>'4096'),
//     array('name'=>'Coder 2', 'rep'=>'2048'),
//     //...
// );
			// print_r([$saldos]); die;
			// $insert = array($saldos);
			// end

			$oke = Saldo::insert($saldos);

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = ['angka' => $oke];
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

}
