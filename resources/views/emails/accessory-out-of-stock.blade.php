<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notificación de Inventario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">

    <!-- Preheader (hidden preview text) -->
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; mso-hide:all;">
        Accesorios sin disponibilidad. Se solicita reposición a la brevedad.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6; padding:24px 12px;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.06);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#1f429b; padding:24px 32px;">
                            <h1 style="margin:0; font-size:20px; font-weight:700; color:#ffffff; letter-spacing:0.2px;">
                                Notificación de Inventario
                            </h1>
                            <p style="margin:6px 0 0 0; font-size:13px; color:#dbe4ff;">
                                Accesorios sin disponibilidad
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:28px 32px; color:#374151; font-size:14px; line-height:1.7;">
                            <p style="margin:0 0 16px 0;">
                                Estimado equipo de <strong>Compras</strong>,
                            </p>
                            <p style="margin:0 0 16px 0;">
                                Tras la última validación de inventario, actualmente <strong>no se cuenta con stock disponible</strong> de los siguientes accesorios:
                            </p>

                            <!-- Accessories List -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:8px 0 20px 0;">
                                @foreach($assets as $asset)
                                <tr>
                                    <td style="padding:6px 0; font-size:14px; color:#111827;">
                                        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#1f429b; margin-right:8px; vertical-align:middle;"></span>
                                        <span style="vertical-align:middle;">{{ $asset->full_name }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            <!-- Info Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:20px 0; background-color:#f8fafc; border-left:4px solid #1f429b;">
                                <tr>
                                    <td style="padding:16px; font-size:13px; color:#1f2937;">
                                        Esta situación puede impactar próximas asignaciones y requerimientos operativos del área.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px 0;">
                                Agradecemos su apoyo para gestionar la <strong>reposicion o compra</strong> correspondiente a la mayor brevedad posible.
                            </p>

                            <p style="margin:0;">
                                Saludos cordiales,<br>
                                Área de TI / Sistemas
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:0 32px;">
                            <hr style="border:none; border-top:1px solid #e5e7eb;">
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:20px 32px; font-size:12px; color:#6b7280;">
                            <p style="margin:0;">Área de TI / Sistemas</p>
                            <p style="margin:4px 0 0 0;">Este correo ha sido generado automáticamente.</p>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>

</body>
</html>