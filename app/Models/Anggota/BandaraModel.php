<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class BandaraModel extends Model {

    // use SoftDeletes;

	protected $table = 'apps_bandara_indonesia';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id';
    public $incrementing = false;

	protected $fillable = [
		'id',
		'lat',
		'lot',
		'name',
		'city',
		'state',
		'country',
		'woeid',
		'tz',
		'phone',
		'type',
		'email',
		'url',
		'runway_length',
		'elev',
		'icao',
		'direct_flights',
		'carriers'
	];

	public $timestamps = true;
}
