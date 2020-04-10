<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class KantinModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_kantin';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama',
        'hargajual',
        'namasatuan',
        'namakategori'
    ];

    public $timestamps = true;
}
