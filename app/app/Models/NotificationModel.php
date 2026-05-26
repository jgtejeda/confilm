<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;
    // created_at se maneja por DEFAULT CURRENT_TIMESTAMP en la columna DB.
    // No se incluye en $allowedFields para evitar sobreescritura accidental.
    protected $allowedFields = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'body',
        'read_at',
        'send_email',
        'email_sent_at',
    ];
}