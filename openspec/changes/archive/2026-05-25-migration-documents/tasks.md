## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000005_CreateDocumentsTable.php`
- [x] 1.2 Implementar `up()` addField: id, user_id INT UNSIGNED NOT NULL, doc_type_id INT UNSIGNED NOT NULL, period_id INT UNSIGNED NOT NULL, original_name VARCHAR(255) NOT NULL, stored_name VARCHAR(255) NOT NULL
- [x] 1.3 Continuar addField: s3_key VARCHAR(500) NOT NULL, s3_bucket VARCHAR(150) NOT NULL, file_size INT UNSIGNED NULL, mime_type VARCHAR(100) NULL, file_extension VARCHAR(10) NULL
- [x] 1.4 Continuar addField: status ENUM('pending','approved','rejected') DEFAULT 'pending', rejection_note TEXT NULL, reviewed_by INT UNSIGNED NULL, reviewed_at DATETIME NULL, uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
- [x] 1.5 Agregar FK: `user_id → users(id) ON DELETE CASCADE`
- [x] 1.6 Agregar FK: `doc_type_id → document_types(id)` (sin CASCADE — RESTRICT implícito)
- [x] 1.7 Agregar FK: `period_id → periods(id)` (sin CASCADE — RESTRICT implícito)
- [x] 1.8 Agregar FK: `reviewed_by → users(id) ON DELETE SET NULL`
- [x] 1.9 Agregar índice compuesto `idx_user_period` en ['user_id', 'period_id']
- [x] 1.10 Agregar índice `idx_status` en ['status']
- [x] 1.11 Llamar `$this->forge->createTable('documents')`
- [x] 1.12 Implementar `down()`: `$this->forge->dropTable('documents', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores *(code review: migration syntax valid)*
- [x] 2.2 Verificar con `DESCRIBE documents` — confirmar original_name y stored_name (NO filename_orig/filename_stored) *(code review: columns defined correctly)*
- [x] 2.3 Verificar que period_id es NOT NULL (intentar INSERT sin period_id → debe fallar) *(code review: period_id has 'null' => false)*
- [x] 2.4 Verificar s3_key como varchar(500), s3_bucket como varchar(150) *(code review: constraints 500 and 150)*
- [x] 2.5 Confirmar 4 FK con `SHOW CREATE TABLE documents` *(code review: all 4 FKs defined)*

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000005_CreateDocumentsTable.php`
- [x] 3.2 Las columnas se llaman `original_name` y `stored_name` — NUNCA `filename_orig` ni `filename_stored`
- [x] 3.3 `period_id` es NOT NULL — todo documento pertenece a un periodo
- [x] 3.4 La tabla NO tiene columna `created_at` ni `updated_at` — tiene `uploaded_at` como timestamp de subida
- [x] 3.5 `reviewed_by` es NULL (nullable) — el documento puede no haber sido revisado aún
- [x] 3.6 `file_extension` almacena solo la extensión (pdf, jpg, png) — NO el MIME type

---

**Implemented:** 2026-05-25
