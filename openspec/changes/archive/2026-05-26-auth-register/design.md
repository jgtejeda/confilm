## Context

Ya existen: todos los modelos (UserModel, DocumentModel, InscriptionModel, PeriodModel, DocumentTypeModel con sus `$allowedFields`), S3Service (upload/presignedUrl/archive/delete), FileValidator (validate/checkMagicBytes), RegisterController stub con solo `index()`, `views/auth/register.php` con campos personales. El sistema corre en Docker `rcf_app`. La tabla `documents` tiene columnas: `user_id`, `doc_type_id`, `period_id`, `original_name`, `stored_name`, `s3_key`, `s3_bucket`, `file_size`, `mime_type`, `file_extension`, `status`. La tabla `inscriptions` tiene UNIQUE(user_id, period_id).

## Goals / Non-Goals

**Goals:**
- RegisterController::index() verifica periodo activo y carga sus document_types
- RegisterController::process() con validación, FileValidator, S3 upload, transacción DB
- Vista register.php con slots dinámicos de documentos generados desde DB
- UsernameGenerator y PasswordGenerator libraries
- Vista no_period.php cuando no hay convocatoria
- POST /registro en Routes.php

**Non-Goals:**
- Verificación de correo (P06)
- Login posterior al registro (P07)
- MailService real (se usa log stub si aún no existe P09)
- Documentos complementarios (P20)

## Decisions

### D1 — Query de periodo activo
```php
$period = $periodModel->where('active', 1)
    ->where('start_date <=', date('Y-m-d H:i:s'))
    ->where('end_date >=', date('Y-m-d H:i:s'))
    ->first();
```
Si NULL → retornar `view('layouts/auth', ['card'=>'register'])` con `$noPeriod = true` o retornar `view('auth/no_period')`.

### D2 — Carga de document_types del periodo
```php
$docTypes = $db->table('period_document_types pdt')
    ->select('dt.*, pdt.sort_order as pdt_sort')
    ->join('document_types dt', 'dt.id = pdt.doc_type_id')
    ->where('pdt.period_id', $period['id'])
    ->where('dt.active', 1)
    ->orderBy('pdt.sort_order', 'ASC')
    ->get()->getResultArray();
```
Pasar `$period` y `$docTypes` a la vista.

### D3 — Transacción atómica (orden crítico)
```
1. $db->transStart()
2. INSERT users → $userId
3. Para cada $file: S3Service::upload(tempPath, s3Key, mime) → push a $uploadedKeys[]
4. INSERT documents (por cada archivo)
5. INSERT inscriptions (period_id del periodo activo)
6. $db->transComplete()
7. Si $db->transStatus() === false: foreach $uploadedKeys → S3Service::delete()
```
Si S3 upload falla en paso 3 antes de DB: lanzar return con error (no se llegó a transStart o se llama transRollback).

### D4 — S3 key structure
`rcf/{$period['id']}/{$userId}/inicial/{$uuid}.{$ext}` — EXACTAMENTE esta estructura (ARQUITECTURA.md §10)
- `stored_name` en DB = `{$uuid}.{$ext}` (solo filename)
- `s3_key` en DB = el path completo `rcf/...`

### D5 — UsernameGenerator
```php
// 1ª letra nombre + apellido_pat normalizado + _ + 4 chars aleatorios
// Normalizar: iconv('UTF-8','ASCII//TRANSLIT',$str) o str_replace manual á→a, etc.
// Anti-colisión: máx 10 intentos con nuevos 4 chars
// Constructor recibe UserModel para verificar colisiones con where('username', $candidate)->first()
```

### D6 — PasswordGenerator
```php
// 12 chars: 2 mayúsculas + 2 minúsculas + 2 dígitos + 2 símbolos (!@#$%^&*) + 4 aleatorios de todos
// str_shuffle() para mezclar
// NUNCA almacenar en texto plano — solo pasar al correo de bienvenida y al hash bcrypt
```

### D7 — Post-registro
- `verify_token = bin2hex(random_bytes(32))` (64 chars hex)
- `verify_exp = date('Y-m-d H:i:s', strtotime('+24 hours'))`
- `email_verified = 0`, `status = 'pending'`
- Si MailService existe: llamar `sendVerifyEmail` y `sendWelcome`; si no: `log_message('info', 'Correo pendiente: ...')`
- Redirect a `site_url('verificar-pendiente')`

## Risks / Trade-offs

- **[Riesgo] S3 sube pero DB falla** → Mitigación: cleanup explícito de S3 tras `transStatus() === false`
- **[Riesgo] allowed_types es JSON en VARCHAR** → Mitigación: siempre `json_decode($docType['allowed_types'], true)` antes de usar
- **[Trade-off] Archivos temporales en el servidor** → Los archivos se mueven a temp de PHP, se suben a S3, y PHP los borra automáticamente al terminar el request. No hay almacenamiento persistente en el servidor.
