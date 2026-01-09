📋 Documentación del Módulo: Gestión de Solicitudes de Segunda Opinión
Este documento detalla los endpoints para la gestión de solicitudes de segunda opinión, incluyendo la visualización para especialistas, la creación por parte de usuarios y la consulta de datos compartidos.

1. Obtener Solicitudes (Vista de Especialista)
Función: getRequestsForSpecialist()

Endpoint: GET /second-opinion-requests

Roles: user, specialist

Descripción: Devuelve una lista de todas las solicitudes de segunda opinión asignadas al especialista autenticado (user_id de la sesión). La respuesta incluye datos del paciente (usuario) y detalles del servicio asociado.

Parámetros: Ninguno (el ID del especialista se toma de la sesión).

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_id": "uuid-request-1",
      "status": "pending",
      "notes": "Revisión de historial.",
      "created_at": "2023-10-27 10:00:00",
      "type_request": "appointment_request",
      "scope_request": "share_custom",
      "cost_request": "150.00",
      "request_date_to": "2023-11-05 14:00:00",
      "user_id": "uuid-patient-1",
      "pricing_id": "uuid-pricing-1",
      "first_name": "John",
      "last_name": "Doe",
      "sex": "Male",
      "service_type": "Consulta de Seguimiento",
      "duration_services": "60",
      "description": "Consulta virtual de 60 minutos.",
      "user_image": true
    }
  ]
}

Error (400 Bad Request): Si ocurre un error en la base de datos.

2. Obtener Detalle de una Solicitud (Vista de Especialista)
Función: getRequestsByIdForSpecialist()

Endpoint: GET /second-opinion-requests/{id}

Roles: user (Nota: la ruta indica user, pero la lógica parece de especialista)

Descripción: Devuelve los detalles de una única solicitud de segunda opinión, siempre que esté asignada al especialista autenticado.

Parámetros:

URL: id (string, requerido) - El UUID de la solicitud.

Respuestas Posibles
Éxito (200 OK): Devuelve un objeto único con la misma estructura que el endpoint getRequestsForSpecialist.

No Encontrado (200 OK con data: []): Si el ID no existe o no pertenece al especialista.

Error (400 Bad Request): Si ocurre un error en la base de datos.

3. Obtener Datos Compartidos de una Solicitud
Función: getRequestData()

Endpoint: GET /second-opinion-exams/{id}

Roles: user, specialist

Descripción: Devuelve la información médica específica (paneles, biomarcadores y registros de exámenes) que el usuario ha compartido para una solicitud de segunda opinión.

Parámetros:

URL: id (string, requerido) - El UUID de la solicitud.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "second_opinion_data_id": "uuid-data-1",
      "share_type": "records",
      "panel_id": "uuid-panel-lipids",
      "panel_name": "lipid_panel",
      "biomarkers": [
        {
          "biomarker_id": "uuid-cholesterol",
          "name_db": "cholesterol",
          "name_es": "Colesterol Total"
        }
      ],
      "records": [
        {
          "record_id": "uuid-record-1",
          "lipid_panel_id": "uuid-record-1",
          "user_id": "uuid-patient-1",
          "cholesterol": "210"
        }
      ]
    }
  ]
}

Error (400 Bad Request): Si ocurre un error al procesar la información.

4. Crear una Nueva Solicitud (Usuario)
Función: create()

Endpoint: POST /second-opinion-requests

Roles: user

Descripción: Un usuario autenticado crea una nueva solicitud para un especialista. El sistema valida que la fecha y hora solicitadas no entren en conflicto con otras citas del especialista, considerando la duración del servicio y el tiempo de búfer.

Parámetros (Cuerpo JSON o form-data):

specialist_id (string, requerido)

type_request (string, requerido): document_review o appointment_request.

pricing_id (string, opcional): UUID de la tarifa seleccionada, determina el costo y la duración.

request_date_to (datetime, opcional): Fecha y hora solicitada para la cita (YYYY-MM-DD HH:MM:SS). Crítico para la validación de agenda.

duration_request (string/int, opcional): Duración en minutos como fallback si no se provee pricing_id. Ej: "60", "90m", "1:30".

scope_request (string, opcional): share_none, share_all, share_custom.

notes (string, opcional): Notas para el especialista.

data (array, opcional): Array de objetos que detallan la información a compartir.

Cada objeto debe contener: { "panel_id": "...", "biomarkers_selected": [...], "exams": [...] }

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Second opinion request created successfully",
  "data": { "second_opinion_id": "new-uuid-request" }
}

Error de Validación (400 Bad Request):

Si faltan campos requeridos.

Si el type_request o scope_request son inválidos.

Si hay un conflicto de agenda (ej. Schedule conflict: start must be >= 2023-11-05 15:30:00 (prev appt + duration + buffer).).

5. Actualizar una Solicitud
Función: update()

Endpoint: PUT /second-opinion-requests/{id}

Roles: user

Descripción: Actualiza una solicitud existente. Si se modifica la fecha (request_date_to), se vuelve a ejecutar la validación de conflictos de agenda.

Parámetros:

URL: id (string, requerido)

Cuerpo (JSON o form-data):

status (string, requerido): pending, confirmed, cancelled, etc.

Otros campos opcionales como en create() para ser actualizados.

Respuestas Posibles
Éxito (200 OK): Mensaje de éxito.

Error (400 Bad Request): Si faltan el id o status, o si la actualización causa un conflicto de agenda.

6. Eliminar una Solicitud
Función: delete()

Endpoint: DELETE /second-opinion-requests/{id}

Roles: user

Descripción: Realiza un borrado lógico (soft delete) de una solicitud.

Parámetros (URL): id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Mensaje de éxito.

Error (400 Bad Request): Si falta el id.