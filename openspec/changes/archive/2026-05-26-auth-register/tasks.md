## 1. Verificación previa

- [x] 1.1 Leer ARQUITECTURA.md §5 (tablas: users, documents, inscriptions — columnas exactas), §9 (FileValidator), §10 (S3 key structure rcf/{period_id}/{user_id}/inicial/)
- [x] 1.2 Verificar que `DocumentModel.$allowedFields` incluye: `period_id, s3_key, s3_bucket, file_extension, original_name, stored_name, file_size, mime_type` (propuesta 02)
- [x] 1.3 Verificar que `InscriptionModel.$allowedFields` incluye: `user_id, period_id, status` (propuesta 02)
- [x] 1.4 Verificar que `S3Service.php` tiene método `upload(tempPath, s3Key, mimeType): bool` y `delete(s3Key): bool` (propuesta 03)
- [x] 1.5 Verificar que `FileValidator.php` tiene `validate(file, allowedTypes, maxSizeMb): array` y `checkMagicBytes(tempPath, ext): bool` (propuesta 03)
- [x] 1.6 Verificar que `RegisterController.php` tiene solo `index()` stub (propuesta 04)

## 2. Libraries

- [x] 2.1 Crear `app/app/Libraries/UsernameGenerator.php` — constructor recibe `UserModel $model`, método `generate(string $nombres, string $apellidoPat): string`
- [x] 2.2 En `generate()`: tomar 1ª letra de `$nombres`, concatenar `$apellidoPat` normalizado (quitar acentos: á→a, é→e, í→i, ó→o, ú→u, ñ→n, solo a-z), agregar `_` y 4 chars alfanuméricos aleatorios con `substr(bin2hex(random_bytes(4)), 0, 4)`
- [x] 2.3 Anti-colisión: verificar `$this->userModel->where('username', $candidate)->first()` — si existe, reintentar máx 10 veces; si 10 fallos, lanzar excepción `\RuntimeException`
- [x] 2.4 Crear `app/app/Libraries/PasswordGenerator.php` — método estático `generate(): string`
- [x] 2.5 En `generate()`: construir pool `$upper.$lower.$digits.$symbols.$random`, usar `str_shuffle()`, retornar los primeros 12 chars — garantizando composición mínima (2+2+2+2+4)

## 3. RegisterController — index() actualizado

- [x] 3.1 En `index()`: query periodo activo con `PeriodModel` o Query Builder (`active=1 AND start_date<=NOW() AND end_date>=NOW() LIMIT 1`)
- [x] 3.2 Si no hay periodo: retornar `view('auth/no_period')`
- [x] 3.3 Si hay periodo: query `$docTypes` via JOIN `period_document_types` + `document_types` WHERE `period_id=$period['id']` AND `dt.active=1` ORDER BY `pdt.sort_order`
- [x] 3.4 Retornar `view('layouts/auth', ['card'=>'register', 'period'=>$period, 'docTypes'=>$docTypes])`

## 4. RegisterController — process()

- [x] 4.1 Re-verificar periodo activo en POST (mismo query que index)
- [x] 4.2 Validar datos personales con CI4 Validation: nombres (required, min 2, max 100), apellido_pat (required), apellido_mat (max 80), phone (required, regex_match[/^\d{10}$/]), email (required, valid_email, is_unique[users.email])
- [x] 4.3 Para cada `$docType` en `$docTypes`: obtener `$file = $request->getFile('doc_'.$docType['id'])`, llamar `FileValidator::validate($file, json_decode($docType['allowed_types'],true), $docType['max_size_mb'])`
- [x] 4.4 Mover archivo a temp del servidor, llamar `FileValidator::checkMagicBytes($tempPath, $ext)` — si falla: limpiar temps y retornar error
- [x] 4.5 `$db->transStart()`
- [x] 4.6 Generar username (UsernameGenerator), password (PasswordGenerator), verify_token, verify_exp
- [x] 4.7 INSERT users: `password_hash($rawPass, PASSWORD_BCRYPT, ['cost'=>12])`, `email_verified=0`, `status='pending'`
- [x] 4.8 Para cada archivo: generar UUID, construir `s3Key = 'rcf/'.$period['id'].'/'.$userId.'/inicial/'.$uuid.'.'.$ext`, llamar `S3Service::upload($tempPath, $s3Key, $mimeType)` — si falla: break y flag error
- [x] 4.9 Si algún S3 upload falló: NO llegar a INSERT documents, retornar error inmediato
- [x] 4.10 INSERT documents por cada archivo: `user_id, doc_type_id, period_id, original_name, stored_name={uuid}.{ext}, s3_key, s3_bucket=env('AWS_S3_BUCKET'), file_size, mime_type, file_extension, status='pending'`
- [x] 4.11 INSERT inscriptions: `user_id, period_id, status='incomplete'`
- [x] 4.12 `$db->transComplete()` — si `$db->transStatus()===false`: foreach `$uploadedKeys` → `S3Service::delete($key)`, retornar error
- [x] 4.13 Enviar correos (o log_message si MailService no disponible), redirect a `site_url('verificar-pendiente')`

