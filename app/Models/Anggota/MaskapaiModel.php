<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class MaskapaiModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_maskapai';

    protected $casts = [ 'maskapai_id' => 'int'];
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'maskapai_id',
        'nama_maskapai'
    ];

    public $timestamps = false;
}
