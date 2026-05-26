<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentTypeModel extends Model
{
    protected $table = 'document_types';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'description',
        'category',
        'required',
        'allowed_types',
        'max_size_mb',
        'max_months',
        'sort_order',
        'active',
        'created_by',
    ];
}