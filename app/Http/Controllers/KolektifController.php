<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\KolektifDataModel AS KD;
use App\Helpers\Api;

class KolektifController extends Controller
{
	public function update(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'tahun'          		=> 'required|integer',
				'shu_toko_sp'			=> 'required|integer',
				'shu_modal'				=> 'required|integer'
			]);

			$tahun = $request->tahun ?? 0;
			$shu_toko_sp = $request->shu_toko_sp ?? 0;
			$shu_modal = $request->shu_modal ?? 0;

			$update = array(
				'shu_toko_sp' => $shu_toko_sp,
				'shu_modal' => $shu_modal,
			);
			$data_res = KD::where('tahun' , $tahun)->update($update);

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
}
