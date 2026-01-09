# Second Opinion Data API (`SecondOpinionDataModel` + Controller)

Guía en **Markdown** de qué recibe cada función (entrada) y qué devuelve (salida), organizada por **ruta** del controlador. Incluye ejemplos de `curl` y detalles clave del **modelo**.

---

## Resumen

- **Entidad**: `second_opinion_data`
- **PK**: `second_opinion_data_id` (UUID v4)
- **FK**: `second_opinion_id` → `second_opinion_requests.second_opinion_id`
- **Respuestas JSON** (todas):
  ```json
  { "value": true|false, "message": "string", "data": [...] }
  ```
- **Errores**: `mysqli_sql_exception` / excepciones → controlador responde `400` o `500` según el caso.
- **Auditoría / Zona horaria**: usa `ClientEnvironmentInfo` y `TimezoneManager` para `created_at`, `updated_at`, `deleted_at` y `*_by` con `$_SESSION['user_id']`.

---

## Rutas y acciones del controlador

### 1) `GET /second_opinion_data` → `SecondOpinionDataController::index()`

**Entrada**: sin parámetros.

**Salida (`200`)**: lista de filas de `second_opinion_data` (ordenadas por `created_at DESC`), cada una con resolución de datos del panel:

```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_data_id": "UUID",
      "second_opinion_id": "UUID",
      "share_type": "panel|biomarkers|records|null",
      "panel_id": "UUID|null",
      "biomarkers_id": "["UUID","UUID2"]|null",
      "records_id": "["UUID","UUID2"]|null",
      "created_at": "YYYY-MM-DD HH:MM:SS",
      "created_by": "UUID|null",
      "updated_at": "YYYY-MM-DD HH:MM:SS|null",
      "updated_by": "UUID|null",
      "deleted_at": null,
      "deleted_by": null,

      "second_opinion_request": { "...": "fila de second_opinion_requests" },
      "resolved_table": "nombre_tabla_panel|null",
      "resolved_pk": "pk_del_panel|null",
      "resolved_columns": ["user_id", "created_at", "biomarkers..."], 
      "resolved_rows": [ { "...": "registros del panel" } ]
    }
  ]
}
```

**Notas**:
- `resolved_*` proviene de `resolvePanelData()`:
  - Determina la tabla del panel desde `test_panels.panel_id → panel_name`.
  - `pk` por convención es `panel_name + '_id'` (o mapeo especial si lo agregas).
  - Si hay `biomarkers_id`, obtiene `name_db` desde `biomarkers` y **solo** selecciona columnas existentes (`baseCols + name_db`). Si no, usa `*`.
  - Si hay `records_id`, filtra por esos IDs; en todos los casos filtra por `user_id` del request, y `deleted_at IS NULL`.
  - Orden: `created_at DESC`.

**`curl`**:
```bash
curl -s http://localhost/second_opinion_data
```

---

### 2) `GET /second_opinion_data/{id}` → `show($params)`

**Entrada**:
- `id`: `string` (UUID).

**Salida (`200`)**: una fila con la misma expansión `resolved_*` que en `index()`.
- Si no existe: `404` con `{"value": false, "message": "Record not found"}` (o traducción).

**`curl`**:
```bash
curl -s http://localhost/second_opinion_data/2f0c9a2a-bb0a-4c7a-9c7f-1a2b3c4d5e6f
```

---

### 3) `GET /second_opinion_data/by-request/{second_opinion_id}` → `listByRequest($params)`

**Entrada**:
- `second_opinion_id`: `string` (UUID).

**Salida (`200`)**: lista de filas de `second_opinion_data` pertenecientes al request indicado (sin `resolved_*`).

**`curl`**:
```bash
curl -s http://localhost/second_opinion_data/by-request/157098d4-0490-431f-a339-b174f1c04164
```

---

### 4) `POST /second_opinion_data` → `create()`

