## 1. Verificación previa

- [x] 1.1 Verificar AdminFilter registrado como 'admin' en Filters.php (P07)
- [x] 1.2 Verificar que existe `views/admin/dashboard.php` parcial o vacío — no sobreescribir sin revisar
- [x] 1.3 Confirmar estructura de tablas: users.role, documents.status, inscriptions.status (P02)

## 2. admin.css

- [x] 2.1 Crear `public/assets/css/admin.css` — extender variables de auth.css o redefinir: `--sidebar-width:240px`, `--color-bg:#0f0f0f`, `--color-surface:#1a1a1a`, `--color-accent:#d4a04a`
- [x] 2.2 Sidebar: `position:fixed; width:var(--sidebar-width); height:100vh; background:var(--color-surface); overflow-y:auto`
- [x] 2.3 Main content: `margin-left:var(--sidebar-width); padding:24px`
- [x] 2.4 Stat cards: grid 2x2, cada card con número grande, label, color de acento
- [x] 2.5 Shimmer loading placeholder para tablas (CSS @keyframes)

## 3. Layout admin.php

- [x] 3.1 Crear `views/layouts/admin.php` — HTML5, `<head>` con admin.css y GSAP CDN
- [x] 3.2 Sidebar: links con `site_url('admin')`, `site_url('admin/usuarios')`, `site_url('admin/tipos-documento')`, `site_url('admin/periodos')`, `site_url('admin/notificaciones')`
- [x] 3.3 Header: `<span><?= session('nombres') ?></span>` y `<a href="<?= site_url('logout') ?>">Cerrar sesión</a>`
- [x] 3.4 Antes de `</body>`: GSAP CDN, luego `<script src="<?= base_url('assets/js/admin.js') ?>"></script>`

## 4. DashboardController

- [x] 4.1 Crear `Controllers/Admin/DashboardController.php` namespace `App\Controllers\Admin`
- [x] 4.2 `index()`: ejecutar 4 queries COUNT() con `$db->table(...)->where(...)->countAllResults()`
- [x] 4.3 Retornar `view('layouts/admin', ['content' => view('admin/dashboard', ['stats' => $stats])])`

## 5. Vista dashboard.php y admin.js

- [x] 5.1 Crear/actualizar `views/admin/dashboard.php` — 4 cards con `<span class="stat-value" data-value="<?= $stats['x'] ?>">0</span>`
- [x] 5.2 Crear `public/assets/js/admin.js` — GSAP counter en DOMContentLoaded sobre `.stat-value`

## 6. Verificación final

- [x] 6.1 GET /admin (logueado como admin) → dashboard con 4 stats, sidebar visible
- [x] 6.2 Animación counter: números van de 0 al valor real en ~1.5s
- [x] 6.3 Links del sidebar navegan a sus respectivas rutas
- [x] 6.4 GET /admin sin sesión o con role=user → AdminFilter redirige

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `countAllResults()` — NO `get()->getNumRows()` ni cargar todos los registros
2. `session('nombres')` en el header — NO `session('name')` ni `session('username')`
3. Todas las URLs del sidebar usan `site_url('admin/...')` — nunca `/admin/...`
4. GSAP se carga ANTES de admin.js — mismo orden que en auth.php
5. NO incluir lógica de usuarios, documentos ni periodos aquí — eso es P11-P16
