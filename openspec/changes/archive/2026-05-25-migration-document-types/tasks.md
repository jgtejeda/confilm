## 1. Crear archivo de migración

- [x] 1.1 Crear `app/app/Database/Migrations/2025-01-01-000003_CreateDocumentTypesTable.php`
- [x] 1.2 Implementar `up()` con addField: id, name VARCHAR(200) NOT NULL, description TEXT NULL, category ENUM('inicial','complementario') NOT NULL, required TINYINT(1) DEFAULT 1
- [x] 1.3 Continuar addField: allowed_types VARCHAR(500) NOT NULL, max_size_mb INT DEFAULT 5, max_months INT NULL, sort_order INT DEFAULT 0, active TINYINT(1) DEFAULT 1
- [x] 1.4 Continuar addField: created_by INT UNSIGNED NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
- [x] 1.5 Agregar FK: `created_by → users(id) ON DELETE SET NULL`
- [x] 1.6 Llamar `$this->forge->createTable('document_types')`
- [x] 1.7 Implementar `down()`: `$this->forge->dropTable('document_types', true)`

## 2. Verificación

- [x] 2.1 Ejecutar `php spark migrate` — sin errores
- [x] 2.2 Verificar con `DESCRIBE document_types` — confirmar todos los campos
- [x] 2.3 Verificar que `allowed_types` es varchar(500) NOT NULL (no TEXT, no tabla separada)
- [x] 2.4 Verificar ENUM de category: debe ser ENUM('inicial','complementario')
- [x] 2.5 Insertar registro de prueba con allowed_types='["pdf","jpg"]' — confirma que json_decode lo retorna como array

## ⚠️ Anti-alucinación

- [x] 3.1 El archivo se llama EXACTAMENTE `2025-01-01-000003_CreateDocumentTypesTable.php`
- [x] 3.2 `allowed_types` es VARCHAR(500) NOT NULL — NO crear tabla `document_type_allowed_types` ni similar
- [x] 3.3 Los valores del ENUM de category son EXACTAMENTE 'inicial' y 'complementario' (sin mayúsculas, sin acentos)
- [x] 3.4 `max_months` es INT NULL (nullable — NULL significa sin restricción de vigencia)
- [x] 3.5 NO hay seed de document_types — el admin los crea desde el panel
