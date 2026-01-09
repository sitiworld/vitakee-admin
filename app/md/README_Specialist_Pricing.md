📋 Documentación del Módulo: Precios de Especialista (Actualizado)
Este documento detalla los endpoints para la gestión de las tarifas de servicios ofrecidos por un especialista. El specialist_id se obtiene de la sesión activa.

1. Listar Todas las Tarifas
Función: getAll()

Endpoint: GET /specialist-pricing

Roles: specialist

Descripción: Devuelve una lista de todas las tarifas de servicios que el especialista autenticado ha configurado y que no han sido eliminadas.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "pricing_id": "uuid-pricing-1",
      "specialist_id": "uuid-specialist-1",
      "service_type": "Consulta Inicial",
      "duration_services": "60",
      "description": "Primera evaluación completa.",
      "price_usd": "100.00",
      "is_active": "1",
      "created_at": "2023-10-27 10:00:00",
      "created_by": "uuid-user-1"
    }
  ]
}

Error (400 Bad Request): Si ocurre un error en la base de datos.

2. Obtener una Tarifa por ID
Función: getById()

Endpoint: GET /specialist-pricing/{id}

Roles: specialist

Descripción: Busca y devuelve los detalles de una única tarifa a partir de su pricing_id.

Parámetros (URL):

id (string, requerido): El UUID de la tarifa.

Respuestas Posibles
Éxito (200 OK): Devuelve el objeto de la tarifa encontrada.

No Encontrado (200 OK con value: false): Si el id no corresponde a ninguna tarifa.

ID Inválido (200 OK con value: false): Si el id proporcionado no es válido.

3. Crear una Nueva Tarifa
Función: create()

Endpoint: POST /specialist-pricing

Roles: specialist

Descripción: Crea una nueva tarifa para el especialista autenticado.

Parámetros (Cuerpo form-data):

service_type (string, requerido): Nombre del servicio.

price_usd (numeric, requerido): Precio en USD. Admite punto o coma como separador.

description (string, opcional): Descripción del servicio.

duration_services (string, opcional): Duración del servicio en minutos.

is_active (opcional): Se considera 1 (activo) si se envía cualquier valor no vacío. Si se omite, es 0 (inactivo).

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Pricing created successfully",
  "data": []
}

Error de Método (405 Method Not Allowed): Si se usa un método diferente a POST.

No Autorizado (401 Unauthorized): Si no hay un especialista en la sesión.

Error de Validación (422 Unprocessable Entity): Si faltan campos requeridos o si price_usd no es un número válido.

4. Actualizar una Tarifa
Función: update()

Endpoint: PUT /specialist-pricing/{id} (Soporta POST con _method=PUT)

Roles: specialist

Descripción: Actualiza los datos de una tarifa existente.

Parámetros:

URL: id (string, requerido)

Cuerpo (form-data):

service_type (string, requerido)

price_usd (numeric, requerido)

duration_services (string, opcional)

description (string, opcional)

is_active (opcional, por defecto 1)

Respuestas Posibles
Éxito (200 OK): Mensaje de confirmación.

Error de Parámetro (200 OK con value: false): Si falta el id o los campos requeridos.

Error de Método (405 Method Not Allowed): Si no se usa PUT o el método emulado.

5. Eliminar una Tarifa
Función: delete()

Endpoint: DELETE /specialist-pricing/{id}

Roles: specialist

Descripción: Realiza un borrado lógico (soft delete) de una tarifa.

Parámetros (URL):

id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Mensaje de confirmación.

Error (400 Bad Request): Si falta el id.

Error de Método (405 Method Not Allowed): Si no se usa DELETE.