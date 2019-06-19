<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriLayananModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_kategori_channel';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id_layanan',
        'nama_layanan',
        'icon_layanan'
    ];

    public $timestamps = false;
}
