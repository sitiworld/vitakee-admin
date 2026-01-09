# Second Opinion Requests API (`SecondOpinionRequestsModel` + Controller)

Guía rápida en **Markdown** de qué recibe cada función (entrada) y qué devuelve (salida), organizada por **ruta** del controlador. Incluye ejemplos de `curl`.

---

## Resumen

- **Entidad principal**: `second_opinion_requests`
- **PK**: `second_opinion_id` (UUID v4)
- **Nuevos campos**:
  - `pricing_id`: UUID que referencia a `specialist_pricing`
  - `type_request`: `document_review | appointment_request`
  - `scope_request`: `share_none | share_all | share_custom`
  - `cost_request`: número decimal con dos decimales (ej: `123.45`)
- **Respuestas JSON** (todas):  
  ```json
  { "value": true|false, "message": "string", "data": [...] }
  ```
- **Errores**: lanzan `mysqli_sql_exception` → el controlador responde `400` con `"value": false`.

---

## Rutas y acciones

### 1) `GET /second-opinion-requests` → `SecondOpinionRequestsController::getAll()`

**Entrada**: sin parámetros.

**Salida (`200`)**: lista de solicitudes (ordenadas por `created_at DESC`).  
Cada item incluye, cuando es posible, un bloque `information`:

```json
{
  "second_opinion_id": "UUID",
  "user_id": "UUID",
  "specialist_id": "UUID",
  "pricing_id": "UUID|null",
  "status": "PENDING|APPROVED|REJECTED|...",
  "type_request": "document_review|appointment_request",
  "scope_request": "share_none|share_all|share_custom|null",
  "cost_request": "123.45|null",
  "notes": "string",
  "shared_until": "YYYY-MM-DD HH:MM:SS|null",
  "created_at": "YYYY-MM-DD HH:MM:SS",
  "created_by": "UUID|null",
  "updated_at": "YYYY-MM-DD HH:MM:SS|null",
  "updated_by": "UUID|null",
  "deleted_at": null,
  "deleted_by": null,
  "information": [ ... ]
}
```

---

### 2) `GET /second-opinion-requests/{id}` → `getById($parametros)`

**Entrada**:
- `id`: `string` (UUID).

**Salida (`200`)**: un objeto de solicitud con `information` (mismo formato que en `getAll`).

---

### 3) `POST /second-opinion-requests` → `create()`

**Entrada** (JSON puro **o** `form-data`):
- **Requeridos**:
  - `user_id`: `UUID`
  - `specialist_id`: `UUID`
  - `type_request`: `document_review|appointment_request`
- **Opcionales** (persisten en la tabla principal):
  - `pricing_id`: UUID de la tabla `specialist_pricing`
  - `scope_request`: `share_none|share_all|share_custom`
  - `cost_request`: número decimal (`123.45`)
  - `status`: `string` (default: `"PENDING"`)
  - `notes`: `string` (default: `""`)
  - `shared_until`: `"YYYY-MM-DD HH:MM:SS"|null`
- **Opcionales para crear filas relacionadas en `second_opinion_data`**:
  - `panel_id`: `UUID`  
  - `biomarkers_selects`: array **o** JSON serializado.
  - `exams`: array **o** JSON serializado.

**Salida (`200`)**:
```json
{
  "value": true,
  "message": "Second opinion request created successfully",
  "data": [ { "second_opinion_id": "UUID" } ]
}
```

---

### 4) `PUT /second-opinion-requests/{id}` → `update($parametros)`

**Entrada**:
- `id`: `UUID` (en la ruta)
- Cuerpo **JSON** o `form-data`:
  - **Requerido**:
    - `status`: `string`
  - **Opcionales**:
    - `pricing_id`: UUID de la tabla `specialist_pricing`
    - `notes`: `string`
    - `shared_until`: fecha/hora
    - `type_request`: `document_review|appointment_request`
    - `scope_request`: `share_none|share_all|share_custom`
    - `cost_request`: número decimal

