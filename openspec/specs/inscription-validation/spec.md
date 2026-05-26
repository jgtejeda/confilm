# inscription-validation Specification

## Purpose
TBD - created by archiving change admin-inscription. Update Purpose after archive.
## Requirements
### Requirement: Aprobación requiere todos los docs aprobados
`validateInscription($userId)` con action='approve' SHALL verificar que no existe ningún documento del usuario en el periodo activo con status distinto de 'approved'. Si hay documentos pendientes/rechazados: retornar error "Hay documentos sin aprobar".

#### Scenario: Aprobación con todos los docs aprobados actualiza ambas tablas
- **WHEN** admin aprueba inscripción y todos los documentos del periodo están 'approved'
- **THEN** `inscriptions.status='approved'`, `users.status='active'`, `reviewed_by=session('user_id')`, `reviewed_at=NOW()`

#### Scenario: Aprobación con docs pendientes es bloqueada
- **WHEN** admin intenta aprobar inscripción con al menos un documento 'pending' o 'rejected'
- **THEN** retorna error "Hay documentos sin aprobar" sin modificar ningún registro

---

### Requirement: Rechazo no cambia users.status
`validateInscription($userId)` con action='reject' SHALL actualizar solo `inscriptions.status='rejected'` y `inscriptions.rejection_note`. NO SHALL modificar `users.status`.

#### Scenario: Rechazo con motivo mínimo 30 chars
- **WHEN** admin rechaza inscripción con rejection_note de >= 30 chars
- **THEN** `inscriptions.status='rejected'`, `rejection_note` guardado, `users.status` sin cambio

#### Scenario: Rechazo con motivo corto es rechazado
- **WHEN** admin intenta rechazar con rejection_note < 30 chars
- **THEN** retorna error de validación sin actualizar DB

