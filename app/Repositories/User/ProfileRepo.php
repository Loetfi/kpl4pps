<?php

namespace App\Repositories\User;

use App\Models\User\AddressModel as AddressDB;
use App\Models\User\ProfileManagement as ProfileDB;
use Illuminate\Database\QueryException; 
use DB;

class ProfileRepo {

	public function getprofile($id_user = null){
		try { 
			$res = DB::select(DB::raw("
				SELECT a.*
				, (SELECT TOP 1 name_religion FROM [user].[master_religion] WHERE id_religion=a.id_religion) AS religion
				, (SELECT TOP 1 name_gender FROM [user].[master_gender] WHERE id_gender=a.id_gender) AS gender
				, (SELECT TOP 1 path FROM [user].[user_documents] WHERE id_user=a.id_user AND id_document_type='DOC001') AS personal_identity_path_new
				, (SELECT TOP 1 marriage_status_name FROM [user].[master_marriage_status] WHERE id_marriage_status=a.id_marriage_status) AS marriage_status
				, (SELECT TOP 1 name_domicile_address_status FROM [user].[master_domicile_address_status] WHERE id_domicile_address_status=a.id_domicile_address_status) AS domicile_address_status
				FROM [user].[user_profile] a 
				WHERE a.id_user = ".$id_user."
			"
			));
			return $res[0];
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function getnik(){
		try { 
			return DB::table('user.user_profile')
			->select(DB::raw('max(id_koperasi) as NIK'))
			->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public function GenerateNik(){
		return array('nomor_NIK' => sprintf("%s", (($res = $this->getnik())?(!is_null($res->NIK)?(int)$res->NIK:0):0)+1));
	}

}
