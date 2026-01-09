# README — `/specialists/search` (searchByFilters)

Este README documenta el **payload**, el **comportamiento de combinación de filtros**, los **posibles conflictos** (y cómo tratarlos en UI), ejemplos de **requests/responses**, paginación/orden y consideraciones.

> **Resumen:** Puedes enviar **uno, varios o todos** los filtros a la vez. Todos los filtros se combinan con lógica **AND** salvo donde se indique lo contrario (por ejemplo, `languages` usa **OR** entre sus elementos). El filtro por **nombre** se combina con el resto (no los reemplaza).

---

## Endpoint

- **Ruta (sugerida)**: `POST /specialists/search`
- **Controlador**: `SpecialistController::searchFilters()`
- **Modelo**: `SpecialistModel::searchByFilters(array $f = [])`

---

## Cuerpo de la solicitud (JSON)

```jsonc
{
  "q": "john doe",              // opcional. alias: "name"
  "verified": true,             // opcional. boolean
  "specialty_ids": [            // opcional. array<uuid>
    "0bff4eaa-ed0d-44b6-9dd2-86de60e47e34"
  ],
  "languages": [ "EN", "ES" ],  // opcional. array<string>. Se aplica con OR
  "availability": {             // opcional. todos los campos son opcionales dentro del objeto
    "date": "2025-09-20",       // ISO yyyy-mm-dd. Convierte a weekday: Monday..Sunday
    "weekdays": ["Monday","Wednesday","Friday"], // strings en inglés (Monday..Sunday)
    "time_start": "09:00:00",   // HH:MM:SS (24h)
    "time_end":   "18:00:00"    // HH:MM:SS (24h)
  },
  "min_cost": 30.0,             // opcional. number (USD). Post-aggregate
  "min_rating": 4.3,            // opcional. number 0..5.   Post-aggregate
  "min_evaluations": 10,        // opcional. number (conteo de reviews). Post-aggregate
  "min_consultations": 25,      // opcional. number. Post-aggregate
  "order": "rating_cost",       // opcional. valores: "rating_cost" | (otro => fecha desc)
  "limit": 20,                  // opcional. entero > 0
  "offset": 0                   // opcional. entero >= 0
}
```

### Notas rápidas
- `q` y `name` son equivalentes; si llegan ambos y `q` está vacío, se usa `name`.
- `languages` se evalúa con **OR** entre elementos (al menos uno debe coincidir).
- `availability.date` y `availability.weekdays` **no** se necesitan juntos; con `date` es suficiente.
- `min_*` filtran **después** de las agregaciones (rating, conteos y min(price)).
- Ahora también se pueden filtrar y devolver los **specialties** asociados al especialista.

---

## Respuesta (formato de “cards”)

```jsonc
{
  "value": true,
  "message": "",
  "data": [
    {
      "specialist_id": "uuid",
      "full_name": "Jane Doe",
      "handle": "@Therapist",              // a partir del título traducido
      "website_url": "https://...",
      "avatar_url": "https://...",
      "lab_reports_evaluated": 3,          // VERIFICATION completadas
      "consultations_completed": 12,       // CONSULTATION completadas
      "avg_rating": 4.6,                   // null si no hay ratings
      "rating_text": "4.6/5",              // null si avg_rating es null
      "reviews_count": 7,
      "specialist_image": true,            // existe imagen de specialist
      "specialty_id": "uuid",              // id de la especialidad
      "specialty_display": "Psicología"     // nombre de la especialidad en idioma según sesión
    }
  ]
}
```

- **Orden:** por defecto `created_at_max DESC`. Con `order: "rating_cost"`, ordena por `avg_rating DESC` y **dentro** por `min_consult_price_for_filter ASC`. Los nulos van al final.
- **Paginación:** `limit` y `offset` son soportados.

---

## Combinación de filtros (AND / OR)

- Entre **diferentes filtros** (por ejemplo `verified` + `specialty_ids` + `availability`), la combinación es **AND**.
- **Dentro** de `languages`, la combinación es **OR** (basta con que coincida **un** idioma).
- Para `availability`:
  - Si envías **date**, se convierte a un `weekday` y se usa ese día;
  - Si **no** envías `date`, puedes enviar `weekdays` (uno o varios). Entre múltiples weekdays se usa `IN (...)` (equivalente a OR).
  - Para la franja horaria, si envías `time_start` y/o `time_end`, se comprueba el **solapamiento** de intervalos con la disponibilidad del especialista.

---

## Posibles conflictos y cómo alertar en la UI

*(Se mantienen los mismos conflictos que antes; ahora aplica también que `specialty_ids` deben coincidir con specialties válidas.)*

---

## Ejemplos de solicitudes

### 1) Solo por nombre
```http
POST /specialists/search
Content-Type: application/json

{ "q": "Ana Pérez" }
```

### 2) Nombre + especialidad + rating mínimo
```json
{
  "q": "Pérez",
  "specialty_ids": ["0bff4eaa-ed0d-44b6-9dd2-86de60e47e34"],
  "min_rating": 4.2
}
```

### 3) Idiomas (EN o ES) + verificados
```json
{
  "languages": ["EN","ES"],
  "verified": true
}
```

### 4) Disponibilidad por fecha concreta + franja horaria
```json
{
  "availability": {
    "date": "2025-09-20",
    "time_start": "10:00:00",
    "time_end":   "14:00:00"
  }
}
```

### 5) Disponibilidad por weekdays + costo/rating mínimos + orden compuesto
```json
{
  "availability": {
    "weekdays": ["Monday","Wednesday"],
    "time_start": "09:00:00",
    "time_end":   "18:00:00"
  },
  "min_cost": 25,
  "min_rating": 4,
  "order": "rating_cost",
  "limit": 12,
  "offset": 0
}
```

### 6) Filtros combinados (nombre + idiomas + no verificado + paginación)
```json
{
  "q": "john",
  "languages": ["EN"],
  "verified": false,
  "limit": 10,
  "offset": 20
}
```

---

## Cambios respecto a versiones previas

- Se integró la **búsqueda por nombre** (`q`/`name`) **combinable** con el resto de filtros.  
- Todos los filtros ahora son **opcionales** y **combinables** (AND), con OR interno para `languages`.  
- Se documentaron **conflictos** para guiar mensajes de UI.  
- **Nuevo:** se agregó el soporte para **specialties**: ahora se puede filtrar por `specialty_ids` y en la respuesta se devuelven `specialty_id` y `specialty_display` traducido.

---

¿Dudas o mejoras? Abre un issue e indica payload de ejemplo y resultado esperado.

