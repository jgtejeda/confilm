<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ CAMBIAR CONTRASEÑA ANTES DE DEPLOY EN PRODUCCIÓN
        $rawPassword = 'admin123';
        $hash = password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $db = db_connect();
        $exists = $db->table('users')->where('email', 'admin@comisionfilm.gob.mx')->countAllResults();

        if ($exists === 0) {
            $db->table('users')->insert([
                'username' => 'admin',
                'email' => 'admin@comisionfilm.gob.mx',
                'phone' => '0000000000',
                'password_hash' => $hash,
                'nombres' => 'Administrador',
                'apellido_pat' => 'Sistema',
                'role' => 'admin',
                'status' => 'active',
                'email_verified' => 1,
            ]);
        }
    }
}