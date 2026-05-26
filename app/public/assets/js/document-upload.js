/**
 * document-upload.js — Drag & drop upload para documentos complementarios
 * 
 * Funcionalidad:
 * - Drag & drop zones por cada slot de documento
 * - Validación client-side de extensión
 * - Upload via fetch con FormData y CSRF
 * - Actualización UI tras respuesta exitosa
 * - Notify.promise() para feedback visual
 */
(function () {
    'use strict';

    var DocumentUpload = {
        baseUrl: null,
        csrfToken: null,
        csrfHeader: 'X-CSRF-TOKEN',

        /**
         * Inicializar con la URL base y CSRF token
         */
        init: function (baseUrl, csrfToken) {
            this.baseUrl = baseUrl;
            this.csrfToken = csrfToken;
            this.bindDropZones();
            this.bindFileInputs();
            this.bindSubmitButton();
        },

        /**
         * Bind drag & drop events a todas las zonas
         */
        bindDropZones: function () {
            var self = this;
            var zones = document.querySelectorAll('.doc-drop-zone');

            zones.forEach(function (zone) {
                var docTypeId = zone.getAttribute('data-doc-type-id');
                var allowedExts = zone.getAttribute('data-allowed-exts') || '';

                zone.addEventListener('dragover', function (e) {
                    e.preventDefault();
                    zone.classList.add('drag-over');
                });

                zone.addEventListener('dragleave', function (e) {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                });

                zone.addEventListener('drop', function (e) {
                    e.preventDefault();
                    zone.classList.remove('drag-over');
                    var file = e.dataTransfer.files[0];
                    if (file) {
                        self.uploadFile(file, parseInt(docTypeId), allowedExts, zone);
                    }
                });
            });
        },

        /**
         * Bind file input change events
         */
        bindFileInputs: function () {
            var self = this;
            var inputs = document.querySelectorAll('.doc-file-input');

            inputs.forEach(function (input) {
                input.addEventListener('change', function () {
                    var file = input.files[0];
                    if (file) {
                        var docTypeId = parseInt(input.getAttribute('data-doc-type-id'));
                        var allowedExts = input.getAttribute('data-allowed-exts') || '';
                        var zone = input.closest('.doc-slot').querySelector('.doc-drop-zone');
                        self.uploadFile(file, docTypeId, allowedExts, zone);
                    }
                });
            });
        },

        /**
         * Bind submit button
         */
        bindSubmitButton: function () {
            var self = this;
            var submitBtn = document.getElementById('submit-docs-btn');

            if (submitBtn) {
                submitBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    self.submitDocuments();
                });
            }
        },

        /**
         * Validar extensión client-side y subir archivo
         */
        uploadFile: function (file, docTypeId, allowedExts, zone) {
            var self = this;

            // Validación client-side de extensión
            if (allowedExts) {
                var exts = allowedExts.split(',').map(function (e) { return e.trim().toLowerCase(); });
                var fileExt = file.name.split('.').pop().toLowerCase();
                if (exts.length > 0 && exts.indexOf(fileExt) === -1) {
                    Notify.error('Tipo no permitido', 'Extensiones aceptadas: ' + allowedExts);
                    return;
                }
            }

            // Preparar FormData
            var formData = new FormData();
            formData.append('file', file);
            formData.append('doc_type_id', docTypeId);

            // CSRF token
            if (this.csrfToken) {
                formData.append('csrf_test_name', this.csrfToken);
            }

            // Fetch con Notify.promise
            var fetchPromise = fetch(this.baseUrl + 'dashboard/documentos/subir', {
                method: 'POST',
                body: formData,
            });

            Notify.promise(fetchPromise, {
                loading: 'Subiendo...',
                success: 'Documento cargado',
                error: 'Error al subir'
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.error) {
                    throw new Error(data.error);
                }

                // Actualizar UI del slot tras respuesta exitosa
                if (zone) {
                    self.updateSlotUI(zone, data);
                }

                // Actualizar botón submit
                self.updateSubmitButton();
            }).catch(function (err) {
                console.error('Upload error:', err);
            });
        },

        /**
         * Actualizar UI del slot tras upload exitoso
         */
        updateSlotUI: function (zone, data) {
            var slot = zone.closest('.doc-slot');
            if (!slot) return;

            // Actualizar badge de status a "Pendiente"
            var badge = slot.querySelector('.doc-status-badge');
            if (badge) {
                badge.textContent = 'Pendiente';
                badge.className = 'doc-status-badge badge-pending';
            }

            // Habilitar botón "Ver"
            var viewBtn = slot.querySelector('.doc-view-btn');
            if (viewBtn) {
                viewBtn.disabled = false;
                viewBtn.style.opacity = '1';
                viewBtn.style.cursor = 'pointer';
                viewBtn.setAttribute('data-doc-id', data.doc_id);
            }

            // Mostrar indicador visual de que hay documento
            zone.classList.add('has-document');
            var placeholder = zone.querySelector('.drop-placeholder');
            if (placeholder) {
                placeholder.textContent = 'Archivo cargado — arrastra otro para reemplazar';
            }
        },

        /**
         * Actualizar estado del botón submit
         */
        updateSubmitButton: function () {
            var submitBtn = document.getElementById('submit-docs-btn');
            if (!submitBtn) return;

            var allSlots = document.querySelectorAll('.doc-slot');
            var slotsWithDoc = document.querySelectorAll('.doc-slot .doc-drop-zone.has-document');

            if (slotsWithDoc.length === allSlots.length && allSlots.length > 0) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-disabled');
            }
        },

        /**
         * Submit documentos para revisión
         */
        submitDocuments: function () {
            var self = this;
            var submitBtn = document.getElementById('submit-docs-btn');

            if (!submitBtn) return;

            // Preparar FormData para submit
            var formData = new FormData();
            if (this.csrfToken) {
                formData.append('csrf_test_name', this.csrfToken);
            }

            var fetchPromise = fetch(this.baseUrl + 'dashboard/documentos/enviar', {
                method: 'POST',
                body: formData,
            });

            Notify.promise(fetchPromise, {
                loading: 'Enviando documentos...',
                success: 'Documentos enviados para revisión',
                error: 'Error al enviar'
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                if (data.error) {
                    throw new Error(data.error);
                }

                // Deshabilitar botón y zona tras submit exitoso
                submitBtn.disabled = true;
                submitBtn.textContent = 'Enviado para revisión';
                submitBtn.classList.add('btn-disabled');

                // Redirigir al dashboard después de un momento
                setTimeout(function () {
                    window.location.href = self.baseUrl + 'dashboard';
                }, 2000);
            }).catch(function (err) {
                console.error('Submit error:', err);
            });
        },
    };

    // Exponer globalmente
    window.DocumentUpload = DocumentUpload;

    // Auto-inicializar si hay elementos en el DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.baseUrl !== 'undefined' && document.querySelector('.doc-drop-zone')) {
                DocumentUpload.init(window.baseUrl, window.csrfToken || null);
            }
        });
    } else {
        if (typeof window.baseUrl !== 'undefined' && document.querySelector('.doc-drop-zone')) {
            DocumentUpload.init(window.baseUrl, window.csrfToken || null);
        }
    }
})();
