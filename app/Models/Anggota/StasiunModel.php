<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class StasiunModel extends Model {

    // use SoftDeletes;

	protected $table = 'apps_stasiun_kereta';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id';
    public $incrementing = false;

	protected $fillable = [
		'id',
		'kota',
		'nama',
		'status',
		'singkatan'
	];

	public $timestamps = true;
}
