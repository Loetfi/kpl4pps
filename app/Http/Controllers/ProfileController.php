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
use Exception;

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

	// edit profile 
	public function checkUsername(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				// 'email'       	=> 'required',
				// 'anggota_id'	=> 'required',
				'username'		=> 'required',
				// 'no_hp'			=> 'required',
				// 'alamat'		=> 'required'
			]);  

			$username = $request->username ? strtolower($request->username) : 0;

			$check_username = AnggotaModel::where('username', $username)->get()->first();
			
			if (sizeof($check_username)) {
				$get =  "ada";
			} else {
				$get = "tidak";
			} 
			
			// $get = AnggotaModel::where('noanggota' , $anggota_id)->select('photo')->get();
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


	// edit profile 
	public function edit(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				// 'email'       	=> 'required',
				'anggota_id'	=> 'required',
				'username'		=> 'required',
				'no_hp'			=> 'required',
				'alamat'		=> 'required'
			]);  

			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;
			$username = $request->username ? strtolower($request->username) : 0;
			$alamat = $request->alamat ? $request->alamat : 0;
			$no_hp = $request->no_hp ? $request->no_hp : 0;

			$check = AnggotaModel::where('username', $username);

			// jika id anggota username yang sama di post, maka di allow saja 
			$check_username =  ($check->get()->first());
			// dd($check_username);

			if (count(($check)->where('id',$anggota_id)->get()->first())>0) {
				// echo "username kepemilikan si id ini";
			}  elseif(is_null($check_username)){
				// echo "null";
			} elseif (count($check_username)>0) {
				// echo "gagal";
				throw new \Exception("Usename sudah ada yang punya, ubah profil tidak dapat disimpan", 400);	
			}
			// die();

			$update = array(
				'username' 	=> $username,
				'nohp' 		=> $no_hp,
				'alamat' 	=> $alamat,
			);

			$updateProses = AnggotaModel::where('id', $anggota_id)->update($update);

			if ($updateProses) {
				$Message = 'Berhasil';
				$code = 200;
				$res = 1;
				$data = [];
			} else {
				throw new \Exception("Tidak Berhasil", 400);	
			}
			
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
