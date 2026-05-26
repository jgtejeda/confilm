<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'email',
        'phone',
        'password_hash',
        'nombres',
        'apellido_pat',
        'apellido_mat',
        'role',
        'status',
        'email_verified',
        'verify_token',
        'verify_exp',
        'recovery_token',
        'recovery_exp',
        'last_login',
    ];
}