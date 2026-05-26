## 1. Verificación previa

- [x] 1.1 Verificar rutas GET /dashboard y /dashboard/* con filter 'auth' en Routes.php (P07)
- [x] 1.2 Verificar que session('user_id') está disponible y tiene los 6 campos (P07)
- [x] 1.3 Verificar que notifications count endpoint existe o crear en P21

## 2. user.css

- [x] 2.1 Crear `public/assets/css/user.css` — navbar horizontal, variables CSS de la paleta
- [x] 2.2 Chips de status: pending (ámbar), approved (verde), rejected (rojo), no-cargado (gris)
- [x] 2.3 Timeline: pasos verticales con línea conectora, paso activo destacado

## 3. layouts/user.php

- [x] 3.1 Crear `views/layouts/user.php` — navbar con: nombre del usuario (`session('nombres')`), campana con badge `<span id="notif-badge">`, link a notificaciones, link a documentos, logout
- [x] 3.2 Cargar: user.css, GSAP CDN, PDF.js CDN, notifications.js, document-viewer.js — en ese orden
- [x] 3.3 Script JS: `var baseUrl = '<?= site_url() ?>';` y `setInterval(...)` para polling 30s

## 4. DashboardController

- [x] 4.1 Crear `Controllers/User/DashboardController.php` namespace `App\Controllers\User`
- [x] 4.2 `index()`: cargar usuario de DB (no solo de sesión), inscripción más reciente, periodo del usuario
- [x] 4.3 Si no hay inscripción: pasar `$noInscription=true` a vista
- [x] 4.4 Si hay periodo: query JOIN para doc_types iniciales + estado de cada doc del usuario
- [x] 4.5 Retornar `view('layouts/user', ['content' => view('user/dashboard', $data)])`

## 5. Vista user/dashboard.php

- [x] 5.1 Sección "Mis Datos": nombre completo, username, email, phone, fecha de registro
- [x] 5.2 Sección "Documentos Iniciales": foreach $initialDocTypes → chip con nombre + badge de status
- [x] 5.3 Timeline 5 pasos: Registro completado, Correo verificado, Docs enviados, En revisión, Aprobado — paso activo según inscription.status
- [x] 5.4 GSAP stagger en DOMContentLoaded: `gsap.from('.timeline-step', {opacity:0,y:30,stagger:0.15,ease:'power2.out'})`

## 6. Verificación final

- [x] 6.1 GET /dashboard → docs iniciales del periodo del usuario, no hardcodeados
- [x] 6.2 Timeline refleja inscription.status correctamente
- [x] 6.3 Badge de campana se actualiza al llegar notificaciones (polling 30s)
- [x] 6.4 `baseUrl` en JS tiene /comisionfilm/ — verificar en source del layout

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. Los docs iniciales vienen de `period_document_types JOIN document_types` del periodo del usuario — NO hardcodeados
2. `session('user_id')` para obtener el usuario logueado — NO `$_SESSION['user_id']`
3. La URL del polling: `var baseUrl = '<?= site_url() ?>'` inyectada desde PHP — NUNCA hardcodeada
4. `setInterval(..., 30000)` — 30000ms = 30 segundos
5. Si el usuario no tiene inscripción: mostrar mensaje apropiado, no error 500
6. `session('nombres')` en el navbar — este campo está en sesión (P07)
7. Los docs iniciales con status NULL (no cargados) deben mostrarse como "No cargado" — no ocultar el chip
