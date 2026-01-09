📋 Documentación del Módulo: Contacto (Emails y Teléfonos)
Este documento detalla los endpoints para la gestión de correos electrónicos y teléfonos de contacto. Estos métodos se
pueden asociar a diferentes tipos de entidades (como specialist, user, etc.).

📧 Correos Electrónicos de Contacto (Contact Emails)
Endpoints para administrar los correos electrónicos.

1. Obtener Todos los Correos
Función: getAll()

Endpoint: GET /contact-emails

Roles: specialist, administrator, user

Descripción: Devuelve una lista de todos los correos de contacto en el sistema.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
{
"value": true,
"message": "",
"data": [
{
"contact_email_id": "uuid-email-1",
"entity_type": "specialist",
"entity_id": "uuid-specialist-1",
"email": "correo@dominio.com",
"is_primary": "1",
"is_active": "1"
}
]
}

2. Obtener Correo por ID
Función: getById()

Endpoint: GET /contact-emails/{id}

Roles: specialist, administrator, user

Descripción: Busca un correo por su contact_email_id.

Parámetros (URL): id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Devuelve el objeto del correo.

No Encontrado (404 Not Found): Si el ID no existe.

3. Obtener Correos por Entidad
Función: getByEntity()

Endpoint: GET /contact-emails/entity/{type}/{id}

Roles: specialist, administrator, user

Descripción: Devuelve todos los correos asociados a una entidad específica.

Parámetros (URL):

type (string, requerido): El tipo de entidad (ej. 'specialist', 'user').

id (string, requerido): El UUID de la entidad.

Respuestas Posibles
Éxito (200 OK): Devuelve un array de correos, ordenados por primario primero.

4. Obtener Correo por Dirección de Email
Función: getByEmail()

Endpoint: GET /contact-emails/email/{email}

Roles: specialist, administrator, user

Descripción: Busca el primer correo que coincida con la dirección proporcionada.

Parámetros (URL): email (string, requerido).

Respuestas Posibles
Éxito (200 OK): Devuelve el objeto del correo.

No Encontrado (404 Not Found): Si el email no se encuentra.

5. Crear Correo de Contacto
Función: create()

Endpoint: POST /contact-emails

Roles: specialist, administrator, user

Descripción: Crea un nuevo correo y lo asocia a una entidad.

Parámetros (Cuerpo form-data o JSON):

entity_type (string, requerido)

entity_id (string, requerido)

email (string, requerido)

is_primary (int, opcional, 0 o 1)

is_active (int, opcional, 0 o 1)

Respuestas Posibles
Éxito (200 OK)
{
"value": true,
"message": "Contact email created successfully",
"data": {
"contact_email_id": "new-uuid-email"
}
}

Error (400 Bad Request): Si faltan campos, el email es inválido o ya existe para esa entidad.

6. Actualizar Correo de Contacto
Función: update()

Endpoint: POST /contact-emails/{id} (Emula PUT)

Roles: specialist, administrator, user

Parámetros:

URL: id (string, requerido)

Cuerpo: Campos a actualizar.

Respuestas Posibles
Éxito (200 OK): Mensaje de éxito.

7. Establecer como Primario
Función: setPrimary()

Endpoint: POST /contact-emails/{id}/set-primary

Roles: specialist, administrator, user

Descripción: Marca un correo como primario y desmarca los demás para la misma entidad.

Parámetros (URL): id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Mensaje de éxito.

8. Eliminar Correo de Contacto
Función: delete()

Endpoint: DELETE /contact-emails/{id}

Roles: specialist, administrator, user

Descripción: Realiza un borrado lógico del correo.

Parámetros (URL): id (string, requerido).

Respuestas Posibles
Éxito (200 OK): Mensaje de éxito.

📞 Teléfonos de Contacto (Contact Phones)
Endpoints para administrar los teléfonos. La estructura es muy similar a la de los correos.

1. Obtener Todos los Teléfonos
Función: getAll()

Endpoint: GET /contact-phones

Roles: specialist, administrator, user

Descripción: Devuelve una lista de todos los teléfonos.

2. Obtener Teléfono por ID
Función: getById()

Endpoint: GET /contact-phones/{id}

Roles: specialist, administrator, user

Parámetros (URL): id (string, requerido).

3. Obtener Teléfonos por Entidad
Función: getByEntity()

Endpoint: GET /contact-phones/entity/{type}/{id}

Roles: specialist, administrator, user

Parámetros (URL): type y id de la entidad.

4. Obtener Teléfono por Número
Función: getByTelephone()

Endpoint: GET /contact-phones/telephone/{telephone}

Roles: specialist, administrator, user

Descripción: Busca un teléfono por su número completo (solo dígitos).

Parámetros (URL): telephone (string de dígitos, requerido).

5. Crear Teléfono de Contacto
Función: create()

Endpoint: POST /contact-phones

Roles: specialist, administrator, user

Parámetros (Cuerpo form-data o JSON):

entity_type (string, requerido)

entity_id (string, requerido)

country_code (string, requerido)

phone_number (string, requerido)

phone_type (string, opcional, ej: 'mobile', 'home')

is_primary (int, opcional, 0 o 1)

is_active (int, opcional, 0 o 1)

Respuestas Posibles
Éxito (200 OK)
{
"value": true,
"message": "Contact phone created successfully",
"data": {
"contact_phone_id": "new-uuid-phone"
}
}

6. Actualizar Teléfono de Contacto
Función: update()

Endpoint: POST /contact-phones/{id} (Emula PUT)

Roles: specialist, administrator, user

Parámetros:

URL: id (string, requerido)

Cuerpo: Campos a actualizar.

7. Establecer como Primario
Función: setPrimary()

Endpoint: POST /contact-phones/{id}/set-primary

Roles: specialist, administrator, user

Descripción: Marca un teléfono como primario para su entidad.

Parámetros (URL): id (string, requerido).

8. Eliminar Teléfono de Contacto
Función: delete()

Endpoint: DELETE /contact-phones/{id}

Roles: specialist, administrator, user

Descripción: Realiza un borrado lógico del teléfono.

Parámetros (URL): id (string, requerido).