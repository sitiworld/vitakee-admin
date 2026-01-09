# Specialist Locations API — README.md

> **Contexto**: Este documento describe **qué recibe y qué envía** el módulo de *Specialist Locations* (modelo, controlador y rutas) y **cómo funciona**: validaciones, reglas de negocio, auditoría, ejemplos de uso (cURL/XHR) y convenciones de respuesta.

---

## 1) Resumen del módulo

Gestiona las **ubicaciones** de un especialista. Cada especialista puede tener **múltiples ubicaciones** y **una sola** marcada como **principal** (`is_primary = 1`).

- **Modelo**: `SpecialistLocationsModel`
- **Controlador**: `SpecialistLocationsController`
- **Tabla**: `specialist_locations`
- **Rutas** (métodos REST): `GET /specialist-locations`, `GET /specialist-locations/{id}`, `POST /specialist-locations`, `PUT /specialist-locations/{id}`, `DELETE /specialist-locations/{id}`
- **Formato de respuesta** (en todos los endpoints):

```json
{
  "value": true,
  "message": "",
  "data": []
}
```

> `value`: bool, `message`: string, `data`: array (colección o un objeto envuelto en array).

---

## 2) Dependencias de sesión y auditoría

- El controlador **requiere sesión**. En `__construct()` hace `session_start()` si es necesario.
- Para determinar propietario: toma `$_SESSION['specialist_id']` o, en su defecto, `$_SESSION['user_id']`.
- El modelo usa **auditoría y TZ** si existen los helpers:
  - `ClientEnvironmentInfo::applyAuditContext($mysqli, $userId)`
  - `TimezoneManager::applyTimezone()`
  - Tiempos mediante `$env->getCurrentDatetime()`
- Las operaciones de **create**, **update**, **delete** se ejecutan en **transacciones** y llenan `created_at|by`, `updated_at|by` o `deleted_at|by`.

> Si los helpers no están disponibles (autoload/composer), descomentar los `require_once` o ajustar según tu estructura.

---

## 3) Esquema de datos (tabla `specialist_locations`)

| Campo           | Tipo        | Reglas / Notas                                         |
|-----------------|-------------|---------------------------------------------------------|
| `location_id`   | `char(36)`  | UUID v4 generado en el modelo al crear                 |
| `specialist_id` | `char(36)`  | **Requerido**. Dueño de la ubicación                   |
| `city_id`       | `char(36)`  | Opcional (FK a `cities.city_id`)                        |
| `state_id`      | `char(36)`  | Opcional (FK a `states.state_id`)                       |
| `country_id`    | `char(36)`  | Opcional (FK a `countries.country_id`)                  |
| `is_primary`    | `tinyint`   | 0/1. Al marcar 1, se desmarcan las demás del mismo especialista |
| `created_at/by` | `datetime`  | Seteado por el modelo                                   |
| `updated_at/by` | `datetime`  | Seteado por el modelo                                   |
| `deleted_at/by` | `datetime`  | **Borrado lógico**                                      |

> El modelo hace **LEFT JOIN** a `cities`, `states`, `countries` para exponer `city_name`, `state_name`, `country_name` en lecturas.

---

## 4) Reglas de negocio clave

1. **Un solo principal por especialista**: si `is_primary = 1` durante `create` o `update`, el modelo ejecuta `clearOtherPrimaries(specialist_id, exclude_location_id)` para desmarcar el resto.
2. **Ownership** (propiedad): el controlador valida que la ubicación a modificar/eliminar **pertenezca** al especialista de la sesión.
3. **Borrado lógico**: `delete(id)` marca `deleted_at` y `deleted_by`. Los `SELECT` filtran `deleted_at IS NULL`.
4. **Validaciones mínimas** en el controlador (entradas requeridas) y **tipos** en el modelo (bind params con `mysqli`).

---

## 5) Mapeo de inputs: ⚠️ Nota importante

- **Modelo** espera **IDs**: `city_id`, `state_id`, `country_id`.
- **Controlador (código pegado)** actualmente lee **strings** `city`, `state`, `country` (nombres).

### Recomendación
- Estandarizar **todo** a **IDs** en el controlador para alinearlo con el modelo y las FKs.
- Si se mantiene el ingreso por **nombres**, se debe **resolver** `city/state/country` → `city_id/state_id/country_id` antes de invocar el modelo.

Este README documenta **ambos** formatos de payload para claridad.

---

## 6) Rutas y roles

```php
// Specialist Locations
$router->get   ('/specialist-locations',        ['controlador' => SpecialistLocationsController::class, 'accion' => 'getAll',   'roles' => ['specialist']]);
$router->get   ('/specialist-locations/{id}',   ['controlador' => SpecialistLocationsController::class, 'accion' => 'getById',  'roles' => ['specialist']]);
$router->post  ('/specialist-locations',        ['controlador' => SpecialistLocationsController::class, 'accion' => 'create',  'roles' => ['specialist']]);
$router->put   ('/specialist-locations/{id}',   ['controlador' => SpecialistLocationsController::class, 'accion' => 'update',  'roles' => ['specialist']]);
$router->delete('/specialist-locations/{id}',   ['controlador' => SpecialistLocationsController::class, 'accion' => 'delete',  'roles' => ['specialist']]);
```

