<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class UserBackendModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_user_backend';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id_user_backend',
        'username_backend',
        'password_backend',
        'status'
    ];

    public $timestamps = true;
}
