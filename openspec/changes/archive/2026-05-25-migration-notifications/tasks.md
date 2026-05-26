## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000007_CreateNotificationsTable.php`
- [x] 1.2 Implementar `up()` addField: id, user_id INT UNSIGNED NOT NULL, sender_id INT UNSIGNED NULL
- [x] 1.3 Continuar addField: type ENUM('info','success','warning','error','document','inscription') NOT NULL, title VARCHAR(200) NOT NULL, body TEXT NOT NULL
- [x] 1.4 Continuar addField: read_at DATETIME NULL, send_email TINYINT(1) DEFAULT 0, email_sent_at DATETIME NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP
- [x] 1.5 Agregar FK: `user_id → users(id) ON DELETE CASCADE`
- [x] 1.6 Agregar índice compuesto `idx_user_read` en `['user_id', 'read_at']`
- [x] 1.7 Llamar `$this->forge->createTable('notifications')`
- [x] 1.8 Implementar `down()`: `$this->forge->dropTable('notifications', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores
- [x] 2.2 Verificar con `DESCRIBE notifications` — confirmar todos los campos
- [x] 2.3 Confirmar ENUM de type con los 6 valores exactos
- [x] 2.4 Verificar `SHOW INDEX FROM notifications` — debe aparecer idx_user_read
- [x] 2.5 Probar inserción con sender_id NULL (notificación de sistema) — debe funcionar

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000007_CreateNotificationsTable.php`
- [x] 3.2 `sender_id` es NULL (sin FK declarada explícitamente si causa problemas — o con FK a users sin CASCADE)
- [x] 3.3 El ENUM de type tiene EXACTAMENTE estos 6 valores: 'info','success','warning','error','document','inscription'
- [x] 3.4 La tabla NO tiene `updated_at` — solo `created_at` (las notificaciones no se editan)
- [x] 3.5 `read_at` es DATETIME NULL — NULL significa no leída; con valor = fecha de lectura
