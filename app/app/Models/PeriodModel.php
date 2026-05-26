<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodModel extends Model
{
    protected $table = 'periods';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'description',
        'start_date',
        'end_date',
        'active',
        'created_by',
    ];
}