<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class BeritaModel extends Model {

    // use SoftDeletes;

	protected $table = 'apps_berita';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id';
    public $incrementing = false;

	protected $fillable = [
		'id_berita',
		'judul_berita',
		'isi_berita',
		'gambar_berita',
		'status',
	];

	public $timestamps = true;
}
