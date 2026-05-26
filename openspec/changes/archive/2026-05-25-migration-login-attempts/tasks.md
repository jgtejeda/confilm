## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000008_CreateLoginAttemptsTable.php`
- [x] 1.2 Implementar `up()` addField: id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, identifier VARCHAR(150) NOT NULL, ip_address VARCHAR(45) NOT NULL, success TINYINT(1) DEFAULT 0, attempted_at DATETIME DEFAULT CURRENT_TIMESTAMP
- [x] 1.3 Agregar índice compuesto `idx_identifier_ip` en `['identifier', 'ip_address']`
- [x] 1.4 Agregar índice `idx_attempted_at` en `['attempted_at']`
- [x] 1.5 Llamar `$this->forge->createTable('login_attempts')`
- [x] 1.6 Implementar `down()`: `$this->forge->dropTable('login_attempts', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores
- [x] 2.2 Verificar con `DESCRIBE login_attempts` — 5 columnas: id, identifier, ip_address, success, attempted_at
- [x] 2.3 Verificar índices con `SHOW INDEX FROM login_attempts` — idx_identifier_ip y idx_attempted_at
- [x] 2.4 Insertar un intento de prueba sin attempted_at — confirmar que toma el timestamp automático

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000008_CreateLoginAttemptsTable.php`
- [x] 3.2 NO hay FK a users — el identifier puede ser un email/username que no existe
- [x] 3.3 `success DEFAULT 0` — los intentos fallidos son el caso más común
- [x] 3.4 `ip_address VARCHAR(45)` — soporta IPv6 (39 chars) con margen