**Entrada** (JSON o `form-data`). **Acepta entradas flexibles** y las normaliza a JSON arrays para `biomarkers_id` / `records_id`:

- **Requeridos**:
  - `second_opinion_id`: `UUID`
- **Opcionales**:
  - `share_type`: `"panel" | "biomarkers" | "records" | null`  
  - `panel_id`: `UUID|null` (**obligatorio** si envías `biomarkers_id` o `records_id`)
  - `biomarkers_id`: lista de IDs en cualquiera de estas formas:
    - `[{ "id": "UUID" }, ...]`
    - `["UUID","UUID2"]`
    - `"UUID,UUID2"` (CSV)
    - JSON string de cualquiera de las anteriores
  - `records_id`: misma regla que `biomarkers_id`

**Reglas del modelo** (`create()`):
- Si llega `biomarkers_id` o `records_id` y **no** hay `panel_id` → **error** `400` (“panel_id is required…”).
- **Upsert por (second_opinion_id, panel_id)** (si `panel_id` es `NULL`, coincide por `IS NULL`):
  - Si **ya existe** fila: **merge** de listas (`mergeJsonLists`) para `biomarkers_id`/`records_id`, `update` de `share_type` y auditoría.
  - Si **no existe**: `insert` con nuevo `second_opinion_data_id` (UUID).
- Transacción interna automática cuando `inTransaction = false` (por defecto).

**Salida (`201`)**:
```json
{
  "value": true,
  "message": "Created successfully",
  "data": [
    { "second_opinion_data_id": "UUID" }
  ]
}
```

**`curl` (JSON)**:
```bash
curl -X POST http://localhost/second_opinion_data   -H "Content-Type: application/json"   -d '{
        "second_opinion_id":"157098d4-0490-431f-a339-b174f1c04164",
        "share_type":"biomarkers",
        "panel_id":"renal_function",
        "biomarkers_id":[{"id":"b-1"},{"id":"b-2"}]
      }'
```

**`curl` (form-data)**:
```bash
curl -X POST http://localhost/second_opinion_data   -F "second_opinion_id=157098d4-0490-431f-a339-b174f1c04164"   -F "share_type=records"   -F "panel_id=renal_function"   -F "records_id=rec-1,rec-2"
```

---

### 5) `POST /second_opinion_data/{id}` → `update($params)`

**Entrada**:
- `id`: `UUID` (en ruta)
- **Body**: JSON o `form-data`

Campos que puedes enviar (todos **opcionales**; el método actualiza solo los recibidos):
- `share_type`: `string|null`
- `panel_id`: `UUID|null`
- `biomarkers_id`: lista de IDs (mismas formas de entrada que en `create()`); **se hace merge** con lo existente
- `records_id`: lista de IDs (idem); **se hace merge** con lo existente

**Reglas del modelo** (`update()`):
- Valida existencia con `ensureExistsById()` (si no existe → `Record not found`).
- Carga listas actuales (`biomarkers_id`, `records_id`) para **merge** con lo entrante (`mergeJsonLists`).
- Aplica auditoría (`updated_at`, `updated_by`).
- Transacción interna si no recibe `inTransaction`.

**Salida (`200`)**:
```json
{ "value": true, "message": "Updated successfully", "data": [{ "second_opinion_data_id": "UUID" }] }
```

**`curl`**:
```bash
curl -X POST http://localhost/second_opinion_data/6a44f0b0-84e1-4c9d-bb7d-1e2c3d4b5a6f   -H "Content-Type: application/json"   -d '{ "biomarkers_id": ["b-3","b-4"], "records_id": [{"id":"rec-3"}] }'
```

---

### 6) `DELETE /second_opinion_data/{id}` → `delete($params)`

**Entrada**:
- `id`: `UUID` (en ruta)

**Comportamiento**:
- **Borrado lógico**: setea `deleted_at`, `deleted_by` (con auditoría).

