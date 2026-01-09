# Rutas y métodos: Test Panels (second)

Este documento describe **qué hace**, **qué recibe** y **qué retorna** cada una de las piezas que agregaste: rutas del router, métodos del *model* y acciones del *controller*.

---

## 1) Rutas agregadas al `Router`

### 1.1 `GET test-panels/second/records/{panel_id}`
- **Controlador**: `TestPanelController::class`
- **Acción**: `getPanelRecords`
- **Roles autorizados**: `user`, `specialist`, `administrator`
- **Path params**:
  - `panel_id` *(string, requerido)*: ID del panel de pruebas.
- **Qué hace**: invoca al controlador para traer **todos los registros crudos** de la tabla que corresponde al panel, deduciendo el nombre de tabla a partir de `test_panels.panel_name`.
- **Qué retorna**: JSON con `success`, `message` y `data` (lista de filas tal cual existen en la tabla del panel).

### 1.2 `GET test-panels/second/biomarkers/{panel_id}`
- **Controlador**: `TestPanelController::class`
- **Acción**: `getPanelBiomarkers`
- **Roles autorizados**: `user`, `specialist`, `administrator`
- **Path params**:
  - `panel_id` *(string, requerido)*.
- **Qué hace**: invoca al controlador para traer **biomarcadores** vinculados al panel, aplicando idioma (ES/EN) según `$_SESSION['idioma']`.
- **Qué retorna**: JSON con `success`, `message` y `data` (lista de biomarcadores con `biomarker_id`, `name` y `description` en el idioma activo cuando existan traducciones).

---

## 2) Métodos del **Model**

> A continuación se documentan los métodos que viven en tu *model* de paneles (p.ej. `TestPanelModel`).

### 2.1 `public function getBiomarkersByPanelId(string $panel_id): array`
- **Parámetros**:
  - `panel_id` *(string, requerido)*: ID del panel.
- **Qué hace**:
  1. Determina el idioma activo leyendo `$_SESSION['idioma']` (por defecto **EN**). Si es `ES`, intentará usar columnas en español.
  2. Ejecuta:
     ```sql
     SELECT biomarker_id, name, name_es, description, description_es
     FROM biomarkers
     WHERE panel_id = ? AND deleted_at IS NULL
     ORDER BY name ASC
     ```
  3. Construye un arreglo de salida donde `name` y `description` se resuelven en **ES** cuando `name_es/description_es` no estén vacíos y el idioma sea `ES`; de lo contrario, usa **EN**.
- **Retorno**:
  - `array` de biomarcadores con la forma:
    ```json
    [
      {
        "biomarker_id": "<uuid>",
        "name": "<name | name_es>",
        "description": "<description | description_es>"
      }
    ]
    ```
  - En caso de excepción, retorna `[]`.
- **Errores/Manejo**:
  - Si `prepare` falla lanza `mysqli_sql_exception`.
  - *Catch-all*: ante cualquier `Exception`, devuelve arreglo vacío (opcionalmente loguea).

---

### 2.2 `public function getAllRecordsByPanelId(string $panel_id): array`
- **Parámetros**:
  - `panel_id` *(string, requerido)*.
- **Qué hace**:
  1. **Resuelve el nombre de la tabla** asociada al panel mediante:
     ```sql
     SELECT panel_name
     FROM test_panels
     WHERE panel_id = ? AND deleted_at IS NULL
     ```
     - Si no encuentra el panel o `panel_name` viene vacío, lanza excepción.
  2. Construye la consulta base `SELECT * FROM \`{panel_name}\``.
     - Si la tabla posee columna `deleted_at` (verifica con `tableHasColumn`), agrega filtro `WHERE deleted_at IS NULL`.
  3. Ejecuta la consulta y **retorna todas las filas** como arreglo asociativo **sin transformar**.
- **Retorno**:
  - `array` de filas de la tabla concreta (estructura variable según el panel), p.ej.:
    ```json
    [
      {"lipid_profile_record_id":"...","user_id":"...","ldl":120,...},
      {"lipid_profile_record_id":"...","user_id":"...","ldl":95,...}
    ]
    ```
  - En caso de error, retorna `[]` y escribe en *error_log* un mensaje con el `panel_id` y el detalle.
- **Notas de seguridad**:
  - El nombre de la tabla se inserta entre *backticks* y se usa `real_escape_string` en el *helper* de columnas. Aun así, el origen del `panel_name` está bajo tu control vía DB, minimizando riesgo de inyección.

