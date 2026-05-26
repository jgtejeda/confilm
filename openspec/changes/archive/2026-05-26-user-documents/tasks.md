## 1. Verificación previa

- [x] 1.1 Verificar S3Service::archive(s3Key) existe (P03)
- [x] 1.2 Verificar que User\DocumentController::view($id) ya existe (P17)
- [x] 1.3 Verificar rutas /dashboard/documentos* en Routes.php (P07)
- [x] 1.4 Verificar `documents.doc_type_id` tiene FK a `document_types.id` (P02)

## 2. User\DocumentController — 3 métodos nuevos

- [x] 2.1 `index()`: cargar periodo del usuario, query doc_types complementarios del periodo + docs existentes del usuario (LEFT JOIN)
- [x] 2.2 `upload()`: validar archivo con FileValidator (json_decode allowed_types), si existe doc previo → S3Service::archive() + INSERT nuevo, si no existe → solo INSERT; S3 key: `rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}`; retornar JSON `{success, doc_id, status}` para AJAX
- [x] 2.3 `submit()`: contar doc_types complementarios asignados al periodo vs docs del usuario con period_id; si igual: UPDATE inscriptions (status='under_review', submitted_at=NOW()), crear notificación al admin; retornar redirect o JSON

## 3. document-upload.js

- [x] 3.1 Crear `public/assets/js/document-upload.js`
- [x] 3.2 Drag & drop: dragover (prevent default, agregar clase), dragleave (quitar clase), drop (extraer file, llamar uploadFile)
- [x] 3.3 `uploadFile(file, docTypeId, allowedExts)`: validar ext client-side, luego `fetch(baseUrl+'dashboard/documentos/subir', {method:'POST', body:FormData})` con CSRF
- [x] 3.4 Actualizar UI del slot tras respuesta exitosa: badge de status "Pendiente", botón "Ver" habilitado
- [x] 3.5 `Notify.promise(fetchPromise, {loading:'Subiendo...', success:'Documento cargado', error:'Error al subir'})`

## 4. Vista user/documents.php

- [x] 4.1 Crear `views/user/documents.php` — grid de slots complementarios con foreach($docTypes)
- [x] 4.2 Cada slot: nombre, descripción, tipos aceptados legibles, status chip, zona drag&drop, botón "Ver" si tiene doc
- [x] 4.3 Botón "Enviar para revisión" al final — deshabilitado si no todos los slots tienen doc
- [x] 4.4 JS: verificación visual client-side del botón enviar (opcional, server siempre verifica)

## 5. Verificación final

- [x] 5.1 Vista muestra slots complementarios dinámicos del periodo
- [x] 5.2 Subir archivo → aparece en S3, nuevo registro en documents
- [x] 5.3 Re-subir → S3::archive del anterior, nuevo INSERT en documents
- [x] 5.4 Submit incompleto → error sin cambiar inscription.status
- [x] 5.5 Submit completo → inscription.status='under_review'
- [x] 5.6 s3_key en DB: `rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}`

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. Al re-subir: S3::archive() + INSERT NUEVO — NO UPDATE del registro existente
2. s3_key: `rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}` — carpeta 'complementario' no 'inicial'
3. `json_decode($docType['allowed_types'], true)` antes de FileValidator
4. upload() retorna JSON para AJAX — no redirect
5. submit() verifica server-side — no confiar solo en el client-side
6. `FileValidator::checkMagicBytes()` después de mover a temp — mismo flujo que en P05
7. Los slots son de category='complementario' — no mezclar con 'inicial'
8. `Notify.promise()` del sistema de toasts (P18) para feedback visual del upload
