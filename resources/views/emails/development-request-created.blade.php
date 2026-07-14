<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva solicitud de desarrollo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body style="margin:0; padding:0; background-color:#eef2f7; font-family: Arial, Helvetica, sans-serif;">
    <div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; mso-hide:all;">
        Se ha registrado una nueva solicitud de desarrollo para el area de Sistemas.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#eef2f7; padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:680px; background-color:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 28px rgba(15,23,42,0.10);">
                    <tr>
                        <td style="background-color:#0f766e; padding:26px 32px;">
                            <p style="margin:0 0 8px 0; font-size:12px; letter-spacing:0.08em; text-transform:uppercase; color:#ccfbf1; font-weight:700;">
                                Desarrollo
                            </p>
                            <h1 style="margin:0; font-size:22px; line-height:1.25; font-weight:700; color:#ffffff;">
                                Nueva solicitud registrada
                            </h1>
                            <p style="margin:8px 0 0 0; font-size:13px; color:#d1fae5;">
                                Solicitud #{{ $developmentRequest->id }} - {{ $typeLabel }}
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:30px 32px; color:#334155; font-size:14px; line-height:1.7;">
                            <p style="margin:0 0 18px 0;">
                                Equipo de <strong>Sistemas</strong>,
                            </p>
                            <p style="margin:0 0 22px 0;">
                                Se ha creado una nueva solicitud de desarrollo. Estos son los datos principales para iniciar la revision:
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0; font-size:19px; line-height:1.35; font-weight:700; color:#0f172a;">
                                            {{ $developmentRequest->title }}
                                        </p>
                                        <p style="margin:10px 0 0 0; color:#475569; white-space:pre-wrap;">
                                            {{ $developmentRequest->description }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:20px;">
                                <tr>
                                    <td width="50%" style="padding:0 8px 0 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#ecfeff; border:1px solid #a5f3fc; border-radius:10px;">
                                            <tr>
                                                <td style="padding:14px 16px;">
                                                    <p style="margin:0; color:#0e7490; font-size:12px; font-weight:700; text-transform:uppercase;">Tipo</p>
                                                    <p style="margin:6px 0 0 0; color:#164e63; font-size:16px; font-weight:700;">{{ $typeLabel }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding:0 0 0 8px;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#fff7ed; border:1px solid #fed7aa; border-radius:10px;">
                                            <tr>
                                                <td style="padding:14px 16px;">
                                                    <p style="margin:0; color:#c2410c; font-size:12px; font-weight:700; text-transform:uppercase;">Prioridad</p>
                                                    <p style="margin:6px 0 0 0; color:#7c2d12; font-size:16px; font-weight:700;">{{ $priorityLabel }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; width:42%; border-bottom:1px solid #e2e8f0;">Solicitante</td>
                                    <td style="padding:9px 0; color:#0f172a; font-weight:700; border-bottom:1px solid #e2e8f0;">
                                        {{ $developmentRequest->requestedBy?->full_name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Area solicitante</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">
                                        {{ $developmentRequest->area?->descripcion_area ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Departamento</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">
                                        {{ $developmentRequest->requestedBy?->department?->name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Estado inicial</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">{{ $statusLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Horas estimadas</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">
                                        {{ $developmentRequest->estimated_hours !== null ? $developmentRequest->estimated_hours . 'h' : 'Por definir' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b; border-bottom:1px solid #e2e8f0;">Fecha estimada</td>
                                    <td style="padding:9px 0; color:#0f172a; border-bottom:1px solid #e2e8f0;">
                                        {{ $developmentRequest->estimated_end_date ? \Illuminate\Support\Carbon::parse($developmentRequest->estimated_end_date)->format('d/m/Y') : 'Por definir' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:9px 0; color:#64748b;">Fecha de registro</td>
                                    <td style="padding:9px 0; color:#0f172a;">
                                        {{ optional($developmentRequest->created_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                    </td>
                                </tr>
                            </table>

                            @if($developmentRequest->impact)
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f8fafc; border-left:4px solid #0f766e; margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 18px;">
                                            <p style="margin:0 0 6px 0; color:#0f766e; font-size:12px; font-weight:700; text-transform:uppercase;">Impacto esperado</p>
                                            <p style="margin:0; color:#334155; white-space:pre-wrap;">{{ $developmentRequest->impact }}</p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if($developmentRequest->requirement_path)
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:0 0 22px 0; background-color:#fefce8; border:1px solid #fde68a; border-radius:10px;">
                                    <tr>
                                        <td style="padding:15px 16px; font-size:13px; color:#713f12;">
                                            Esta solicitud incluye un archivo de requerimiento. Se adjunta en este correo y tambien queda disponible en el sistema.
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table cellpadding="0" cellspacing="0" role="presentation" style="margin:22px 0 0 0;">
                                <tr>
                                    <td style="background:#0f766e; border-radius:8px;">
                                        <a href="{{ $developmentUrl }}" style="display:inline-block; padding:11px 18px; color:#ffffff; text-decoration:none; font-weight:700; font-size:14px;">
                                            Ver solicitud
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0 0;">
                                Saludos cordiales,<br>
                                Area de TI / Sistemas
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px;">
                            <hr style="border:none; border-top:1px solid #e2e8f0;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px; font-size:12px; color:#64748b;">
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
