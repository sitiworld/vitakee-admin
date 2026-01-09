📋 Documentación del Módulo: Reseñas de Especialistas
Este documento describe los endpoints para la gestión de las reseñas (reviews) que los usuarios realizan a los especialistas.

1. Listar Todas las Reseñas
Función: getAll()

Endpoint: GET /specialist-reviews

Roles: specialist

Descripción: Devuelve una lista de todas las reseñas que no han sido eliminadas. Cada reseña incluye el nombre completo del usuario que la realizó.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "review_id": "uuid-review-1",
      "specialist_id": "uuid-specialist-1",
      "user_id": "uuid-user-1",
      "second_opinion_id": "uuid-request-1",
      "rating": 5,
      "comment": "Excelente atención y muy profesional.",
      "created_at": "2023-10-27 11:00:00",
      "user_name": "John Doe"
    }
  ]
}

Error (400 Bad Request): Si ocurre un error en la base de datos.

2. Obtener una Reseña por ID
Función: getById()

Endpoint: GET /specialist-reviews/{id}

Roles: specialist

Descripción: Devuelve una única reseña identificada por su review_id.

Parámetros (URL):

id (string, requerido): El UUID de la reseña.

Respuestas Posibles
Éxito (200 OK): Devuelve el objeto completo de la reseña, incluyendo user_name.

No Encontrado (200 OK con value: false): Si el id no existe.

ID Inválido (200 OK con value: false): Si el id no es válido.

3. Crear una Nueva Reseña
Función: create()

Endpoint: POST /specialist-reviews

Roles: specialist (Aunque lógicamente debería ser un rol user, la ruta indica specialist).

Descripción: Crea una nueva reseña para un especialista.

Parámetros (Cuerpo form-data):

specialist_id (string, requerido)

user_id (string, requerido)

rating (int, requerido): Calificación numérica.

second_opinion_id (string, opcional): El ID de la solicitud de segunda opinión asociada.

comment (string, opcional): Comentario de texto.

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Review created successfully",
  "data": []
}

Error de Validación (400 Bad Request): Si faltan los campos requeridos (specialist_id, user_id, rating).

4. Actualizar una Reseña
Función: update()

Endpoint: POST /specialist-reviews/{id} (usa POST para emular PUT).

Descripción: Actualiza la calificación (rating) y/o el comentario (comment) de una reseña existente.

Parámetros:

URL: id (string, requerido)

Cuerpo (form-data):

rating (int, requerido)

comment (string, opcional)

_method (string, constante=PUT): Campo para emular el método PUT.

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Review updated successfully",
  "data": []
}

Error de Validación (200 OK con value: false): Si falta el id en la URL o el rating en el cuerpo.

Error de Método (405 Method Not Allowed): Si no se usa el método emulado correctamente.

5. Eliminar una Reseña
Función: delete()

Endpoint: DELETE /specialist-reviews/{id}

Descripción: Realiza un borrado lógico (soft delete) de una reseña.

Parámetros (URL):

id (string, requerido).

Respuestas Posibles
Éxito (200 OK):

{
  "value": true,
  "message": "Review deleted successfully",
  "data": []
}

Error (400 Bad Request): Si falta el id.

Error de Método (405 Method Not Allowed): Si no se usa DELETE.