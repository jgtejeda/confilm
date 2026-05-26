<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Documento rechazado — Comisión Film</title>
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
                  <div style="text-align:center;font-size:42px;margin-bottom:20px;">❌</div>

                  <!-- Title -->
                  <h1 style="margin:0 0 10px;font-size:26px;font-weight:600;color:#f5f0e8;text-align:center;font-family:Georgia,serif;letter-spacing:-0.02em;">
                    Documento rechazado
                  </h1>

                  <!-- Subtitle -->
                  <p style="margin:0 0 28px;font-size:14px;color:rgba(245,240,232,0.55);text-align:center;line-height:1.6;">
                    Hola, <strong style="color:rgba(245,240,232,0.85);"><?= esc($user['nombres'] ?? $user['username'] ?? 'usuario') ?></strong>.<br>
                    Tu documento no pudo ser aprobado. Revisa el motivo a continuación.
                  </p>

                  <!-- Document info box -->
                  <table width="100%" cellpadding="0" cellspacing="0"
                         style="background-color:rgba(220,60,60,0.08);border:1px solid rgba(220,60,60,0.2);border-radius:10px;margin-bottom:28px;">
                    <tr>
                      <td style="padding:20px 24px;">
                        <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(240,180,180,0.6);margin-bottom:4px;">Tipo de documento</div>
                        <div style="font-size:17px;font-weight:600;color:#f0b4b4;letter-spacing:0.02em;">
                          <?= esc($docTypeName) ?>
                        </div>
                        <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(245,240,232,0.06);">
                          <div style="font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:rgba(240,180,180,0.6);margin-bottom:4px;">Motivo de rechazo</div>
                          <div style="font-size:14px;color:rgba(245,240,232,0.75);line-height:1.6;">
                            <?= esc($doc['rejection_note'] ?? 'No se proporcionó un motivo.') ?>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>

                  <!-- CTA -->
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td align="center">
                        <a href="<?= site_url('login') ?>"
                           style="display:inline-block;background-color:#d4a04a;color:#0a0a0a;text-decoration:none;font-weight:700;font-size:15px;padding:14px 36px;border-radius:8px;letter-spacing:0.02em;">
                          Subir documento nuevamente
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
                    Puedes subir un nuevo documento desde tu panel de usuario.<br>
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
