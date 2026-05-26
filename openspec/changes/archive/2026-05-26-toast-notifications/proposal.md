## Why

El sistema necesita feedback visual para acciones AJAX (validar documentos, subir archivos, aprobar inscripciones). El sistema de toasts Sileo-style con física de resorte GSAP provee notificaciones elegantes sin depender de librerías externas como SweetAlert o Bootstrap.

## What Changes

- **NUEVO** `public/assets/js/notifications.js` — objeto global `window.Notify` con success, error, warning, info, promise

## Capabilities

### New Capabilities

- `toast-system`: Toasts Sileo-style con GSAP elastic/back, SVG morph, auto-dismiss 4s, stack máx 5, Promise support; sin jQuery ni frameworks

### Modified Capabilities

(ninguna)

## Impact

- Nuevo: `public/assets/js/notifications.js`
- Cargar en `layouts/user.php` y `layouts/admin.php` después de GSAP
- Sin dependencias de DB, S3 ni backend
