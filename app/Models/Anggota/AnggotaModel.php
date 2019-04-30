<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class AnggotaModel extends Model {

    // use SoftDeletes;

    protected $table = 'anggota';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id',
        'noanggota',
        'pin',
        'nama',
        'tanggal',
        'alamat',
        'kabupatenid',
        'kelurahanid',
        'kelompokid',
        'alamatsurat',
        'tmplahir',
        'tgllahir',
        'gender',
        'agamaid',
        'pekerjaanid',
        'pendidikanid',
        'anggota',
        'aktif',
        'jenisid'
    ];

    public $timestamps = true;
}
