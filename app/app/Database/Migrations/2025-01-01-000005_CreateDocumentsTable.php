<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración 005: Tabla documents
 *
 * Depende de: users (001), document_types (003), periods (002).
 *
 * Nota técnica: Se usa SQL raw (igual que migrations 001, 002, 003) para:
 *   1. Garantizar PRIMARY KEY con AUTO_INCREMENT correctamente.
 *   2. Evitar que CI4 Forge cite CURRENT_TIMESTAMP entre comillas, lo cual
 *      MySQL rechaza para defaults de funciones en columnas DATETIME.
 */
class CreateDocumentsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `documents` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`        INT UNSIGNED NOT NULL,
            `doc_type_id`    INT UNSIGNED NOT NULL,
            `period_id`      INT UNSIGNED NOT NULL,
            `original_name`  VARCHAR(255) NOT NULL,
            `stored_name`    VARCHAR(255) NOT NULL,
            `s3_key`         VARCHAR(500) NOT NULL,
            `s3_bucket`      VARCHAR(150) NOT NULL,
            `file_size`      INT UNSIGNED NULL,
            `mime_type`      VARCHAR(100) NULL,
            `file_extension` VARCHAR(10) NULL,
            `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            `rejection_note` TEXT NULL,
            `reviewed_by`    INT UNSIGNED NULL,
            `reviewed_at`    DATETIME NULL,
            `uploaded_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_user_period` (`user_id`, `period_id`),
            KEY `idx_status` (`status`),
            CONSTRAINT `fk_documents_user`        FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)          ON DELETE CASCADE  ON UPDATE CASCADE,
            CONSTRAINT `fk_documents_doc_type`    FOREIGN KEY (`doc_type_id`) REFERENCES `document_types`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            CONSTRAINT `fk_documents_period`      FOREIGN KEY (`period_id`)   REFERENCES `periods`(`id`)        ON DELETE RESTRICT ON UPDATE RESTRICT,
            CONSTRAINT `fk_documents_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`)          ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('documents', true);
    }
}
