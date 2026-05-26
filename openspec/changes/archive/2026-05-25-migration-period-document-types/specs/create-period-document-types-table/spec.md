## ADDED Requirements

### Requirement: Migración crea la tabla pivote period_document_types
El sistema SHALL disponer del archivo `2025-01-01-000004_CreatePeriodDocumentTypesTable.php` que crea la tabla con FK a periods y document_types.

#### Scenario: Migración ejecuta sin errores
- **WHEN** se ejecuta `php spark migrate` con periods y document_types ya creados
- **THEN** la tabla `period_document_types` existe sin errores

#### Scenario: Rollback limpio
- **WHEN** se ejecuta rollback de esta migración
- **THEN** la tabla se elimina sin errores (nada tiene FK a period_document_types)

### Requirement: UNIQUE KEY compuesta impide duplicados
La tabla SHALL tener UNIQUE KEY `uq_period_doctype` en `(period_id, doc_type_id)` para evitar asignar el mismo tipo de documento dos veces al mismo periodo.

#### Scenario: Inserción duplicada es rechazada
- **WHEN** se intenta insertar dos registros con el mismo period_id y doc_type_id
- **THEN** MySQL lanza error de duplicate key

#### Scenario: Inserción con distinto period_id es permitida
- **WHEN** se inserta el mismo doc_type_id para dos period_id distintos
- **THEN** ambos registros se insertan correctamente

### Requirement: FK con CASCADE eliminan asignaciones automáticamente
Las FK SHALL usar `ON DELETE CASCADE` en ambas referencias.

#### Scenario: Eliminar periodo elimina sus asignaciones
- **WHEN** se elimina un periodo
- **THEN** todos los registros de period_document_types con ese period_id son eliminados automáticamente

#### Scenario: Eliminar tipo elimina sus asignaciones
- **WHEN** se elimina un document_type
- **THEN** todos los registros de period_document_types con ese doc_type_id son eliminados automáticamente
