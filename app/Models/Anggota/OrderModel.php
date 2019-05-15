<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model {

    protected $table = 'apps_order';

    protected $fillable = [
        'id_order' , 
        'id_anggota' , 
        'tanggal_order', 
        'id_layanan', 
		'id_kategori'
    ];

    public $timestamps = true;
}
