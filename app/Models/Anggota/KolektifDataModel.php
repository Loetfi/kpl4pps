<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class KolektifDataModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_kolektif_data';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'tahun',
        'total_jasa_pinjaman',
        'total_simpanan_pokok',
        'shu_toko_sp',
        'shu_modal'
    ];

    public $timestamps = false;
}
