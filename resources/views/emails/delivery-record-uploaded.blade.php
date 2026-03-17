<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Constancia de {{ $recordTypeLabel }}</title>
</head>

<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.5;">
    <p>Hola,</p>

    @if(!empty($customMessage))
        <div style="white-space: pre-wrap;">{{ $customMessage }}</div>
    @else
        <p>
            Se ha compartido la constancia de <strong>{{ $recordTypeLabel }}</strong>
            correspondiente al activo <strong>{{ $assetName }}</strong>
            asignado a <strong>{{ $assignedToName }}</strong>.
        </p>

        <p>
            El documento principal va adjunto por defecto.
            Si se agregaron imágenes adicionales, también se incluyen como adjuntos.
        </p>
    @endif

    <p>Saludos.</p>
</body>

</html>
