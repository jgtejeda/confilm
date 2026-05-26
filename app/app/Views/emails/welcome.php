<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bienvenido — Comisión Film</title>
</head>
<body style="margin:0;padding:0;background-color:#0a0a0a;font-family:'Helvetica Neue',Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#0a0a0a;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;">

        <!-- Logo / Header -->
        <tr>
          <td align="center" style="padding-bottom:28px;">
            <div style="display:inline-block;background:linear-gradient(135deg,#d4a04a,#b8862e);border-radius:12px;width:44px;height:44px;line-height:44px;text-align:center;font-size:18px;font-weight:700;color:#0a0a0a;font-family:Georgia,serif;">CF</div>
            <div style="margin-top:10px;font-size:13px;letter-spacing:0.12em;text-transform:uppercase;color:rgba(245,240,232,0.45);">Comisión Film México</div>
          </td>
        </tr>

        <!-- Card -->
        <tr>
          <td style="background-color:#111111;border-radius:16px;border:1px solid rgba(212,160,74,0.15);overflow:hidden;">

            <!-- Gold top stripe -->
            <div style="height:2px;background:linear-gradient(90deg,transparent,#d4a04a,rgba(212,160,74,0.5),transparent);"></div>

            <!-- Body -->
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding:40px 40px 32px;">

                  <!-- Icon -->
                  <div style="text-align:center;font-size:42px;margin-bottom:20px;">🎬</div>

                  <!-- Title -->
                  <h1 style="margin:0 0 10px;font-size:26px;font-weight:600;color:#f5f0e8;text-align:center;font-family:Georgia,serif;letter-spacing:-0.02em;">
                    ¡Bienvenido, <?= esc($user['nombres'] ?? $user['username'] ?? 'usuario') ?>!
                  </h1>

                  <p style="margin:0 0 28px;font-size:14px;color:rgba(245,240,232,0.55);text-align:center;line-height:1.6;">
                    Tu cuenta en Comisión Film ha sido creada.<br>
                    Guarda estas credenciales en un lugar seguro.
                  </p>

                  <!-- Credentials box -->
                  <table width="100%" cellpadding="0" cellspacing="0"
                         style="background-color:rgba(212,160,74,0.07);border:1px solid rgba(212,160,74,0.2);border-radius:10px;margin-bottom:28px;">
                    <tr>
                      <td style="padding:20px 24px;">

                        <table width="100%" cellpadding="0" cellspacing="0">
                          <tr>
                            <td style="padding-bottom:12px;border-bottom:1px solid rgba(245,240,232,0.06);">
                              <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(245,240,232,0.4);margin-bottom:4px;">Correo / Usuario</div>
                              <div style="font-size:15px;font-weight:600;color:#f5f0e8;letter-spacing:0.02em;font-family:'Courier New',monospace;">
                                <?= esc($user['email']) ?>
                              </div>
                            </td>
                          </tr>
                          <tr>
                            <td style="padding-top:12px;">
                              <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(245,240,232,0.4);margin-bottom:4px;">Contraseña</div>
                              <div style="font-size:17px;font-weight:600;color:#d4a04a;letter-spacing:0.04em;font-family:'Courier New',monospace;">
                                <?= esc($rawPassword) ?>
                              </div>
                            </td>
                          </tr>
                        </table>

                      </td>
                    </tr>
                  </table>

                  <!-- Step notice -->
                  <table width="100%" cellpadding="0" cellspacing="0"
                         style="background-color:rgba(40,160,80,0.08);border:1px solid rgba(40,160,80,0.2);border-radius:8px;margin-bottom:28px;">
                    <tr>
                      <td style="padding:14px 18px;">
                        <p style="margin:0;font-size:13px;color:rgba(180,240,200,0.85);line-height:1.6;">
                          <strong>Siguiente paso:</strong> verifica tu correo electrónico haciendo clic en el link que te enviamos por separado.
                        </p>
                      </td>
                    </tr>
                  </table>

                  <!-- CTA -->
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="center">
                        <a href="<?= site_url('login') ?>"
                           style="display:inline-block;background-color:#d4a04a;color:#0a0a0a;text-decoration:none;font-weight:700;font-size:15px;padding:14px 36px;border-radius:8px;letter-spacing:0.02em;">
                          Ir al inicio de sesión
                        </a>
                      </td>
                    </tr>
                  </table>

                </td>
              </tr>
            </table>

            <!-- Footer -->
            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(245,240,232,0.06);">
              <tr>
                <td style="padding:20px 40px;text-align:center;">
                  <p style="margin:0;font-size:11px;color:rgba(245,240,232,0.3);line-height:1.6;">
                    Si no creaste esta cuenta, contáctanos de inmediato.<br>
                    Comisión Film México — Sistema de Registro
                  </p>
                </td>
              </tr>
            </table>

          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
