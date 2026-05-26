<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración 006: Tabla inscriptions
 *
 * Depende de: users (001), periods (002).
 *
 * Nota técnica: Se usa SQL raw para timestamps (igual que migrations 001, 002, 003)
 * porque CI4 Forge:
 *   1. Cita el valor 'CURRENT_TIMESTAMP' como string literal, que MySQL rechaza.
 *   2. No reconoce la clave 'update' => 'CURRENT_TIMESTAMP' para generar
 *      la cláusula ON UPDATE CURRENT_TIMESTAMP en updated_at.
 *
 * UNIQUE(user_id, period_id): un usuario solo puede tener una inscripción por periodo.
 */
class CreateInscriptionsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `inscriptions` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`        INT UNSIGNED NOT NULL,
            `period_id`      INT UNSIGNED NOT NULL,
            `status`         ENUM('incomplete','under_review','approved','rejected') NOT NULL DEFAULT 'incomplete',
            `rejection_note` TEXT NULL,
            `reviewed_by`    INT UNSIGNED NULL,
            `reviewed_at`    DATETIME NULL,
            `submitted_at`   DATETIME NULL,
            `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_user_period` (`user_id`, `period_id`),
            CONSTRAINT `fk_inscriptions_user`        FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)   ON DELETE CASCADE  ON UPDATE CASCADE,
            CONSTRAINT `fk_inscriptions_period`      FOREIGN KEY (`period_id`)  REFERENCES `periods`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
            CONSTRAINT `fk_inscriptions_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('inscriptions', true);
    }
}
