# Specialist Search by Name

Este documento describe la nueva funcionalidad agregada al sistema para buscar especialistas por nombre completo, manteniendo la misma estructura de salida que `searchByFilters`.

---

## Descripción General

La nueva función permite realizar búsquedas por **nombre y apellido** de los especialistas mediante una ruta **GET**, mientras que la función existente `searchByFilters` usa una ruta **POST** con múltiples filtros.

- **Función de modelo:** `searchByName(string $q, array $opts = []): array`
- **Función de controlador:** `searchByName()`
- **Ruta:** `GET /specialists/search?q=...`

La salida mantiene el mismo formato de **cards** que `searchByFilters`.

---

## Modelo: `searchByName`

La función recibe un parámetro de búsqueda (`$q`) y opciones adicionales (`$opts`).

### Flujo

1. **Normalización del input**
   - Se limpia `$q` y se descarta si está vacío.

2. **Selección de idioma**
   - Determina si mostrar el título en `ES` o `EN`.

3. **Construcción de joins**
   - Se incluyen las mismas relaciones que en `searchByFilters`: títulos, transacciones, reviews y precios.

4. **Condiciones WHERE**
   - Base: `s.deleted_at IS NULL`.
   - Condición de búsqueda: compara `$q` contra:
     - `CONCAT(first_name, ' ', last_name)`
     - `CONCAT(last_name, ' ', first_name)`
     - Cada token del query debe aparecer en `CONCAT_WS(' ', first_name, last_name)`.

5. **Subquery con agregados**
   - Métricas incluidas:
     - `lab_reports_evaluated` → transacciones de tipo *VERIFICATION* completadas.
     - `consultations_completed` → transacciones de tipo *CONSULTATION* completadas.
     - `avg_rating` → promedio de calificaciones.
     - `reviews_count` → número de reviews.
     - `min_consult_price_for_filter` → precio mínimo de consulta.

6. **Ordenación y paginación**
   - Orden por `created_at_max DESC` o `rating_cost`.
   - Parámetros opcionales: `limit`, `offset`.

7. **Ejecución segura**
   - Se usa `prepare` y `bind_param` para evitar inyecciones SQL.

8. **Estructura de salida**
   - Se devuelve un array con:
     ```json
     [
       {
         "specialist_id": "...",
         "full_name": "Nombre Apellido",
         "handle": "@Titulo",
         "website_url": "...",
         "avatar_url": "...",
         "lab_reports_evaluated": 0,
         "consultations_completed": 0,
         "avg_rating": 4.5,
         "rating_text": "4.5/5",
         "reviews_count": 12
       }
     ]
     ```

---

## Controlador: `searchByName`

El controlador recibe los parámetros por `GET`:

- `q` → cadena de búsqueda (requerido).
- `limit` → límite de resultados.
- `offset` → desplazamiento.
- `order` → `'rating_cost'` o default (`created_at_max DESC`).

Ejemplo de uso:

```http
GET /specialists/search?q=ana%20garcia
GET /specialists/search?q=garcia&order=rating_cost&limit=10&offset=0
```

Respuestas:

- **Éxito** → JSON con `value: true`, lista de especialistas.
- **Error** → JSON con `value: false` y mensaje de error.

---

## Ruta

Se define en el router:

```php
$router->get('/specialists/search', [
    'controlador' => SpecialistController::class,
    'accion'      => 'searchByName',
    'roles'       => ['specialist','administrator','user']
]);
```

---

## Comparación con `searchByFilters`

| Característica        | `searchByFilters` (POST) | `searchByName` (GET) |
|------------------------|--------------------------|-----------------------|
| Método HTTP            | POST                     | GET                   |
| Parámetro principal    | JSON body (filtros)      | Query string (`q`)    |
| Uso típico             | Búsqueda avanzada        | Búsqueda rápida       |
| Estructura de salida   | Cards                    | Cards                 |

---

## Conclusión

La función `searchByName` complementa a `searchByFilters`, proporcionando una búsqueda rápida por nombre completo manteniendo la misma estructura de datos para integrarse fácilmente en las cards existentes.
