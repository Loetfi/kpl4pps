<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\ProfileModel AS ProfileModel;
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

			// cek 
			$anggota_data = AnggotaModel::where('noanggota', $anggota_id)->get()->first();
			// dd($anggota_data);

			ProfileModel::where('id', $anggota_data->id)
			->update(['photo' => $photo]);
			
			$get = ProfileModel::where('id' , $anggota_data->id)->select('photo')->get();
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
				'anggota_id'	=> 'required',
				'username'		=> 'required',
				// 'no_hp'			=> 'required',
				// 'alamat'		=> 'required'
			]);  

			$username = $request->username ? strtolower($request->username) : 0;
			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;
			

			$check = ProfileModel::where('username', $username);

			// jika id anggota username yang sama di post, maka di allow saja 
			$check_username =  ($check->get()->first());
			// dd($check_username);

			if (count(($check)->where('id',$anggota_id)->get()->first())>0) {
				// echo "username kepemilikan si id ini";
				$get = 'ada';
			}  elseif(is_null($check_username)){
				$get = 'ada';
				// echo "null";
			} elseif (count($check_username)>0) {
				// echo "gagal";
				// throw new \Exception("Username sudah ada dimilik pengguna lain", 400);	
				$get = 'tidak';
			}
			// $get = ProfileModel::where('noanggota' , $anggota_id)->select('photo')->get();
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
				'alamat'		=> 'required',
				'password'		=> 'nullable'
			]);  

			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;
			$username = $request->username ? strtolower($request->username) : 0;
			$alamat = $request->alamat ? $request->alamat : 0;
			$no_hp = $request->no_hp ? $request->no_hp : 0;
			// $istoko = $request->has('istoko') ? $request->istoko : $request->istoko = 0;

			if (!empty($request->has('password'))) {
				// dd('ganti password');
				$change_password =  array(
					'pin'	=> $request->password
				);
				AnggotaModel::where('id',$anggota_id)->update($change_password);

			}

			// cek 
			// $anggota_data = AnggotaModel::where('noanggota', $anggota_id)->get()->first();

			$check = ProfileModel::where('username', $username);

			// jika id anggota username yang sama di post, maka di allow saja 
			$check_username =  ($check->get()->first());
			// dd($check_username);

			if (count((array) ($check)->where('id',$anggota_id)->get()->first())>0) {
				// echo "username kepemilikan si id ini";
			}  elseif(is_null($check_username)){
				// echo "null";
			} elseif (count((array) $check_username)>0) {
				// echo "gagal";
				throw new \Exception("Usename sudah ada yang punya, ubah profil tidak dapat disimpan", 400);	
			}
			// die();

			$update = array(
				'username' 	=> $username
			);
			$updateProses = ProfileModel::where('id', $anggota_id)->update($update);

			
			$update2 = array(
				'nohp' 		=> $no_hp,
				'alamat' 	=> $alamat,
			);

			$updateProsess = AnggotaModel::where('id', $anggota_id)->update($update2);


			if ($updateProses || $updateProsess) {
				$Message = 'Berhasil';
				$code = 200;
				$res = 1;
				$data = AnggotaModel::where('id', $anggota_id)->get()->first();
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
