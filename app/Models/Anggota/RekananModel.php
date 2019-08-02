<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class RekananModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_rekanan';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'rekanan_id',
        'nama_rekanan',
        'deskripsi_rekanan',
        'kelompok_id'
    ];

    public $timestamps = true;
}
