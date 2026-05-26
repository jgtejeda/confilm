<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recupera tu contraseña — Comisión Film</title>
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
                <td style="padding:40px 40px 36px;">

                  <!-- Icon -->
                  <div style="text-align:center;font-size:42px;margin-bottom:20px;">🔑</div>

                  <!-- Title -->
                  <h1 style="margin:0 0 10px;font-size:26px;font-weight:600;color:#f5f0e8;text-align:center;font-family:Georgia,serif;letter-spacing:-0.02em;">
                    Recupera tu contraseña
                  </h1>

                  <!-- Subtitle -->
                  <p style="margin:0 0 28px;font-size:14px;color:rgba(245,240,232,0.55);text-align:center;line-height:1.6;">
                    Hola, <strong style="color:rgba(245,240,232,0.85);"><?= esc($user['nombres'] ?? $user['username'] ?? 'usuario') ?></strong>.<br>
                    Haz clic en el botón para establecer una nueva contraseña.
                  </p>

                  <!-- CTA Button -->
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="center" style="padding-bottom:28px;">
                        <a href="<?= site_url('reset/' . $token) ?>"
                           style="display:inline-block;background-color:#d4a04a;color:#0a0a0a;text-decoration:none;font-weight:700;font-size:15px;padding:14px 36px;border-radius:8px;letter-spacing:0.02em;">
                          Restablecer contraseña
                        </a>
                      </td>
                    </tr>
                  </table>

                  <!-- Divider -->
                  <hr style="border:none;border-top:1px solid rgba(245,240,232,0.07);margin:0 0 24px;">

                  <!-- Link fallback -->
                  <p style="margin:0 0 8px;font-size:12px;color:rgba(245,240,232,0.4);text-align:center;line-height:1.5;">
                    Si el botón no funciona, copia y pega este enlace en tu navegador:
                  </p>
                  <p style="margin:0;font-size:11px;text-align:center;word-break:break-all;">
                    <a href="<?= site_url('reset/' . $token) ?>"
                       style="color:#d4a04a;text-decoration:none;">
                      <?= site_url('reset/' . $token) ?>
                    </a>
                  </p>

                </td>
              </tr>
            </table>

            <!-- Footer -->
            <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(245,240,232,0.06);">
              <tr>
                <td style="padding:20px 40px;text-align:center;">
                  <p style="margin:0;font-size:11px;color:rgba(245,240,232,0.3);line-height:1.6;">
                    Este enlace expira en <strong style="color:rgba(245,240,232,0.45);">1 hora</strong>.<br>
                    Si no solicitaste este cambio, puedes ignorar este correo.<br>
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
