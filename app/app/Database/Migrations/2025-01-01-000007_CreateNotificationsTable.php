<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE `notifications` (
            `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id`        INT UNSIGNED NOT NULL,
            `sender_id`      INT UNSIGNED NULL,
            `type`           ENUM('info','success','warning','error','document','inscription') NOT NULL,
            `title`          VARCHAR(200) NOT NULL,
            `body`           TEXT NOT NULL,
            `read_at`        DATETIME NULL,
            `send_email`     TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `email_sent_at`  DATETIME NULL,
            `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_user_read` (`user_id`, `read_at`),
            CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->db->query($sql);
    }

    public function down(): void
    {
        $this->forge->dropTable('notifications', true);
    }
}