---

## 7) Endpoints — Especificación

### 7.1 GET `/specialist-locations`
**Descripción**: Lista ubicaciones. Si el modelo implementa `getAllBySpecialist($sid)`/`getBySpecialist($sid)`, el controlador las usa para **filtrar por el especialista en sesión**; si no, usa `getAll()`.

**Respuesta 200**
```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "location_id": "uuid",
      "specialist_id": "uuid",
      "city_id": "uuid|null",
      "state_id": "uuid|null",
      "country_id": "uuid|null",
      "is_primary": 1,
      "created_at": "2025-09-18 12:34:56",
      "created_by": "uuid",
      "updated_at": null,
      "updated_by": null,
      "deleted_at": null,
      "deleted_by": null,
      "city_name": "Caracas",
      "state_name": "Distrito Capital",
      "country_name": "Venezuela"
    }
  ]
}
```

**Errores**
- 400 con `value=false` si hay fallo de consulta.
- 401 si no hay specialist en sesión (si tu middleware lo exige para listar por usuario).

---

### 7.2 GET `/specialist-locations/{id}`
**Descripción**: Obtiene una ubicación por `location_id`. Valida formato de ID (numérico positivo o UUID v4). Si hay sesión, verifica **propiedad**.

**Respuesta 200**
```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "location_id": "uuid",
      "specialist_id": "uuid",
      "city_id": "uuid|null",
      "state_id": "uuid|null",
      "country_id": "uuid|null",
      "is_primary": 0,
      "city_name": "Maracaibo",
      "state_name": "Zulia",
      "country_name": "Venezuela"
    }
  ]
}
```

**Errores**
- 422/400 `Invalid ID` si el formato no es válido.
- 404 lógico (`value=false`, `Record not found`).
- 403 si el registro no pertenece al especialista autenticado.

---

### 7.3 POST `/specialist-locations`
**Descripción**: Crea una ubicación. **Requiere** especialista en sesión.

**Cuerpo — Opción A (recomendada, con IDs)**
`Content-Type: application/x-www-form-urlencoded` o `multipart/form-data`:
```
city_id=...&state_id=...&country_id=...&is_primary=0|1
```
> El controlador del ejemplo pegado usa `city/state/country` (nombres). Si mantienes esa versión, envía **Opción B**.

**Cuerpo — Opción B (nombres, si no usas IDs en el controller)**
```
city=Caracas&state=Distrito Capital&country=Venezuela&is_primary=1
```
*(recuerda resolver a IDs antes de invocar el modelo o adaptar el modelo a nombres)*

**Respuesta 200**
```json
{ "value": true, "message": "Location created successfully", "data": [] }
```
> El modelo retorna internamente `{ value:true, id:"uuid" }`, pero el controlador del snippet normaliza a mensaje sin exponer `id`. Si te conviene, puedes modificar `create()` del controlador para incluir `id` en `data`.

**Errores**
- 405 si no es `POST`.
- 422 `Missing fields: country, state, city` (según faltantes).
- 400 si falla la inserción/tx.

**Efectos laterales**
- Si `is_primary = 1`, el modelo desmarca otras ubicaciones del mismo `specialist_id`.

---

### 7.4 PUT `/specialist-locations/{id}`
**Descripción**: Actualiza una ubicación. Soporta override `POST + _method=PUT`.

**Cuerpo — Opción A (con IDs)**
```
city_id=...&state_id=...&country_id=...&is_primary=0|1
```

**Cuerpo — Opción B (nombres)**
```
city=...&state=...&country=...&is_primary=0|1
```

**Respuesta 200**
```json
{ "value": true, "message": "Location updated successfully", "data": [] }
```

**Errores**
- 405 si no es `PUT`.
- 422 `Invalid ID` o faltantes.
- 404 lógico si no existe.
- 403 si no pertenece al especialista autenticado.
- 400 por error en DB/tx.

**Efectos laterales**
- Si `is_primary = 1`, el modelo desmarca otras ubicaciones del mismo `specialist_id` excepto la actual.

---

### 7.5 DELETE `/specialist-locations/{id}`
**Descripción**: Borrado **lógico** de una ubicación. Soporta override `POST + _method=DELETE`.

**Respuesta 200**
```json
{ "value": true, "message": "Location deleted successfully", "data": [] }
```

**Errores**
- 405 si no es `DELETE`.
- 422 `Invalid ID`.
- 404 lógico si no existe.
- 403 si no pertenece al especialista autenticado.
- 400 por error en DB/tx.

---

## 8) Esquemas de respuesta (contrato)

Siempre:

```ts
interface ApiResponse<T = any> {
  value: boolean;      // éxito o error
  message: string;     // texto corto explicativo
  data: T[];           // arreglo (puede estar vacío). Para GET by id, el objeto viene en data[0]
}
```

