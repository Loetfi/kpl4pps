<?php

namespace App\Repositories\Finance;

use App\Models\Finance\Doku as Doku;
use Illuminate\Database\QueryException; 
use DB;

class DokuRepo {

  public static function create(array $data){
		try {
			return Doku::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return Doku::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

}
