## 1. Verificación previa

- [x] 1.1 Verificar S3Service::presignedUrl(s3Key, minutes) existe (P03)
- [x] 1.2 Verificar que Admin\DocumentController::view() ya existe (P14)
- [x] 1.3 Verificar rutas GET /dashboard/documentos/ver/(:num) en Routes.php (P07)

## 2. User\DocumentController

- [x] 2.1 Crear `Controllers/User/DocumentController.php` namespace `App\Controllers\User`
- [x] 2.2 `view($id)`: ownership check `WHERE id=$id AND user_id=session('user_id')` → 403 si falla
- [x] 2.3 Retornar JSON: `{url, mime_type, file_extension, original_name, file_size}`
- [x] 2.4 `url = $s3Service->presignedUrl($doc['s3_key'], 15)`

## 3. document-viewer.js

- [x] 3.1 Crear `public/assets/js/document-viewer.js` — objeto `const DocumentViewer = {}`
- [x] 3.2 `DocumentViewer.open(docId, endpoint)`: fetch JSON del endpoint, llamar openModal con los datos
- [x] 3.3 `openModal(data)`: crear overlay div + modal div en DOM, animar con GSAP
- [x] 3.4 `renderContent(data, container)`: switch por `data.file_extension`
- [x] 3.5 PDF: `pdfjsLib.getDocument({url:data.url}).promise` + renderizar página 1 en canvas, botones prev/next
- [x] 3.6 JPG/PNG: `<img src="data.url" style="max-height:75vh;object-fit:contain;width:100%">`
- [x] 3.7 DOCX/XLSX/PPTX: nombre, tamaño formateado (bytes→KB→MB), fecha, `<a href="data.url" download="data.original_name">Descargar</a>`
- [x] 3.8 Cerrar: click X, click overlay, keydown Escape → GSAP salida + `overlay.remove()`
- [x] 3.9 `file_size` formateado: `size > 1048576 ? (size/1048576).toFixed(1)+'MB' : (size/1024).toFixed(0)+'KB'`

## 4. Integración en layouts y vistas

- [x] 4.1 Cargar PDF.js CDN en `layouts/user.php` y `layouts/admin.php` (antes de document-viewer.js)
- [x] 4.2 Cargar `document-viewer.js` en los layouts mencionados
- [x] 4.3 Agregar botón "Ver" en `views/user/documents.php` (P20) y `views/admin/users/detail.php` (P13)

## 5. Verificación final

- [x] 5.1 Click "Ver" en PDF → modal con PDF renderizado
- [x] 5.2 Click "Ver" en JPG → modal con imagen
- [x] 5.3 Click "Ver" en DOCX → modal con metadata y botón descarga
- [x] 5.4 Escape o click overlay → modal cerrado y DOM limpio
- [x] 5.5 Doc de otro usuario → 403

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. El endpoint retorna JSON — NO HTML, NO redirect
2. `DocumentViewer` crea y destruye el DOM en cada apertura — NO reusar DOM de apertura anterior
3. S3Service::presignedUrl() expira en 15 minutos — no cachear en el cliente
4. `pdfjsLib.getDocument({url: data.url}).promise` — cargar desde CDN cdnjs, NO npm package
5. Para DOCX/XLSX/PPTX: NO intentar Google Docs Viewer (las presigned URLs no funcionan bien) — solo descarga directa
6. `documents.user_id = session('user_id')` para ownership en endpoint de usuario
7. Admin endpoint (P14) ya existe — NO duplicar en DocumentController del usuario
