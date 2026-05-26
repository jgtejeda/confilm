## Context

ARQUITECTURA.md §11: El visor usa GSAP scale(0.8)→scale(1) + opacity 0→1, ease back.out(1.5). DOCX/XLSX/PPTX muestran metadata + botón descarga (Google Docs Viewer no es confiable con presigned URLs). S3Service::presignedUrl ya existe.

## Decisions

### D1 — API JS pública
```javascript
DocumentViewer.open(docId, endpoint)
// endpoint: '/dashboard/documentos/ver/' o '/admin/documentos/ver/'
// fetch(endpoint+docId) → {url, mime_type, file_extension, original_name, file_size}
```

### D2 — Renderer por extensión
- `pdf` → PDF.js: `pdfjsLib.getDocument({url}).promise.then(pdf => { /* render page 1 */ })`
- `jpg|png` → `<img src={url} style="max-height:80vh;object-fit:contain">`
- `docx|xlsx|pptx` → metadata card + `<a href={url} download={original_name}>Descargar</a>`

### D3 — Modal DOM creado y destruido en cada apertura
```javascript
function openModal(content) {
    const overlay = document.createElement('div');
    overlay.className = 'dv-overlay';
    // ... armar modal
    document.body.appendChild(overlay);
    gsap.fromTo(modal, {scale:0.8,opacity:0}, {scale:1,opacity:1,ease:'back.out(1.5)',duration:0.3});
}
function closeModal() {
    gsap.to(modal, {scale:0.8,opacity:0,duration:0.2,onComplete:()=>overlay.remove()});
}
```

### D4 — Ownership check en User\DocumentController::view($id)
```php
$doc = $documentModel->where('id',$id)->where('user_id', session('user_id'))->first();
if (!$doc) return $this->response->setStatusCode(403)->setJSON(['error'=>'Forbidden']);
```

### D5 — PDF.js desde CDN
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.min.js"></script>
```
Solo en layouts que lo necesiten (user.php y admin.php).