**Salida (`200`)**:
```json
{ "value": true, "message": "Deleted successfully", "data": [] }
```

**`curl`**:
```bash
curl -X DELETE http://localhost/second_opinion_data/6a44f0b0-84e1-4c9d-bb7d-1e2c3d4b5a6f
```

---

## Detalles del Modelo (público)

### `SecondOpinionDataModel::getAll(): array`
- **Recibe**: nada.
- **Devuelve**: todas las filas activas + `resolved_*` por cada fila.

### `SecondOpinionDataModel::getById(string $id): ?array`
- **Recibe**: `second_opinion_data_id`.
- **Devuelve**: fila única + `resolved_*` (o `null` si no existe).

### `SecondOpinionDataModel::listBySecondOpinionId(string $sid): array`
- **Recibe**: `second_opinion_id`.
- **Devuelve**: filas activas (sin `resolved_*`).

### `SecondOpinionDataModel::create(array $data, bool $inTransaction=false): string`
- **Recibe**:
  - `second_opinion_id` (**req**)
  - `share_type` (`"panel"|"biomarkers"|"records"|null`)
  - `panel_id` (`UUID|null`) → **requerido** si envías `biomarkers_id` o `records_id`
  - `biomarkers_id` y/o `records_id`: listas flexibles (array plano, array de objetos `{id}`, CSV, JSON string)
- **Devuelve**: `second_opinion_data_id` (nuevo o existente si se hizo merge).

### `SecondOpinionDataModel::update(string $id, array $data, bool $inTransaction=false): bool`
- **Recibe**: `id` y cualquier combinación de `share_type`, `panel_id`, `biomarkers_id`, `records_id`.
- **Devuelve**: `true` si el `UPDATE` fue ok (o no hay cambios).

### `SecondOpinionDataModel::delete(string $id): bool`
- **Recibe**: `id`.
- **Devuelve**: `true` si marcó borrado lógico (afectó 1+ filas).

---

## Normalización de listas (cómo interpreta `biomarkers_id` y `records_id`)

El modelo acepta múltiples formatos y los convierte a **JSON array** (o `NULL`) mediante:

- `jsonIds($input, 'id')`: convierte a JSON array **o** `NULL`:
  - `[{ "id": "A" }, {"id":"B"}]` → `["A","B"]`
  - `["A","B"]` → `["A","B"]`
  - `"A,B"` → `["A","B"]`
  - `"["A","B"]"` (string JSON) → `["A","B"]`
  - `"A"` → `["A"]`
- `mergeJsonLists($existing, $incoming)`: une y **deduplica**.

**Consejo**: si envías arrays desde JS, puedes mandar directamente `["ID1","ID2"]` o `[{id:"ID1"},{id:"ID2"}]`.

---

## Resolución de datos del panel (`resolvePanelData`) — cómo se construye `resolved_*`

1. Recupera la fila del request (`second_opinion_requests`) para obtener `user_id`.
2. Traduce `panel_id` → `panel_name` en `test_panels` (éste es el **nombre de la tabla** del panel).
3. Determina `pk` como `panel_name + '_id'` (o usa un mapeo especial que definas en `guessPk()`).
4. Construye el `SELECT`:
   - Si hay `biomarkers_id`: busca `name_db` en `biomarkers` y solo selecciona `[$pk, user_id, created_at, updated_at] + name_db_existentes`.
   - Si **no** hay `biomarkers_id`: usa `SELECT *`.
5. `WHERE`:
   - Siempre filtra por `user_id` (si existe en el request).
   - Si hay `records_id`, agrega `pk IN (...)`.
   - Siempre agrega `deleted_at IS NULL`.
6. Ordena por `created_at DESC`.
7. Devuelve:
   ```json
   { "table": "tabla", "pk": "tabla_id", "columns": ["..."], "rows": [ ... ], "request": { ... } }
   ```

---

## Códigos de estado y errores

