<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
// use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif;

// model
use App\Models\Anggota\NotifModel AS NM;
use App\Models\Anggota\AnggotaModel AS Anggota;

class NotificationController extends Controller
{
	public function list(Request $request){
		try {

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
                'id_anggota'    => 'required',
                'limit'		    => 'required',
                'offset'		=> 'required'
			]);

            // insert header order detail
            $id_anggota = $request->id_anggota ? $request->id_anggota : 0;
            $take = $request->limit ? $request->limit : 0;
            $skip = $request->offset ? $request->offset : 0;
            $get_anggota = Anggota::where('id' , $id_anggota)->select('noanggota')->get()->first();
            $res = NM::where('topic',$get_anggota->noanggota)->orderBy('notif_id','DESC')->skip($skip)->take($take)->get();
            // end 

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