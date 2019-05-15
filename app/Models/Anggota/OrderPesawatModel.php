<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;

class OrderPesawatModel extends Model {

    protected $table = 'order_pesawat';

    protected $fillable = [
        'id_order'
    ];

    public $timestamps = true;
}
