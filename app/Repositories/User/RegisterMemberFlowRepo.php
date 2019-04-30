<?php

namespace App\Repositories\User;

use App\Models\Master\RoleMaster as Role;
use App\Models\Master\RegisterMemberFlowMaster as MasterRegisterFlow;
use App\Models\Master\WorkflowMaster AS Workflow;
use App\Models\User\RegisterMemberFlowManagement as RegisterFlow;
use App\Models\User\AuthCompanyManagement AS AuthCompany;
use Illuminate\Database\QueryException;
use DB;

class RegisterMemberFlowRepo{
	
	public static function all($columns = array('*')){
		try {
			if($columns == array('*')) return RegisterFlow::all();
			else return RegisterFlow::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return RegisterFlow::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function create(array $data){
		try {
			return RegisterFlow::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function find($column, $value){
		try {
			return RegisterFlow::where($column, $value)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function update($id, array $data){
		try { 
			return RegisterFlow::where('id_register_member_flow',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public static function delete($id){
		try { 
			return RegisterFlow::where('id_register_member_flow',$id)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
		
	}
	
	public static function deleteByParam($column, $value){
		try {
			return RegisterFlow::where($column, $value)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function last(){
		try{
			return RegisterFlow::orderBy('id_register_member_flow', 'desc')->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
    
    public static function flow($id){
        try{
            // get count by role
			$role = Role::where('is_front_end',0)->where('status',1)->get();
			// get active id
			$active = Workflow::where('workflow_status_name', "like", "Active%")->where('workflow_status_desc', "like", "%user status%")->get()->first()->id_workflow_status;
			
			// get user with count approval in register member flow
            $user = [];
            if($role->count() > 0)
                foreach($role AS $row){
					$tmp = DB::select(DB::raw("SELECT TOP 1 tables.id_user FROM (
						SELECT a.id_user, COUNT(b.id_user) AS count_member FROM [user].[user] a
						LEFT JOIN [user].[register_member_flow] b ON a.id_user=b.approve_by
						WHERE a.id_role_master = '".$row->id_role_master."'
							AND a.id_workflow_status = '".$active."'
						GROUP BY a.id_user, b.approve_by
						) tables ORDER BY tables.count_member ASC"));
					if(count($tmp) > 0)
						$user[$row->id_role_master] = $tmp[0]->id_user;
				}

            // insert into register member flow
            if (count($user) > 0) {
                // get master register member flow
                $mstRegFlow = MasterRegisterFlow::all();
                $roles = [];
                foreach ($mstRegFlow as $i => $data) {
                    $roles[$data->id_role_master] = $data;
                }

                foreach ($user as $id_master_role => $approve_by) {
                    // save data for approval
                    $mstRegFlow = isset($roles[$id_master_role])?$roles[$id_master_role]:false;
                    $regFlow = new RegisterFlow;
                    $regFlow->id_user = $id;
                    $regFlow->level = $mstRegFlow?$mstRegFlow->level:null;
                    $regFlow->id_master_register_member_flow = $mstRegFlow?$mstRegFlow->id_master_register_member_flow:null;
                    $regFlow->approve_by = $approve_by;
                    $regFlow->save();
                }
                return true;
			}
			return false;
        }catch(QueryException $e){
            throw new \Exception($e->getMessage(), 500);
        }
	}
	
	public static function approve_list($request, $d=null,$st=0,$l=10,$sr=null,$mw=null){
		if(is_numeric($st) && is_numeric($l)){

			$field = ["c.name", "c.phone_number", "f.name_company", "g.name_grade", "e.employee_starting_date", "c.npwp", "c.email", "c.loan_plafond", "c.microloan_plafond", "d.id_workflow_status"];
			$m_field = [
				"nama"           => ["c.name", "like"],
				"no_anggota"     => ["e.id_employee", "like"],
				"golongan"       => ["e.id_grade", "=="],
				"tgl_masuk"      => ["c.date_become_member", "between"],
				"tgl_pengajuan"  => ["d.created_at", "between"],
				"status"         => ["d.id_workflow_status", "=="],
				"company"        => ["e.id_company", "=="]
			];
			// where indication
			$where = [];

			// get another role
			$master_role = MasterRegisterFlow::where("id_role_master",$request->input('id_role_master'))->get()->first();
			$master_flow = ((int)$master_role->level-1)!=0?MasterRegisterFlow::where("level",((int)$master_role->level-1))->get()->first()->set_workflow_status_code:"MBRSTS01";

			// get by company if ROLE003
			// $comp = $request->input('id_role_master') == "ROLE003"? " AND e.id_company='".(AuthCompany::where('id_user',$request->input('id_user'))->get()->first()->id_company)."'" : "";
			$login_company = AuthCompany::where('id_user',$request->input('id_user'))->get()->first()->id_company;
			$comp = !isset($mw->company)? " AND e.id_company = '".$login_company."' " : "";

			// exception for ROLE002
			// filter only show for HR
			$filter = $request->input('id_role_master')=="ROLE003" ?" AND d.id_workflow_status='".$master_flow."' AND a.approve_by='".$request->input('id_user')."' AND a.approve_at IS NULL ".$comp:"";

			$filter .= $request->input('id_role_master')=="ROLE003" ?" AND a.approve_by = '".$request->input('id_user')."' ":" AND b.id_role_master='".$request->input('id_role_master')."' ";

			// filter only not in active
			$filter .= " AND d.id_workflow_status NOT IN ('MBRSTS05')";




			$c_all = DB::select(DB::raw("
				SELECT COUNT(a.id_register_member_flow) AS cnt 
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				JOIN [user].[user_profile] c ON a.id_user=c.id_user
				JOIN [user].[user] d ON a.id_user=d.id_user
				JOIN [user].[user_company] e ON c.id_user_profile=e.id_user_profile
				WHERE 1=1 ".$filter
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

							// exception for HR
							if($fl == "company" && $request->input("id_role_master")=="ROLE003")
								$where[] = $m_field[$fl][0]." = '".$login_company."'";
							else
								$where[] = $m_field[$fl][0]." ".$operand;
						}
					}
				}
			}

			$c_fil = DB::select(DB::raw("
				SELECT COUNT(a.id_register_member_flow) AS cnt
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				JOIN [user].[user_profile] c ON a.id_user=c.id_user
				JOIN [user].[user_company] e ON c.id_user_profile=e.id_user_profile
				JOIN [user].[user] d ON a.id_user=d.id_user
				JOIN [user].[master_company] f ON e.id_company=f.id_company
				JOIN [user].[master_grade] g ON e.id_grade=g.id_grade
				WHERE 1=1 ".$filter.(count($where)>0?"AND (".implode((count($mw)>0?' AND ':' OR '), $where).")":"")
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
				SELECT c.name, c.id_koperasi, e.id_employee, c.phone_number, f.name_company, g.name_grade, e.employee_starting_date, c.npwp, c.email, c.loan_plafond, c.microloan_plafond, d.id_workflow_status, c.personal_photo, (SELECT TOP 1 path FROM [user].[user_documents] WHERE id_user=d.id_user AND id_document_type='DOC001') AS personal_identity_path, (SELECT TOP 1 path FROM [user].[user_documents] WHERE id_user=d.id_user AND id_document_type='DOC002') AS company_identity_path, a.*, b.*, d.created_at as requested_date
				FROM [user].[register_member_flow] a 
				JOIN [user].[master_register_member_flow] b ON a.id_master_register_member_flow=b.id_master_register_member_flow
				JOIN [user].[user_profile] c ON a.id_user=c.id_user
				JOIN [user].[user_company] e ON c.id_user_profile=e.id_user_profile
				JOIN [user].[user] d ON a.id_user=d.id_user
				JOIN [user].[master_company] f ON e.id_company=f.id_company
				JOIN [user].[master_grade] g ON e.id_grade=g.id_grade
				WHERE 1=1 ".$filter.(count($where)>0?"AND (".implode((count($mw)>0?' AND ':' OR '), $where).")":"")." ".$order." ".$length[0]." ".$length[1]
			));

			return ['count_all'=>$c_all,'count_filter'=>$c_fil,'data'=>$data];
		}
		return [];
	} 
}