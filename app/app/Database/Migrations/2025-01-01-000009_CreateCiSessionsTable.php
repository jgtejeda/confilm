<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCiSessionsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `ci_sessions` (
            `id`          VARCHAR(128) NOT NULL,
            `ip_address`  VARCHAR(45)  NOT NULL,
            `timestamp`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `data`        BLOB        NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('ci_sessions', true);
    }
}