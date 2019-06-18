<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\AnggotaModel AS AnggotaModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Helpers\PutImage;
// use File;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
	public function imageProfile(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'image'       => 'required',
				'anggota_id'       => 'required'

			]);  

			$img = $request->image;
			$name = $request->anggota_id;

			$photo = PutImage::base64($img , $name);
			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;

			AnggotaModel::where('noanggota', $anggota_id)
					->update(['photo' => $photo]);
			
			$get = AnggotaModel::where('noanggota' , $anggota_id)->select('photo')->get();
			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $get;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
