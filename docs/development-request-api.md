# API de Solicitudes de Desarrollo

Base URL: `https://sistemas-ti.cechriza.com/api`

Estos endpoints estan pensados para que otras areas registren solicitudes de desarrollo sin
iniciar sesion en el sistema web, consulten el tablero agrupado por estado y revisen el historial
de progreso de una solicitud. El analisis, aprobaciones, asignacion, registro de progreso y
cambios de estado se mantienen en el flujo interno del modulo de Desarrollo.

## Autenticacion por API key

La ruta requiere una API key configurada en el servidor:

```env
ACCESS_API=elGR1asaKISMNSHKt6RkBBLzTQRnycRM
```

El cliente debe enviar esa key en uno de estos formatos:

```http
X-API-Key: elGR1asaKISMNSHKt6RkBBLzTQRnycRM
```

Si la key no se envia o no coincide, la API responde `401`:

```json
{
  "error": "API key invalida o no enviada."
}
```

Si `ACCESS_API` no esta configurado en el servidor, la API responde `500`:

```json
{
  "error": "ACCESS_API no esta configurado en el servidor."
}
```

## Flujo general

1. El cliente externo envia una solicitud a `/api/development-requests`.
2. El middleware `access.api` valida la API key contra `ACCESS_API`.
3. Para registrar, `StoreDevelopmentRequest` valida el cuerpo. En rutas API, `requested_by_id` es obligatorio.
4. `StoreDevelopmentRequestDto` normaliza los datos y toma `requested_by_id` desde el cuerpo.
5. `DevelopmentRequestService::store()` crea la solicitud con tipo `type` y estado `REGISTERED`.
6. Para consultas, el controlador reutiliza `getSectionsByStatus()` y `getProgressHistory()`.
7. La API responde en JSON con `message`, `data` o `error`.

## Rutas disponibles

| Metodo | Ruta | Descripcion |
| --- | --- | --- |
| `GET` | `/api/development-requests` | Lista todas las solicitudes agrupadas por estado. |
| `POST` | `/api/development-requests` | Registra una nueva solicitud. |
| `GET` | `/api/development-requests/{id}/progress-history` | Lista el historial de progreso de una solicitud. |

## Listar solicitudes por estado

```http
GET /api/development-requests
```

Devuelve todas las solicitudes agrupadas por `status`. Internamente usa
`DevelopmentRequestService::getSectionsByStatus()`.

Relaciones incluidas:

| Relacion | Descripcion |
| --- | --- |
| `area` | Area solicitante. |
| `requested_by` | Usuario solicitante. |
| `technical_approval.approved_by` | Aprobacion tecnica y aprobador. |
| `strategic_approval.approved_by` | Aprobacion estrategica y aprobador. |
| `latest_progress` | Ultimo avance registrado. |
| `developers` | Desarrolladores asignados. |

Respuesta `200`:

```json
{
  "data": {
    "REGISTERED": [
      {
        "id": 25,
        "title": "Automatizar reporte de ventas",
        "type": "NEW_PROJECT",
        "priority": "HIGH",
        "status": "REGISTERED",
        "position": 1,
        "description": "Necesitamos generar automaticamente el reporte semanal de ventas por sede.",
        "impact": "Reduce trabajo manual y errores de consolidacion.",
        "estimated_hours": null,
        "estimated_end_date": null,
        "project_url": null,
        "completed_at": null,
        "actual_hours": null,
        "area_id": 4,
        "requested_by_id": 12,
        "requirement_url": null,
        "area": {
          "id_area": 4,
          "descripcion_area": "Comercial"
        },
        "requested_by": {
          "staff_id": 12,
          "firstname": "Juan",
          "lastname": "Perez"
        },
        "technical_approval": null,
        "strategic_approval": null,
        "latest_progress": null,
        "developers": []
      }
    ],
    "IN_DEVELOPMENT": [
      {
        "id": 18,
        "title": "Portal de indicadores",
        "type": "NEW_MODULE",
        "priority": "MEDIUM",
        "status": "IN_DEVELOPMENT",
        "position": 1,
        "latest_progress": {
          "id": 9,
          "percentage": 45,
          "notes": "Modulo de autenticacion completado.",
          "development_request_id": 18,
          "performed_by": 8,
          "created_at": "2026-07-02T09:30:00.000000Z",
          "updated_at": "2026-07-02T09:30:00.000000Z"
        },
        "developers": [
          {
            "staff_id": 8,
            "firstname": "Ana",
            "lastname": "Torres"
          }
        ]
      }
    ]
  }
}
```