**Objeto Location (lecturas)**
```ts
interface SpecialistLocation {
  location_id: string;
  specialist_id: string;
  city_id: string | null;
  state_id: string | null;
  country_id: string | null;
  is_primary: 0 | 1;
  created_at?: string | null;
  created_by?: string | null;
  updated_at?: string | null;
  updated_by?: string | null;
  deleted_at?: string | null;
  deleted_by?: string | null;
  // Enriquecidos por LEFT JOIN
  city_name?: string | null;
  state_name?: string | null;
  country_name?: string | null;
}
```

---

## 9) Ejemplos (cURL)

### Crear (IDs)
```bash
curl -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -b cookies.txt -c cookies.txt \
  -d "city_id=01e...&state_id=02f...&country_id=03a...&is_primary=1" \
  http://localhost/specialist-locations
```

### Crear (nombres)
```bash
curl -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -b cookies.txt -c cookies.txt \
  -d "city=Caracas&state=Distrito Capital&country=Venezuela&is_primary=1" \
  http://localhost/specialist-locations
```

### Actualizar
```bash
curl -X PUT \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -b cookies.txt -c cookies.txt \
  -d "city_id=...&state_id=...&country_id=...&is_primary=0" \
  http://localhost/specialist-locations/{location_id}
```

### Obtener todos
```bash
curl -X GET -b cookies.txt -c cookies.txt http://localhost/specialist-locations
```

### Obtener por ID
```bash
curl -X GET -b cookies.txt -c cookies.txt http://localhost/specialist-locations/{location_id}
```

### Eliminar
```bash
curl -X DELETE -b cookies.txt -c cookies.txt http://localhost/specialist-locations/{location_id}
```

---

## 10) Ejemplo XHR (vanilla JS)

```js
function createLocation(data) {
  const form = new URLSearchParams(data).toString();
  return fetch('/specialist-locations', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    credentials: 'include',
    body: form,
  }).then(r => r.json());
}

// Ejemplo (IDs):
createLocation({
  city_id: '01e-uuid',
  state_id: '02f-uuid',
  country_id: '03a-uuid',
  is_primary: 1,
}).then(console.log).catch(console.error);
```

---

## 11) Validaciones y errores

**Controlador**
- `requireSpecialistIdFromSession()` → 401 si no hay sesión válida.
- `isValidId()` valida numérico positivo o UUID (regex `[0-9a-fA-F-]{36}`) para rutas con `{id}`.
- `toBool01()` normaliza booleanos ("1,true,on,yes,si,sí").
- `cleanStr()` trunca strings a longitud segura.
- En `create`/`update` valida campos requeridos (`city/state/country` en versión por nombres).

**Modelo**
- `exists(id)` para `update/delete`.
- Uso de `prepare/bind_param` para evitar SQLi.
- Manejo de transacciones + rollback en errores.

**Códigos HTTP** (en `errorResponse`)
- 400: error de DB o petición mal formada.
- 401: sin sesión de especialista.
- 403: acceso a recurso de tercero.
- 405: método no permitido.
- 422: validación de inputs.

---

## 12) Auditoría y TZ

- Antes de escribir, el modelo ejecuta:
  - `applyAuditContext(...)` → fija variables de sesión/usuario/IP/etc. para triggers o auditoría.
  - `applyTimezone()` → asegura TZ correcta.
- Tiempos (`created_at`, `updated_at`, `deleted_at`) salen de `ClientEnvironmentInfo::getCurrentDatetime()`.

---

## 13) Extensiones útiles

- **Exponer `id` creado**: adaptar `SpecialistLocationsController::create()` para incluir `{ id }` del modelo en `data`.
- **Resolver nombres → IDs** en el controlador si decides mantener payload por nombres (consultando `countries`, `states`, `cities`).
- **Endpoints por especialista**: añadir `GET /specialist-locations/mine` que siempre use el `specialist_id` de sesión.

---

## 14) Seguridad

- **Propiedad obligatoria** en `update/delete`: `ensureOwnership()` corta con 403 si la ubicación no es del especialista autenticado.
- **Borrado lógico** evita pérdida de datos y facilita auditoría.
- **Prepared statements** en todas las operaciones.

---

## 15) Checklist de integración

- [ ] Confirmar que la sesión define `$_SESSION['specialist_id']` o `$_SESSION['user_id']`.
- [ ] Si se usan **nombres**, implementar resolución a **IDs** antes de invocar el modelo.
- [ ] Verificar existan FKs: `city_id` → `cities`, `state_id` → `states`, `country_id` → `countries`.
- [ ] Probar caso `is_primary=1` (debe desmarcar otras ubicaciones del especialista).
- [ ] Asegurar que las vistas/JS consuman el **contrato de respuesta** descrito.

---

## 16) Cambios futuros sugeridos

- Unificar definitivamente el **formato de entrada** a **IDs**.
- Añadir **índices** por `specialist_id` y (`specialist_id`, `is_primary`).
- Endpoint para **set primary** atómico: `PUT /specialist-locations/{id}/primary`.
- Añadir **paginación** y filtros en `GET /specialist-locations` (p.ej. `?primary=1`).

---

**Fin del documento** ✅

