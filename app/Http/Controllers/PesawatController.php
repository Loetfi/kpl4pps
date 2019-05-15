<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Telegram;
use App\Models\Anggota\BandaraModel AS BandaraModel;
use App\Helpers\Api;
use App\Helpers\RestCurl;

class PesawatController extends Controller
{

	public function bandara(Request $request){
		try { 

			$data_res = BandaraModel::select('code','name')->orderby('name','asc')->get();

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



	public function insert(Request $request){
		try { 

			$res = RestCurl::exec('GET','https://gist.githubusercontent.com/tdreyno/4278655/raw/7b0762c09b519f40397e4c3e100b097d861f5588/airports.json',[],'');

			// print_r($res['data']);
			foreach ($res['data'] as $d) {
				
				$insert = array(
					'code' => $d->code,
					'lat' => $d->lat,
					'lot' => $d->lon,
					'name' => $d->name,
					'city' => $d->city,
					'state' => $d->state,
					'country' => $d->country ,
					'woeid' => $d->woeid ,
					'tz' => $d->tz ,
					'phone' => $d->phone ,
					'type' => $d->type ,
					'email' => $d->email,
					'url' => $d->url,
					'runway_length' => $d->runway_length,
					'elev' => $d->elev,
					'icao' => $d->icao,
					'direct_flights' => $d->direct_flights,
					'carriers' => $d->carriers
				);

			 //	BandaraModel::insert($insert);
			} 

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_agama;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}


