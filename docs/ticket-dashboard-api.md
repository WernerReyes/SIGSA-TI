# API del dashboard de tickets

El dashboard web y la API usan el mismo servicio (`TicketDashboardService`), por lo que ambos
entregan los mismos indicadores y aplican los mismos filtros.

## Rutas

| Uso | Método y ruta | Autenticación |
| --- | --- | --- |
| Vista Vue/Inertia | `GET /tickets/dashboard` | Sesión web y usuario del departamento TI |
| API JSON | `GET /api/tickets/dashboard` | API key mediante `X-API-Key` o `Authorization: Bearer` |

La URL base de producción para la API es:

```text
https://sistemas-ti.cechriza.com/api
```

Todas las solicitudes API deben incluir:

```http
Accept: application/json
X-API-Key: <valor-configurado-en-ACCESS_API>
```

También se acepta la key como token Bearer:

```http
Authorization: Bearer <valor-configurado-en-ACCESS_API>
```

## Consultar el dashboard

```http
GET /api/tickets/dashboard
```

No se envía cuerpo. Los filtros se envían como parámetros de consulta.

| Parámetro | Tipo | Requerido | Descripción |
| --- | --- | --- | --- |
| `start_date` | fecha `YYYY-MM-DD` | No | Inicio inclusivo. Por defecto: 29 días antes de hoy. |
| `end_date` | fecha `YYYY-MM-DD` | No | Fin inclusivo. Por defecto: hoy. Debe ser igual o posterior a `start_date`. |
| `responsible_id` | entero | No | `staff_id` del responsable. Debe existir en `ost_staff`. |
| `requester_id` | entero | No | `staff_id` del solicitante. Debe existir en `ost_staff`. |
| `type` | texto | No | `INCIDENT` o `SERVICE_REQUEST`. |
| `category` | texto | No | `ACCESS`, `SOFTWARE` o `EQUIPMENT`. |

Ejemplo con todos los tipos de filtro:

```http
GET /api/tickets/dashboard?start_date=2026-07-01&end_date=2026-07-31&responsible_id=8&type=INCIDENT&category=SOFTWARE
```

Ejemplo con cURL:

```bash
curl --request GET \
  --url "https://sistemas-ti.cechriza.com/api/tickets/dashboard?start_date=2026-07-01&end_date=2026-07-31" \
  --header "Accept: application/json" \
  --header "X-API-Key: <API_KEY>"
```

## Respuesta exitosa

Respuesta `200 OK`:

```json
{
  "data": {
    "filters": {
      "start_date": "2026-07-01",
      "end_date": "2026-07-31",
      "responsible_id": null,
      "requester_id": null,
      "type": null,
      "category": null
    },
    "summary": {
      "total": 42,
      "active": 12,
      "resolved": 25,
      "closed": 18,
      "unassigned": 3,
      "sla_breached": 4,
      "sla_compliance_rate": 84.0,
      "average_resolution_minutes": 315
    },
    "by_status": [
      {
        "value": "OPEN",
        "label": "Abierto",
        "count": 5,
        "percentage": 11.9
      },
      {
        "value": "IN_PROGRESS",
        "label": "En Progreso",
        "count": 6,
        "percentage": 14.3
      }
    ],
    "by_priority": [
      {
        "value": "URGENT",
        "label": "Urgente",
        "count": 4,
        "percentage": 9.5
      }
    ],
    "by_type": [
      {
        "value": "INCIDENT",
        "label": "Incidente",
        "count": 30,
        "percentage": 71.4
      }
    ],
    "by_category": [
      {
        "value": "SOFTWARE",
        "label": "Software",
        "count": 14,
        "percentage": 33.3
      },
      {
        "value": "UNCATEGORIZED",
        "label": "Sin categoría",
        "count": 10,
        "percentage": 23.8
      }
    ],
    "daily_trend": [
      {
        "date": "2026-07-01",
        "created": 3,
        "resolved": 2
      }
    ],
    "technicians": [
      {
        "staff_id": 8,
        "name": "Ana Torres",
        "total": 12,
        "resolved": 9,
        "active": 3,
        "sla_breached": 1,
        "resolution_rate": 75.0
      }
    ],
    "recent_tickets": [
      {
        "id": 125,
        "title": "No puedo ingresar al ERP",
        "status": "IN_PROGRESS",
        "priority": "HIGH",
        "type": "INCIDENT",
        "requester_id": 12,
        "responsible_id": 8,
        "requester": {
          "staff_id": 12,
          "firstname": "Juan",
          "lastname": "Pérez",
          "full_name": "Juan Pérez"
        },
        "responsible": {
          "staff_id": 8,
          "firstname": "Ana",
          "lastname": "Torres",
          "full_name": "Ana Torres"
        },
        "created_at": "2026-07-31T14:30:00.000000Z"
      }
    ]
  }
}
```

Las colecciones `by_status`, `by_priority`, `by_type` y `by_category` siempre incluyen todos los
valores permitidos, aunque su conteo sea cero. `recent_tickets` y `technicians` devuelven como
máximo ocho registros.

## Definición de indicadores

| Indicador | Cálculo |
| --- | --- |
| `total` | Tickets creados dentro del rango y demás filtros. |
| `active` | Tickets que no están en `RESOLVED` ni `CLOSED`. |
| `resolved` | Tickets con `resolved_at` informado. Incluye los que después fueron cerrados. |
| `closed` | Tickets cuyo estado actual es `CLOSED`. |
| `unassigned` | Tickets sin `responsible_id`. |
| `sla_breached` | Tickets marcados con incumplimiento o activos cuyo vencimiento de resolución ya pasó. |
| `sla_compliance_rate` | Porcentaje de tickets resueltos que no incumplieron el SLA. |
| `average_resolution_minutes` | Promedio transcurrido entre creación y resolución, descontando pausas registradas. |
| `resolution_rate` | Porcentaje resuelto por cada responsable sobre sus tickets del periodo. |

El resumen, distribuciones, desempeño y tickets recientes forman una cohorte por `created_at`.
La serie `daily_trend.created` usa `created_at`; `daily_trend.resolved` usa `resolved_at` para mostrar
cuántos tickets se resolvieron realmente cada día dentro del rango.

## Respuestas de error

API key ausente o inválida, respuesta `401`:

```json
{
  "error": "API key invalida o no enviada."
}
```

API key no configurada en el servidor, respuesta `500`:

```json
{
  "error": "ACCESS_API no esta configurado en el servidor."
}
```

Filtros inválidos, respuesta `422`:

```json
{
  "message": "La fecha final debe ser igual o posterior a la fecha inicial.",
  "errors": {
    "end_date": [
      "La fecha final debe ser igual o posterior a la fecha inicial."
    ]
  }
}
```

Otros ejemplos de validación que generan `422`:

- `responsible_id` o `requester_id` no existen.
- `type` no es `INCIDENT` ni `SERVICE_REQUEST`.
- `category` no es `ACCESS`, `SOFTWARE` ni `EQUIPMENT`.
