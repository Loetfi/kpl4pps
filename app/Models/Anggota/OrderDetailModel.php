<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;

class OrderDetailModel extends Model {

    protected $table = 'apps_order_detail';

    protected $fillable = [
        'id_order_detail' , 
        'id_order' , 
        'dari' , 
        'ke', 
        'penumpang', 
		'waktu_keberangkatan',
		'kursi_kelas',
		'nama_penumpang',
        'nama_barang'
    ];

    public $timestamps = true;
}
