📋 Documentación del Módulo: Specialist Social Links
Este documento detalla todos los endpoints disponibles para la gestión de los enlaces a redes sociales de los especialistas.

1. Obtener Todos los Enlaces Sociales
Función: getAll()

Endpoint: GET /specialist-social-links

Rol Requerido: specialist

Descripción: Recupera una lista de todos los enlaces a redes sociales de un especialista que no hayan sido eliminados.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
Devuelve una lista de todos los enlaces sociales del especialista.

{
  "value": true,
  "message": "",
  "data": [
    {
      "social_link_id": "uuid-link-1",
      "specialist_id": "uuid-specialist-1",
      "platform": "LinkedIn",
      "url": "[https://linkedin.com/in/especialista](https://linkedin.com/in/especialista)"
    },
    {
      "social_link_id": "uuid-link-2",
      "specialist_id": "uuid-specialist-1",
      "platform": "Twitter",
      "url": "[https://twitter.com/especialista](https://twitter.com/especialista)"
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

2. Obtener Enlace Social por ID
Función: getById()

Endpoint: GET /specialist-social-links/{id}

Rol Requerido: specialist

Descripción: Busca y devuelve un único enlace social que coincida con el id proporcionado.

Parámetros:

URL: id (string, requerido) - El UUID del enlace social.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "social_link_id": "uuid-link-1",
      "specialist_id": "uuid-specialist-1",
      "platform": "LinkedIn",
      "url": "[https://linkedin.com/in/especialista](https://linkedin.com/in/especialista)"
    }
  ]
}

No Encontrado (200 OK)
{
  "value": false,
  "message": "Record not found",
  "data": []
}

ID Inválido (200 OK)
{
  "value": false,
  "message": "Invalid ID",
  "data": []
}

3. Crear un Nuevo Enlace Social
Función: create()

Endpoint: POST /specialist-social-links

Rol Requerido: specialist

Descripción: Crea un nuevo registro de enlace social. Acepta datos tanto de form-data como de un cuerpo JSON.

Parámetros:

Cuerpo (Form-Data o JSON):

specialist_id (string, requerido)

platform (string, requerido) - Ej: "LinkedIn", "GitHub", "Twitter".

url (string, requerido) - La URL completa del perfil.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Record created successfully",
  "data": []
}

Error de Parámetros (200 OK)
{
  "value": false,
  "message": "Missing required fields",
  "data": []
}

Error de Base de Datos (400 Bad Request)
{
  "value": false,
  "message": "Error creating record: [Mensaje del error]",
  "data": []
}

4. Actualizar un Enlace Social
Función: update()

Endpoint: POST /specialist-social-links/{id} (Emula PUT)

Rol Requerido: specialist

Descripción: Actualiza un enlace social existente. Se debe enviar como POST pero puede incluir un campo _method=PUT para cumplir con las convenciones RESTful que el controlador espera.

Parámetros:

URL: id (string, requerido) - El UUID del registro.

Cuerpo (Form-Data):

platform (string, requerido)

url (string, requerido)

_method (string, opcional) - Debe ser 'PUT'.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Record updated successfully",
  "data": []
}

Error de Parámetros (200 OK)
{
  "value": false,
  "message": "Missing required fields",
  "data": []
}

Método No Permitido (405 Method Not Allowed)
Si la solicitud no se interpreta como PUT.

{
  "value": false,
  "message": "Method not allowed. PUT required.",
  "data": []
}

5. Eliminar un Enlace Social
Función: delete()

Endpoint: DELETE /specialist-social-links/{id}

Rol Requerido: specialist

Descripción: Realiza un borrado lógico (soft delete) de un enlace social.

Parámetros:

URL: id (string, requerido) - El UUID del registro a eliminar.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Record deleted successfully",
  "data": []
}

Error de Parámetros (200 OK)
{
  "value": false,
  "message": "ID is required for deletion",
  "data": []
}

Método No Permitido (405 Method Not Allowed)
{
  "value": false,
  "message": "Method not allowed. DELETE required.",
  "data": []
}
