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

**Ejemplo `curl`**:
```bash
curl -X POST http://localhost/second-opinion-requests \
  -H "Content-Type: application/json" \
  -d '{
    "user_id":"<UUID-user>",
    "specialist_id":"<UUID-spec>",
    "type_request":"appointment_request",
    "scope_request":"share_custom",
    "cost_request":"200.00",
    "status":"PENDING",
    "notes":"Primera consulta",
    "panel_id":"<UUID-panel>",
    "biomarkers_selects":[{"id":"<UUID-b1>"},{"id":"<UUID-b2>"}],
    "exams":["<UUID-rec1>","<UUID-rec2>"]
  }'
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

**Ejemplo `curl` (JSON PUT)**:
```bash
curl -X PUT http://localhost/second-opinion-requests/<UUID> \
  -H "Content-Type: application/json" \
  -d '{
    "status":"APPROVED",
    "type_request":"document_review",
    "scope_request":"share_all",
    "cost_request":"150.00",
    "notes":"Ok, aprobado"
  }'
```

---

### 5) `DELETE /second-opinion-requests/{id}` → `delete($parametros)`

Sin cambios (borrado lógico).

---

## Detalles del Modelo (público)

### `SecondOpinionRequestsModel::getAll(): array`
- Devuelve todas las filas con los campos nuevos: `type_request`, `scope_request`, `cost_request`, `pricing_id` + objeto `pricing`.

### `SecondOpinionRequestsModel::getById(string $id): ?array`
- Devuelve fila única con `information` + los campos nuevos, incluyendo `pricing_id` y objeto `pricing`.

### `SecondOpinionRequestsModel::create(array $data): string`
- **Requeridos**: `user_id`, `specialist_id`, `type_request`
- **Opcionales**:
    - `pricing_id`: UUID de la tabla `specialist_pricing` `scope_request`, `cost_request`, `status`, `notes`, `shared_until`, relaciones (`panel_id`, `biomarkers_selects`, `exams`).

### `SecondOpinionRequestsModel::update(string $id, array $data): bool`
- **Requeridos**: `status`
- **Opcionales**:
    - `pricing_id`: UUID de la tabla `specialist_pricing` `notes`, `shared_until`, `type_request`, `scope_request`, `cost_request`.

### `SecondOpinionRequestsModel::delete(string $id): bool`
- Sin cambios.

---

## Reglas importantes y convenciones internas

- **Validación estricta**:
  - `type_request`: solo `document_review` o `appointment_request`.
  - `scope_request`: solo `share_none`, `share_all`, `share_custom`.
  - `cost_request`: validado como número decimal con dos decimales.
- **Transacciones**: `create`, `update`, `delete` abren transacción y aplican contexto de auditoría.
- **Resolución de panel/records**: se mantiene igual.
- **Formas admitidas de listas de IDs**: se mantienen igual.

---

## Ejemplo de ciclo completo

1) **Crear**:
```bash
curl -X POST http://localhost/second-opinion-requests \
  -H "Content-Type: application/json" \
  -d '{
    "user_id":"<UUID-user>",
    "specialist_id":"<UUID-spec>",
    "type_request":"appointment_request",
    "scope_request":"share_none",
    "cost_request":"100.00",
    "status":"PENDING",
    "notes":"Notas"
 }'
```

2) **Consultar**:
```bash
curl http://localhost/second-opinion-requests/<UUID-devuelto>
```

3) **Actualizar**:
```bash
curl -X PUT http://localhost/second-opinion-requests/<UUID-devuelto> \
  -H "Content-Type: application/json" \
  -d '{"status":"APPROVED","scope_request":"share_all","cost_request":"200.00"}'
```

4) **Eliminar (lógico)**:
```bash
curl -X DELETE http://localhost/second-opinion-requests/<UUID-devuelto>
```

---

## Notas finales

- El controlador mezcla `$_POST` con JSON raw.
- En `update` se admite `_method=PUT`.
- Los campos `type_request`, `scope_request`, `cost_request` ahora forman parte del contrato de la API.

