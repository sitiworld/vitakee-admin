# 📑 API - Second Opinion Requests

Este módulo maneja solicitudes de segunda opinión médica en dos variantes:

- **Standard**: solicitudes normales (consulta, revisión de documentos, etc.)
- **Block**: bloqueos de agenda (intervalos reservados sin detalle biomédico)

---

## 🔑 Requisitos generales

- **Autenticación**: vía sesión (`$_SESSION['user_id']`)
- **Roles**:
  - `user` → puede **crear**, **actualizar**, **eliminar**
  - `specialist` → puede **listar**, **consultar detalle**
- **Métodos permitidos**: `GET`, `POST`, `DELETE` (no se usa `PUT`)
- **Formato**: `application/json` (acepta también `form-data` mezclado)

---

## 📂 Endpoints STANDARD (no-block)

### 1. Crear solicitud standard
`POST /second-opinion/requests`

#### Necesita
- Sesión activa (`user_id`)
- Parámetros obligatorios:
  - `specialist_id` (UUID)
  - `request_date_to` (YYYY-MM-DD HH:MM:SS)

#### Recibe (body JSON o form-data)
```json
{
  "specialist_id": "uuid",
  "type_request": "appointment_request", // o "document_review"
  "status": "pending",
  "request_date_to": "2025-10-05 10:00:00",
  "request_date_end": "2025-10-05 10:30:00",
  "notes": "Detalle opcional",
  "shared_until": "2025-12-31",
  "pricing_id": "uuid",
  "scope_request": "share_all",
  "cost_request": "50.00",
  "duration_request": "30",
  "data": [
    {
      "panel_id": "uuid",
      "biomarkers_selected": [{ "id": "bm_uuid" }],
      "exams": [{ "id": "record_uuid" }]
    }
  ]
}
```

#### Entrega
```json
{
  "value": true,
  "message": "Second opinion (standard) created",
  "data": {
    "second_opinion_id": "uuid"
  }
}
```

---

### 2. Actualizar solicitud standard
`POST /second-opinion/requests/{id}`

#### Necesita
- `id` en ruta (UUID)
- Body con al menos `status`

#### Recibe
```json
{
  "status": "upcoming",
  "notes": "Nuevo detalle",
  "request_date_to": "2025-10-05 11:00:00",
  "request_date_end": "2025-10-05 11:30:00",
  "scope_request": "share_custom",
  "data": [
    {
      "panel_id": "uuid",
      "biomarkers_selected": [{ "id": "bm_uuid" }],
      "exams": [{ "id": "record_uuid" }]
    }
  ]
}
```

#### Entrega
```json
{
  "value": true,
  "message": "Standard request updated",
  "data": []
}
```

---

### 3. Listar solicitudes standard (especialista)
`GET /second-opinion/requests`

#### Necesita
- Rol `specialist`
- Sesión activa (`user_id` del especialista)

#### Entrega
```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_id": "uuid",
      "status": "pending",
      "user_id": "uuid",
      "type_request": "appointment_request",
      "request_date_to": "2025-10-05 10:00:00",
      "pricing": { ... },
      "user_image": true,
      "information": [ ... ]
    }
  ]
}
```

---

### 4. Obtener detalle de solicitud standard
`GET /second-opinion/requests/{id}`

#### Necesita
- Rol `specialist`
- Sesión activa

#### Entrega
```json
{
  "value": true,
  "message": "",
  "data": {
    "second_opinion_id": "uuid",
    "status": "pending",
    "type_request": "appointment_request",
    "user_id": "uuid",
    "pricing": { ... },
    "information": [ ... ]
  }
}
```

---

### 5. Obtener exámenes/datos asociados
`GET /second-opinion/requests/{id}/exams`

#### Entrega
```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_data_id": "uuid",
      "share_type": "records",
      "panel_id": "uuid",
      "panel_name": "renal_function",
      "biomarkers": [ ... ],
      "records": [
        { "record_id": "uuid", "glucose": 110, "ketone": 0.4 }
      ]
    }
  ]
}
```

---

## 📂 Endpoints BLOCKS

### 1. Crear bloqueo
`POST /second-opinion/blocks`

#### Necesita
- Sesión activa (`user_id`)
- Parámetros obligatorios:
  - `specialist_id`
  - `request_date_to`
  - `request_date_end`

#### Recibe
```json
{
  "specialist_id": "uuid",
  "type_request": "block",
  "status": "pending",
  "request_date_to": "2025-10-05 12:00:00",
  "request_date_end": "2025-10-05 13:00:00"
}
```

#### Entrega
```json
{
  "value": true,
  "message": "Block created",
  "data": {
    "second_opinion_id": "uuid"
  }
}
```

---

### 2. Actualizar bloqueo
`POST /second-opinion/blocks/{id}`

#### Recibe
```json
{
  "status": "cancelled",
  "request_date_to": "2025-10-05 12:30:00",
  "request_date_end": "2025-10-05 13:30:00"
}
```

#### Entrega
```json
{
  "value": true,
  "message": "Block updated",
  "data": []
}
```

---

### 3. Listar bloqueos (especialista)
`GET /second-opinion/blocks`

#### Entrega
```json
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_id": "uuid",
      "type_request": "block",
      "status": "pending",
      "request_date_to": "2025-10-05 12:00:00",
      "request_date_end": "2025-10-05 13:00:00"
    }
  ]
}
```

---

### 4. Obtener bloqueo por ID
`GET /second-opinion/blocks/{id}`

#### Entrega
```json
{
  "value": true,
  "message": "",
  "data": {
    "second_opinion_id": "uuid",
    "type_request": "block",
    "status": "pending",
    "request_date_to": "2025-10-05 12:00:00",
    "request_date_end": "2025-10-05 13:00:00"
  }
}
```

---

## ❌ DELETE (común)

### Eliminar solicitud o bloqueo
`DELETE /second-opinion/requests/{id}`  
`DELETE /second-opinion/blocks/{id}`

#### Entrega
```json
{
  "value": true,
  "message": "Request deleted successfully",
  "data": []
}
```

---

## 📝 Notas

- `status` permitido: `pending`, `awaiting_payment`, `upcoming`, `completed`, `cancelled`, `rejected`
- `type_request` permitido en **standard**: `document_review`, `appointment_request`
- `type_request = block` solo en endpoints `/blocks`
- `scope_request`: `share_none`, `share_all`, `share_custom`
- Campos anulados en **blocks**: `notes`, `pricing_id`, `scope_request`, `cost_request`, `duration_request`, `data`
