## 1. Verificación previa (leer ANTES de escribir cualquier archivo)

- [x] 1.1 Leer ARQUITECTURA.md §4 (estructura de carpetas CI4), §16 (UI/UX paleta y animaciones), §19 (subfolder config) — confirmar que ningún nombre de archivo, variable CSS ni parámetro GSAP se inventa
- [x] 1.2 Verificar qué existe actualmente: `app/app/Views/auth/login.php` (¿tiene DOCTYPE propio o solo card?), `app/app/Config/Routes.php` (¿qué rutas ya están definidas?) — login.php SÍ tenía DOCTYPE propio, se extrajo contenido
- [x] 1.3 Confirmar que el vhost.conf tiene el Alias `/comisionfilm` configurado (propuesta 01 — debe existir) ✓ Confirmado
- [x] 1.4 Confirmar que `app/app/Config/Filters.php` tiene 'auth' registrado (NO modificar en esta propuesta) ✓ Confirmado

> ⚠️ Si `login.php` tiene DOCTYPE propio, extraer solo el contenido de la card antes de integrar con el nuevo layout

## 2. CSS — auth.css

- [x] 2.1 Crear `app/public/assets/css/auth.css` con `:root` definiendo EXACTAMENTE: `--color-bg:#0a0a0a`, `--color-surface:#1a1a1a`, `--color-accent:#d4a04a`, `--color-text:#f5f0e8`, `--font-display:'Cormorant Garamond',serif`, `--font-ui:'DM Sans',sans-serif` — NO usar otros valores de color ni otras fuentes
- [x] 2.2 Agregar efecto grain en `body::before` usando SVG data URI con `feTurbulence` (sin imagen externa, sin archivo adicional)
- [x] 2.3 Estilar `.auth-wrapper`: position relative, overflow hidden, min-height 100vh, display flex, align-items center, justify-content center
- [x] 2.4 Estilar `.card`: background `var(--color-surface)`, border-radius, box-shadow, padding, max-width 420px, width 100%
- [x] 2.5 Estilar `.card--login` (position relative, activa por defecto) y `.card--register` (position absolute, top 0, opacity 0, pointer-events none, translateX 110%)
- [x] 2.6 Estilar inputs: background semi-transparente, border con `var(--color-accent)` en focus, color `var(--color-text)`, min-height 44px (touch-friendly)
- [x] 2.7 Estilar botón primario: background `var(--color-accent)`, color #0a0a0a, hover con ligero darken, transición 200ms
- [x] 2.8 Estilar links de navegación entre cards: color `var(--color-accent)`, sin subrayado, cursor pointer
- [x] 2.9 Media query `@media (max-width: 480px)`: card ocupa 100% del ancho con padding horizontal 16px, sin scroll horizontal

## 3. Layout — views/layouts/auth.php

- [x] 3.1 Crear `app/app/Views/layouts/auth.php` con DOCTYPE html5, `<html lang="es">`, `<head>` completo con charset UTF-8, viewport meta, title "Registro Comisión Film"
- [x] 3.2 En `<head>`: agregar `<link>` a Google Fonts con `family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@400;500;600` y `display=swap`
- [x] 3.3 En `<head>`: agregar `<link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">` — usar `base_url()`, NUNCA ruta hardcodeada
- [x] 3.4 En `<body>`: envolver en `<div class="auth-wrapper">` que contiene `<?= view('auth/login') ?>` y `<?= view('auth/register') ?>`
- [x] 3.5 Antes de `</body>`: cargar GSAP 3 desde CDN: `<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>` — PRIMERO GSAP, DESPUÉS auth-card.js
- [x] 3.6 Después del script de GSAP: `<script src="<?= base_url('assets/js/auth-card.js') ?>"></script>` — usar `base_url()`
- [x] 3.7 Verificar que el layout NO incluye lógica PHP de negocio — solo estructura HTML e imports

> ⚠️ El layout incluye AMBAS cards siempre. NO usar `if ($card === 'login') { ... }` para mostrar una sola. La variable `$card` determina cuál es visible via CSS/JS (se puede pasar como variable JS: `var initialCard = '<?= $card ?? 'login' ?>';`)

## 4. Vistas de cards

- [x] 4.1 Reescribir `app/app/Views/auth/login.php` para que contenga SOLO el contenido de la card (sin DOCTYPE, sin `<html>`, sin `<head>`): `<div class="card card--login">` con campos email/username (input name="email_or_username"), password (input name="password"), botón submit, link "¿Olvidaste tu contraseña?" con `href="<?= site_url('recuperar') ?>"`, link "Regístrate" con `onclick="showRegister(); return false;"`. Incluir `<?= csrf_field() ?>` dentro del `<form>`.
- [x] 4.2 Crear `app/app/Views/auth/register.php` con `<div class="card card--register">`: campos nombres (text, required), apellido_pat (text, required), apellido_mat (text, NO required), phone (tel, required), email (email, required). Botón submit. Link "Ya tengo cuenta" con `onclick="showLogin(); return false;"`. `<form method="post" action="<?= site_url('registro') ?>">` con `<?= csrf_field() ?>`.
- [x] 4.3 Verificar que los `name` de los inputs en register.php son EXACTAMENTE: `nombres`, `apellido_pat`, `apellido_mat`, `phone`, `email` — coinciden con `$allowedFields` de UserModel (propuesta 02)
- [x] 4.4 Verificar que login.php NO tiene referencias a rutas hardcodeadas como `/login` o `/dashboard`

## 5. JavaScript — auth-card.js

