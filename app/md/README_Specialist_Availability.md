📋 Documentación del Módulo: Disponibilidad del Especialista
Este documento detalla los endpoints para que un especialista gestione sus horarios de disponibilidad semanales.

1. Listar Toda la Disponibilidad
Función: getAll()

Endpoint: GET /specialist-availability

Roles: specialist

Descripción: Devuelve todos los bloques de disponibilidad configurados por el especialista, ordenados por día de la semana.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "availability_id": "uuid-availability-1",
      "specialist_id": "uuid-specialist-1",
      "weekday": "Monday",
      "start_time": "09:00:00",
      "end_time": "17:00:00",
      "buffer_time_minutes": "15",
      "timezone": "America/Caracas",
      "created_at": "2023-10-27 10:00:00"
    }
  ]
}

Error (400 Bad Request): Si ocurre un error en la base de datos.

2. Obtener Disponibilidad por ID
Función: getById()

Endpoint: GET /specialist-availability/{id}

Roles: specialist

Descripción: Busca y devuelve un bloque de disponibilidad específico.

Parámetros (URL):

id (string, requerido): El UUID del bloque de disponibilidad.

Respuestas Posibles
Éxito (200 OK): Devuelve el objeto del bloque de disponibilidad.

No Encontrado (200 OK con value: false): Si el id no existe.

3. Crear un Nuevo Bloque de Disponibilidad
Función: create()

Endpoint: POST /specialist-availability

Roles: specialist

Descripción: Crea un nuevo horario de disponibilidad para el especialista autenticado. Realiza validaciones para asegurar que los campos requeridos estén presentes, que el formato de la hora sea HH:MM y que la hora de inicio sea anterior a la de fin.

Parámetros (Cuerpo form-data):

weekday (string, requerido): Día de la semana (ej. 'Monday', 'Tuesday').

start_time (string, requerido): Hora de inicio en formato HH:MM.

end_time (string, requerido): Hora de fin en formato HH:MM.

timezone (string, requerido): Zona horaria (ej. 'America/Caracas').

buffer_time_minutes (int, opcional, por defecto 0): Minutos de búfer entre citas.

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Availability created",
  "data": []
}

No Autorizado (401 Unauthorized): Si el especialista no está autenticado en la sesión.

Error de Validación (422 Unprocessable Entity):

Si faltan campos requeridos.

Si el formato de start_time o end_time no es HH:MM.

Si start_time es mayor o igual a end_time.

Error de Método (405 Method Not Allowed): Si se usa un método diferente a POST.

4. Actualizar un Bloque de Disponibilidad
Función: update()

Endpoint: PUT /specialist-availability/{id} (Soporta POST con _method=PUT)

Roles: specialist

Descripción: Actualiza un bloque de disponibilidad existente.

Parámetros:

URL: id (string, requerido)

Cuerpo (form-data):

weekday (string, requerido)

start_time (string, requerido)

end_time (string, requerido)

timezone (string, requerido)

buffer_time_minutes (int, opcional, por defecto 0)

Respuestas Posibles
Éxito (200 OK): Mensaje de confirmación.

Error (400 Bad Request): Si faltan campos requeridos o hay un error de base de datos.

Error de Método (405 Method Not Allowed): Si no se usa PUT o el método emulado.

5. Eliminar un Bloque de Disponibilidad
Función: delete()

Endpoint: DELETE /specialist-availability/{id}

Roles: specialist

Descripción: Realiza un borrado lógico (soft delete) de un bloque de disponibilidad.

Parámetros (URL):

id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Mensaje de confirmación.

Error (400 Bad Request): Si falta el id.

Error de Método (405 Method Not Allowed): Si no se usa DELETE.