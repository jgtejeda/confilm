## ADDED Requirements

### Requirement: Transacción atómica DB + S3 con rollback
El sistema SHALL ejecutar el registro completo en una transacción: `$db->transStart()` → INSERT users → S3 upload por cada archivo → INSERT documents → INSERT inscriptions → `$db->transComplete()`. Si `$db->transStatus() === false`, SHALL llamar `S3Service::delete()` por cada archivo ya subido. Si S3 upload falla antes de completar todos los archivos, SHALL retornar error sin insertar nada en DB.

#### Scenario: Fallo en DB hace rollback y limpia S3
- **WHEN** los archivos se suben exitosamente a S3 pero INSERT inscriptions falla (ej: violación UNIQUE)
- **THEN** `$db->transStatus()` es false, se hace rollback de la DB, y S3Service::delete() es llamado para cada s3Key ya subido

#### Scenario: Registro exitoso crea los registros correctos en DB
- **WHEN** POST /registro es válido con periodo activo y archivos correctos
- **THEN** se crea 1 fila en `users`, N filas en `documents` (una por cada archivo, con `period_id`, `s3_key`, `s3_bucket`, `file_extension`, `original_name`, `stored_name`), y 1 fila en `inscriptions` con `period_id` y `status='incomplete'`

---

### Requirement: Estructura correcta de s3_key en documents
El campo `documents.s3_key` SHALL contener el path completo: `rcf/{period_id}/{user_id}/inicial/{uuid}.{ext}`. El campo `documents.stored_name` SHALL contener solo `{uuid}.{ext}`. El campo `documents.original_name` SHALL contener el nombre original del archivo del usuario.

#### Scenario: s3_key sigue la estructura definida
- **WHEN** un usuario con id=5 sube un PDF al periodo con id=2
- **THEN** `documents.s3_key` es `rcf/2/5/inicial/{uuid}.pdf` y `documents.stored_name` es `{uuid}.pdf`

#### Scenario: original_name preserva el nombre del usuario
- **WHEN** el usuario sube un archivo llamado "mi_rfc_2025.pdf"
- **THEN** `documents.original_name` = "mi_rfc_2025.pdf" (nombre que subió el usuario)
