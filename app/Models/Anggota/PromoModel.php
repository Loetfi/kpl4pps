<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PromoModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_promo';

    protected $fillable = [
        'id_promo',
        'nama_promo',
        'url_promo',
        'status',
        'position'
    ];

    public $timestamps = false;
}
