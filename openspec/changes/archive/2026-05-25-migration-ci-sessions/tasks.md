## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000009_CreateCiSessionsTable.php`
- [x] 1.2 Implementar `up()` con SQL raw via `$this->db->query()` para garantizar la estructura exacta:
  ```sql
  CREATE TABLE IF NOT EXISTS ci_sessions (
      id          VARCHAR(128) NOT NULL,
      ip_address  VARCHAR(45)  NOT NULL,
      timestamp   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
      data        BLOB         NOT NULL,
      PRIMARY KEY (id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ```
- [x] 1.3 Implementar `down()`: `$this->forge->dropTable('ci_sessions', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores
- [x] 2.2 Verificar con `SHOW CREATE TABLE ci_sessions` — id debe ser VARCHAR(128) PRIMARY KEY (sin AUTO_INCREMENT)
- [x] 2.3 Verificar que data es BLOB NOT NULL
- [x] 2.4 Verificar que timestamp es TIMESTAMP (no DATETIME) con DEFAULT CURRENT_TIMESTAMP
- [x] 2.5 Verificar que la tabla es escribible (INSERT exitoso con datos de prueba)

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000009_CreateCiSessionsTable.php`
- [x] 3.2 `id` es VARCHAR(128) PRIMARY KEY — NO INT AUTO_INCREMENT
- [x] 3.3 `timestamp` es TIMESTAMP — NO DATETIME (CI4 DatabaseHandler requiere TIMESTAMP)
- [x] 3.4 `data` es BLOB — NO TEXT, NO VARCHAR
- [x] 3.5 Sin FK — CI4 gestiona esta tabla directamente
- [x] 3.6 Usar SQL raw (`$this->db->query()`) para garantizar la estructura exacta — el Forge puede generar PRIMARY KEY diferente para VARCHAR
