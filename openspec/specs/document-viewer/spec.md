# document-viewer Specification

## Purpose
TBD - created by archiving change document-viewer-modal. Update Purpose after archive.
## Requirements
### Requirement: Endpoint JSON con presigned URL
`User\DocumentController::view($id)` SHALL verificar ownership (`documents.user_id = session('user_id')`), generar presigned URL vía S3Service, retornar JSON `{url, mime_type, file_extension, original_name, file_size}`. NO retorna HTML ni redirect.

#### Scenario: view() retorna JSON con URL válida
- **WHEN** GET /dashboard/documentos/ver/{id} con doc del usuario logueado
- **THEN** retorna JSON con url (presigned S3, 15 min), mime_type, file_extension, original_name, file_size

#### Scenario: Doc de otro usuario retorna 403
- **WHEN** GET /dashboard/documentos/ver/{id} con doc que pertenece a otro usuario
- **THEN** retorna HTTP 403

---

### Requirement: Modal GSAP con renderer por tipo
`DocumentViewer.open(docId, endpoint)` SHALL: fetch el endpoint, crear overlay+modal en DOM, animar con GSAP scale(0.8)→(1) + opacity 0→1 ease back.out(1.5), renderizar según file_extension: PDF con PDF.js, JPG/PNG con img, DOCX/XLSX/PPTX con metadata+descarga. Al cerrar: animación inversa y `overlay.remove()`.

#### Scenario: PDF renderiza en modal
- **WHEN** usuario hace click en "Ver" de un documento PDF
- **THEN** modal abre con PDF renderizado en canvas via PDF.js, controles prev/next

#### Scenario: JPG muestra imagen en modal
- **WHEN** usuario hace click en "Ver" de un documento JPG
- **THEN** modal abre con `<img src={presignedUrl}>` con object-fit contain

#### Scenario: DOCX muestra metadata y botón descarga
- **WHEN** usuario hace click en "Ver" de un documento DOCX
- **THEN** modal muestra nombre original, tamaño formateado, fecha y botón `<a download>` — sin intento de preview embebido

#### Scenario: Escape o click fuera cierra el modal
- **WHEN** usuario presiona Escape o hace click en el overlay
- **THEN** modal cierra con animación inversa y el DOM es limpiado

