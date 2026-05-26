# complementary-documents Specification

## Purpose
TBD - created by archiving change user-documents. Update Purpose after archive.
## Requirements
### Requirement: Slots de documentos complementarios dinámicos
`views/user/documents.php` SHALL mostrar un slot por cada doc_type complementario del periodo del usuario. Cada slot muestra: nombre, instrucción, tipos aceptados, status actual (o "No cargado"), botón "Subir" y botón "Ver" si ya existe.

#### Scenario: Slots complementarios provienen de la DB del periodo
- **WHEN** usuario accede a GET /dashboard/documentos con 10 docs complementarios en su periodo
- **THEN** se muestran 10 slots — ni más ni menos, según lo configurado por el admin

---

### Requirement: Re-subida archiva el anterior en S3
`DocumentController::upload()` SHALL: validar con FileValidator, si ya existe doc del mismo tipo → S3Service::archive(existingDoc.s3_key), INSERT nuevo doc. NO hace UPDATE del registro existente.

#### Scenario: Re-subida archiva el anterior
- **WHEN** usuario sube un nuevo archivo para un slot que ya tiene documento
- **THEN** `S3Service::archive()` es llamado con el s3_key anterior, luego INSERT nuevo documento

#### Scenario: S3 key de complementario usa la ruta correcta
- **WHEN** usuario sube un complementario al periodo 3 siendo user_id=7
- **THEN** `documents.s3_key = 'rcf/3/7/complementario/{uuid}.{ext}'`

---

### Requirement: Submit verifica todos los slots server-side
`DocumentController::submit()` SHALL verificar server-side que todos los doc_types complementarios del periodo tienen al menos un documento del usuario. Si OK: `UPDATE inscriptions SET status='under_review', submitted_at=NOW()`. Si falta alguno: retornar error.

#### Scenario: Submit con slots incompletos es rechazado
- **WHEN** usuario hace submit con 8 de 10 slots cargados
- **THEN** retorna error "Faltan documentos por cargar" sin cambiar inscription.status

#### Scenario: Submit completo actualiza inscription a under_review
- **WHEN** usuario hace submit con todos los slots complementarios cargados
- **THEN** `inscriptions.status='under_review'`, `submitted_at=NOW()`, notificación al admin

