<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserSalaryFlowManagement extends Model {

    protected $table = 'user.user_company_salary_flow';
    protected $primaryKey = 'id_user_company_salary_flow';

    protected $fillable = [
        'id_user_company',
        'approve_by',
        'approve_at',
        'id_master_user_company_flow'
    ];

    public $timestamps = false;
}