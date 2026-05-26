## Context

`documents` columnas: id, user_id, doc_type_id, period_id, original_name, stored_name, s3_key, s3_bucket, file_size, mime_type, file_extension, status ENUM('pending','approved','rejected'), rejection_note TEXT NULL, reviewed_by INT NULL, reviewed_at DATETIME NULL. S3Service::presignedUrl(s3Key, 15) ya existe.

## Decisions

### D1 — Verificación de ownership
```php
$doc = $documentModel->where('id',$docId)->where('user_id',$userId)->first();
if (!$doc) return $this->response->setStatusCode(403)->setJSON(['error'=>'Forbidden']);
```

### D2 — validate(): recibe action + rejection_note
```php
$action = $request->getPost('action'); // 'approve' o 'reject'
$note   = $request->getPost('rejection_note');
if ($action === 'reject' && strlen(trim($note)) < 20) { /* error */ }
UPDATE documents SET status=$action=='approve'?'approved':'rejected', reviewed_by=session('user_id'), reviewed_at=NOW(), rejection_note=$note
```

### D3 — view(): retorna JSON con presigned URL
```php
return $this->response->setJSON([
    'url'           => $s3Service->presignedUrl($doc['s3_key'], 15),
    'mime_type'     => $doc['mime_type'],
    'file_extension'=> $doc['file_extension'],
    'original_name' => $doc['original_name'],
    'file_size'     => $doc['file_size'],
]);
```

### D4 — Notificaciones tras validación
Después de UPDATE: llamar `create_notification($doc['user_id'], 'document', ...)` (helper P21) y `MailService::sendDocumentStatus(...)` (P09).
