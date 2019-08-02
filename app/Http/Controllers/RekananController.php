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

use App\Models\Anggota\RekananModel AS Rekanan;
use DB;

class RekananController extends Controller
{
	public function detail(Request $request){
		try { 
			
            if(empty($request->json())) throw New \Exception('Params not found', 500);
            
            $this->validate($request, [
				'kelompok_id'      => 'required'
			]);
            
            $kelompok_id = $request->kelompok_id ? $request->kelompok_id : 0;
            
			$data = Rekanan::where('kelompok_id', $kelompok_id)->get();
			
			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
    }
}