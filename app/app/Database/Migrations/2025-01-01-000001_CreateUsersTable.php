<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración 001: Tabla users
 *
 * Primera migración del proyecto — sin dependencias FK.
 * Todas las demás tablas (periods, documents, inscriptions, etc.) referencian users.id.
 *
 * Nota técnica: Se usa SQL raw para los timestamps porque el Forge API de CI4
 * no puede emitir DEFAULT CURRENT_TIMESTAMP sin comillas para columnas DATETIME.
 * El resto de la estructura sigue el esquema exacto de ARQUITECTURA.md §5.
 */
class CreateUsersTable extends Migration
{
    public function up(): void
    {
        // SQL raw necesario para DEFAULT CURRENT_TIMESTAMP en columnas DATETIME.
        // CI4 Forge 4.x cita los defaults de string, lo cual MySQL rechaza para funciones.
        $sql = "CREATE TABLE `users` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `username`       VARCHAR(60) NOT NULL,
            `email`          VARCHAR(255) NOT NULL,
            `phone`          VARCHAR(20) NOT NULL,
            `password_hash`  VARCHAR(255) NOT NULL,
            `nombres`        VARCHAR(100) NOT NULL,
            `apellido_pat`   VARCHAR(80) NOT NULL,
            `apellido_mat`   VARCHAR(80) NULL,
            `role`           ENUM('user','admin','superadmin') NOT NULL DEFAULT 'user',
            `status`         ENUM('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
            `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
            `verify_token`   VARCHAR(100) NULL,
            `verify_exp`     DATETIME NULL,
            `recovery_token` VARCHAR(100) NULL,
            `recovery_exp`   DATETIME NULL,
            `last_login`     DATETIME NULL,
            `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_username` (`username`),
            UNIQUE KEY `idx_email` (`email`),
            KEY `idx_status` (`status`),
            KEY `idx_verify_token` (`verify_token`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('users', true);
    }
}
