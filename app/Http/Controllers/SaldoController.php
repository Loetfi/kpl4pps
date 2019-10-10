<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\AgamaModel AS AgamaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use DB;

class SaldoController extends Controller
{
	public function sisa(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_anggota'          	=> 'required'
			]);

			$bulan = date('m');
			$tahun = date('Y');

			$anggotaid = $request->id_anggota ? $request->id_anggota : 0;

			$data_saldo = DB::select(DB::raw("SELECT sum(saldo) as saldo , sum(harga) as harga , sum(saldo) - sum(harga) as saldosisa from (
				SELECT b.saldo , 0 as harga from anggota a 
				inner join apps_saldo_monthly b on a.id = b.id
				where b.status = 1 and ( MONTH(date) = $bulan and YEAR(date) = $tahun )
				and a.id = '$anggotaid'
				union all
				SELECT 0 as saldo, sum(harga) from penjualan a 
				inner join penjualandetail b on a.id = b.id
				where pelangganid = '$anggotaid'
				and ( MONTH(tanggal) = $bulan and YEAR(tanggal) = $tahun )
				and tempo > 0 ) sisa "));


			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_saldo;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
