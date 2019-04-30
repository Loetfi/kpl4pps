<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class GenderMaster extends Model {

    protected $table = 'user.master_gender';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_gender',
        'name_gender'
    ];

    public $timestamps = false;
}