<?php

namespace App\Models;

use CodeIgniter\Model;

class InscriptionModel extends Model
{
    protected $table = 'inscriptions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'user_id',
        'period_id',
        'status',
        'rejection_note',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];
}