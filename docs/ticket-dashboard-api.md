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
| `start_date` | fecha `YYYY-MM-DD` | No | Inicio inclusivo. Si se omite, no se aplica límite inferior. |
| `end_date` | fecha `YYYY-MM-DD` | No | Fin inclusivo. Si se omite, no se aplica límite superior. Debe ser igual o posterior a `start_date` cuando ambas fechas están presentes. |
| `responsible_ids[]` | array de enteros | No | Uno o más `staff_id` de responsables. Cada ID debe existir en `ost_staff`. |
| `requester_ids[]` | array de enteros | No | Uno o más `staff_id` de solicitantes. Cada ID debe existir en `ost_staff`. |
| `statuses[]` | array de textos | No | Uno o más: `OPEN`, `IN_PROGRESS`, `ON_HOLD`, `RESOLVED`, `CLOSED`. |
| `types[]` | array de textos | No | Uno o más: `INCIDENT`, `SERVICE_REQUEST`. |
| `categories[]` | array de textos | No | Uno o más: `ACCESS`, `SOFTWARE`, `EQUIPMENT`. |

Los valores de una misma lista se combinan con lógica **OR** (`whereIn`), mientras que filtros
de grupos diferentes se combinan con lógica **AND**. Por ejemplo, dos estados y dos tipos
seleccionan tickets que pertenezcan a cualquiera de esos estados y a cualquiera de esos tipos.

Por compatibilidad, la API todavía acepta los nombres singulares anteriores (`responsible_id`,
`requester_id`, `status`, `type`, `category`) y los convierte internamente en listas de un elemento.

Para consultar todo el histórico no se envían parámetros:

```http
GET /api/tickets/dashboard
```

En ese caso, `filters.start_date` y `filters.end_date` serán `null` y todos los tickets
participarán en los indicadores. La vista web conserva un rango inicial de 30 días únicamente
como ayuda visual; este valor predeterminado no se aplica a la API.

Ejemplo con todos los tipos de filtro:

```http
GET /api/tickets/dashboard?start_date=2026-07-01&end_date=2026-07-31&responsible_ids[]=8&responsible_ids[]=9&requester_ids[]=12&statuses[]=OPEN&statuses[]=IN_PROGRESS&types[]=INCIDENT&types[]=SERVICE_REQUEST&categories[]=SOFTWARE&categories[]=ACCESS
```

Ejemplo con cURL:

```bash
curl --globoff --request GET \
  --url "https://sistemas-ti.cechriza.com/api/tickets/dashboard?statuses[]=OPEN&statuses[]=IN_PROGRESS&types[]=INCIDENT" \
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
      "responsible_ids": [8, 9],
      "requester_ids": [12],
      "statuses": ["OPEN", "IN_PROGRESS"],
      "types": ["INCIDENT", "SERVICE_REQUEST"],
      "categories": ["SOFTWARE", "ACCESS"]
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
    "tickets": [
      {
        "id": 125,
        "title": "No puedo ingresar al ERP",
        "status": "IN_PROGRESS",
        "priority": "HIGH",
        "type": "INCIDENT",
        "category": "SOFTWARE",
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
    ],
    "recent_tickets": [
      {
        "id": 125,
        "title": "No puedo ingresar al ERP",
        "status": "IN_PROGRESS",
        "priority": "HIGH",
        "type": "INCIDENT",
        "category": "SOFTWARE",
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
valores permitidos, aunque su conteo sea cero. `tickets` contiene todos los tickets que coinciden
con los filtros, ordenados del más reciente al más antiguo. `recent_tickets` y `technicians`
devuelven como máximo ocho registros.

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

El resumen, distribuciones, desempeño y las colecciones de tickets forman una cohorte por
`created_at`.
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

- `responsible_ids` o `requester_ids` no son arrays, contienen duplicados o incluyen IDs inexistentes.
- Algún elemento de `statuses` no es `OPEN`, `IN_PROGRESS`, `ON_HOLD`, `RESOLVED` ni `CLOSED`.
- Algún elemento de `types` no es `INCIDENT` ni `SERVICE_REQUEST`.
- Algún elemento de `categories` no es `ACCESS`, `SOFTWARE` ni `EQUIPMENT`.
