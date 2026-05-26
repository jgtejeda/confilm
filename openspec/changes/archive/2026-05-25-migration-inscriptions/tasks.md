## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000006_CreateInscriptionsTable.php`
- [x] 1.2 Implementar `up()` addField: id, user_id INT UNSIGNED NOT NULL, period_id INT UNSIGNED NOT NULL
- [x] 1.3 Continuar addField: status ENUM('incomplete','under_review','approved','rejected') DEFAULT 'incomplete', rejection_note TEXT NULL
- [x] 1.4 Continuar addField: reviewed_by INT UNSIGNED NULL, reviewed_at DATETIME NULL, submitted_at DATETIME NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
- [x] 1.5 Agregar FK: `user_id → users(id) ON DELETE CASCADE`
- [x] 1.6 Agregar FK: `period_id → periods(id)` (RESTRICT implícito — no CASCADE)
- [x] 1.7 Agregar FK: `reviewed_by → users(id) ON DELETE SET NULL`
- [x] 1.8 Agregar UNIQUE KEY `uq_user_period` en `['user_id', 'period_id']`
- [x] 1.9 Llamar `$this->forge->createTable('inscriptions')`
- [x] 1.10 Implementar `down()`: `$this->forge->dropTable('inscriptions', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores
- [x] 2.2 Verificar UNIQUE compuesta con `SHOW INDEX FROM inscriptions` — debe aparecer uq_user_period sobre (user_id, period_id)
- [x] 2.3 Probar: insertar dos inscripciones con mismo user_id y period_id → debe fallar
- [x] 2.4 Probar: insertar inscripciones con mismo user_id pero distintos period_id → debe funcionar
- [x] 2.5 Confirmar status DEFAULT 'incomplete' insertando sin ese campo

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000006_CreateInscriptionsTable.php`
- [x] 3.2 La UNIQUE KEY es COMPUESTA en (user_id, period_id) — NO solo user_id
- [x] 3.3 Los valores del ENUM son exactamente: 'incomplete','under_review','approved','rejected'
- [x] 3.4 `reviewed_by` referencia a `users.id` ON DELETE SET NULL (el admin revisor)
- [x] 3.5 `period_id` FK sin CASCADE — no se puede eliminar un periodo con inscripciones
