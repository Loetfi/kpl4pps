<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ProfileModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_anggota_detail';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'photo',
        'username'
    ];

    public $timestamps = false;
}
