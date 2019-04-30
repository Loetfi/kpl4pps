<?php 

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class UserDocumentManagement extends Model {

    protected $table = 'user.user_documents';
    protected $primaryKey = 'id_user_document';

    protected $fillable = [
        'id_user',
        'id_document_type',
        'path',
        'created_by',
        'updated_by',
        'id_workflow_status'
    ];

    public $timestamps = true;
}