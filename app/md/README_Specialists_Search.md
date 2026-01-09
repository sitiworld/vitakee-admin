# Specialists Search API — Cards (con o sin filtros)

Este documento define el contrato de la API para la **búsqueda de especialistas** con múltiples filtros **(opcional)** y **también sin filtros**.
**Todas las respuestas** retornan objetos en formato **Card** (idéntico a `getAllForCards()`), adecuados para renderizar directamente en la UI.

---

## Endpoint

- **URL**: `/specialists/search`
- **Método**: `POST`
- **Roles permitidos**: `specialist`, `administrator`, `user`
- **Headers**: `Content-Type: application/json; charset=utf-8`

---

## Request Body

**Todos los filtros son opcionales**. Si envías `{}` (o no envías body) obtendrás resultados **sin filtros**.
Los filtros entre sí se combinan con **AND**. Dentro de `languages` y `weekdays`, la coincidencia es **OR**.

> Si necesitas paginar, puedes incluir `limit` y `offset`. Para cambiar el orden, puedes incluir `order`.

### Esquema de filtros admitidos

```json
{
  "specialty_ids": ["<uuid>", "<uuid>"],
  "verified": true,
  "languages": ["es", "en"],
  "min_cost": 25.0,
  "min_rating": 4.2,
  "min_evaluations": 5,
  "min_consultations": 10,
  "availability": {
    "date": "YYYY-MM-DD",
    "weekdays": ["Monday", "Wednesday"],
    "time_start": "HH:MM:SS",
    "time_end": "HH:MM:SS"
  },

  "order": "recent | rating_cost",
  "limit": 20,
  "offset": 0
}
```

**Detalles de cada campo**

- `specialty_ids: string[]`  
  Lista de IDs de especialidades. Devuelve especialistas cuya `specialty_id` coincida.

- `verified: boolean`  
  - `true` → incluye especialistas con `verified_status = 'VERIFIED'` **o** que tengan solicitud de verificación con `status='APPROVED'`.
  - `false` → excluye los anteriores.

- `languages: string[]`  
  Idiomas del especialista (match tipo `OR` por cada idioma).

- `min_cost: number`  
  Filtra por `MIN(price_usd)` en `specialist_pricing` con `service_type='CONSULTATION'`.

- `min_rating: number`  
  Filtra por promedio `AVG(rating)`.

- `min_evaluations: integer`  
  Filtra por cantidad de reviews (`COUNT(review_id)` o `COUNT(rating)`).

- `min_consultations: integer`  
  Filtra por cantidad de consultas completadas (`transactions` con `status='COMPLETED'` y `type='CONSULTATION'`).

- `availability: object`  
  - `date: YYYY-MM-DD` → mapeada internamente a `weekday`.
  - `weekdays: string[]` → lista de días (ej.: `"Monday"`, `"Wednesday"`).
  - `time_start`, `time_end` → ventana horaria; se busca **solapamiento** con la disponibilidad del especialista.

- `order: string` (opcional)  
  - `recent` (por defecto) → **recientes primero** según `created_at DESC` (comportamiento de `getAllForCards()`).
  - `rating_cost` → ordena por `AVG(rating) DESC`, luego por `MIN(price_usd) ASC` (si hay precio).

- `limit: integer` y `offset: integer` (opcionales)  
  Soportan paginación clásica: `LIMIT ? OFFSET ?`.

---

## Response Body

### Éxito

```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "specialist_id": "UUID",
      "full_name": "John Doe",
      "handle": "@Therapist",
      "website_url": "https://...",
      "avatar_url": "https://...",

      "lab_reports_evaluated": 12,
      "consultations_completed": 28,

      "avg_rating": 4.7,
      "rating_text": "4.7/5",
      "reviews_count": 12
    }
  ]
}
```

**Notas**
- `handle` se construye como `@` + título localizado del especialista (según idioma de sesión: `ES`/`EN`).  
- `rating_text` se arma a partir de `avg_rating` como `"<avg>/5"`.  
- Métricas calculadas:
  - `lab_reports_evaluated`: `COUNT(transactions)` con `type='VERIFICATION'` y `status='COMPLETED'`.
  - `consultations_completed`: `COUNT(transactions)` con `type='CONSULTATION'` y `status='COMPLETED'`.

### Error

```json
{
  "value": false,
  "message": "Error searching specialists: <detalle>",
  "data": null
}
```

---

## Orden de resultados

Por defecto (**sin especificar `order`**): **recientes primero** → `created_at DESC` (igual a `getAllForCards()`).

Si envías `"order": "rating_cost"`, el listado ordenará por:
1) `AVG(rating)` **DESC**, y luego  
2) `MIN(price_usd)` **ASC** (si hay precio).

> `MIN(price_usd)` se utiliza para ordenar o filtrar, pero **no** se expone en la card por defecto.
> Si necesitas exponerlo, propón una `v2` de la card para incluirlo explícitamente.

---

## Ejemplos de Requests

### 0) **Sin filtros** (todas las cards, recientes primero)
```json
{}
```

### 1) Especialidad + Verificados + Rating mínimo
```json
{
  "specialty_ids": ["0bff4eaa-ed0d-44b6-9dd2-86de60e47e34"],
  "verified": true,
  "min_rating": 4.5
}
```

### 2) Idiomas + Costo mínimo + Evaluaciones mínimas
```json
{
  "languages": ["es", "en"],
  "min_cost": 30,
  "min_evaluations": 10
}
```

### 3) Disponibilidad por fecha + ventana horaria
```json
{
  "availability": {
    "date": "2025-09-22",
    "time_start": "09:00:00",
    "time_end": "12:00:00"
  }
}
```

### 4) Disponibilidad por weekdays + Consultas mínimas
```json
{
  "availability": {
    "weekdays": ["Monday", "Wednesday"]
  },
  "min_consultations": 15
}
```

### 5) Búsqueda completa (varios filtros)
```json
{
  "specialty_ids": ["9ae67f3a-a551-460f-a7be-3f9ec65ca3c6"],
  "verified": true,
  "languages": ["es"],
  "min_cost": 20,
  "min_rating": 4.2,
  "min_evaluations": 5,
  "min_consultations": 10,
  "availability": {
    "date": "2025-09-22",
    "time_start": "08:00:00",
    "time_end": "11:00:00"
  },
  "order": "recent",
  "limit": 20,
  "offset": 0
}
```

---

## Cambios respecto a la versión anterior

- ✅ **Nuevo:** Soporte de **búsqueda sin filtros** (`{}`) que devuelve todas las cards (recientes primero).  
- ✅ **Nuevo:** La respuesta **siempre** viene en formato **Card** (lista para UI).  
- ✅ **Nuevo:** Parámetros opcionales de **paginación** (`limit`, `offset`) y **orden** (`order`).  
- ℹ️ `min_cost` sigue afectando orden/filtrado pero **no** se expone en la card por defecto.
