## 1. Verificación previa

- [x] 1.1 Verificar GSAP se carga en layouts/user.php y layouts/admin.php ANTES de notifications.js
- [x] 1.2 Confirmar que no existe ya un sistema de toasts en el proyecto

## 2. notifications.js

- [x] 2.1 Crear `public/assets/js/notifications.js` — script global, sin import/export
- [x] 2.2 Contenedor de toasts: `<div id="notify-container">` posición fixed bottom-right, z-index alto, creado en DOMContentLoaded si no existe
- [x] 2.3 `_show(type, title, body)`: crear elemento toast con clase `notify-toast notify-${type}`, estructura: ícono SVG, title, body, progress bar, botón X
- [x] 2.4 GSAP entrada: `gsap.from(toast, {y:100, opacity:0, ease:'elastic.out(1,0.5)', duration:0.6})`
- [x] 2.5 Auto-dismiss: `setTimeout(() => this._dismiss(toast), 4000)` — CSS progress bar @keyframes 4s
- [x] 2.6 `_dismiss(toast)`: `gsap.to(toast, {y:-20, opacity:0, ease:'back.in(2)', duration:0.3, onComplete:()=>toast.remove()})`
- [x] 2.7 Stack: antes de agregar, si `container.children.length >= 5` → `this._dismiss(container.firstChild)` (el más antiguo)
- [x] 2.8 Métodos públicos: `success`, `error`, `warning`, `info` — todos llaman `_show(type, title, body)`
- [x] 2.9 `promise(p, opts)`: mostrar toast loading (sin auto-dismiss), al resolver cambiar ícono y contenido, luego auto-dismiss

## 3. Estilos (inline en JS o CSS separado)

- [x] 3.1 Estilos del toast via CSS Custom Properties: `--color-success:#2ecc71`, `--color-error:#e74c3c`, `--color-warning:#f39c12`, `--color-info:#3498db`
- [x] 3.2 Progress bar: `@keyframes progress-shrink { from{width:100%} to{width:0} }` con animation 4s linear

## 4. Integración en layouts

- [x] 4.1 Cargar en `layouts/user.php` después de GSAP
- [x] 4.2 Cargar en `layouts/admin.php` después de GSAP

## 5. Verificación final

- [x] 5.1 `Notify.success('Test','Mensaje')` → toast verde aparece desde abajo
- [x] 5.2 Toast se auto-cierra en 4s con barra de progreso
- [x] 5.3 6 toasts en secuencia → máx 5 visibles
- [x] 5.4 `Notify.promise(Promise.resolve(), {loading:'Cargando',success:'OK'})` → transiciona correctamente
- [x] 5.5 `window.Notify` disponible en consola del browser

---

## ⚠️ INSTRUCCIÓN ANTI-ALUCINACIÓN

1. `window.Notify` es global — sin ES6 `export default`
2. GSAP debe estar cargado ANTES en el layout — no importar dentro de notifications.js
3. Colores via CSS Custom Properties — no hardcodear hex en JS
4. Auto-dismiss: 4000ms (4 segundos)
5. GSAP entrada: `elastic.out(1,0.5)` — GSAP salida: `back.in(2)` — no mezclar
6. NO depende de jQuery, Bootstrap ni ningún otro framework
