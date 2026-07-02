<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo ticket registrado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family: Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; mso-hide:all;">
        Se ha registrado un nuevo ticket para el area de Sistemas.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:640px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="background-color:#1f429b; padding:24px 32px;">
                            <h1 style="margin:0; font-size:20px; font-weight:700; color:#ffffff;">
                                Nuevo ticket registrado
                            </h1>
                            <p style="margin:6px 0 0 0; font-size:13px; color:#dbe4ff;">
                                Ticket #{{ $ticket->id }} - {{ $priorityLabel }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px 32px; color:#374151; font-size:14px; line-height:1.7;">
                            <p style="margin:0 0 16px 0;">
                                Equipo de <strong>Sistemas</strong>,
                            </p>
                            <p style="margin:0 0 20px 0;">
                                Se ha registrado un nuevo ticket en el sistema. Estos son los detalles principales:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:8px; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0; font-size:18px; font-weight:700; color:#111827;">
                                            {{ $ticket->title }}
                                        </p>
                                        <p style="margin:8px 0 0 0; color:#4b5563; white-space:pre-wrap;">
                                            {{ $ticket->description }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280; width:42%;">Solicitante</td>
                                    <td style="padding:8px 0; color:#111827; font-weight:600;">
                                        {{ $ticket->requester?->full_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Departamento</td>
                                    <td style="padding:8px 0; color:#111827;">
                                        {{ $ticket->requester?->department?->name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Tipo</td>
                                    <td style="padding:8px 0; color:#111827;">{{ $typeLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Categoria</td>
                                    <td style="padding:8px 0; color:#111827;">{{ $categoryLabel }}</td>
                                </tr>
                                <!-- <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Impacto</td>
                                    <td style="padding:8px 0; color:#111827;">{{ $impactLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Urgencia</td>
                                    <td style="padding:8px 0; color:#111827;">{{ $urgencyLabel }}</td>
                                </tr> -->
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Prioridad</td>
                                    <td style="padding:8px 0; color:#111827; font-weight:700;">{{ $priorityLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Estado</td>
                                    <td style="padding:8px 0; color:#111827;">{{ $statusLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; color:#6b7280;">Fecha de registro</td>
                                    <td style="padding:8px 0; color:#111827;">
                                        {{ optional($ticket->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>

                            @if(is_array($ticket->images_urls) && count($ticket->images_urls) > 0)
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:20px 0; background-color:#f8fafc; border-left:4px solid #1f429b;">
                                    <tr>
                                        <td style="padding:16px; font-size:13px; color:#1f2937;">
                                            Este ticket incluye {{ count($ticket->images_urls) }} imagen(es) adjunta(s) en el sistema.
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <p style="margin:22px 0 0 0;">
                                Saludos cordiales,<br>
                                Area de TI / Sistemas
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;">
                            <hr style="border:none; border-top:1px solid #e5e7eb;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px; font-size:12px; color:#6b7280;">
                            <p style="margin:0;">Area de TI / Sistemas</p>
                            <p style="margin:4px 0 0 0;">Este correo ha sido generado automaticamente.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
