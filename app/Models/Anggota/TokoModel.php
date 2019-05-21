<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class TokoModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_list_toko';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id',
        'nama',
        'hargajual',
        'namasatuan',
        'namakategori'
    ];

    public $timestamps = true;
}
