<?php 

namespace App\Models\Anggota;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class NotifModel extends Model {

    // use SoftDeletes;

    protected $table = 'apps_notif';
    // protected $dates = ['deleted_at'];
    // protected $primaryKey = 'id_authorization_company';

    protected $fillable = [
        'notif_id',
        'topic',
        'title',
        'message'
    ];

    public $timestamps = true;
}
