📋 Documentación del Módulo: Specialist Certifications
Este documento detalla todos los endpoints para la gestión de las certificaciones de los especialistas. Este módulo incluye subida de archivos.

1. Obtener Todas las Certificaciones
Función: getAll()

Endpoint: GET /specialist-certifications (Ruta inferida)

Rol Requerido: specialist

Descripción: Recupera una lista de todas las certificaciones de un especialista que no hayan sido eliminadas.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
Devuelve una lista de todas las certificaciones.

{
  "value": true,
  "message": "",
  "data": [
    {
      "certification_id": "uuid-cert-1",
      "specialist_id": "uuid-specialist-1",
      "file_url": "uploads/certifications/cert_654abc.pdf",
      "title": "Certificación en Terapia Cognitivo-Conductual",
      "description": "Otorgado por el Instituto de Psicología.",
      "visibility": "PUBLIC"
    }
  ]
}

Error (400 Bad Request)
Ocurre si hay un problema al consultar la base de datos.

{
  "value": false,
  "message": "Error retrieving certifications: [Mensaje del error]",
  "data": []
}

2. Obtener Certificación por ID
Función: getById()

Endpoint: GET /specialist-certifications/{id} (Ruta inferida)

Rol Requerido: specialist

Descripción: Busca y devuelve una única certificación que coincida con el id proporcionado.

Parámetros:

URL: id (string, requerido) - El UUID de la certificación.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "certification_id": "uuid-cert-1",
      "specialist_id": "uuid-specialist-1",
      "file_url": "uploads/certifications/cert_654abc.pdf",
      "title": "Certificación en Terapia Cognitivo-Conductual",
      "description": "Otorgado por el Instituto de Psicología.",
      "visibility": "PUBLIC"
    }
  ]
}

No Encontrado (200 OK)
{
  "value": false,
  "message": "Certification not found",
  "data": []
}

3. Crear una Nueva Certificación
Función: create()

Endpoint: POST /specialist-certifications (Ruta inferida)

Rol Requerido: specialist

Descripción: Sube un archivo de certificación (PDF, PNG, JPG) y crea un nuevo registro asociado. La petición debe ser de tipo multipart/form-data.

Parámetros:

Cuerpo (multipart/form-data):

specialist_id (string, requerido)

file (archivo, requerido) - El archivo de la certificación. Extensiones permitidas: pdf, png, jpg, jpeg.

title (string, opcional) - Título de la certificación.

description (string, opcional) - Descripción detallada.

visibility (string, opcional) - PUBLIC o PRIVATE. Por defecto PUBLIC.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Certification created successfully",
  "data": []
}

Error de Parámetros (400 Bad Request)
{
  "value": false,
  "message": "Missing required fields: specialist_id and file",
  "data": []
}

Error General (400 Bad Request)
Puede ocurrir por un tipo de archivo no permitido, un error al mover el archivo subido o un error de base de datos.

{
  "value": false,
  "message": "Error creating certification: [Mensaje del error]",
  "data": []
}

4. Actualizar una Certificación
Función: update()

Endpoint: POST /specialist-certifications/{id} (Emula PUT)

Rol Requerido: specialist

Descripción: Actualiza los datos de una certificación existente. Opcionalmente, puede reemplazar el archivo asociado. La petición debe ser multipart/form-data.

Parámetros:

URL: id (string, requerido) - El UUID de la certificación.

Cuerpo (multipart/form-data):

_method (string, requerido) - Debe ser 'PUT'.

file (archivo, opcional) - Un nuevo archivo para reemplazar el existente.

title (string, opcional) - El nuevo título.

description (string, opcional) - La nueva descripción.

visibility (string, opcional) - La nueva visibilidad.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Certification updated successfully",
  "data": []
}

Error de Parámetros (200 OK)
{
  "value": false,
  "message": "Missing ID",
  "data": []
}

Método No Permitido (405 Method Not Allowed)
{
  "value": false,
  "message": "Method not allowed. PUT required.",
  "data": []
}

5. Eliminar una Certificación
Función: delete()

Endpoint: DELETE /specialist-certifications/{id} (Ruta inferida)

Rol Requerido: specialist

Descripción: Realiza un borrado lógico (soft delete) de una certificación.

Parámetros:

URL: id (string, requerido) - El UUID de la certificación a eliminar.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Certification deleted successfully",
  "data": []
}

Error de Parámetros (200 OK)
{
  "value": false,
  "message": "Certification ID is required for deletion",
  "data": []
}
