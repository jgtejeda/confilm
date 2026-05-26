## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000001_CreateUsersTable.php` con clase que extiende `Migration`
- [x] 1.2 Implementar `up()`: definir todos los campos con `$this->forge->addField()` — incluir verify_token, verify_exp, recovery_token, recovery_exp, email_verified, last_login
- [x] 1.3 Agregar UNIQUE KEY en `username` y `email` con `$this->forge->addUniqueKey()`
- [x] 1.4 Agregar índices `idx_status`, `idx_verify_token` con `$this->forge->addKey()`
- [x] 1.5 Llamar `$this->forge->createTable('users')` al final de `up()`
- [x] 1.6 Implementar `down()`: solo `$this->forge->dropTable('users', true)` — sin DROP de FK previos

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` dentro del contenedor `rcf_app` — debe completar sin errores
- [x] 2.2 Verificar en MySQL que `users` existe con `DESCRIBE users` — confirmar todas las columnas
- [x] 2.3 Confirmar ENUMs: `role` y `status` con los valores exactos definidos
- [x] 2.4 Confirmar índices con `SHOW INDEX FROM users` — deben aparecer idx_email, idx_username, idx_status, idx_verify_token
- [x] 2.5 Ejecutar `php spark migrate:rollback` — la tabla `users` debe eliminarse sin error (solo si es la única migración aplicada)

## ⚠️ Anti-alucinación

- [x] 3.1 Verificar que `verify_token` es VARCHAR(100) NULL (no NOT NULL, no TEXT)
- [x] 3.2 Verificar que `verify_exp` es DATETIME NULL (no TIMESTAMP, no INT)
- [x] 3.3 Verificar que `password_hash` es VARCHAR(255) (no VARCHAR(60), no TEXT)
- [x] 3.4 Verificar que `apellido_mat` es NULL (no NOT NULL — el apellido materno es opcional)
- [x] 3.5 Verificar que el archivo se llama EXACTAMENTE `2025-01-01-000001_CreateUsersTable.php`
