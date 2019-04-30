<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ReligionMaster extends Model {

    protected $table = 'user.master_religion';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_religion',
        'name_religion'
    ];

    public $timestamps = false;
}