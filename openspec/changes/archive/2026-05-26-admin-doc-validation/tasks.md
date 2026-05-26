## 1. Verificación previa

- [x] 1.1 Verificar columnas `documents`: status, reviewed_by, reviewed_at, rejection_note (P02)
- [x] 1.2 Verificar S3Service::presignedUrl(s3Key, minutes) existe (P03)
- [x] 1.3 Verificar rutas `/admin/usuarios/(:num)/documento/(:num)` y `/admin/documentos/ver/(:num)` en Routes.php (P07)

## 2. Admin\DocumentController

- [x] 2.1 Crear `Controllers/Admin/DocumentController.php` namespace `App\Controllers\Admin`
- [x] 2.2 `validate($userId, $docId)`: verificar ownership (`WHERE id=$docId AND user_id=$userId`), validar action y rejection_note, UPDATE documents, crear notificación, enviar correo
- [x] 2.3 `view($docId)`: cargar doc, generar presigned URL, retornar JSON — sin verificación de ownership (admin ve todos)
- [x] 2.4 `reviewed_by = session('user_id')` del ADMIN logueado — NO del usuario dueño del doc
- [x] 2.5 Si create_notification helper no está listo (P21): `log_message('info', 'Notif pendiente doc:'.$docId)` y continuar

## 3. Modal en views/admin/users/detail.php

- [x] 3.1 Botón "Aprobar" y botón "Rechazar" por cada documento en la vista de detalle
- [x] 3.2 Modal de rechazo: textarea con contador de chars, mínimo 20 visualmente indicado
- [x] 3.3 Formulario de acción con POST a `site_url('admin/usuarios/'.$userId.'/documento/'.$docId)`

## 4. Verificación final

- [x] 4.1 Aprobar doc → status='approved' en DB, notificación creada o loggeada
- [x] 4.2 Rechazar con nota < 20 chars → error de validación
- [x] 4.3 Rechazar con nota >= 20 chars → status='rejected', rejection_note guardada
- [x] 4.4 GET /admin/documentos/ver/{id} → JSON con presigned URL, no redirect

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `reviewed_by = session('user_id')` del ADMIN — no del usuario dueño del documento
2. Ownership check: `WHERE id=? AND user_id=?` — si no existe: 403 (no 404)
3. `rejection_note` mínimo 20 chars — validar server-side, no solo client-side
4. `view()` retorna JSON — NO redirect, NO HTML, NO view de CI4
5. `S3Service::presignedUrl($doc['s3_key'], 15)` — usar el s3_key del registro en DB
6. Ambas notificaciones (interna + correo) son intentadas — si fallan se loggean, no se revierten
