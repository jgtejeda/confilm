<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentModel extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id',
        'doc_type_id',
        'period_id',
        'original_name',
        'stored_name',
        's3_key',
        's3_bucket',
        'file_size',
        'mime_type',
        'file_extension',
        'status',
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
        'uploaded_at',
    ];
}