📋 Documentación del Módulo: Ubicaciones (Países, Estados, Ciudades)
Este documento detalla los endpoints para la gestión de datos geográficos. Se divide en tres secciones: Países, Estados y Ciudades.

🌍 Países (Countries)
Endpoints para la administración de países.

1. Obtener Todos los Países
Función: showAll()

Endpoint: GET /countries

Rol Requerido: administrator

Descripción: Devuelve una lista de todos los países que no han sido eliminados.

Parámetros: Ninguno.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "country_id": "uuid-country-1",
      "country_name": "Venezuela",
      "suffix": "VE",
      "full_prefix": "+58",
      "normalized_prefix": "58",
      "phone_mask": "####-#######"
    }
  ]
}

2. Obtener País por ID
Función: showById()

Endpoint: GET /countries/{id}

Rol Requerido: administrator

Descripción: Busca y devuelve un único país por su country_id.

Parámetros:

URL: id (string, requerido) - El UUID del país.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": {
    "country_id": "uuid-country-1",
    "country_name": "Venezuela",
    "suffix": "VE",
    "full_prefix": "+58",
    "normalized_prefix": "58",
    "phone_mask": "####-#######"
  }
}

No Encontrado (200 OK)
{
  "value": false,
  "message": "Not found",
  "data": null
}

3. Crear un Nuevo País
Función: create()

Endpoint: POST /countries

Rol Requerido: administrator

Descripción: Crea un nuevo país.

Parámetros:

Cuerpo (JSON):

country_name (string, requerido)

suffix (string, requerido)

full_prefix (string, requerido)

normalized_prefix (string, requerido)

phone_mask (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Created successfully",
  "data": null
}

4. Actualizar un País
Función: update()

Endpoint: PUT /countries/{id}

Rol Requerido: administrator

Descripción: Actualiza la información de un país existente.

Parámetros:

URL: id (string, requerido)

Cuerpo (JSON): Campos a actualizar (ej. country_name, phone_mask, etc.).

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Updated successfully",
  "data": null
}

5. Eliminar un País
Función: delete()

Endpoint: DELETE /countries/{id}

Rol Requerido: administrator

Descripción: Realiza un borrado lógico de un país. Falla si existen registros (usuarios, especialistas, etc.) que dependen de él.

Parámetros:

URL: id (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "Deleted successfully",
  "data": null
}

Error de Dependencia (200 OK)
{
  "value": false,
  "message": "Cannot delete country: related records exist in users.",
  "data": null
}

6. Exportar Países a CSV
Función: export()

Endpoint: GET /countries/export/{id} (El {id} parece no usarse, basado en el modelo)

Rol Requerido: administrator

Descripción: Inicia la descarga de un archivo CSV con todos los países.

Respuesta:

Éxito (200 OK): Un archivo countries_export.csv con Content-Type: text/csv.

🗺️ Estados (States)
Endpoints para la administración de estados/provincias.

1. Obtener Estados (con filtros)
Función: getAll()

Endpoint: GET /states

Roles Requeridos: specialist, administrator, user

Descripción: Devuelve una lista de estados. Soporta múltiples filtros para acotar la búsqueda.

Parámetros (Query String):

country_id (string, opcional): Filtra estados por el UUID del país.

q (string, opcional): Búsqueda por coincidencia parcial en state_name.

state_code (string, opcional): Búsqueda por código de estado exacto.

iso (string, opcional): Búsqueda por código iso3166_2 exacto.

type (string, opcional): Filtra por tipo (ej. 'state', 'province').

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "state_id": "uuid-state-1",
      "country_id": "uuid-country-1",
      "state_name": "Distrito Capital",
      "state_code": "VE-A",
      "iso3166_2": "VEDC"
    }
  ]
}

2. Obtener Estado por ID
Función: getById()

Endpoint: GET /states/{id}

Roles Requeridos: specialist, administrator, user

Descripción: Busca y devuelve un único estado por su state_id.

Parámetros:

URL: id (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "state_id": "uuid-state-1",
      ...
    }
  ]
}

No Encontrado (404 Not Found)
{
    "value": false,
    "message": "State not found.",
    "data": []
}

3. Crear Estado
Función: create()

Endpoint: POST /states

Rol Requerido: administrator

Descripción: Crea un nuevo estado, validando la unicidad de nombre, código e ISO dentro del mismo país.

Parámetros (Cuerpo form-data o JSON):

country_id (string, requerido)

state_name (string, requerido)

state_code, iso3166_2, type, timezone, latitude, longitude (opcionales)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "State created successfully.",
  "data": [ true ]
}

4. Actualizar Estado
Función: update()

Endpoint: POST /states/{id} (Emula PUT)

Rol Requerido: administrator

Descripción: Actualiza un estado.

Parámetros:

URL: id (string, requerido)

Cuerpo: Campos a actualizar.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "State updated successfully.",
  "data": [ true ]
}

5. Eliminar Estado
Función: delete()

Endpoint: DELETE /states/{id}

Rol Requerido: administrator

Descripción: Realiza un borrado lógico de un estado.

Parámetros:

URL: id (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "State deleted successfully.",
  "data": [ true ]
}

🏙️ Ciudades (Cities)
Endpoints para la administración de ciudades.

1. Obtener Ciudades (con filtros)
Función: getAll()

Endpoint: GET /cities

Roles Requeridos: specialist, administrator, user

Descripción: Devuelve una lista de ciudades, con filtros opcionales.

Parámetros (Query String):

country_id (string, opcional)

state_id (string, opcional)

q (string, opcional): Búsqueda por nombre de ciudad (city_name).

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "city_id": "uuid-city-1",
      "state_id": "uuid-state-1",
      "country_id": "uuid-country-1",
      "city_name": "Caracas"
    }
  ]
}

2. Obtener Ciudad por ID
Función: getById()

Endpoint: GET /cities/{id}

Roles Requeridos: specialist, administrator, user

Descripción: Busca y devuelve una única ciudad por su city_id.

Parámetros:

URL: id (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "",
  "data": [
    {
      "city_id": "uuid-city-1",
      ...
    }
  ]
}

3. Crear Ciudad
Función: create()

Endpoint: POST /cities

Rol Requerido: administrator

Descripción: Crea una nueva ciudad, validando unicidad de nombre dentro del mismo estado/país.

Parámetros (Cuerpo form-data o JSON):

country_id (string, requerido)

state_id (string, requerido)

city_name (string, requerido)

timezone, latitude, longitude (opcionales)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "City created successfully.",
  "data": [ true ]
}

4. Actualizar Ciudad
Función: update()

Endpoint: POST /cities/{id} (Emula PUT)

Rol Requerido: administrator

Descripción: Actualiza una ciudad.

Parámetros:

URL: id (string, requerido)

Cuerpo: Campos a actualizar.

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "City updated successfully.",
  "data": [ true ]
}

5. Eliminar Ciudad
Función: delete()

Endpoint: DELETE /cities/{id}

Rol Requerido: administrator

Descripción: Realiza un borrado lógico de una ciudad.

Parámetros:

URL: id (string, requerido)

Respuestas Posibles
Éxito (200 OK)
{
  "value": true,
  "message": "City deleted successfully.",
  "data": [ true ]
}
