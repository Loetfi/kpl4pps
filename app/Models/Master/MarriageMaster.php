<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class MarriageMaster extends Model {

    protected $table = 'user.master_marriage_status';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_marriage_status',
        'name_marriage_status'
    ];

    public $timestamps = false;
}