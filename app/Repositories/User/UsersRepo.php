<?php

namespace App\Repositories\User;

use App\Models\User\UserManagement as User;
use App\Models\Users\UserTokenDeviceModel as UserToken;
use Illuminate\Database\QueryException;
use DB;

class UsersRepo{
	
	public static function all($columns = array('*')){
		try {
			if($columns == array('*')) return User::all();
			else return User::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return User::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function create(array $data){
		try {
			return User::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function find($column, $value){
		try {
			return User::where($column, $value)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function update($id, array $data){
		try { 
			return User::where('id_user',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public static function delete($id){
		try { 
			return User::where('id_user',$id)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
		
	}
	
	public static function deleteByParam($column, $value){
		try {
			return User::where($column, $value)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function last(){
		try{
			return User::orderBy('id_user', 'desc')->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
  }

	public static function user_list($request, $d=null,$st=0,$l=10,$sr=null,$mw=null){
		if(is_numeric($st) && is_numeric($l)){

			$field = ["b.name", "d.name_company", "b.id_koperasi", "b.phone_number", "c.employee_starting_date", "e.name_grade", "b.npwp", "b.email", "b.loan_plafond", "b.microloan_plafond", "a.id_workflow_status"];
			$m_field = [
				"nama"           => ["b.name", "like"],
				"nik"            => ["c.id_employee", "like"],
				"golongan"       => ["c.id_grade", "=="],
				"tgl_masuk"      => ["b.date_become_member", "between"],
				"tgl_pengajuan"  => ["a.created_at", "between"],
				"status"         => ["a.id_workflow_status", "=="],
				"company"        => ["c.id_company", "=="]
			];
			// where indication
			$where = [];

			$c_all = DB::select(DB::raw("
				SELECT COUNT(a.id_user) AS cnt 
        FROM [user].[user] a
				LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
        LEFT JOIN [user].[user_company] c ON b.id_user_profile=c.id_user_profile
				WHERE c.id_workflow_status='EMPSTS01'
					AND (SELECT COUNT(id_user) FROM [user].[register_member_flow] WHERE id_user=a.id_user AND approve_at IS NOT NULL) = (SELECT COUNT(id_master_register_member_flow) FROM [user].[master_register_member_flow]) "
			))[0]->cnt;

			if(count($mw) == 0){
				if(!is_null($d) && !empty($d))
					foreach($field AS $i => $row){
						$where[] = $row." LIKE '%".$d."%'";
					}
			} else {
				foreach($mw AS $fl => $vl){
					if(isset($m_field[$fl])){
						if($vl != "" && !is_null($vl)){
							if(in_array($m_field[$fl][1],array("==", "like")))
								$operand = $m_field[$fl][1] == "=="? "='".$vl."'":($m_field[$fl][1] == "like"? "LIKE '%".$vl."%'":NULL);
							else if($m_field[$fl][1] == "between"){
								$dte = explode(" - ", $vl);
								if(count($dte) == 2){
									$operand = "BETWEEN '".$dte[0]."' AND '".$dte[1]."'";
								}
							}

							$where[] = $m_field[$fl][0]." ".$operand;
						}
					}
				}
			}

			$c_fil = DB::select(DB::raw("
        SELECT COUNT(a.id_user) AS cnt 
        FROM [user].[user] a
        LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
        LEFT JOIN [user].[user_company] c ON b.id_user_profile=c.id_user_profile
        LEFT JOIN [user].[master_company] d ON c.id_company=d.id_company
        LEFT JOIN [user].[master_grade] e ON c.id_grade=e.id_grade
        WHERE c.id_workflow_status='EMPSTS01'
					AND (SELECT COUNT(id_user) FROM [user].[register_member_flow] WHERE id_user=a.id_user AND approve_at IS NOT NULL) = (SELECT COUNT(id_master_register_member_flow) FROM [user].[master_register_member_flow]) ".(count($where)>0?"AND (".implode(' OR ', $where).")":"")
			))[0]->cnt;

			// order by
			$order = "";
			if(!is_null($sr)){
				$sort = explode(",",$sr);
				if(count($sort) > 1)
					$order .= "ORDER BY ".$sort[0]." ".$sort[1];
				else
				$order .= "ORDER BY ".$sr." asc";
			}

			// get length
			$length = [
				"OFFSET ".$st." ROWS",
				"FETCH NEXT ".$l." ROWS ONLY"
			];

			$data = DB::select(DB::raw("
				SELECT b.name, b.id_koperasi, d.name_company, b.phone_number, b.id_koperasi, c.employee_starting_date, e.name_grade, b.npwp, b.email, b.loan_plafond, b.microloan_plafond, b.personal_photo, b.personal_identity_path, c.company_identity_path, a.*
				FROM [user].[user] a
        LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
        LEFT JOIN [user].[user_company] c ON b.id_user_profile=c.id_user_profile
        LEFT JOIN [user].[master_company] d ON c.id_company=d.id_company
        LEFT JOIN [user].[master_grade] e ON c.id_grade=e.id_grade
        WHERE c.id_workflow_status='EMPSTS01'
					AND (SELECT COUNT(id_user) FROM [user].[register_member_flow] WHERE id_user=a.id_user AND approve_at IS NOT NULL) = (SELECT COUNT(id_master_register_member_flow) FROM [user].[master_register_member_flow]) ".(count($where)>0?"AND (".implode(' OR ', $where).")":"")." ".$order." ".$length[0]." ".$length[1]
			));

			return ['count_all'=>$c_all,'count_filter'=>$c_fil,'data'=>$data];
		}
		return [];
	} 
}
