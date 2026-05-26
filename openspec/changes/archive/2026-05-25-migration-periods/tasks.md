## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000002_CreatePeriodsTable.php` con clase que extiende `Migration`
- [x] 1.2 Implementar `up()`: addField para id, name VARCHAR(200) NOT NULL, description TEXT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, active TINYINT(1) DEFAULT 1, created_by INT UNSIGNED NULL, created_at, updated_at
- [x] 1.3 Agregar FK: `created_by → users(id) ON DELETE SET NULL` usando `$this->forge->addForeignKey()`
- [x] 1.4 Agregar índice compuesto `idx_active_dates` en `['active', 'start_date', 'end_date']`
- [x] 1.5 Llamar `$this->forge->createTable('periods')` al final de `up()`
- [x] 1.6 Implementar `down()`: `$this->forge->dropTable('periods', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores (users debe existir primero)
- [x] 2.2 Verificar con `DESCRIBE periods` — confirmar todos los campos y tipos
- [x] 2.3 Verificar FK con `SHOW CREATE TABLE periods` — debe mostrar FK a users con ON DELETE SET NULL
- [x] 2.4 Verificar índice con `SHOW INDEX FROM periods` — debe aparecer idx_active_dates
- [x] 2.5 Confirmar que `active DEFAULT 1` insertando un periodo sin ese campo

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000002_CreatePeriodsTable.php`
- [x] 3.2 `created_by` es NULL (no NOT NULL) — puede ser NULL si el admin fue eliminado
- [x] 3.3 La FK usa `ON DELETE SET NULL`, NO `ON DELETE CASCADE` ni `ON DELETE RESTRICT`
- [x] 3.4 El índice es COMPUESTO en (active, start_date, end_date) — no tres índices separados
- [x] 3.5 NO hay seed de periods — el admin los crea desde el panel
