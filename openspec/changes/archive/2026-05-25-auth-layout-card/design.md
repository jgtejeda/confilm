## Context

El proyecto corre en Docker con Apache 2.4 (`rcf_app`, `rcf_mysql`, `rcf_maildev`, red `red_interna`) y el sistema vive en el subfolder `/comisionfilm/`. CI4 está instalado en `app/` con PHP 8.2. Ya existen:
- `app/app/Controllers/Auth/LoginController.php` con `index()` y `login()` (no sobreescribir, solo refinar la vista)
- `app/app/Views/auth/login.php` (card incompleta, necesita adaptarse al nuevo layout)
- Todos los modelos (UserModel, DocumentModel, PeriodModel, etc.) y migraciones completadas
- `app/app/Filters/AuthFilter.php` — registrado como 'auth' en Filters.php

El layout de autenticación es la capa de presentación pura: no toca DB, no toca S3, no toca correo.

## Goals / Non-Goals

**Goals:**
- Layout `auth.php` con estructura HTML5 completa, imports de fuentes y librerías JS
- Tarjeta animada que alterne entre login y registro via GSAP con física de resorte
- CSS con Custom Properties coherentes con la identidad visual (ARQUITECTURA.md §16)
- Swipe gesture en mobile (delta X > 50px)
- RegisterController stub que solo retorna la vista (lógica completa en P05)
- Rutas GET `/login` y GET `/registro` añadidas a Routes.php existente
- Funcional en viewport 375px

**Non-Goals:**
- Lógica de validación y POST del formulario de login (P07)
- Lógica de validación y POST del formulario de registro (P05)
- Slots dinámicos de documentos en el formulario de registro (P05)
- Filtro `noauth` (se implementa en P07)
- Recuperación de contraseña (P08)
- Animaciones de toast (P18)

## Decisions

### D1 — Layout único `auth.php` con variable `$card`
**Decisión**: Un solo layout `views/layouts/auth.php` que recibe `$card = 'login'|'register'` e incluye ambas cards en el DOM, ocultando la inactiva vía CSS (position absolute, opacity 0).

**Por qué**: Permite que GSAP anime entre cards sin navegación de página (SPA-feel). La alternativa de dos páginas independientes requeriría recargar la página y perder el efecto de slide.

**Alternativa descartada**: Dos layouts separados `auth-login.php` y `auth-register.php` — descartada porque GSAP necesita ambos elementos en el DOM simultáneamente para animar.

### D2 — GSAP desde CDN cdnjs
**Decisión**: Cargar GSAP 3 desde `https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js` en el `<head>` de `auth.php`, ANTES de `auth-card.js`.

**Por qué**: ARQUITECTURA.md §2 lista `public/assets/vendor/gsap.min.js` como opción local, pero el CDN simplifica el setup inicial y ya está disponible para todos los entornos (dev Docker + prod). Si hay restricciones de red se puede copiar al vendor/ sin cambiar la lógica.

**Estructura de carga**:
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="<?= base_url('assets/js/auth-card.js') ?>"></script>
```

### D3 — CSS con Custom Properties + pseudo-element para grain
**Decisión**: `auth.css` define todas las variables en `:root` y el efecto de grain/noise en fondo via `body::before` con SVG data URI embebido (filtro `feTurbulence`), sin imagen externa.

**Por qué**: Sin dependencia de imagen externa, funciona offline en Docker. El SVG inline es liviano (~300 bytes) y reproducible.

### D4 — Estructura de cards en el DOM
```html
<div class="auth-wrapper">
  <div class="card card--login">   <!-- siempre en DOM --></div>
  <div class="card card--register"> <!-- siempre en DOM --></div>
</div>
```
La card inactiva tiene `position: absolute; opacity: 0; pointer-events: none;` via CSS. GSAP anima x y opacity. La card activa es `position: relative`.

### D5 — Namespace y estructura de controladores
- `App\Controllers\Auth\LoginController` — YA EXISTE, solo refinar la vista que retorna
- `App\Controllers\Auth\RegisterController` — NUEVO stub:
  ```php
  namespace App\Controllers\Auth;
  use App\Controllers\BaseController;
  class RegisterController extends BaseController {
      public function index(): string {
          return view('layouts/auth', ['card' => 'register']);
      }
  }
  ```

### D6 — Rutas en Routes.php
Agregar dentro del grupo sin filtro (el filtro `noauth` se añade en P07):
```php
$routes->get('login',    'Auth\LoginController::index');
$routes->get('registro', 'Auth\RegisterController::index');
```
LoginController::index ya retorna `view('auth/login')` — se actualiza para retornar `view('layouts/auth', ['card' => 'login'])`.

### D7 — URLs con site_url() / base_url()
- Rutas de navegación: `site_url('login')`, `site_url('registro')` — NUNCA strings hardcodeados
- Assets (CSS, JS): `base_url('assets/css/auth.css')`, `base_url('assets/js/auth-card.js')`
- Resultado en dev: `http://localhost/comisionfilm/login`

## Risks / Trade-offs

- **[Riesgo] GSAP CDN no disponible en red restringida** → Mitigación: copiar `gsap.min.js` a `public/assets/vendor/` y actualizar el `src` en el layout. La lógica JS no cambia.
- **[Riesgo] Google Fonts bloqueadas en producción** → Mitigación: añadir `font-display: swap` + definir fuentes del sistema como fallback en auth.css (`serif` para Cormorant, `sans-serif` para DM Sans).
- **[Trade-off] Ambas cards en DOM simultáneo** → Incrementa levemente el tamaño del HTML, pero es < 2KB adicional. Beneficio: animación GSAP fluida sin recargas.
- **[Riesgo] LoginController::index() ya retorna `view('auth/login')` directamente** → Al actualizar a `view('layouts/auth', ['card' => 'login'])` se cambia la respuesta. Hay que asegurarse de que `auth/login.php` no tenga su propio DOCTYPE (debe ser solo el contenido de la card, no un HTML completo).

## Open Questions

- ¿El vhost.conf de Docker ya tiene el Alias `/comisionfilm` configurado? (Respuesta: Sí, propuesta 01 lo implementó — verificar antes de implementar).
- ¿El archivo `auth/login.php` actual tiene DOCTYPE propio o solo el contenido de la card? → Verificar antes de modificar para evitar HTML anidado.