## 5. Vista register.php actualizada

- [x] 5.1 Después de los campos personales, agregar `<?php foreach($docTypes as $dt): ?>` para generar slots de documentos
- [x] 5.2 Cada slot: `<label>` con `$dt['name']`, párrafo con `$dt['description']` (instrucción), texto de tipos aceptados legibles, input file con `name="doc_<?= $dt['id'] ?>"` y `accept=` generado
- [x] 5.3 Mapeo accept=: `pdf→.pdf`, `jpg→.jpg,.jpeg`, `png→.png`, `docx→.docx`, `xlsx→.xlsx`, `pptx→.pptx` — construir string desde `json_decode($dt['allowed_types'],true)`
- [x] 5.4 Mostrar `max_size_mb`: "Máximo <?= $dt['max_size_mb'] ?> MB"
- [x] 5.5 Si `$errors` o `session('errors')` existen: mostrar mensajes de error correspondientes

## 6. Vista no_period.php

- [x] 6.1 Crear `app/app/Views/auth/no_period.php` — puede ser simple: mensaje "No hay convocatoria abierta en este momento. Intenta más tarde." con link de regreso al login usando `site_url('login')`

## 7. Rutas

- [x] 7.1 Agregar en `Routes.php` en el grupo noauth (o sin filter por ahora): `$routes->post('registro', 'Auth\RegisterController::process');`

## 8. Verificación final

- [x] 8.1 Con periodo activo: GET /registro muestra slots de documentos dinámicos
- [x] 8.2 Sin periodo activo: GET /registro muestra vista no_period
- [x] 8.3 POST válido con archivos correctos: usuario creado en DB, archivos en S3, inscripción creada, redirect a verificar-pendiente
- [x] 8.4 Archivo con tipo incorrecto (magic bytes): error específico, ningún registro en DB ni S3
- [x] 8.5 Email duplicado: error de validación CI4, ningún registro creado
- [x] 8.6 Verificar en DB: `documents.s3_key` empieza con `rcf/{period_id}/{user_id}/inicial/`
- [x] 8.7 Verificar en DB: `documents.period_id` NO es NULL
- [x] 8.8 Verificar en DB: `inscriptions` tiene UNIQUE(user_id, period_id) — no hay duplicados

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `documents.period_id` es FK a `periods.id` — es el ID del periodo activo del proceso — NUNCA NULL
2. `s3_key` = `rcf/{period_id}/{user_id}/inicial/{uuid}.{ext}` — EXACTAMENTE (ARQUITECTURA.md §10)
3. `stored_name` = solo `{uuid}.{ext}` (no el path completo)
4. `original_name` = el nombre original que subió el usuario (`$file->getClientName()`)
5. `json_decode($docType['allowed_types'], true)` — siempre `true` para array asociativo
6. `allowed_types` válidos son EXACTAMENTE: `"pdf"`, `"docx"`, `"xlsx"`, `"pptx"`, `"jpg"`, `"png"`
7. Input file `name` debe ser `doc_{$docType['id']}` — NO `doc_type_id` ni `documento[]`
8. `$db->transStatus() === false` (triple igual) para detectar fallo de transacción
9. UsernameGenerator recibe UserModel en constructor — NO hacer queries directas con `$db` dentro
10. PasswordGenerator::generate() es método ESTÁTICO — no necesita instancia
11. NO hay `writable/uploads/` — los archivos van directo de temp de PHP a S3
12. El seeder de admin ya existe (propuesta 02) — NO recrear ni modificar
13. Si MailService no existe aún: `log_message('info', 'Correo pendiente welcome: '.$email)` y continuar
