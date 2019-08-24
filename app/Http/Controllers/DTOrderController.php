<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Api;
use App\Models\Anggota\OrderPesawatModel as OrderPesawat;
use App\Models\Anggota\OrderListModel AS OrderList;
use Carbon\Carbon;
use DB;
class DTOrderController extends Controller
{
    // backend
	public function getData(Request $request)
	{
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_kategori'	=> 'required|integer',
			// 	'id_layanan'	=> 'required|integer'
			]);

			$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			// $id_layanan = $request->id_layanan ? $request->id_layanan : 0;

            $res = DB::table('view_order')->where('id_kategori',$id_kategori)->join('anggota','view_order.id_anggota','=','anggota.id')->orderBy('id_order','asc')->get();

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
}
