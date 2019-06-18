<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\AgamaModel AS AgamaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\Notif AS Notif;

class PushNotifController extends Controller
{
	public function push(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$result = Notif::push(197411052007011001 , 'Pendaftaran akan diproses' , 'akan dilakukan manual oleh admin koperasi lemigas');

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $result;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}

