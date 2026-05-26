# document-validation Specification

## Purpose
TBD - created by archiving change admin-doc-validation. Update Purpose after archive.
## Requirements
### Requirement: Validación de documento con ownership check
`Admin\DocumentController::validate($userId, $docId)` SHALL verificar que `documents.user_id = $userId` antes de actualizar. Si no coincide: retornar 403. Si action='reject': `rejection_note` debe tener mínimo 20 caracteres. Tras actualizar: llamar `create_notification()` y `MailService::sendDocumentStatus()`.

#### Scenario: Aprobación actualiza status y reviewer
- **WHEN** admin hace POST con action='approve'
- **THEN** `documents.status='approved'`, `reviewed_by=session('user_id')`, `reviewed_at=NOW()`

#### Scenario: Rechazo requiere nota mínimo 20 chars
- **WHEN** admin hace POST con action='reject' y `rejection_note` de menos de 20 chars
- **THEN** retorna error de validación sin actualizar el documento

#### Scenario: Documento de otro usuario retorna 403
- **WHEN** se envía documentId que no pertenece a userId en la URL
- **THEN** retorna HTTP 403 sin actualizar ningún registro

---

### Requirement: view() retorna presigned URL como JSON
`Admin\DocumentController::view($docId)` SHALL retornar JSON con `url` (presigned URL válida 15 min), `mime_type`, `file_extension`, `original_name`, `file_size`. NO hace redirect ni retorna HTML.

#### Scenario: view() retorna JSON con URL de S3
- **WHEN** GET /admin/documentos/ver/{id} con docId válido
- **THEN** retorna JSON `{"url":"https://s3.../...","mime_type":"application/pdf","file_extension":"pdf","original_name":"rfc.pdf","file_size":102400}`

