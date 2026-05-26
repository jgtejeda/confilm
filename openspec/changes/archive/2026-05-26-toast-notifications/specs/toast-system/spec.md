## ADDED Requirements

### Requirement: Sistema de toasts global window.Notify
`notifications.js` SHALL exponer `window.Notify` con métodos: success, error, warning, info (reciben title y body opcional). Cada toast SHALL aparecer desde bottom-right con GSAP elastic.out(1,0.5), auto-dismissarse en 4 segundos con barra de progreso CSS, y poder cerrarse con click. El stack SHALL soportar máximo 5 toasts simultáneos.

#### Scenario: Notify.success muestra toast verde
- **WHEN** se llama `Notify.success('Guardado', 'Los cambios se guardaron correctamente')`
- **THEN** aparece un toast con ícono de check, color verde, entra desde abajo con elastic.out y se auto-cierra en 4s

#### Scenario: Stack de 5 toasts simultáneos
- **WHEN** se llaman 6 Notify.success() en secuencia rápida
- **THEN** máximo 5 toasts son visibles simultáneamente; el 6to reemplaza al más antiguo

#### Scenario: Notify.promise actualiza el toast según resolución
- **WHEN** se llama `Notify.promise(fetchPromise, {loading:'Subiendo...', success:'Listo', error:'Error'})`
- **THEN** aparece toast "Subiendo..." en estado loading; al resolver, cambia a estado success/error

---

### Requirement: Sin dependencias externas
`notifications.js` SHALL ser vanilla JS (sin jQuery, sin import/export ES6). Solo depende de GSAP que debe estar cargado previamente en el layout.

#### Scenario: Notify disponible globalmente
- **WHEN** se accede a `window.Notify` en la consola del browser
- **THEN** es un objeto con los métodos success, error, warning, info, promise
