# auth-animated-card Specification

## Purpose
TBD - created by archiving change auth-layout-card. Update Purpose after archive.
## Requirements
### Requirement: Layout de autenticación base
El sistema SHALL servir un layout HTML5 completo (`views/layouts/auth.php`) para todas las pantallas de autenticación. Este layout DEBE cargar: Google Fonts (Cormorant Garamond + DM Sans), GSAP 3 desde CDN cdnjs, `auth.css` vía `base_url()`, y `auth-card.js` vía `base_url()`. El layout DEBE recibir la variable `$card` ('login' | 'register') y renderizar ambas cards en el DOM simultáneamente (la inactiva oculta con CSS).

#### Scenario: Login accesible en /login
- **WHEN** el usuario accede a `http://localhost/comisionfilm/login`
- **THEN** el servidor retorna HTTP 200 con el layout auth.php, la card--login visible y la card--register oculta (opacity: 0, pointer-events: none)

#### Scenario: Registro accesible en /registro
- **WHEN** el usuario accede a `http://localhost/comisionfilm/registro`
- **THEN** el servidor retorna HTTP 200 con el layout auth.php, la card--register visible y la card--login oculta

#### Scenario: Layout carga GSAP antes de auth-card.js
- **WHEN** se inspecciona el HTML renderizado de cualquier ruta de auth
- **THEN** el tag `<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/...">` aparece ANTES del tag `<script src=".../auth-card.js">`

#### Scenario: Layout incluye ambas fuentes de Google
- **WHEN** se inspecciona el `<head>` del layout
- **THEN** existe un `<link>` a Google Fonts con `family=Cormorant+Garamond` y `family=DM+Sans`

---

### Requirement: Identidad visual cinematográfica
El sistema SHALL aplicar la paleta exacta definida en ARQUITECTURA.md §16 mediante CSS Custom Properties en `auth.css`. Las variables requeridas son:
- `--color-bg: #0a0a0a`
- `--color-surface: #1a1a1a`
- `--color-accent: #d4a04a`
- `--color-text: #f5f0e8`
El fondo SHALL tener efecto grain/noise via `body::before` con SVG data URI (feTurbulence), sin imagen externa. La card SHALL tener borde sutil, border-radius y box-shadow usando las variables CSS.

#### Scenario: Variables CSS presentes en :root
- **WHEN** se carga `auth.css` en el browser
- **THEN** `--color-bg`, `--color-surface`, `--color-accent` y `--color-text` están definidas en `:root` con exactamente los valores hex especificados

#### Scenario: Fondo oscuro con grain aplicado
- **WHEN** el usuario abre cualquier ruta de autenticación
- **THEN** el fondo es `#0a0a0a` con un overlay semi-transparente de ruido via `body::before`

#### Scenario: Tipografía correcta aplicada
- **WHEN** el usuario visualiza el título de la card (h1/h2 de display)
- **THEN** la fuente es Cormorant Garamond (font-family del display)

#### Scenario: Acento ámbar en elementos interactivos
- **WHEN** el usuario hace hover sobre el botón primario de la card
- **THEN** el botón aplica `--color-accent` (#d4a04a) como color de fondo o borde

---

### Requirement: Animación GSAP de tarjeta deslizante
El sistema SHALL implementar en `auth-card.js` dos funciones globales: `showRegister()` y `showLogin()`. Cada función SHALL ejecutar un GSAP timeline con los parámetros exactos de ARQUITECTURA.md §16:

**Login → Registro:**
- `gsap.to(loginCard, { x: '-110%', opacity: 0, ease: 'power2.in', duration: 0.45 })`
- `gsap.from(regCard, { x: '110%', opacity: 0, ease: 'elastic.out(1, 0.6)', duration: 0.6 })`

**Registro → Login:** inverso exacto.

Los links "Regístrate" y "Ya tengo cuenta" SHALL disparar estas funciones vía onclick o addEventListener, SIN navegar a otra página.

#### Scenario: Click "Regístrate" anima la card
- **WHEN** el usuario hace click en el link "Regístrate" dentro de la card--login
- **THEN** la card--login sale por la izquierda con ease power2.in (450ms) y la card--register entra desde la derecha con ease elastic.out (600ms), sin recarga de página

#### Scenario: Click "Ya tengo cuenta" anima la card
- **WHEN** el usuario hace click en el link "Ya tengo cuenta" dentro de la card--register
- **THEN** la card--register sale por la derecha y la card--login entra desde la izquierda, con tiempos y easings inversos exactos

#### Scenario: Sin errores JS en consola
- **WHEN** se ejecuta la animación de cualquier dirección
- **THEN** la consola del browser no muestra errores de JavaScript

---

### Requirement: Swipe gesture en mobile
El sistema SHALL detectar swipe horizontal en el wrapper de las cards. Si el usuario desliza con delta X > 50px hacia la izquierda (estando en login), SHALL llamar `showRegister()`. Si desliza hacia la derecha (estando en registro), SHALL llamar `showLogin()`.

#### Scenario: Swipe izquierda en login abre registro
- **WHEN** el usuario en mobile realiza touchstart y touchend con delta X > 50px hacia la izquierda sobre el auth-wrapper (estando en login)
- **THEN** se ejecuta `showRegister()` con la animación GSAP correspondiente

#### Scenario: Swipe derecha en registro vuelve a login
- **WHEN** el usuario en mobile realiza touchstart y touchend con delta X > -50px (positivo > 50) hacia la derecha sobre el auth-wrapper (estando en registro)
- **THEN** se ejecuta `showLogin()` con la animación GSAP correspondiente

#### Scenario: Swipe corto no dispara animación
- **WHEN** el usuario desliza con delta X <= 50px en cualquier dirección
- **THEN** no se ejecuta ninguna animación de card

---

### Requirement: Responsivo en mobile (375px)
El sistema SHALL renderizar correctamente en viewport de 375px de ancho mínimo. La card SHALL ocupar el ancho disponible con padding horizontal adecuado, los inputs SHALL ser touch-friendly (min-height 44px), y la animación GSAP SHALL funcionar sin overflow horizontal.

#### Scenario: Layout funcional en 375px
- **WHEN** el usuario accede con un viewport de 375px de ancho
- **THEN** la card de autenticación es completamente visible sin scroll horizontal, los inputs son accesibles y el texto no desborda el contenedor

