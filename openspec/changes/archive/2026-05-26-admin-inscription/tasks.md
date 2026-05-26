## 1. Verificación previa

- [x] 1.1 Verificar columnas `inscriptions`: status, rejection_note, reviewed_by, reviewed_at, period_id (P02)
- [x] 1.2 Verificar `users.status` ENUM incluye 'active' (P02)
- [x] 1.3 Verificar que validateInscription stub existe en UserController (P13)

## 2. validateInscription() completo

- [x] 2.1 Obtener la inscripción del usuario: `WHERE user_id=$userId ORDER BY created_at DESC LIMIT 1`
- [x] 2.2 Si action='approve': query COUNT documentos con status!='approved' para ese user_id+period_id
- [x] 2.3 Si COUNT > 0: retornar error "Hay documentos sin aprobar"
- [x] 2.4 Si COUNT = 0: UPDATE inscriptions (status='approved', reviewed_by, reviewed_at) + UPDATE users (status='active')
- [x] 2.5 Si action='reject': validar rejection_note >= 30 chars; UPDATE inscriptions (status='rejected', rejection_note, reviewed_by, reviewed_at); NO modificar users.status
- [x] 2.6 Después de ambas acciones: create_notification + MailService::sendInscriptionResult

## 3. Verificación final

- [x] 3.1 Aprobar con todos docs aprobados → inscriptions.status='approved', users.status='active'
- [x] 3.2 Aprobar con doc pendiente → error, nada cambia
- [x] 3.3 Rechazar con nota >= 30 → inscriptions rechazada, users.status sin cambio
- [x] 3.4 Rechazar con nota < 30 → error de validación

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. La verificación de docs: `WHERE user_id=? AND period_id=? AND status!='approved'` — INCLUYE period_id
2. En aprobación: actualizar AMBAS tablas (inscriptions + users)
3. En rechazo: actualizar SOLO inscriptions — NO tocar users.status
4. `reviewed_by = session('user_id')` del ADMIN — no del usuario
5. Motivo de rechazo: mínimo 30 chars (vs documentos que son 20 chars)
6. Notificaciones: create_notification Y MailService — ambas obligatorias, si fallan: log y continuar
