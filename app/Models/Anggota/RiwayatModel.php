<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatModel extends Model {

    // use SoftDeletes;

    protected $table = 'view_order_list';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $casts = [ 'id_layanan' => 'int' ];

    protected $fillable = [
        'id_order',
        'id_layanan'
    ];

    public $timestamps = false;
}
