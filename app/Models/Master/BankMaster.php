<?php 

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class BankMaster extends Model {

    protected $table = 'user.master_bank';
    // protected $primaryKey = 'id_master_role';

    protected $fillable = [
        'id_bank',
        'name_bank'
    ];

    public $timestamps = false;
}