---

### 2.3 Helper privado `private function tableHasColumn(string $table, string $column): bool`
- **Parámetros**:
  - `table` *(string, requerido)*: nombre de la tabla.
  - `column` *(string, requerido)*: nombre de la columna a verificar.
- **Qué hace**: ejecuta `SHOW COLUMNS FROM \`{table}\` LIKE '{column}'` para saber si existe la columna.
- **Retorno**: `true` si hay al menos una coincidencia; `false` en caso contrario.

---

## 3) Acciones del **Controller** (`TestPanelController`)

> Ambas acciones usan `$this->jsonResponse($success, $message = '', $data = [])` como envoltorio estándar.

### 3.1 `public function getPanelRecords($params)`
- **Entrada (`$params`)**:
  - `panel_id` *(string, requerido)*.
- **Flujo**:
  1. Valida que `panel_id` exista y sea `string`.
  2. Llama a `$this->testPanelModel->getAllRecordsByPanelId($panel_id)`.
  3. Retorna respuesta JSON **exitoso** con los registros como `data`.
- **Salida (éxito)**:
  ```json
  {
    "success": true,
    "message": "",
    "data": [ { /* filas crudas de la tabla del panel */ } ]
  }
  ```
- **Salida (error)**:
  ```json
  {
    "success": false,
    "message": "Error al obtener registros: <detalle>",
    "data": []
  }
  ```

### 3.2 `public function getPanelBiomarkers($params)`
- **Entrada (`$params`)**:
  - `panel_id` *(string, requerido)*.
- **Flujo**:
  1. Valida `panel_id`.
  2. Llama a `$this->testPanelModel->getBiomarkersByPanelId($panel_id)`.
  3. Retorna respuesta JSON con biomarcadores ya resueltos al idioma activo.
- **Salida (éxito)**:
  ```json
  {
    "success": true,
    "message": "",
    "data": [
      {
        "biomarker_id": "...",
        "name": "Albumin | Albúmina",
        "description": "..."
      }
    ]
  }
  ```
- **Salida (error)**:
  ```json
  {
    "success": false,
    "message": "Error al obtener biomarcadores: <detalle>",
    "data": []
  }
  ```

---

## 4) Contratos de entrada/salida resumidos

| Recurso | Método | Ruta | Params | Respuesta `200` (estructura) |
|---|---|---|---|---|
| Registros por panel | GET | `/test-panels/second/records/{panel_id}` | `panel_id` (path) | `{ success: boolean, message: string, data: any[] }` |
| Biomarcadores por panel | GET | `/test-panels/second/biomarkers/{panel_id}` | `panel_id` (path) | `{ success: boolean, message: string, data: Biomarker[] }` |

**`Biomarker`**:
```ts
{
  biomarker_id: string;
  name: string;          // en ES o EN
  description: string;   // en ES o EN
}
```

---

## 5) Ejemplos rápidos de consumo

### 5.1 cURL – Registros del panel
```bash
curl -X GET \
  "https://<host>/test-panels/second/records/81054d57-92c9-4df8-a6dc-51334c1d82c4" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

### 5.2 cURL – Biomarcadores del panel
```bash
curl -X GET \
  "https://<host>/test-panels/second/biomarkers/e6861593-7327-4f63-9511-11d56f5398dc" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer <token>"
```

---

## 6) Consideraciones y buenas prácticas
- **Errores controlados**: el *model* atrapa excepciones y devuelve `[]`; el *controller* transforma errores en `success=false` con un `message` claro.
- **Localización (i18n)**: los biomarcadores cambian `name/description` según `$_SESSION['idioma']` (`ES` vs `EN`).
- **Soft-delete**: cuando las tablas lo soportan (`deleted_at`), los métodos sólo retornan registros **no eliminados lógicamente**.
- **Auditoría**: si activas *logging* (comentado), tendrás trazabilidad sin romper la respuesta.

---

## 7) Dependencias implícitas
- Tabla `test_panels` con columnas `panel_id`, `panel_name`, `deleted_at`.
- Tabla `biomarkers` con `biomarker_id`, `panel_id`, `name`, `name_es`, `description`, `description_es`, `deleted_at`.
- Método de controlador `jsonResponse(...)` y propiedad `testPanelModel` correctamente inyectada.

> **Listo.** Con esto ya tienes un resumen claro para teammates y para QA/API. Si quieres, agrego ejemplos de respuestas reales de tu BD o pruebas unitarias mínimas.

