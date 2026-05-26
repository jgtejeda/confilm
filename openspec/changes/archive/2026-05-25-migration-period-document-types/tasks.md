## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000004_CreatePeriodDocumentTypesTable.php`
- [x] 1.2 Implementar `up()` con addField: id INT UNSIGNED AUTO_INCREMENT PRIMARY, period_id INT UNSIGNED NOT NULL, doc_type_id INT UNSIGNED NOT NULL, sort_order INT DEFAULT 0
- [x] 1.3 Agregar FK: `period_id → periods(id) ON DELETE CASCADE`
- [x] 1.4 Agregar FK: `doc_type_id → document_types(id) ON DELETE CASCADE`
- [x] 1.5 Agregar UNIQUE KEY `uq_period_doctype` en `['period_id', 'doc_type_id']` con `$this->forge->addUniqueKey(['period_id', 'doc_type_id'], 'uq_period_doctype')`
- [x] 1.6 Llamar `$this->forge->createTable('period_document_types')`
- [x] 1.7 Implementar `down()`: `$this->forge->dropTable('period_document_types', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores (periods y document_types deben existir primero)
- [x] 2.2 Verificar UNIQUE con `SHOW INDEX FROM period_document_types` — debe aparecer uq_period_doctype
- [x] 2.3 Probar inserción duplicada (mismo period_id + doc_type_id) — debe dar error de duplicate key
- [x] 2.4 Verificar CASCADE: eliminar un periodo de prueba y confirmar que sus asignaciones desaparecen

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000004_CreatePeriodDocumentTypesTable.php`
- [x] 3.2 Ambas FK usan ON DELETE CASCADE (no SET NULL, no RESTRICT)
- [x] 3.3 La UNIQUE KEY es COMPUESTA en (period_id, doc_type_id) — NO solo en period_id
- [x] 3.4 La tabla tiene campo `id` propio (CI4 requiere primary key para el Query Builder por defecto)
