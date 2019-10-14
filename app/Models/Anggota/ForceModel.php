<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class ForceModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_force_update';
    protected $casts = [ 'force_update' => 'int'];
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'apps_version',
        'force_update'
    ];

    public $timestamps = false;
}