## Ver historial de progreso

```http
GET /api/development-requests/{id}/progress-history
```

Devuelve el historial completo de avances de una solicitud, ordenado desde el primer registro
hasta el mas reciente. Internamente usa `DevelopmentRequestService::getProgressHistory()`.

Respuesta `200`:

```json
{
  "data": [
    {
      "id": 7,
      "notes": "Inicio de desarrollo",
      "percentage": 0,
      "development_request_id": 18,
      "created_at": "2026-07-01T08:00:00.000000Z",
      "updated_at": "2026-07-01T08:00:00.000000Z",
      "performed_by": {
        "staff_id": 8,
        "firstname": "Ana",
        "lastname": "Torres"
      }
    },
    {
      "id": 9,
      "notes": "Modulo de autenticacion completado.",
      "percentage": 45,
      "development_request_id": 18,
      "created_at": "2026-07-02T09:30:00.000000Z",
      "updated_at": "2026-07-02T09:30:00.000000Z",
      "performed_by": {
        "staff_id": 8,
        "firstname": "Ana",
        "lastname": "Torres"
      }
    }
  ]
}
```

Si la solicitud no existe:

```json
{
  "error": "Solicitud de desarrollo no encontrada."
}
```

## Registrar solicitud

```http
POST /api/development-requests
```

Headers recomendados:

```http
Accept: application/json
X-API-Key: valor-secreto
Content-Type: application/json
```

Si se envia un PDF de requerimiento, usar:

```http
Accept: application/json
X-API-Key: valor-secreto
Content-Type: multipart/form-data
```

## Cuerpo JSON

```json
{
  "title": "Automatizar reporte de ventas",
  "type": "NEW_PROJECT",
  "priority": "HIGH",
  "description": "Necesitamos generar automaticamente el reporte semanal de ventas por sede.",
  "impact": "Reduce trabajo manual y errores de consolidacion.",
  "area_id": 4,
  "requested_by_id": 12
}
```

## Cuerpo multipart/form-data

```text
title=Automatizar reporte de ventas
type=NEW_PROJECT
priority=HIGH
description=Necesitamos generar automaticamente el reporte semanal de ventas por sede.
impact=Reduce trabajo manual y errores de consolidacion.
area_id=4
requested_by_id=12
requirement_file=requerimiento.pdf
```

## Campos

| Campo | Requerido | Tipo | Regla |
| --- | --- | --- | --- |
| `title` | Si | string | Maximo 255 caracteres. |
| `type` | No | string | `NEW_PROJECT` o `NEW_MODULE`. Si no se envia, se usa `NEW_PROJECT`. |
| `priority` | Si | string | `LOW`, `MEDIUM`, `HIGH` o `URGENT`. |
| `description` | Si | string | Detalle funcional de la necesidad. |
| `impact` | No | string | Impacto esperado en el area o proceso. |
| `estimated_hours` | No | integer | Minimo 0, maximo 6 digitos. Normalmente lo completa TI despues. |
| `estimated_end_date` | No | date | Formato `YYYY-MM-DD`, hoy o fecha futura. Normalmente lo completa TI despues. |
| `area_id` | Si | integer | Debe existir en `area.id_area`. |
| `requested_by_id` | Si, en API | integer | Debe existir en `ost_staff.staff_id`. |
| `requirement_file` | No | file | PDF de maximo 4 MB. Solo con `multipart/form-data`. |

