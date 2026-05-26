<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoginAttemptsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `login_attempts` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `identifier`     VARCHAR(150) NOT NULL,
            `ip_address`     VARCHAR(45) NOT NULL,
            `success`        TINYINT(1) NOT NULL DEFAULT 0,
            `attempted_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_identifier_ip` (`identifier`, `ip_address`),
            KEY `idx_attempted_at` (`attempted_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('login_attempts', true);
    }
}
