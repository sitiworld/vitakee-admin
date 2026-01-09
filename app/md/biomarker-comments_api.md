# Biomarker Comments API (`CommentBiomarkerModel` + Controller)

Guía en **Markdown** de qué recibe cada función (entrada) y qué devuelve (salida), organizada por **ruta** del controlador. Incluye ejemplos de `curl` y detalles clave del **modelo**.

---

## Resumen

- **Entidad**: `comment_biomarker`
- **PK**: `comment_biomarker_id` (UUID v4)
- **FKs**:
  - `id_test_panel` → `test_panels.panel_id`
  - `id_biomarker` → `biomarkers.biomarker_id`
- **Respuestas JSON** (todas):
  ```json
  { "value": true|false, "message": "string", "data": [...] }
  ```
- **Errores**: `mysqli_sql_exception` / excepciones → controlador responde `400` o `500` según el caso.
- **Auditoría / Zona horaria**: usa `ClientEnvironmentInfo` y `TimezoneManager` para `created_at`, `updated_at`, `deleted_at` y `*_by` con `$_SESSION['user_id']`.

---

## Rutas y acciones del controlador

### 1) `GET /biomarker-comments/{panel}/{test}` → `CommentBiomarkerController::showCommentsByPanelAndTest()`

**Entrada**:
- `panel`: `UUID` (panel_id del test panel)
- `test`: `UUID` (ID del registro dentro del panel)

**Salida (`200`)**: lista de comentarios con sus biomarcadores asociados.

```json
{
  "value": true,
  "message": "Comments fetched successfully",
  "data": [
    {
      "comment_biomarker_id": "UUID",
      "id_test_panel": "UUID",
      "id_test": "UUID",
      "id_biomarker": "UUID",
      "id_specialist": "UUID",
      "comment": "Texto del comentario",
      "biomarker_name": "LDL Cholesterol",
      "user_id": "UUID",
      "extra_data": { "ldl": 130, "hdl": 40, "triglycerides": 120 }
    }
  ]
}
```

**`curl`**:
```bash
curl -s http://localhost/biomarker-comments/81054d57-92c9-4df8-a6dc-51334c1d82c4/9acddcb1-0f43-4373-9a6b-35640ef323e9
```

---

### 2) `GET /biomarker-comment/{id}` → `CommentBiomarkerController::showCommentById()`

**Entrada**:
- `id`: `string` (UUID)

**Salida (`200`)**: devuelve un comentario individual.

```json
{
  "value": true,
  "message": "",
  "data": {
    "comment_biomarker_id": "UUID",
    "id_test_panel": "UUID",
    "id_test": "UUID",
    "id_biomarker": "UUID",
    "id_specialist": "UUID",
    "comment": "Comentario del especialista",
    "biomarker_name": "Glucose"
  }
}
```

**`curl`**:
```bash
curl -s http://localhost/biomarker-comment/2f0c9a2a-bb0a-4c7a-9c7f-1a2b3c4d5e6f
```

---

### 3) `GET /biomarker-comments/specialist/{id_specialist}` → `CommentBiomarkerController::showCommentsBySpecialist()`

**Entrada**:
- `id_specialist`: `string` (UUID)

**Salida (`200`)**: lista de comentarios realizados por el especialista.

```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "comment_biomarker_id": "UUID",
      "id_test_panel": "UUID",
      "id_test": "UUID",
      "id_biomarker": "UUID",
      "id_specialist": "UUID",
      "comment": "Texto del comentario",
      "biomarker_name": "HDL Cholesterol",
      "created_at": "YYYY-MM-DD HH:MM:SS",
      "updated_at": null,
      "user_id": "UUID",
      "extra_data": { "hdl": 50, "ldl": 120 }
    }
  ]
}
```

**`curl`**:
```bash
curl -s http://localhost/biomarker-comments/specialist/2a0c9b12-d45e-4aa3-8c7b-4acb1d2b6a7e
```

---

### 4) `POST /biomarker-comments` → `CommentBiomarkerController::createComment()`

**Entrada (`application/x-www-form-urlencoded`)**:
- **Requeridos**:
  - `id_test_panel`: `UUID`
  - `id_test`: `UUID`
  - `id_biomarker`: `UUID`
  - `comment`: `string`

**Reglas del modelo** (`upsert()`):
- Si ya existe una combinación (`id_test_panel`, `id_test`, `id_biomarker`) → **update()**
- Si no existe → **create()**
- Auditoría (`created_by`, `created_at`) automática.

**Salida (`201`)**:
```json
{ "value": true, "message": "Comment created successfully.", "data": [{ "id": "UUID" }] }
```

**`curl`**:
```bash
curl -X POST http://localhost/biomarker-comments   -d "id_test_panel=81054d57-92c9-4df8-a6dc-51334c1d82c4"   -d "id_test=9acddcb1-0f43-4373-9a6b-35640ef323e9"   -d "id_biomarker=134e2679-164c-45d2-9293-f88164abdce6"   -d "comment=Valores de glucosa dentro del rango normal."
```

