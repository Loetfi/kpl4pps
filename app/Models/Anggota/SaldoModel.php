<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class SaldoModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_saldo_monthly';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'id',
        'saldo',
        'date',
        'status'
    ];

    public $timestamps = true;
}
