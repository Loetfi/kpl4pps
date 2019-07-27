<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PaketModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_paket_kategori';

    protected $fillable = [
        'id_channel_kat',
        'id_kategori',
        'nama_paket',
        'des_paket',
        'status'
    ];

    public $timestamps = true;
}
