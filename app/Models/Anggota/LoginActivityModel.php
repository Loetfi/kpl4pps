<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;

class LoginActivityModel extends Model {

    protected $table = 'apps_login_activity';

    protected $fillable = [
        'id_login',
        'id_anggota',
        'datetime'
    ];

    public $timestamps = true;
}
