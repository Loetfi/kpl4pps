<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserSalaryManagement extends Model {

    protected $table = 'user.user_company_salary';
    protected $primaryKey = 'id_user_company_salary';

    protected $fillable = [
        'id_user_company',
        'salary_photo',
        'salary_amount',
        'id_workflow_status',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
        'valid_from',
        'id_grade'
    ];

    public $timestamps = true;
}