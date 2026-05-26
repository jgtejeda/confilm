/**
 * Document Viewer Modal
 *
 * Modal animado con GSAP para visualizar documentos desde S3.
 * - PDF: PDF.js renderizado en canvas con controles prev/next
 * - JPG/PNG: img tag con object-fit contain
 * - DOCX/XLSX/PPTX: metadata + botón descarga
 *
 * Uso básico (usuario):
 *   DocumentViewer.open(docId, endpoint)
 *
 * Uso admin (con botones de validación):
 *   DocumentViewer.open(docId, endpoint, {
 *     approveUrl: '/admin/usuarios/5/documento/9',
 *     rejectBtnId: 'reject-btn-9'   // ID del botón rechazar del DOM
 *   })
 */

const DocumentViewer = {};

(function () {
    let overlay   = null;
    let modal     = null;
    let pdfDoc    = null;
    let currentPage = 1;
    let totalPages  = 1;

    // ─── API pública ────────────────────────────────────────────────────────────

    /**
     * @param {number} docId
     * @param {string} endpoint  - URL base sin el ID  (ej. '/comisionfilm/admin/documentos/ver/')
     * @param {object} [actions] - { approveUrl, rejectBtnId }  (solo admin)
     */
    DocumentViewer.open = function (docId, endpoint, actions) {
        fetch(endpoint + docId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) { alert(data.error); return; }
            openModal(data, actions || null);
        })
        .catch(err => {
            console.error('Error fetching document:', err);
            alert('Error al cargar el documento');
        });
    };

    // ─── Modal ──────────────────────────────────────────────────────────────────

    function openModal(data, actions) {
        // Overlay
        overlay = document.createElement('div');
        overlay.style.cssText = [
            'position:fixed;top:0;left:0;width:100%;height:100%;',
            'background:rgba(0,0,0,0.85);z-index:9999;',
            'display:flex;align-items:center;justify-content:center;opacity:0;'
        ].join('');

        // Modal container
        modal = document.createElement('div');
        modal.style.cssText = [
            'background:#1a1a1a;border-radius:8px;',
            'max-width:90vw;max-height:90vh;width:820px;',
            'display:flex;flex-direction:column;',
            'box-shadow:0 20px 60px rgba(0,0,0,0.5);',
            'transform:scale(0.8);opacity:0;'
        ].join('');

        modal.appendChild(buildHeader(data, actions));
        modal.appendChild(buildContent(data));
        if (actions && actions.approveUrl) {
            modal.appendChild(buildFooter(actions));
        }

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Cerrar al hacer clic en la zona oscura (fuera del modal)
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeModal();
        });
        document.addEventListener('keydown', handleKeyDown);

        // Animación entrada
        gsap.fromTo(overlay, { opacity: 0 }, { opacity: 1, duration: 0.3 });
        gsap.fromTo(modal,
            { scale: 0.8, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.3, ease: 'back.out(1.5)' }
        );
    }

    // ─── Header ─────────────────────────────────────────────────────────────────

    function buildHeader(data, actions) {
        const header = document.createElement('div');
        header.style.cssText = [
            'display:flex;align-items:center;justify-content:space-between;',
            'padding:1rem 1.5rem;',
            'border-bottom:1px solid rgba(212,160,74,0.2);',
            'flex-shrink:0;'
        ].join('');

        const title = document.createElement('h3');
        title.style.cssText = [
            'color:#d4a04a;font-size:1.1rem;margin:0;',
            'font-family:system-ui,sans-serif;',
            'overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:60%;'
        ].join('');
        title.textContent = data.original_name || 'Documento';

        // Grupo derecho: botón descargar + botón cerrar
        const rightGroup = document.createElement('div');
        rightGroup.style.cssText = 'display:flex;align-items:center;gap:0.75rem;flex-shrink:0;';

        // ── Botón Descargar ──────────────────────────────────────────────────
        const dlBtn = document.createElement('button');
        dlBtn.style.cssText = [
            'display:inline-flex;align-items:center;gap:0.4rem;',
            'background:rgba(212,160,74,0.15);color:#d4a04a;',
            'border:1px solid rgba(212,160,74,0.4);',
            'padding:0.35rem 0.9rem;border-radius:4px;',
            'font-size:0.82rem;font-weight:600;cursor:pointer;',
            'transition:background 0.15s;'
        ].join('');
        dlBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0">'
            + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>'
            + '</svg>Descargar';
        dlBtn.addEventListener('mouseenter', function () {
            this.style.background = 'rgba(212,160,74,0.28)';
        });
        dlBtn.addEventListener('mouseleave', function () {
            this.style.background = 'rgba(212,160,74,0.15)';
        });
        dlBtn.addEventListener('click', function () {
            triggerDownload(data.url, data.original_name || 'documento', dlBtn);
        });

        // ── Botón Cerrar ─────────────────────────────────────────────────────
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cssText = [
            'background:none;border:none;color:#f5f0e8;',
            'font-size:1.8rem;cursor:pointer;line-height:1;padding:0 0.5rem;'
        ].join('');
        closeBtn.addEventListener('click', closeModal);

        rightGroup.appendChild(dlBtn);
        rightGroup.appendChild(closeBtn);

        header.appendChild(title);
        header.appendChild(rightGroup);
        return header;
    }

    // ─── Descarga via fetch + blob (funciona cross-origin con presigned URLs) ────

    function triggerDownload(url, filename, btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="animation:dv-spin 1s linear infinite;flex-shrink:0">'
            + '<circle cx="12" cy="12" r="10" stroke-opacity="0.3" stroke-width="3"/>'
            + '<path stroke-linecap="round" stroke-width="3" d="M12 2a10 10 0 0110 10" /></svg>Descargando…';

        fetch(url)
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.blob();
            })
            .then(function (blob) {
                const blobUrl = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(function () { URL.revokeObjectURL(blobUrl); }, 1000);
                btn.disabled = false;
                btn.innerHTML = origHtml;
            })
            .catch(function (err) {
                console.warn('Blob download failed, fallback to new tab:', err);
                // Fallback: abrir en nueva pestaña (el usuario puede guardar desde ahí)
                window.open(url, '_blank');
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
    }

    // ─── Content ─────────────────────────────────────────────────────────────────

    function buildContent(data) {
        const wrap = document.createElement('div');
        wrap.style.cssText = 'padding:1.5rem;overflow-y:auto;flex:1;';

        // Banner de rechazo (solo para usuario, cuando no hay acciones de admin)
        if (data.status === 'rejected' && data.rejection_note) {
            const banner = document.createElement('div');
            banner.style.cssText = [
                'display:flex;align-items:flex-start;gap:0.5rem;',
                'background:rgba(231,76,60,0.1);',
                'border:1px solid rgba(231,76,60,0.35);',
                'border-radius:6px;padding:0.7rem 1rem;',
                'margin-bottom:1rem;font-size:0.85rem;color:#f08080;line-height:1.5;'
            ].join('');
            banner.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:2px">'
                + '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>'
                + '<span><strong style="color:#e74c3c;">Motivo del rechazo:</strong> ' + data.rejection_note + '</span>';
            wrap.appendChild(banner);
        }

        renderContent(data, wrap);
        return wrap;
    }

    // ─── Footer con acciones de validación ──────────────────────────────────────

    function buildFooter(actions) {
        const footer = document.createElement('div');
        footer.style.cssText = [
            'display:flex;align-items:center;justify-content:flex-end;gap:0.75rem;',
            'padding:1rem 1.5rem;',
            'border-top:1px solid rgba(212,160,74,0.2);',
            'flex-shrink:0;'
        ].join('');

        // ── Botón Rechazar ──────────────────────────────────────────────────
        if (actions.rejectDocId && actions.rejectUserId) {
            const btnReject = document.createElement('button');
            btnReject.textContent = 'Rechazar';
            btnReject.style.cssText = btnStyle('#ef4444');
            btnReject.addEventListener('click', function () {
                closeModal();
                // Espera la animación de cierre del visor (~250ms) antes de abrir
                // el modal de rechazo (que tiene z-index menor que el visor)
                setTimeout(function () {
                    if (typeof openRejectModal === 'function') {
                        openRejectModal(actions.rejectDocId, actions.rejectUserId);
                    }
                }, 280);
            });
            footer.appendChild(btnReject);
        }

        // ── Botón Aprobar ───────────────────────────────────────────────────
        const btnApprove = document.createElement('button');
        btnApprove.textContent = 'Aprobar';
        btnApprove.style.cssText = btnStyle('#22c55e');
        btnApprove.addEventListener('click', function () {
            btnApprove.disabled = true;
            btnApprove.textContent = 'Aprobando…';
            approveDocument(actions.approveUrl, btnApprove);
        });
        footer.appendChild(btnApprove);

        return footer;
    }

    function btnStyle(bg) {
        return [
            'background:' + bg + ';color:#fff;border:none;',
            'padding:0.6rem 1.4rem;border-radius:4px;',
            'font-size:0.9rem;font-weight:600;cursor:pointer;'
        ].join('');
    }

    // ─── Aprobación via AJAX ─────────────────────────────────────────────────────

    function approveDocument(approveUrl, btn) {
        // Leer token CSRF del campo oculto ya en el DOM (cualquier form de la página)
        const csrfInput = document.querySelector('input[name="csrf_test_name"]');
        const body = new URLSearchParams({ action: 'approve' });
        if (csrfInput) body.append(csrfInput.name, csrfInput.value);

        fetch(approveUrl, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: body,
            redirect: 'follow'
        })
        .then(r => {
            // El servidor devuelve un redirect 302 → lo seguimos y recargamos
            if (r.ok || r.redirected) {
                closeModal();
                location.reload();
            } else {
                alert('Error al aprobar (' + r.status + ')');
                btn.disabled = false;
                btn.textContent = 'Aprobar';
            }
        })
        .catch(err => {
            console.error('approveDocument error:', err);
            alert('Error de conexión al aprobar');
            btn.disabled = false;
            btn.textContent = 'Aprobar';
        });
    }

    // ─── Renderizado de contenido ────────────────────────────────────────────────

    function renderContent(data, container) {
        const ext = (data.file_extension || '').toLowerCase().replace('.', '');
        switch (ext) {
            case 'pdf':  renderPDF(data, container);   break;
            case 'jpg':
            case 'jpeg':
            case 'png':  renderImage(data, container); break;
            default:     renderOfficeDocument(data, container);
        }
    }

    function renderPDF(data, container) {
        container.innerHTML = '<p style="color:rgba(245,240,232,0.5);">Cargando PDF…</p>';

        if (typeof pdfjsLib === 'undefined') {
            container.innerHTML = '<p style="color:#ef4444;">PDF.js no está cargado. Recargue la página.</p>';
            return;
        }

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        pdfjsLib.getDocument({ url: data.url }).promise.then(function (pdf) {
            pdfDoc     = pdf;
            totalPages = pdf.numPages;
            currentPage = 1;

            container.innerHTML = '';

            // Controles de navegación
            const controls = document.createElement('div');
            controls.style.cssText = 'display:flex;align-items:center;justify-content:center;gap:1rem;margin-bottom:1rem;';

            const prevBtn = navBtn('← Anterior');
            const nextBtn = navBtn('Siguiente →');
            const pageInfo = document.createElement('span');
            pageInfo.style.cssText = 'color:#f5f0e8;font-size:0.9rem;';

            function updateControls() {
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
                pageInfo.textContent = 'Página ' + currentPage + ' de ' + totalPages;
            }

            prevBtn.addEventListener('click', function () {
                if (currentPage > 1) { currentPage--; renderPage(container); updateControls(); }
            });
            nextBtn.addEventListener('click', function () {
                if (currentPage < totalPages) { currentPage++; renderPage(container); updateControls(); }
            });

            controls.appendChild(prevBtn);
            controls.appendChild(pageInfo);
            controls.appendChild(nextBtn);
            container.appendChild(controls);

            // Canvas
            const canvasWrap = document.createElement('div');
            canvasWrap.style.cssText = 'display:flex;justify-content:center;';
            const canvas = document.createElement('canvas');
            canvas.className = 'dv-pdf-canvas';
            canvas.style.cssText = 'max-width:100%;border:1px solid rgba(212,160,74,0.1);';
            canvasWrap.appendChild(canvas);
            container.appendChild(canvasWrap);

            updateControls();
            renderPage(container);
        }).catch(function (err) {
            console.error('PDF load error:', err);
            container.innerHTML = '<p style="color:#ef4444;">Error al cargar el PDF</p>';
        });
    }

    function navBtn(label) {
        const b = document.createElement('button');
        b.textContent = label;
        b.style.cssText = [
            'background:#2a2a2a;color:#f5f0e8;',
            'border:1px solid rgba(212,160,74,0.3);',
            'padding:0.5rem 1rem;border-radius:4px;cursor:pointer;'
        ].join('');
        return b;
    }

    function renderPage(container) {
        if (!pdfDoc) return;
        const canvas = container.querySelector('.dv-pdf-canvas');
        if (!canvas) return;

        pdfDoc.getPage(currentPage).then(function (page) {
            const viewport = page.getViewport({ scale: 1.5 });
            canvas.width  = viewport.width;
            canvas.height = viewport.height;
            page.render({ canvasContext: canvas.getContext('2d'), viewport });
        });
    }

    function renderImage(data, container) {
        const img = document.createElement('img');
        img.src = data.url;
        img.style.cssText = 'max-height:65vh;object-fit:contain;width:100%;display:block;margin:0 auto;';
        img.alt = data.original_name || 'Documento';
        container.appendChild(img);
    }

    function renderOfficeDocument(data, container) {
        const card = document.createElement('div');
        card.style.cssText = 'background:#2a2a2a;padding:1.5rem;border-radius:6px;border:1px solid rgba(212,160,74,0.1);';

        const name = document.createElement('h4');
        name.style.cssText = 'color:#d4a04a;margin:0 0 1rem 0;font-size:1.1rem;';
        name.textContent = data.original_name || 'Documento';

        const info = document.createElement('div');
        info.style.cssText = 'color:rgba(245,240,232,0.7);font-size:0.9rem;line-height:1.8;';
        info.innerHTML = '<div><strong>Tipo:</strong> ' + (data.file_extension || 'N/A').toUpperCase() + '</div>'
                       + '<div><strong>Tamaño:</strong> ' + formatFileSize(data.file_size || 0) + '</div>'
                       + '<p style="color:rgba(245,240,232,0.45);font-size:0.82rem;margin:0.75rem 0 0;">Este tipo de archivo no puede visualizarse en el navegador. Usa el botón <strong style="color:#d4a04a;">Descargar</strong> del encabezado para obtenerlo.</p>';

        card.appendChild(name);
        card.appendChild(info);
        container.appendChild(card);
    }

    function formatFileSize(size) {
        return size > 1048576
            ? (size / 1048576).toFixed(1) + ' MB'
            : (size / 1024).toFixed(0) + ' KB';
    }

    // ─── Cerrar modal ────────────────────────────────────────────────────────────

    function closeModal() {
        if (!modal || !overlay) return;
        pdfDoc = null; currentPage = 1; totalPages = 1;
        document.removeEventListener('keydown', handleKeyDown);

        gsap.to(modal,   { scale: 0.8, opacity: 0, duration: 0.2, onComplete: () => { overlay.remove(); overlay = null; modal = null; } });
        gsap.to(overlay, { opacity: 0, duration: 0.2 });
    }

    function handleKeyDown(e) {
        if (e.key === 'Escape' && overlay) closeModal();
    }

})();

/* Animación spinner para el botón de descarga */
(function () {
    const style = document.createElement('style');
    style.textContent = '@keyframes dv-spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
})();
