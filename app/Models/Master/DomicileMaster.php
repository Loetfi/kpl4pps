<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class DomicileMaster extends Model {

    protected $table = 'user.master_domicile_address_status';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_domicile_address_status',
        'name_domicile_address_status'
    ];

    public $timestamps = false;
}