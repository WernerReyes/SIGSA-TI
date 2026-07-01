# API de Tickets

Base URL: `https://sistemas-ti.cechriza.com/api`

Usar el header `Accept: application/json` en todas las solicitudes. Para enviar imagenes, usar
`multipart/form-data`; si no se envian imagenes, tambien se puede usar `application/json`.

## Autenticacion por API key

Todas las rutas de tickets requieren una API key configurada en el servidor:

```env
ACCESS_API=elGR1asaKISMNSHKt6RkBBLzTQRnycRM
```

El cliente debe enviar la misma key en uno de estos formatos:

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

1. El cliente externo envia la solicitud a `/api/tickets`.
2. `StoreTicketRequest` valida los campos. En rutas API, `requester_id` es obligatorio y debe existir en `ost_staff.staff_id`.
3. `StoreTicketDto` normaliza los datos, limpia `category` cuando el tipo no es `SERVICE_REQUEST` y calcula `priority` usando `impact` + `urgency`.
4. `TicketService` crea o actualiza el ticket, calcula vencimientos SLA y registra el historial.
5. La API responde en JSON con `message`, `data` o `error`, segun corresponda.

## Valores permitidos

`type`:

```text
INCIDENT
SERVICE_REQUEST
```

`impact` y `urgency`:

```text
LOW
MEDIUM
HIGH
```

`category`:

```text
ACCESS
SOFTWARE
EQUIPMENT
```

`status`:

```text
OPEN
IN_PROGRESS
ON_HOLD
RESOLVED
CLOSED
```

`priority`:

```text
LOW
MEDIUM
HIGH
URGENT
```

`priority` no se envia desde el cliente. El sistema la calcula automaticamente.

## Listar tickets

```http
GET /api/tickets
```

Parametros opcionales:

| Parametro | Tipo | Descripcion |
| --- | --- | --- |
| `searchTerm` | string | Busca por titulo o descripcion. |
| `requesters[]` | array<int> | Filtra por solicitantes. |
| `responsibles[]` | array<int> | Filtra por responsables. |
| `statuses[]` | array<string> | Filtra por estado. |
| `types[]` | array<string> | Filtra por tipo. |
| `priorities[]` | array<string> | Filtra por prioridad calculada. |
| `categories[]` | array<string> | Filtra por categoria. |
| `startDate` | date `YYYY-MM-DD` | Fecha minima de creacion. |
| `endDate` | date `YYYY-MM-DD` | Fecha maxima de creacion. |

Ejemplo:

```http
GET /api/tickets?searchTerm=correo&requesters[]=12&statuses[]=OPEN
```

Respuesta `200`:

```json
{
  "data": [
    {
      "id": 15,
      "title": "No tengo acceso al correo",
      "description": "El usuario no puede ingresar a su correo corporativo.",
      "status": "OPEN",
      "impact": "MEDIUM",
      "urgency": "HIGH",
      "priority": "HIGH",
      "type": "INCIDENT",
      "category": null,
      "requester_id": 12,
      "responsible_id": null,
      "images": [],
      "images_urls": []
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1
  },
  "links": {
    "first": "http://localhost/api/tickets?page=1",
    "last": "http://localhost/api/tickets?page=1",
    "prev": null,
    "next": null
  }
}
```

## Ver detalle de ticket

```http
GET /api/tickets/{id}
```

Respuesta `200`:

```json
{
  "data": {
    "id": 15,
    "title": "No tengo acceso al correo",
    "description": "El usuario no puede ingresar a su correo corporativo.",
    "status": "OPEN",
    "impact": "MEDIUM",
    "urgency": "HIGH",
    "priority": "HIGH",
    "type": "INCIDENT",
    "category": null,
    "requester_id": 12,
    "responsible_id": null,
    "requester": {
      "staff_id": 12,
      "firstname": "Juan",
      "lastname": "Perez",
      "department": {
        "id": 3,
        "name": "Administracion"
      }
    },
    "responsible": null,
    "images_urls": []
  }
}
```

Si no existe:

```json
{
  "error": "Ticket no encontrado."
}
```

## Crear ticket

```http
POST /api/tickets
```

Cuerpo JSON:

```json
{
  "title": "No tengo acceso al correo",
  "description": "El usuario no puede ingresar a su correo corporativo.",
  "type": "INCIDENT",
  "impact": "MEDIUM",
  "urgency": "HIGH",
  "category": null,
  "requester_id": 12
}
```

Cuerpo `multipart/form-data` con imagenes:

