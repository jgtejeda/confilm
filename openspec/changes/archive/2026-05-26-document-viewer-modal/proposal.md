## Why

Los documentos en S3 son privados (presigned URLs). Usuarios y admins necesitan verlos en un modal dentro del sistema, sin exponer el bucket públicamente. PDF.js para PDFs, img tag para imágenes, fallback de descarga para Office.

## What Changes

- **NUEVO** `Controllers/User/DocumentController.php` — view($id) endpoint JSON con ownership check
- **NUEVO** `public/assets/js/document-viewer.js` — modal animado GSAP, PDF.js, img, fallback
- Los endpoints admin ya se implementaron en P14

## Capabilities

### New Capabilities

- `document-viewer`: Modal con GSAP scale+opacity, PDF.js para PDF, img para jpg/png, fallback descarga para DOCX/XLSX/PPTX; presigned URL via endpoint JSON; limpia DOM al cerrar

### Modified Capabilities

(ninguna)

## Impact

- Nuevos: `User/DocumentController.php`, `public/assets/js/document-viewer.js`
- Usa: S3Service::presignedUrl (P03), GSAP (CDN), PDF.js (CDN)
- Sin cambios en DB, modelos ni migraciones
