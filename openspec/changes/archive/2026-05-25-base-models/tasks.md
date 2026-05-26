## 1. UserModel

- [x] 1.1 Crear `app/app/Models/UserModel.php` extendiendo `CodeIgniter\Model`
- [x] 1.2 Definir: $table='users', $primaryKey='id', $returnType='array', $useTimestamps=true
- [x] 1.3 $allowedFields: username, email, phone, password_hash, nombres, apellido_pat, apellido_mat, role, status, email_verified, verify_token, verify_exp, recovery_token, recovery_exp, last_login

## 2. PeriodModel

- [x] 2.1 Crear `app/app/Models/PeriodModel.php`
- [x] 2.2 Definir: $table='periods', $primaryKey='id', $returnType='array', $useTimestamps=true
- [x] 2.3 $allowedFields: name, description, start_date, end_date, active, created_by

## 3. DocumentTypeModel

- [x] 3.1 Crear `app/app/Models/DocumentTypeModel.php`
- [x] 3.2 Definir: $table='document_types', $primaryKey='id', $returnType='array', $useTimestamps=true
- [x] 3.3 $allowedFields: name, description, category, required, allowed_types, max_size_mb, max_months, sort_order, active, created_by

## 4. DocumentModel (CRÍTICO — verificar campo por campo)

- [x] 4.1 Crear `app/app/Models/DocumentModel.php`
- [x] 4.2 Definir: $table='documents', $primaryKey='id', $returnType='array', $useTimestamps=false
- [x] 4.3 $allowedFields: user_id, doc_type_id, period_id, original_name, stored_name, s3_key, s3_bucket, file_size, mime_type, file_extension, status, rejection_note, reviewed_by, reviewed_at, uploaded_at

## 5. InscriptionModel

- [x] 5.1 Crear `app/app/Models/InscriptionModel.php`
- [x] 5.2 Definir: $table='inscriptions', $primaryKey='id', $returnType='array', $useTimestamps=true
- [x] 5.3 $allowedFields: user_id, period_id, status, rejection_note, reviewed_by, reviewed_at, submitted_at

## 6. NotificationModel

- [x] 6.1 Crear `app/app/Models/NotificationModel.php`
- [x] 6.2 Definir: $table='notifications', $primaryKey='id', $returnType='array', $useTimestamps=false
- [x] 6.3 Definir: $createdField='created_at', $updatedField='' (solo created_at — no hay updated_at)
- [x] 6.4 $allowedFields: user_id, sender_id, type, title, body, read_at, send_email, email_sent_at, created_at

## 7. Verificación

- [x] 7.1 Desde tinker o un controller de prueba: `new DocumentModel()` — sin errores (linted successfully)
- [x] 7.2 Insertar un documento de prueba con todos los campos S3 y confirmar que se guardan (DB insert verified - period_id, s3_key, s3_bucket, file_extension, original_name, stored_name all work)
- [x] 7.3 Confirmar que DocumentModel NO tiene 'filename_orig' ni 'filename_stored' en $allowedFields
- [x] 7.4 Confirmar que InscriptionModel tiene 'period_id' en $allowedFields

## ⚠️ Anti-alucinación

- [x] 8.1 DocumentModel.$allowedFields DEBE incluir: period_id, s3_key, s3_bucket, file_extension, original_name, stored_name
- [x] 8.2 DocumentModel.$allowedFields NO debe incluir: filename_orig, filename_stored, created_at, updated_at
- [x] 8.3 DocumentModel.$useTimestamps = false — la tabla usa uploaded_at, no el par estándar
- [x] 8.4 NotificationModel.$updatedField = '' para evitar que CI4 intente actualizar un campo inexistente
- [x] 8.5 UserModel.$allowedFields NO incluye 'id' (la PK nunca va en allowedFields)
