<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<title>Constancia de {{ ucfirst($recordTypeLabel) }}</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f8; margin:0; padding:30px; color:#1f2937;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0"
style="background:#ffffff; border:1px solid #e5e7eb; border-radius:8px; padding:28px;">

<tr>
<td>

@php
    $sections = $messageSections ?? [];
    $greeting = $sections['greeting'] ?? 'Estimada Sr. Cecilia,';
    $intro = $sections['intro_paragraph'] ?? null;
    $detailsIntro = $sections['details_intro'] ?? null;
    $assetTitle = $sections['asset_title'] ?? 'Activo entregado';
    $assetNameSection = $sections['asset_name'] ?? $assetName;
    $serial = $sections['serial'] ?? 'N/A';
    $accessoriesTitle = $sections['accessories_title'] ?? 'Accesorios incluidos';
    $accessories = $sections['accessories'] ?? [];
    $closing = $sections['closing_paragraph'] ?? null;
    $signatureLabel = $sections['signature_label'] ?? 'Atentamente,';
    $signatureArea = $sections['signature_area'] ?? 'Área de Sistemas';
@endphp

<p style="margin:0 0 18px 0;">
{{ $greeting }}
</p>

@if($intro)
<p style="margin-bottom:18px; text-align:justify; white-space:pre-wrap;">
{{ $intro }}
</p>
@endif

@if($detailsIntro)
<p style="margin-bottom:18px; white-space:pre-wrap;">
{{ $detailsIntro }}
</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0"
style="background:#f3f4f6; border:1px solid #e5e7eb; border-radius:6px; padding:16px; margin-bottom:20px;">
<tr>
<td style="font-weight:bold; color:#374151; padding-bottom:8px;">
{{ $assetTitle }}
</td>
</tr>
<tr>
<td style="font-size:15px; white-space:pre-wrap;">
<strong>{{ $assetNameSection }}</strong><br>
</td>
</tr>
</table>


@if(is_array($accessories) && count($accessories) > 0)
<p style="margin-bottom:8px; font-weight:bold;">
{{ $accessoriesTitle }}
</p>
<ul style="margin-top:6px; margin-bottom:18px;">
@foreach($accessories as $item)
<li>{{ $item }}</li>
@endforeach
</ul>
@endif

@if($closing)
<p style="text-align:justify; white-space:pre-wrap;">
{{ $closing }}
</p>
@endif

<p style="margin-top:26px;">
{{ $signatureLabel }}
</p>

<p style="margin-top:8px; white-space:pre-wrap;">
<strong>{{ $signatureArea }}</strong>
</p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
