<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `periods` (
            `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name`        VARCHAR(200) NOT NULL,
            `description` TEXT NULL,
            `start_date`  DATETIME NOT NULL,
            `end_date`    DATETIME NOT NULL,
            `active`      TINYINT(1) NOT NULL DEFAULT 1,
            `created_by`  INT UNSIGNED NULL,
            `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_active_dates` (`active`, `start_date`, `end_date`),
            CONSTRAINT `fk_periods_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('periods', true);
    }
}
