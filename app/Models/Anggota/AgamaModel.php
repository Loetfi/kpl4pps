<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class AgamaModel extends Model {

    // use SoftDeletes;

    protected $table = 'agama';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id',
        'nama'
    ];

    public $timestamps = true;
}
