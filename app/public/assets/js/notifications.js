/**
 * notifications.js — Sistema de toasts Sileo-style con GSAP
 * Global: window.Notify (sin import/export ES6)
 * Depende de GSAP cargado previamente en el layout
 */
(function () {
    'use strict';

    // CSS Custom Properties para colores
    var COLORS = {
        success: 'var(--color-success, #2ecc71)',
        error: 'var(--color-error, #e74c3c)',
        warning: 'var(--color-warning, #f39c12)',
        info: 'var(--color-info, #3498db)'
    };

    // SVG icons para cada tipo
    var ICONS = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>',
        error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
        loading: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg>'
    };

    var Notify = {
        _container: null,

        /**
         * Inicializa el contenedor de toasts
         */
        _init: function () {
            if (this._container) return;

            this._container = document.getElementById('notify-container');
            if (!this._container) {
                this._container = document.createElement('div');
                this._container.id = 'notify-container';
                document.body.appendChild(this._container);
            }

            this._injectStyles();
        },

        /**
         * Inyecta los estilos CSS en el document
         */
        _injectStyles: function () {
            if (document.getElementById('notify-styles')) return;

            var style = document.createElement('style');
            style.id = 'notify-styles';
            style.textContent = [
                ':root {',
                '  --color-success: #2ecc71;',
                '  --color-error: #e74c3c;',
                '  --color-warning: #f39c12;',
                '  --color-info: #3498db;',
                '}',
                '#notify-container {',
                '  position: fixed;',
                '  bottom: 1.5rem;',
                '  right: 1.5rem;',
                '  z-index: 9999;',
                '  display: flex;',
                '  flex-direction: column;',
                '  gap: 0.75rem;',
                '  pointer-events: none;',
                '}',
                '.notify-toast {',
                '  pointer-events: auto;',
                '  background: #1a1a1a;',
                '  border: 1px solid rgba(255,255,255,0.1);',
                '  border-radius: 8px;',
                '  padding: 1rem;',
                '  min-width: 300px;',
                '  max-width: 400px;',
                '  box-shadow: 0 8px 32px rgba(0,0,0,0.4);',
                '  display: flex;',
                '  align-items: flex-start;',
                '  gap: 0.75rem;',
                '  position: relative;',
                '  overflow: hidden;',
                '}',
                '.notify-toast .notify-icon {',
                '  flex-shrink: 0;',
                '  width: 24px;',
                '  height: 24px;',
                '  display: flex;',
                '  align-items: center;',
                '  justify-content: center;',
                '}',
                '.notify-toast .notify-icon svg {',
                '  width: 100%;',
                '  height: 100%;',
                '}',
                '.notify-toast .notify-content {',
                '  flex: 1;',
                '  min-width: 0;',
                '}',
                '.notify-toast .notify-title {',
                '  font-weight: 600;',
                '  font-size: 0.9rem;',
                '  margin-bottom: 0.25rem;',
                '}',
                '.notify-toast .notify-body {',
                '  font-size: 0.85rem;',
                '  color: rgba(245,240,232,0.7);',
                '  line-height: 1.4;',
                '}',
                '.notify-toast .notify-close {',
                '  flex-shrink: 0;',
                '  width: 20px;',
                '  height: 20px;',
                '  background: none;',
                '  border: none;',
                '  color: rgba(245,240,232,0.5);',
                '  cursor: pointer;',
                '  padding: 0;',
                '  display: flex;',
                '  align-items: center;',
                '  justify-content: center;',
                '}',
                '.notify-toast .notify-close:hover {',
                '  color: #f5f0e8;',
                '}',
                '.notify-toast .notify-progress {',
                '  position: absolute;',
                '  bottom: 0;',
                '  left: 0;',
                '  height: 3px;',
                '  background: currentColor;',
                '  animation: progress-shrink 4s linear forwards;',
                '}',
                '@keyframes progress-shrink {',
                '  from { width: 100%; }',
                '  to { width: 0%; }',
                '}',
                '.notify-toast.notify-success {',
                '  border-left: 3px solid var(--color-success);',
                '  color: var(--color-success);',
                '}',
                '.notify-toast.notify-error {',
                '  border-left: 3px solid var(--color-error);',
                '  color: var(--color-error);',
                '}',
                '.notify-toast.notify-warning {',
                '  border-left: 3px solid var(--color-warning);',
                '  color: var(--color-warning);',
                '}',
                '.notify-toast.notify-info {',
                '  border-left: 3px solid var(--color-info);',
                '  color: var(--color-info);',
                '}'
            ].join('\n');

            document.head.appendChild(style);
        },

        /**
         * Muestra un toast
         */
        _show: function (type, title, body) {
            this._init();

            // Stack: máximo 5 toasts
            if (this._container.children.length >= 5) {
                this._dismiss(this._container.firstChild);
            }

            var toast = document.createElement('div');
            toast.className = 'notify-toast notify-' + type;

            toast.innerHTML =
                '<div class="notify-icon">' + ICONS[type] + '</div>' +
                '<div class="notify-content">' +
                (title ? '<div class="notify-title">' + title + '</div>' : '') +
                (body ? '<div class="notify-body">' + body + '</div>' : '') +
                '</div>' +
                '<button class="notify-close" aria-label="Cerrar">' + ICONS.error + '</button>' +
                '<div class="notify-progress"></div>';

            // Cerrar con botón X
            var closeBtn = toast.querySelector('.notify-close');
            var self = this;
            closeBtn.addEventListener('click', function () {
                self._dismiss(toast);
            });

            this._container.appendChild(toast);

            // GSAP entrada: elastic.out(1,0.5)
            gsap.from(toast, {
                y: 100,
                opacity: 0,
                ease: 'elastic.out(1, 0.5)',
                duration: 0.6
            });

            // Auto-dismiss: 4 segundos
            if (type !== 'loading') {
                setTimeout(function () {
                    self._dismiss(toast);
                }, 4000);
            }

            return toast;
        },

        /**
         * Dismiss de un toast con GSAP
         */
        _dismiss: function (toast) {
            if (!toast || !toast.parentNode) return;

            var self = this;
            gsap.to(toast, {
                y: -20,
                opacity: 0,
                ease: 'back.in(2)',
                duration: 0.3,
                onComplete: function () {
                    toast.remove();
                }
            });
        },

        /**
         * Métodos públicos
         */
        success: function (title, body) {
            return this._show('success', title, body || '');
        },

        error: function (title, body) {
            return this._show('error', title, body || '');
        },

        warning: function (title, body) {
            return this._show('warning', title, body || '');
        },

        info: function (title, body) {
            return this._show('info', title, body || '');
        },

        /**
         * Promise support
         */
        promise: function (p, opts) {
            var self = this;
            var toast = this._show('loading', opts.loading || 'Cargando...');

            // Remover progress bar para loading
            var progress = toast.querySelector('.notify-progress');
            if (progress) progress.remove();

            p.then(function (result) {
                self._updateToast(toast, 'success', opts.success || 'Listo');
                setTimeout(function () {
                    self._dismiss(toast);
                }, 4000);
                return result;
            }).catch(function (err) {
                self._updateToast(toast, 'error', opts.error || 'Error');
                setTimeout(function () {
                    self._dismiss(toast);
                }, 4000);
                throw err;
            });

            return p;
        },

        /**
         * Actualiza un toast existente (para promise)
         */
        _updateToast: function (toast, type, message) {
            // Remover clases anteriores
            toast.className = 'notify-toast notify-' + type;

            // Actualizar ícono
            var iconEl = toast.querySelector('.notify-icon');
            if (iconEl) iconEl.innerHTML = ICONS[type];

            // Actualizar contenido
            var contentEl = toast.querySelector('.notify-content');
            if (contentEl) {
                contentEl.innerHTML =
                    '<div class="notify-title">' + message + '</div>';
            }

            // Agregar progress bar
            if (!toast.querySelector('.notify-progress')) {
                var progress = document.createElement('div');
                progress.className = 'notify-progress';
                toast.appendChild(progress);
            }
        }
    };

    // Inicializar en DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            Notify._init();
        });
    } else {
        Notify._init();
    }

    // Exponer globalmente
    window.Notify = Notify;
})();