```text
title=No tengo acceso al correo
description=El usuario no puede ingresar a su correo corporativo.
type=INCIDENT
impact=MEDIUM
urgency=HIGH
requester_id=12
images[]=archivo1.png
images[]=archivo2.jpg
```

Reglas principales:

| Campo | Requerido | Regla |
| --- | --- | --- |
| `title` | Si | Texto de 5 a 255 caracteres. |
| `description` | Si | Texto de 10 a 1000 caracteres. |
| `type` | Si | `INCIDENT` o `SERVICE_REQUEST`. |
| `impact` | Si | `LOW`, `MEDIUM` o `HIGH`. |
| `urgency` | Si | `LOW`, `MEDIUM` o `HIGH`. |
| `category` | Solo si `type=SERVICE_REQUEST` | `ACCESS`, `SOFTWARE` o `EQUIPMENT`. |
| `requester_id` | Si, en API | Debe existir en `ost_staff.staff_id`. |
| `images[]` | No | Maximo 5 imagenes, 2 MB cada una. |

Respuesta `201`:

```json
{
  "message": "El ticket ha sido creado exitosamente.",
  "data": {
    "id": 15,
    "title": "No tengo acceso al correo",
    "status": "OPEN",
    "impact": "MEDIUM",
    "urgency": "HIGH",
    "priority": "HIGH",
    "type": "INCIDENT",
    "category": null,
    "requester_id": 12
  }
}
```

## Actualizar ticket

```http
PUT /api/tickets/{id}
```

Usa el mismo cuerpo de creacion. `requester_id` sigue siendo obligatorio porque el servicio lo usa
para validar que el solicitante sea el dueno del ticket.

Ejemplo:

```json
{
  "title": "No puedo acceder al correo corporativo",
  "description": "El usuario recibe error de credenciales al ingresar.",
  "type": "INCIDENT",
  "impact": "HIGH",
  "urgency": "HIGH",
  "category": null,
  "requester_id": 12
}
```

Reglas de negocio para actualizar:

| Regla | Resultado si falla |
| --- | --- |
| El ticket debe existir. | `404` |
| `requester_id` debe coincidir con el solicitante original. | `400` |
| El ticket debe estar en estado `OPEN`. | `400` |
| El ticket no debe tener responsable asignado. | `400` |

Respuesta `200`:

```json
{
  "message": "El ticket ha sido actualizado exitosamente.",
  "data": {
    "id": 15,
    "title": "No puedo acceder al correo corporativo",
    "status": "OPEN",
    "impact": "HIGH",
    "urgency": "HIGH",
    "priority": "URGENT",
    "type": "INCIDENT",
    "category": null,
    "requester_id": 12
  }
}
```

## Cerrar ticket

```http
POST /api/tickets/{id}/close
```

No requiere cuerpo. La API siempre envia internamente el estado `CLOSED` al servicio.

Reglas de negocio:

| Regla | Resultado si falla |
| --- | --- |
| El ticket debe existir. | `404` |
| El ticket debe tener responsable asignado. | `400` |
| El ticket debe poder pasar de su estado actual a `CLOSED`. | `400` |

Por las reglas actuales del servicio, el cierre valido es desde `RESOLVED` hacia `CLOSED`.
Como el consumo externo no usa sesion, no se valida `auth()->user()->staff_id`; la seguridad
de esta operacion depende de la API key `ACCESS_API`.

Respuesta `200`:

```json
{
  "message": "Cerrado el ticket",
  "data": {
    "id": 15,
    "title": "No puedo acceder al correo corporativo",
    "status": "CLOSED",
    "requester_id": 12,
    "responsible_id": 8
  }
}
```

## Eliminar ticket

```http
DELETE /api/tickets/{id}
```

No requiere cuerpo. Como el consumo externo no usa sesion, no se valida
`auth()->user()->staff_id`; la seguridad de esta operacion depende de la API key `ACCESS_API`.

Reglas de negocio:

| Regla | Resultado si falla |
| --- | --- |
| El ticket debe existir. | `404` |
| El ticket debe estar en estado `OPEN`. | `400` |
| El ticket no debe tener responsable asignado. | `400` |

Respuesta `200`:

```json
{
  "message": "El ticket ha sido eliminado exitosamente."
}
```

## Respuestas de error

Validacion `422`:

```json
{
  "message": "El ID del solicitante es obligatorio para solicitudes API.",
  "errors": {
    "requester_id": [
      "El ID del solicitante es obligatorio para solicitudes API."
    ]
  }
}
```

Error de negocio o servidor:

```json
{
  "error": "Solo se pueden actualizar los tickets en estado 'Abierto'."
}
```
