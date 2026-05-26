<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comisión Film MX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body>

<!-- ── Aurora background ─────────────────────────────────────── -->
<div class="aurora" aria-hidden="true">
    <div class="orb orb--gold"></div>
    <div class="orb orb--wine"></div>
    <div class="orb orb--slate"></div>
    <div class="orb orb--accent"></div>
</div>

<!-- ── Page ──────────────────────────────────────────────────── -->
<main class="auth-wrapper">

    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">CF</div>
        <div class="brand-text">
            <span class="brand-name">Comisión Film</span>
            <span class="brand-sub">México</span>
        </div>
    </div>

    <!-- Glass card -->
    <div class="glass-card">

        <!-- Tabs -->
        <div class="tabs" role="tablist">
            <button class="tab-btn <?= ($card ?? 'login') === 'login' ? 'is-active' : '' ?>"
                    data-target="login"
                    role="tab"
                    aria-selected="<?= ($card ?? 'login') === 'login' ? 'true' : 'false' ?>">
                Iniciar sesión
            </button>
            <button class="tab-btn <?= ($card ?? 'login') === 'register' ? 'is-active' : '' ?>"
                    data-target="register"
                    role="tab"
                    aria-selected="<?= ($card ?? 'login') === 'register' ? 'true' : 'false' ?>">
                Registrarse
            </button>
        </div>

        <!-- Panels -->
        <div class="panels">

            <!-- ── LOGIN ─────────────────────────────────────── -->
            <div id="panel-login"
                 class="panel <?= ($card ?? 'login') === 'login' ? 'is-active' : '' ?>"
                 role="tabpanel">

                <h1 class="panel-title">Bienvenido</h1>
                <p class="panel-subtitle">Accede a tu cuenta de Comisión Film</p>

                <?php if (session()->get('error')): ?>
                    <div class="alert alert--error"><?= esc(session()->get('error')) ?></div>
                <?php endif; ?>
                <?php if (session()->get('success')): ?>
                    <div class="alert alert--success"><?= esc(session()->get('success')) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= site_url('login') ?>" novalidate>
                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="form-label" for="login-email">Correo electrónico</label>
                        <input class="form-input"
                               type="email"
                               name="email"
                               id="login-email"
                               required
                               autocomplete="email"
                               placeholder="tu@correo.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="login-password">Contraseña</label>
                        <input class="form-input"
                               type="password"
                               name="password"
                               id="login-password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-primary">Iniciar sesión</button>
                </form>

                <button class="auth-link" onclick="switchTo('register')">
                    ¿No tienes cuenta? <strong>Regístrate</strong>
                </button>

            </div><!-- /panel-login -->

            <!-- ── REGISTRO ───────────────────────────────────── -->
            <div id="panel-register"
                 class="panel <?= ($card ?? 'login') === 'register' ? 'is-active' : '' ?>"
                 role="tabpanel">

                <h1 class="panel-title">Crear cuenta</h1>
                <p class="panel-subtitle">Completa tu información para registrarte</p>

                <?php if (session()->get('error')): ?>
                    <div class="alert alert--error"><?= esc(session()->get('error')) ?></div>
                <?php endif; ?>

                <?php if (session()->get('errors')): ?>
                    <div class="alert alert--error">
                        <?php foreach ((array) session('errors') as $e): ?>
                            <div><?= esc($e) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="panel--scrollable">
                    <form method="POST"
                          action="<?= site_url('registro') ?>"
                          enctype="multipart/form-data"
                          novalidate>
                        <?= csrf_field() ?>

                        <!-- Datos personales -->
                        <div class="form-group--row">
                            <div class="form-group">
                                <label class="form-label" for="reg-nombres">Nombre(s)</label>
                                <input class="form-input"
                                       type="text"
                                       name="nombres"
                                       id="reg-nombres"
                                       required
                                       autocomplete="given-name"
                                       value="<?= esc(old('nombres')) ?>"
                                       placeholder="Tus nombres">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reg-apat">Apellido paterno</label>
                                <input class="form-input"
                                       type="text"
                                       name="apellido_pat"
                                       id="reg-apat"
                                       required
                                       autocomplete="family-name"
                                       value="<?= esc(old('apellido_pat')) ?>"
                                       placeholder="Apellido paterno">
                            </div>
                        </div>

                        <div class="form-group--row">
                            <div class="form-group">
                                <label class="form-label" for="reg-amat">Apellido materno</label>
                                <input class="form-input"
                                       type="text"
                                       name="apellido_mat"
                                       id="reg-amat"
                                       autocomplete="additional-name"
                                       value="<?= esc(old('apellido_mat')) ?>"
                                       placeholder="Apellido materno">
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="reg-phone">Teléfono</label>
                                <input class="form-input"
                                       type="tel"
                                       name="phone"
                                       id="reg-phone"
                                       required
                                       autocomplete="tel"
                                       value="<?= esc(old('phone')) ?>"
                                       maxlength="10"
                                       placeholder="10 dígitos">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="reg-email">Correo electrónico</label>
                            <input class="form-input"
                                   type="email"
                                   name="email"
                                   id="reg-email"
                                   required
                                   autocomplete="email"
                                   value="<?= esc(old('email')) ?>"
                                   placeholder="tu@correo.com">
                        </div>

                        <?php if (!empty($docTypes)): ?>
                            <p class="form-section">Documentos requeridos</p>
                            <?php foreach ($docTypes as $dt): ?>
                                <div class="form-group">
                                    <label class="form-label" for="doc-<?= $dt['id'] ?>">
                                        <?= esc($dt['name']) ?>
                                    </label>
                                    <?php
                                        // allowed_types: puede ser JSON array o string con comas
                                        $rawTypes = $dt['allowed_types'] ?? '';
                                        $typesArr = json_decode($rawTypes, true);
                                        if (!is_array($typesArr)) {
                                            $typesArr = array_map('trim', explode(',', $rawTypes));
                                        }
                                        $acceptAttr = implode(',', array_filter($typesArr));
                                    ?>
                                    <input class="form-file"
                                           type="file"
                                           name="doc_<?= $dt['id'] ?>"
                                           id="doc-<?= $dt['id'] ?>"
                                           accept="<?= esc($acceptAttr) ?>"
                                           required>
                                    <p class="form-hint">
                                        Máx. <?= esc($dt['max_size_mb']) ?> MB
                                        <?php if (!empty($dt['description'])): ?>
                                            · <?= esc($dt['description']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <button type="submit" class="btn-primary" id="btn-submit-register">Crear cuenta</button>
                    </form>
                </div>

                <button class="auth-link" onclick="switchTo('login')">
                    ¿Ya tienes cuenta? <strong>Inicia sesión</strong>
                </button>

            </div><!-- /panel-register -->

        </div><!-- /panels -->
    </div><!-- /glass-card -->

</main>

<script>
(function () {
    'use strict';

    // ── Panel switching ──────────────────────────────────────────
    var DURATION_OUT = 220; // ms

    function switchTo(target) {
        var current = document.querySelector('.panel.is-active');
        var next    = document.getElementById('panel-' + target);

        if (!current || !next || current === next) return;

        // Update tabs
        document.querySelectorAll('.tab-btn').forEach(function (btn) {
            var active = btn.dataset.target === target;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        // Animate out
        current.classList.add('is-exiting');
        current.classList.remove('is-active');

        setTimeout(function () {
            current.classList.remove('is-exiting');
            next.classList.add('is-active');
        }, DURATION_OUT);
    }

    // Tab clicks
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () { switchTo(btn.dataset.target); });
    });

    // Expose globally for onclick attributes
    window.switchTo = switchTo;

    // ── Phone input: only digits ─────────────────────────────────
    var phoneInput = document.getElementById('reg-phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    }
})();
</script>

<!-- ── Modal: enviando registro ─────────────────────────────── -->
<div id="sending-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,10,10,.75);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div style="background:#111;border:1px solid rgba(212,160,74,.25);border-radius:20px;padding:44px 48px;text-align:center;max-width:340px;width:90%;">
        <div style="margin-bottom:24px;">
            <!-- Spinner animado -->
            <svg width="52" height="52" viewBox="0 0 52 52" style="animation:spin 1s linear infinite;">
                <circle cx="26" cy="26" r="22" fill="none" stroke="rgba(212,160,74,.2)" stroke-width="4"/>
                <path d="M26 4 a22 22 0 0 1 22 22" fill="none" stroke="#d4a04a" stroke-width="4" stroke-linecap="round"/>
            </svg>
        </div>
        <p style="font-family:Georgia,serif;font-size:20px;font-weight:600;color:#f5f0e8;margin:0 0 8px;">Enviando registro…</p>
        <p style="font-size:13px;color:rgba(245,240,232,.45);margin:0;line-height:1.6;">Estamos subiendo tus documentos.<br>Por favor espera, no cierres esta ventana.</p>
    </div>
</div>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
(function () {
    var form = document.querySelector('#panel-register form');
    var modal = document.getElementById('sending-modal');
    if (form && modal) {
        form.addEventListener('submit', function () {
            modal.style.display = 'flex';
        });
    }
})();
</script>

</body>
</html>