**Salida (`200`)**:
```json
{ "value": true, "message": "Request updated successfully", "data": [] }
```

---

### 5) `DELETE /second-opinion-requests/{id}` → `delete($parametros)`

Sin cambios (borrado lógico).

---

## NUEVAS FUNCIONES DE CAMBIO DE ESTADO

### 6) `POST /second-opinion/requests/{id}/to-awaiting-payment` → `setAwaitingPayment($parametros)`
**Entrada**:
- `id`: `UUID` (en la ruta)

**Salida (`200`)**:
```json
{ "value": true, "message": "Status updated successfully.", "data": [] }
```
**Condición:** cambia a `awaiting_payment` solo si el estado actual es `pending`.

---

### 7) `POST /second-opinion/requests/{id}/to-upcoming` → `setUpcoming($parametros)`
**Entrada**:
- `id`: `UUID` (en la ruta)

**Salida (`200`)**:
```json
{ "value": true, "message": "Status updated successfully.", "data": [] }
```
**Condición:** cambia a `upcoming` solo si el estado actual es `awaiting_payment`.

---

### 8) `POST /second-opinion/requests/{id}/to-completed` → `setCompleted($parametros)`
**Entrada**:
- `id`: `UUID` (en la ruta)

**Salida (`200`)**:
```json
{ "value": true, "message": "Status updated successfully.", "data": [] }
```
**Condición:** cambia a `completed` solo si el estado actual es `upcoming`.

---

### 9) `POST /second-opinion/requests/{id}/cancel` → `setCancelled($parametros)`
**Entrada**:
- `id`: `UUID` (en la ruta)

**Salida (`200`)**:
```json
{ "value": true, "message": "Request cancelled successfully.", "data": [] }
```
**Condición:** cambia a `cancelled` si el estado actual **no** es `completed`, `cancelled` o `rejected`.

---

### 10) `POST /second-opinion/requests/{id}/reject` → `setRejected($parametros)`
**Entrada**:
- `id`: `UUID` (en la ruta)

**Salida (`200`)**:
```json
{ "value": true, "message": "Request rejected successfully.", "data": [] }
```
**Condición:** cambia a `rejected` si el estado actual **no** es `completed`, `cancelled` o `rejected`.

---

## Ejemplo de flujo de estado

1. **Creación inicial** → `status = pending`
2. **Pago iniciado** → `POST /to-awaiting-payment`
3. **Confirmación de cita** → `POST /to-upcoming`
4. **Finalización del servicio** → `POST /to-completed`
5. **Cancelación o rechazo** → `POST /cancel` o `POST /reject`

---

## Rutas asociadas a cambios de estado

```php
$router->post('/second-opinion/requests/{id}/to-awaiting-payment', [
    'controlador' => SecondOpinionRequestsController::class,
    'accion' => 'setAwaitingPayment',
    'roles' => ['specialist', 'user']
]);

$router->post('/second-opinion/requests/{id}/to-upcoming', [
    'controlador' => SecondOpinionRequestsController::class,
    'accion' => 'setUpcoming',
    'roles' => ['specialist', 'user']
]);

$router->post('/second-opinion/requests/{id}/to-completed', [
    'controlador' => SecondOpinionRequestsController::class,
    'accion' => 'setCompleted',
    'roles' => ['specialist', 'user']
]);

$router->post('/second-opinion/requests/{id}/cancel', [
    'controlador' => SecondOpinionRequestsController::class,
    'accion' => 'setCancelled',
    'roles' => ['specialist', 'user']
]);

$router->post('/second-opinion/requests/{id}/reject', [
    'controlador' => SecondOpinionRequestsController::class,
    'accion' => 'setRejected',
    'roles' => ['specialist', 'user']
]);
```

---

## Notas finales

- El controlador mezcla `$_POST` con JSON raw.
- En `update` se admite `_method=PUT`.
- Los campos `type_request`, `scope_request`, `cost_request` y los nuevos cambios de estado forman parte del contrato de la API.