- `200/201` → éxito (`value: true`).
- `400` → error de validación/DB (`value: false` + mensaje).
- `404` → no encontrado en `show()`.
- `405` → método no permitido en `index/create/update` según verificación.
- `500` → error inesperado.

**Mensajes comunes**:
- `"second_opinion_id is required"`
- `"panel_id is required when providing biomarkers_id or records_id"`
- `"Record not found"`
- `"Prepare failed: ..."` / `"Execute failed: ..."`

---

## Ejemplos de flujo completo

1) **Crear share de panel**:
```bash
curl -X POST http://localhost/second_opinion_data  -H "Content-Type: application/json"  -d '{
  "second_opinion_id":"<REQ-UUID>",
  "share_type":"panel",
  "panel_id":"<PANEL-UUID-OR-NAME>"
 }'
```

2) **Agregar biomarkers (merge)** sobre el mismo `(second_opinion_id, panel_id)`:
```bash
curl -X POST http://localhost/second_opinion_data  -H "Content-Type: application/json"  -d '{
  "second_opinion_id":"<REQ-UUID>",
  "share_type":"biomarkers",
  "panel_id":"<PANEL>",
  "biomarkers_id":["b1","b2","b3"]
 }'
```

3) **Agregar records (merge)**:
```bash
curl -X POST http://localhost/second_opinion_data  -H "Content-Type: application/json"  -d '{
  "second_opinion_id":"<REQ-UUID>",
  "share_type":"records",
  "panel_id":"<PANEL>",
  "records_id":[{"id":"r1"},{"id":"r2"}]
 }'
```

4) **Actualizar (merge)** la misma fila por `id`:
```bash
curl -X POST http://localhost/second_opinion_data/<DATA-ID>  -H "Content-Type: application/json"  -d '{
  "biomarkers_id":"b4,b5",
  "records_id":["r3","r4"]
 }'
```

5) **Eliminar lógico**:
```bash
curl -X DELETE http://localhost/second_opinion_data/<DATA-ID>
```

---

## Rutas registradas

```php
$router->agregarRuta('GET', 'second_opinion_data',                     ['controlador' => SecondOpinionDataController::class, 'accion' => 'index',        'roles' => ['administrator','specialist','user']]);
$router->agregarRuta('GET', 'second_opinion_data/{id}',               ['controlador' => SecondOpinionDataController::class, 'accion' => 'show',         'roles' => ['administrator','specialist','user']]);
$router->agregarRuta('GET', 'second_opinion_data/by-request/{second_opinion_id}', ['controlador' => SecondOpinionDataController::class, 'accion' => 'listByRequest', 'roles' => ['administrator','specialist','user']]);
$router->agregarRuta('POST','second_opinion_data',                    ['controlador' => SecondOpinionDataController::class, 'accion' => 'create',       'roles' => ['administrator','specialist','user']]);
$router->agregarRuta('POST','second_opinion_data/{id}',               ['controlador' => SecondOpinionDataController::class, 'accion' => 'update',       'roles' => ['administrator','specialist','user']]);
$router->agregarRuta('DELETE','second_opinion_data/{id}',             ['controlador' => SecondOpinionDataController::class, 'accion' => 'delete',       'roles' => ['administrator','specialist','user']]);
```

---

## Notas finales

- `SecondOpinionDataModel` acepta entradas **muy flexibles** para listas de IDs y se encarga de normalizarlas a JSON (`jsonIds`, `parseIdList`, `mergeJsonLists`).
- Los `resolved_*` ayudan a presentar datos del panel al **frontend** sin que éste tenga que conocer la estructura interna de cada tabla.
- `create()` hace **upsert** por `(second_opinion_id, panel_id)`: facilita agregar biomarcadores o exámenes en varias llamadas sin duplicar filas.
- Si tus tablas de panel usan una PK distinta a la convención, amplía `guessPk()` con tu mapeo.

