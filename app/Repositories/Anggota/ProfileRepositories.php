<?php

namespace App\Repositories\Anggota;

use App\Models\Anggota\ProfileModel AS ProfileModel;
use App\Models\Anggota\AnggotaModel AS AnggotaModel;

class ProfileRepositories {

	public static function change_username($anggota_id , $update)
	{
		try {
			$updateProses = ProfileModel::where('id', $anggota_id)->update($update);
			return true;
		} catch (QueryException $e) {
			return false;
		}
	}

	public static function change_profile($anggota_id , $update)
	{
		try {
			$update_profile = AnggotaModel::where('id', $anggota_id)->update($update);
			return true;
		} catch (QueryException $e) {
			return false;
		}
	} 
}
