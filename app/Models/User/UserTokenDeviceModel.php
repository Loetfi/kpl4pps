<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserTokenDeviceModel extends Model {

    protected $table = 'user.user_token_device';
    protected $primaryKey = 'id_user_token';

    protected $fillable = [
        'id_user_token',
        'id_user',
        'type',
        'token',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
        'deleted_by',
        'deleted_at',
        'status'
    ];

    public $timestamps = true;
}
