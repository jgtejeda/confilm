## Context

ARQUITECTURA.md §13: GSAP elastic.out(1,0.5) en entrada desde bottom-right, back.in(2) en salida. SVG morph entre check/X/! icons. Auto-dismiss 4s con CSS @keyframes en barra de progreso. Stack máx 5.

## Decisions

### D1 — API global (no ES6 module)
```javascript
window.Notify = {
    success(title, body='') { return this._show('success', title, body); },
    error(title, body='')   { return this._show('error',   title, body); },
    warning(title, body='') { return this._show('warning', title, body); },
    info(title, body='')    { return this._show('info',    title, body); },
    promise(promise, {loading, success, error}) { /* ... */ }
};
```

### D2 — Animaciones GSAP
Entrada: `gsap.from(toast, {y:100,opacity:0,ease:'elastic.out(1,0.5)',duration:0.6})`
Salida: `gsap.to(toast, {y:-20,opacity:0,ease:'back.in(2)',duration:0.3,onComplete:()=>toast.remove()})`

### D3 — Stack: máximo 5
Si ya hay 5 toasts: remover el más antiguo antes de agregar uno nuevo.

### D4 — Colores via CSS Custom Properties
`--color-success:#2ecc71`, `--color-error:#e74c3c`, `--color-warning:#f39c12`, `--color-info:#3498db`

### D5 — Promise support
```javascript
promise(p, opts) {
    const toast = this._show('info', opts.loading);
    p.then(() => { /* cambiar a success */ }).catch(() => { /* cambiar a error */ });
    return p;
}
```
