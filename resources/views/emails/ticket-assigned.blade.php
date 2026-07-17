<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $isReassignment ? 'Reasignación' : 'Asignación' }} de ticket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; mso-hide:all;">
        {{ $assignedBy->full_name }} te ha {{ $isReassignment ? 'reasignado' : 'asignado' }} el ticket #{{ $ticket->id }}.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f3f4f6; padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:680px; background-color:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 28px rgba(15,23,42,0.10);">
                    <tr>
                        <td style="background-color:#1f429b; padding:26px 32px;">
                            <p style="margin:0 0 8px 0; font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:#dbe4ff; font-weight:700;">Mesa de ayuda</p>
                            <h1 style="margin:0; font-size:22px; line-height:1.25; font-weight:700; color:#ffffff;">
                                {{ $isReassignment ? 'Ticket reasignado' : 'Nuevo ticket asignado' }}
                            </h1>
                            <p style="margin:8px 0 0 0; font-size:13px; color:#dbe4ff;">Ticket #{{ $ticket->id }} - {{ $priorityLabel }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 32px; color:#334155; font-size:14px; line-height:1.7;">
                            <p style="margin:0 0 18px 0;">Hola, <strong>{{ $responsible->full_name }}</strong>.</p>
                            <p style="margin:0 0 22px 0;">
                                <strong>{{ $assignedBy->full_name }}</strong> te ha {{ $isReassignment ? 'reasignado' : 'asignado' }} el siguiente ticket:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0; font-size:19px; line-height:1.35; font-weight:700; color:#0f172a;">{{ $ticket->title }}</p>
                                        <p style="margin:10px 0 0 0; color:#475569; white-space:pre-wrap;">{{ $ticket->description }}</p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; width:42%; border-bottom:1px solid #e2e8f0;">Asignado por</td>
                                    <td style="padding:9px 0; color:#0f172a; font-weight:700; border-bottom:1px solid #e2e8f0;">{{ $assignedBy->full_name }}</td>
                                </tr>
                                @if($previousResponsible)
                                    <tr>
                                        <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Responsable anterior</td>
                                        <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $previousResponsible->full_name }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Solicitante</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $ticket->requester?->full_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Departamento</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $ticket->requester?->department?->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Tipo</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $typeLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Categoría</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $categoryLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Prioridad</td>
                                    <td style="padding:9px 0; color:#0f172a; font-weight:700; border-bottom:1px solid #e2e8f0;">{{ $priorityLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b;">Estado</td>
                                    <td style="padding:9px 0; color:#0f172a;">{{ $statusLabel }}</td>
                                </tr>
                            </table>

                            <table cellpadding="0" cellspacing="0" role="presentation" style="margin:22px 0 0 0;">
                                <tr>
                                    <td style="background:#1f429b; border-radius:8px;">
                                        <a href="{{ $ticketUrl }}" style="display:inline-block; padding:11px 18px; color:#ffffff; text-decoration:none; font-weight:700; font-size:14px;">Ver ticket</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0 0;">Saludos cordiales,<br>Área de TI / Sistemas</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;"><hr style="border:none; border-top:1px solid #e2e8f0;"></td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; font-size:12px; color:#64748b;">
                            <p style="margin:0;">Área de TI / Sistemas</p>
                            <p style="margin:4px 0 0 0;">Este correo ha sido generado automáticamente.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
