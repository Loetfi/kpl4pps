<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model {

    protected $table = 'user.user_documents';
    protected $primaryKey = 'id_user_document';

    protected $fillable = [
        'id_user_document',
        'id_user',
        'id_document_type',
        'path',
        'created_by',
        'updated_by',
        'deleted_at',
        'deleted_by',
        'id_workflow_status'
    ];

    public $timestamps = true;
}
