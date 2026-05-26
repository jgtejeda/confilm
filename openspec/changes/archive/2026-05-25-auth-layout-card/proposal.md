## Why

El sistema necesita una pantalla de autenticación con identidad visual cinematográfica (oscuro + ámbar dorado) y una tarjeta animada con GSAP que alterne entre Login y Registro con física de resorte. Sin este layout, las propuestas P05–P08 no tienen superficie visual donde implementarse.

## What Changes

- **NUEVO** `app/app/Views/layouts/auth.php` — layout HTML base que carga Google Fonts (Cormorant Garamond + DM Sans), GSAP desde CDN cdnjs, auth.css y auth-card.js; recibe variable `$card` ('login'|'register') para mostrar la tarjeta activa
- **MODIFICAR** `app/app/Views/auth/login.php` — adaptar al nuevo layout y estructura de card (`div.card.card--login`)
- **NUEVO** `app/app/Views/auth/register.php` — card de registro con campos básicos: nombres, apellido_pat, apellido_mat, phone, email (los slots de documentos se agregan en P05)
- **NUEVO** `public/assets/css/auth.css` — paleta cinematográfica con CSS Custom Properties, grain/noise en fondo, estilos de card, inputs, botones, responsive
- **NUEVO** `public/assets/js/auth-card.js` — funciones `showRegister()` y `showLogin()` con GSAP timelines + swipe detector mobile (touchstart/touchend, delta X > 50px)
- **NUEVO** `app/app/Controllers/Auth/RegisterController.php` — stub con método `index()` que retorna la vista register (lógica completa en P05)
- **MODIFICAR** `app/app/Config/Routes.php` — agregar GET /login y GET /registro apuntando a sus controllers (sin filter 'noauth' por ahora, se agrega en P07)

Esta propuesta es **solo visual** — no incluye lógica de formulario.

## Capabilities

### New Capabilities

- `auth-animated-card`: Layout de autenticación con tarjeta deslizante animada GSAP entre login y registro, identidad visual cinematográfica, responsive hasta 375px
- `auth-register-view`: Vista de registro con campos personales básicos (nombres, apellidos, teléfono, correo) como punto de extensión para P05

### Modified Capabilities

- `ci4-subfolder-config`: Las rutas GET /login y GET /registro se añaden respetando la configuración de subfolder /comisionfilm/ con site_url()

## Impact

- Archivos nuevos: `views/layouts/auth.php`, `views/auth/register.php`, `public/assets/css/auth.css`, `public/assets/js/auth-card.js`, `Controllers/Auth/RegisterController.php`
- Archivos modificados: `views/auth/login.php` (refactor de estructura), `Config/Routes.php` (añadir 2 rutas)
- Dependencias externas: GSAP 3 (CDN cdnjs), Google Fonts (Cormorant Garamond + DM Sans)
- Sin cambios en DB, modelos ni migraciones
- Sin cambios en S3Service, FileValidator ni MailService
- El AuthFilter existente NO se modifica en esta propuesta
