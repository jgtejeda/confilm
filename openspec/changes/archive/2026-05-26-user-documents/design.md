## Context

User\DocumentController ya tiene view($id). S3Service::archive(s3Key) ya existe (copia con sufijo _archived_timestamp y borra original). Los complementarios tienen category='complementario' en document_types.

## Decisions

### D1 — Re-subida: archive + INSERT (NO UPDATE)
```php
// Si ya existe doc del tipo:
$existingDoc = $documentModel->where('user_id',session('user_id'))
    ->where('doc_type_id',$docTypeId)->where('period_id',$periodId)->first();
if ($existingDoc) { $s3Service->archive($existingDoc['s3_key']); }
// Siempre INSERT nuevo registro
$documentModel->insert([...s3Key nuevo...]);
```

### D2 — S3 key para complementarios
`rcf/{period_id}/{user_id}/complementario/{uuid}.{ext}` — ARQUITECTURA.md §10

### D3 — submit(): verificación server-side
```php
// Verificar que todos los slots complementarios del periodo tienen al menos un doc
$assigned = /* count of period_document_types WHERE period_id AND category='complementario' */;
$uploaded = /* count of documents WHERE user_id AND period_id AND doc_type_id IN (...complementarios...) */;
if ($uploaded < $assigned) { /* error */ }
// Si ok: UPDATE inscriptions SET status='under_review'
```

### D4 — document-upload.js drag & drop
```javascript
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('drop', e => { e.preventDefault(); const file = e.dataTransfer.files[0]; validateAndUpload(file, docTypeId); });
```
Validación client-side de extensión antes de enviar. Luego fetch al endpoint upload con FormData.
