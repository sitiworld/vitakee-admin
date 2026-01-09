📋 Documentación del Módulo: Specialist Locations
Este documento detalla los endpoints para la gestión de las ubicaciones de consulta de los especialistas, utilizando ahora identificadores para country_id, state_id y city_id.

1. Obtener Todas las Ubicaciones del Especialista
Función: getAll()

Endpoint: GET /specialist-locations

Rol Requerido: specialist

Descripción: Recupera una lista de todas las ubicaciones registradas para el especialista autenticado en la sesión. La respuesta incluye los nombres de la ciudad, estado y país.

Parámetros:

Sesión: Requiere que $_SESSION['specialist_id'] (o user_id) esté definido.

Respuestas Posibles
Éxito (200 OK)
Devuelve una lista con todas las ubicaciones del especialista.

{
  "value": true,
  "message": "",
  "data": [
    {
      "location_id": "uuid-loc-1",
      "specialist_id": "uuid-specialist-1",
      "city_id": "123",
      "state_id": "45",
      "country_id": "6",
      "is_primary": "1",
      "city_name": "Caracas",
      "state_name": "Distrito Capital",
      "country_name": "Venezuela"
    }
  ]
}

Error (400 Bad Request)
Ocurre si hay un problema al consultar la base de datos.

{
  "value": false,
  "message": "Error retrieving records: [Mensaje del error]",
  "data": []
}

2. Obtener Ubicación por ID
Función: getById()

Endpoint: GET /specialist-locations/{id}

Rol Requerido: specialist

Descripción: Busca y devuelve una única ubicación que coincida con el id, siempre que pertenezca al especialista autenticado.

Parámetros:

URL: id (string, requerido) - El UUID de la ubicación.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "location_id": "uuid-loc-1",
      "specialist_id": "uuid-specialist-1",
      "city_id": "123",
      "state_id": "45",
      "country_id": "6",
      "is_primary": "1",
      "city_name": "Caracas",
      "state_name": "Distrito Capital",
      "country_name": "Venezuela"
    }
  ]
}

No Encontrado (200 OK)
{
  "value": false,
  "message": "Record not found",
  "data": []
}

Prohibido (403 Forbidden)
Si la ubicación no pertenece al especialista de la sesión.

{
  "value": false,
  "message": "You cannot modify a location that does not belong to you",
  "data": []
}

3. Crear una Nueva Ubicación
Función: create()

Endpoint: POST /specialist-locations

Rol Requerido: specialist

Descripción: Crea un nuevo registro de ubicación para el especialista autenticado. Si se marca como primaria, las demás ubicaciones se desmarcarán automáticamente.

Parámetros:

Sesión: Requiere $_SESSION['specialist_id'] (o user_id).

Cuerpo (Form-Data):

country_id (string, requerido)

state_id (string, requerido)

city_id (string, requerido)

is_primary (boolean, opcional) - Se interpreta como 1 (verdadero) si el valor es on, 1, true, yes o si. Por defecto es 0.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Location created successfully",
  "data": []
}

No Autorizado (401 Unauthorized)
{
  "value": false,
  "message": "Missing specialist in session (expected specialist_id or user_id)",
  "data": []
}

Error de Validación (422 Unprocessable Entity)
{
  "value": false,
  "message": "Missing fields: country_id, state_id, city_id",
  "data": []
}

4. Actualizar una Ubicación
Función: update()

Endpoint: PUT /specialist-locations/{id} (Soporta POST con _method=PUT)

Rol Requerido: specialist

Descripción: Actualiza una ubicación existente. Se verifica que pertenezca al especialista.

Parámetros:

URL: id (string, requerido) - El UUID de la ubicación.

Cuerpo (Form-Data):

country_id (string, requerido)

state_id (string, requerido)

city_id (string, requerido)

is_primary (boolean, opcional) - Por defecto 0.

_method (string, opcional) - Usar 'PUT' si se envía como POST.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Location updated successfully",
  "data": []
}

Error de Parámetros (422 Unprocessable Entity)
{
  "value": false,
  "message": "Missing fields: country_id, state_id, city_id",
  "data": []
}

5. Eliminar una Ubicación
Función: delete()

Endpoint: DELETE /specialist-locations/{id} (Soporta POST con _method=DELETE)

Rol Requerido: specialist

Descripción: Realiza un borrado lógico (soft delete) de una ubicación, previa validación de pertenencia.

Parámetros:

URL: id (string, requerido) - El UUID de la ubicación a eliminar.

Cuerpo (Form-Data):

_method (string, opcional) - Usar 'DELETE' si se envía como POST.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Location deleted successfully",
  "data": []
}

No Encontrado (200 OK)
{
  "value": false,
  "message": "Record not found",
  "data": []
}
