<?php

namespace App\Repositories\User;

use App\Models\User\UserSalaryFlowManagement as UsersSalaryFlow;
use App\Models\Master\UserSalaryFlowMaster as MstUsersSalaryFlow;
use App\Models\Master\WorkflowMaster AS Workflow;
use App\Models\User\AuthCompanyManagement AS AuthCompany;
use Illuminate\Database\QueryException;
use DB;

class UsersSalaryFlowRepo{
	
	public static function all($columns = array('*')){
		try {
			if($columns == array('*')) return UsersSalaryFlow::all();
			else return UsersSalaryFlow::select($columns)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function getByParam($column, $value){
		try {
			return UsersSalaryFlow::where($column, $value)->get();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function create(array $data){
		try {
			return UsersSalaryFlow::create($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function find($column, $value){
		try {
			return UsersSalaryFlow::where($column, $value)->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function update($id, array $data){
		try { 
			return UsersSalaryFlow::where('id_user_company_salary_flow',$id)->update($data);
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}	
	} 

	public static function delete($id){
		try { 
			return UsersSalaryFlow::where('id_user_company_salary_flow',$id)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
		
	}
	
	public static function deleteByParam($column, $value){
		try {
			return UsersSalaryFlow::where($column, $value)->delete();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
	}

	public static function last(){
		try{
			return UsersSalaryFlow::orderBy('id_user_company_salary_flow', 'desc')->first();
		}catch(QueryException $e){
			throw new \Exception($e->getMessage(), 500);
		}
    }
    
    public static function flow($id){
        try{
            // get master salary flow
            $mstFlow = MstUsersSalaryFlow::get();
            // get user with lower request from user
            $active = Workflow::where('workflow_status_name', "like", "Active%")->where('workflow_status_desc', "like", "%user status%")->get()->first()->id_workflow_status;
            $id_role = [];
            foreach($mstFlow AS $i => $row){
                $mstByRole[$row->id_role_master] = $row;
                $tmp = DB::select(DB::raw("SELECT TOP 1 tables.id_user FROM (
                    SELECT a.id_user, COUNT(a.id_user) AS count_member FROM [user].[user] a
                    LEFT JOIN [user].[user_profile] b ON a.id_user=b.id_user
                    LEFT JOIN [user].[user_company] c ON b.id_user_profile=c.id_user_profile
                    LEFT JOIN [user].[user_company_salary_flow] d ON c.id_user_company=d.id_user_company
                    WHERE a.id_role_master = '".$row->id_role_master."'
                        AND a.id_workflow_status = '".$active."'
                    GROUP BY a.id_user, c.id_user_profile, d.id_user_company
                    ) tables ORDER BY tables.count_member ASC"));
                if(count($tmp) > 0)
                    $id_role[$row->id_role_master] = $tmp[0]->id_user;
            }

            // save to salary flow table
            $ret = true;
            if(count($id_role) > 0){
                foreach($id_role AS $role => $id_user){
                    if($ret){
                        $flow = New UsersSalaryFlow;
                        $flow->id_user_company = $id;
                        $flow->approve_by = $id_user;
                        $flow->id_master_user_company_flow = $mstByRole[$role]->id_master_user_company_salary_flow;
                        if(!$flow->save()) $ret = false;
                    }
                }
            } else $ret = false;

            return $ret;
        }catch(QueryException $e){
            throw new \Exception($e->getMessage(), 500);
        }
	}
	
	public static function approve_list($request, $d=null,$st=0,$l=10,$sr=null){
		if(is_numeric($st) && is_numeric($l)){
			$field = ["c.name", "c.phone_number", "f.name_company", "g.name_grade", "e.employee_starting_date", "c.npwp", "c.email", "c.loan_plafond", "c.microloan_plafond", "d.id_workflow_status"];
			// where indication
			$where = [];

			// get another role
			$master_role = MstUsersSalaryFlow::where("id_role_master",$request->input('id_role_master'))->get()->first();
			$master_flow = ((int)$master_role->level-1)!=0?MstUsersSalaryFlow::where("level",((int)$master_role->level-1))->get()->first()->set_workflow_status_code:"SLRSTS01";

			// get by company if ROLE003
			$comp = $request->input('id_role_master') == "ROLE003"? " AND e.id_company='".(AuthCompany::where('id_user',$request->input('id_user'))->get()->first()->id_company)."'" : "";

			$c_all = DB::select(DB::raw("
				SELECT COUNT(a.id_user_company_salary) AS cnt 
				FROM [user].[user_company_salary] a 
				JOIN [user].[user_company_salary_flow] b ON a.id_user_company=b.id_user_company
				JOIN [user].[master_user_company_salary_flow] h ON h.id_master_user_company_salary_flow=b.id_master_user_company_flow
				JOIN [user].[user_company] e ON a.id_user_company=e.id_user_company
				JOIN [user].[user_profile] c ON e.id_user_profile=c.id_user_profile
				JOIN [user].[user] d ON c.id_user=d.id_user
				WHERE a.id_workflow_status='".$master_flow."' AND h.id_role_master='".$request->input('id_role_master')."' AND a.approved_at IS NULL ".$comp
			))[0]->cnt;

			if(!is_null($d) && !empty($d))
				foreach($field AS $i => $row){
					$where[] = $row." LIKE '%".$d."%'";
				}

			$c_fil = DB::select(DB::raw("
				SELECT COUNT(a.id_user_company_salary) AS cnt
				FROM [user].[user_company_salary] a 
				JOIN [user].[user_company_salary_flow] b ON a.id_user_company=b.id_user_company
				JOIN [user].[master_user_company_salary_flow] h ON h.id_master_user_company_salary_flow=b.id_master_user_company_flow
				JOIN [user].[user_company] e ON a.id_user_company=e.id_user_company
				JOIN [user].[user_profile] c ON e.id_user_profile=c.id_user_profile
				JOIN [user].[user] d ON c.id_user=d.id_user
				JOIN [user].[master_company] f ON e.id_company=f.id_company
				JOIN [user].[master_grade] g ON e.id_grade=g.id_grade
				WHERE a.id_workflow_status='".$master_flow."' AND h.id_role_master='".$request->input('id_role_master')."' AND a.approved_at IS NULL ".(count($where)>0?"AND (".implode(' OR ', $where).")":"").$comp
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
				SELECT c.name, c.phone_number, c.npwp, c.email, c.loan_plafond, c.microloan_plafond, c.personal_photo, c.personal_identity_path, d.created_at as requested_date, e.company_identity_path, d.id_workflow_status, f.name_company, g.name_grade, e.employee_starting_date, a.*, b.*
				FROM [user].[user_company_salary] a 
				JOIN [user].[user_company_salary_flow] b ON a.id_user_company=b.id_user_company
				JOIN [user].[master_user_company_salary_flow] h ON h.id_master_user_company_salary_flow=b.id_master_user_company_flow
				JOIN [user].[user_company] e ON a.id_user_company=e.id_user_company
				JOIN [user].[user_profile] c ON e.id_user_profile=c.id_user_profile
				JOIN [user].[user] d ON c.id_user=d.id_user
				JOIN [user].[master_company] f ON e.id_company=f.id_company
				JOIN [user].[master_grade] g ON e.id_grade=g.id_grade
				WHERE a.id_workflow_status='".$master_flow."' AND h.id_role_master='".$request->input('id_role_master')."' AND a.approved_at IS NULL ".(count($where)>0?"AND (".implode(' OR ', $where).")":"").$comp." ".$order." ".$length[0]." ".$length[1]
			));

			return ['count_all'=>$c_all,'count_filter'=>$c_fil,'data'=>$data];
		}
		return [];
	} 
}