## Valores permitidos

`type`:

```text
NEW_PROJECT
NEW_MODULE
```

`priority`:

```text
LOW
MEDIUM
HIGH
URGENT
```

Estado inicial asignado por el sistema:

```text
REGISTERED
```

Estados posibles del flujo:

```text
REGISTERED
IN_ANALYSIS
APPROVED
IN_DEVELOPMENT
IN_TESTING
COMPLETED
REJECTED
```

El cliente no envia `status`, `position`, `project_url`, `completed_at`, `actual_hours`,
`approvals`, `developers` ni progreso. Esos campos pertenecen al flujo interno de TI.

## Respuesta exitosa

Respuesta `201`:

```json
{
  "message": "Solicitud de desarrollo creada exitosamente.",
  "data": {
    "id": 25,
    "title": "Automatizar reporte de ventas",
    "type": "NEW_PROJECT",
    "priority": "HIGH",
    "status": "REGISTERED",
    "position": 3,
    "description": "Necesitamos generar automaticamente el reporte semanal de ventas por sede.",
    "impact": "Reduce trabajo manual y errores de consolidacion.",
    "estimated_hours": null,
    "estimated_end_date": null,
    "area_id": 4,
    "requested_by_id": 12,
    "requirement_path": "delivery_records/requerimiento.pdf",
    "requirement_url": "http://localhost/storage/delivery_records/requerimiento.pdf",
    "area": {
      "id_area": 4,
      "descripcion_area": "Comercial"
    },
    "requested_by": {
      "staff_id": 12,
      "firstname": "Juan",
      "lastname": "Perez"
    }
  }
}
```

## Respuestas de error

Validacion `422`:

```json
{
  "message": "El solicitante es obligatorio para solicitudes API.",
  "errors": {
    "requested_by_id": [
      "El solicitante es obligatorio para solicitudes API."
    ]
  }
}
```

Area inexistente `422`:

```json
{
  "message": "El area solicitada no existe.",
  "errors": {
    "area_id": [
      "El area solicitada no existe."
    ]
  }
}
```

Prioridad invalida `422`:

```json
{
  "message": "La prioridad seleccionada no es valida.",
  "errors": {
    "priority": [
      "La prioridad seleccionada no es valida."
    ]
  }
}
```

Tipo invalido `422`:

```json
{
  "message": "El tipo de solicitud seleccionado no es valido.",
  "errors": {
    "type": [
      "El tipo de solicitud seleccionado no es valido."
    ]
  }
}
```

Error de negocio o servidor:

```json
{
  "error": "Error al crear la solicitud de desarrollo: detalle del error"
}
```

## Ejemplo cURL

JSON:

```bash
curl -X POST "https://tu-dominio.com/api/development-requests" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: valor-secreto" \
  -d '{
    "title": "Automatizar reporte de ventas",
    "type": "NEW_PROJECT",
    "priority": "HIGH",
    "description": "Necesitamos generar automaticamente el reporte semanal de ventas por sede.",
    "impact": "Reduce trabajo manual y errores de consolidacion.",
    "area_id": 4,
    "requested_by_id": 12
  }'
```

Con PDF:

```bash
curl -X POST "https://tu-dominio.com/api/development-requests" \
  -H "Accept: application/json" \
  -H "X-API-Key: valor-secreto" \
  -F "title=Automatizar reporte de ventas" \
  -F "type=NEW_PROJECT" \
  -F "priority=HIGH" \
  -F "description=Necesitamos generar automaticamente el reporte semanal de ventas por sede." \
  -F "impact=Reduce trabajo manual y errores de consolidacion." \
  -F "area_id=4" \
  -F "requested_by_id=12" \
  -F "requirement_file=@requerimiento.pdf"
```
