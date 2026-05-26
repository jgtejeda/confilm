<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentTypesTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `document_types` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name`           VARCHAR(200) NOT NULL,
            `description`    TEXT NULL,
            `category`       ENUM('inicial','complementario') NOT NULL,
            `required`      TINYINT(1) NOT NULL DEFAULT 1,
            `allowed_types`  VARCHAR(500) NOT NULL,
            `max_size_mb`    INT NOT NULL DEFAULT 5,
            `max_months`    INT NULL,
            `sort_order`     INT NOT NULL DEFAULT 0,
            `active`         TINYINT(1) NOT NULL DEFAULT 1,
            `created_by`     INT UNSIGNED NULL,
            `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            CONSTRAINT `document_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('document_types', true);
    }
}