---

### 5) `POST /biomarker-comments/{id}` → `CommentBiomarkerController::updateComment()`

**Entrada**:
- `id`: `string` (UUID)
- **Body (`application/x-www-form-urlencoded`)**:
  - `id_test_panel`: `UUID`
  - `id_test`: `UUID`
  - `id_biomarker`: `UUID`
  - `comment`: `string`

**Comportamiento**:
- Valida existencia.
- Si no existe → `"Comment not found for update."`
- Si existe → actualiza campos y setea `updated_at`, `updated_by`.

**Salida (`200`)**:
```json
{ "value": true, "message": "Comment updated successfully.", "data": [{ "id": "UUID" }] }
```

**`curl`**:
```bash
curl -X POST http://localhost/biomarker-comments/0fa70cd4-5bb0-41a0-9e6b-7cb5b3ab7c3e   -d "comment=Se recomienda mantener la dieta actual."
```

---

### 6) `DELETE /biomarker-comments/{id}` → `CommentBiomarkerController::deleteComment()`

**Entrada**:
- `id`: `UUID`

**Comportamiento**:
- Verifica existencia.
- Elimina de forma **lógica** (`deleted_at`, `deleted_by`).

**Salida (`200`)**:
```json
{ "value": true, "message": "Comment deleted successfully.", "data": [] }
```

**`curl`**:
```bash
curl -X DELETE http://localhost/biomarker-comments/0fa70cd4-5bb0-41a0-9e6b-7cb5b3ab7c3e
```

---

## Detalles del Modelo (`CommentBiomarkerModel`)

### `getAll(): array`
- **Recibe**: nada.  
- **Devuelve**: todos los comentarios activos (`deleted_at IS NULL`) con `biomarker_name`, `user_id` y `extra_data` según panel.

### `getCommentsByPanelAndTest(string $panelId, string $testId): array`
- **Recibe**: IDs del panel y test.  
- **Devuelve**: comentarios correspondientes.

### `getCommentsBySpecialist(string $id_specialist): array`
- **Recibe**: ID del especialista.  
- **Devuelve**: lista de comentarios realizados por él.

### `getById(string $id): ?array`
- **Recibe**: ID del comentario.  
- **Devuelve**: comentario con `user_id` y `extra_data` o `null` si no existe.

### `create(array $data): string`
- **Recibe**: datos del comentario.  
- **Devuelve**: ID generado (UUID).

### `update(array $data): bool`
- **Recibe**: ID y campos a actualizar.  
- **Devuelve**: `true` si se actualizó correctamente.

### `delete(string $id): bool`
- **Recibe**: ID.  
- **Devuelve**: `true` si se eliminó lógicamente.

### `upsert(array $data): array`
- **Recibe**: combinación (`id_test_panel`, `id_test`, `id_biomarker`) y `comment`.  
- **Devuelve**: resultado de `create()` o `update()` según corresponda.

---

## Códigos de estado y errores

- `200/201` → éxito (`value: true`)
- `400` → error de validación o DB (`value: false` + mensaje)
- `404` → no encontrado (`Comment not found`)
- `405` → método no permitido
- `500` → error inesperado

**Mensajes comunes**:
- `"Invalid panel or test ID"`  
- `"Comment not found for update."`  
- `"Error saving comment: ..."`  
- `"Error fetching comments: ..."`

---

## Rutas registradas

```php
$router->get('/biomarker-comments/{panel}/{test}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentsByPanelAndTest', 'roles' => ['user', 'specialist', 'administrator']]);
$router->get('/biomarker-comment/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentById', 'roles' => ['specialist', 'administrator']]);
$router->get('/biomarker-comments/specialist/{id_specialist}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'showCommentsBySpecialist', 'roles' => ['specialist', 'administrator']]);
$router->post('/biomarker-comments', ['controlador' => CommentBiomarkerController::class, 'accion' => 'createComment', 'roles' => ['specialist', 'administrator']]);
$router->post('/biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'updateComment', 'roles' => ['specialist', 'administrator']]);
$router->delete('/biomarker-comments/{id}', ['controlador' => CommentBiomarkerController::class, 'accion' => 'deleteComment', 'roles' => ['specialist', 'administrator']]);
```

---

## Notas finales

- Todos los identificadores usan **UUID (CHAR(36))**.
- Auditoría automática aplicada a `created_at`, `updated_at`, `deleted_at`.
- Eliminación **lógica**, no física.
- Especialistas y administradores tienen permisos de escritura y eliminación.
- Los usuarios pueden consultar comentarios de sus pruebas según su rol.

---

📅 **Última actualización:** Octubre 2025  
👤 **Autor:** Sistema Vitakee ERP / Módulo Biomarcadores  
📁 **Archivo:** `docs/biomarker-comments.md`