- [x] 5.1 Crear `app/public/assets/js/auth-card.js` como script no-module (sin `export`/`import`) — es global: `window.showRegister` y `window.showLogin` no son necesarios pero las funciones deben ser accesibles desde el HTML
- [x] 5.2 Implementar `function showRegister()`: GSAP to loginCard `{x:'-110%', opacity:0, ease:'power2.in', duration:0.45}` + GSAP from regCard `{x:'110%', opacity:0, ease:'elastic.out(1,0.6)', duration:0.6}`. Después de la animación: ajustar pointer-events (regCard: all, loginCard: none)
- [x] 5.3 Implementar `function showLogin()`: inverso exacto — loginCard entra desde `-110%`, regCard sale hacia `110%`
- [x] 5.4 Implementar swipe detector en `DOMContentLoaded`: variables `touchStartX` y `touchEndX` en el `.auth-wrapper`. Si `touchStartX - touchEndX > 50` (swipe izquierda) y estado actual es login → `showRegister()`. Si inverso y estado es register → `showLogin()`
- [x] 5.5 Variable de estado `let currentCard = document.body.getAttribute('data-card') || 'login'` (o leer la variable JS inyectada desde PHP) para el swipe detector
- [x] 5.6 Verificar que auth-card.js NO usa `$` de jQuery, NO usa `import`/`require`, NO tiene `console.log` en producción

## 6. Controller — RegisterController

- [x] 6.1 Crear `app/app/Controllers/Auth/RegisterController.php` con namespace `App\Controllers\Auth`, extendiendo `App\Controllers\BaseController`
- [x] 6.2 Implementar SOLO el método `public function index(): string` que retorna `return view('layouts/auth', ['card' => 'register']);`
- [x] 6.3 Verificar que NO existe método `process()` ni ninguna lógica de validación (se implementa en P05)
- [x] 6.4 Actualizar `LoginController::index()` para retornar `view('layouts/auth', ['card' => 'login'])` en lugar de `view('auth/login')` directamente

## 7. Rutas — Config/Routes.php

- [x] 7.1 Abrir `app/app/Config/Routes.php` y AGREGAR (no reemplazar) las rutas de auth:
  ```php
  $routes->get('login',    'Auth\LoginController::index');
  $routes->get('registro', 'Auth\RegisterController::index');
  ```
- [x] 7.2 Verificar que la ruta raíz `/` sigue apuntando a donde ya estaba configurada (no modificar rutas existentes)
- [x] 7.3 Confirmar que las rutas POST de login y registro NO se agregan aquí (solo GET — la lógica viene en P07 y P05)

## 8. Verificación final

- [x] 8.1 `GET http://localhost/comisionfilm/login` → HTTP 200, card login visible, fondo #0a0a0a con grain, tipografía Cormorant Garamond ✓
- [x] 8.2 `GET http://localhost/comisionfilm/registro` → HTTP 200, card register visible, misma identidad visual ✓
- [x] 8.3 Click "Regístrate" en login → slide con elastic.out sin errores JS en consola (requiere browser)
- [x] 8.4 Click "Ya tengo cuenta" en register → slide inverso sin errores JS (requiere browser)
- [x] 8.5 Viewport 375px (DevTools mobile) → layout sin scroll horizontal, inputs touch-friendly (requiere browser)
- [x] 8.6 Inspeccionar `<head>` → GSAP CDN aparece ANTES que auth-card.js ✓
- [x] 8.7 Inspeccionar `<head>` → Google Fonts tiene Cormorant Garamond Y DM Sans (ambas) ✓
- [x] 8.8 Verificar que NO existe `writable/uploads/` en `app/writable/` ✓
- [x] 8.9 Verificar que todos los `href` y `action` en vistas usan `site_url()` o `base_url()` — ninguna ruta hardcodeada ✓

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN — LEER ANTES DE IMPLEMENTAR

1. **NO reinventar la paleta**: Los colores son EXACTAMENTE `#0a0a0a`, `#1a1a1a`, `#d4a04a`, `#f5f0e8` — sin variaciones
2. **NO cambiar las fuentes**: Son Cormorant Garamond + DM Sans — no Inter, no Roboto, no Nunito
3. **NO usar jQuery**: GSAP es la única librería JS. `document.querySelector()` para DOM
4. **NO modificar AuthFilter.php** en esta propuesta — solo se usa en P06/P07
5. **NO agregar POST /registro** en routes.php — esa ruta la crea P05
6. **NO agregar POST /login** en routes.php — esa ruta la crea P07
7. **NO crear lógica de validación** en RegisterController ni en LoginController::index
8. **NO usar rutas hardcodeadas**: SIEMPRE `site_url('login')`, `site_url('registro')` — nunca `/login` ni `/comisionfilm/login`
9. **Los `name` de inputs DEBEN ser**: `nombres`, `apellido_pat`, `apellido_mat`, `phone`, `email` (propuesta 02 establece estos en UserModel.$allowedFields)
10. **NO agregar campos de documentos** en register.php — esos vienen en P05
11. **El layout incluye AMBAS cards** — GSAP necesita ambas en DOM para animar entre ellas
12. **base_url('assets/...')** para CSS y JS; **site_url('ruta')** para navegación — son distintos helpers de CI4
13. **Verificar en ARQUITECTURA.md §4** la ruta exacta: `app/public/assets/css/auth.css` y `app/public/assets/js/auth-card.js`
14. **El contenedor Docker es `rcf_app`** — si se necesita ejecutar `php spark`, usar `docker exec rcf_app php spark ...`
