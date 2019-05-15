<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class KotaModel extends Model {

    // use SoftDeletes;

	protected $table = 'apps_kota';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id';
    public $incrementing = false;

	protected $fillable = [
		'id',
		'name'
	];

	public $timestamps = true;
}
