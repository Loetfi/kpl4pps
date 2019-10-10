<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatOrderDetailModel extends Model {

    protected $table = 'view_order_detail';

    protected $casts = [ 'id_layanan' => 'int' ];

    protected $fillable = [
        'id_order',
        'id_layanan'
    ];

    public $timestamps = false;
}
