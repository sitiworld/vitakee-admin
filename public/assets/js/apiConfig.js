import { hideLoader, showAlert, showLoader } from './helpers/helpers.js'

export function getBaseUrl() {
  const localBaseUrl = 'http://localhost/vitakee-admin' // Cambia esta URL según tu configuración local
  const prodBaseUrl = 'https://admin.vitakee.siti.tech' // Cambia esta URL por la de tu servidor de producción

  // Verificamos si estamos en un entorno de localhost
  if (window.location.origin.includes('localhost')) {
    return localBaseUrl
  }

  // Si no estamos en localhost, asumimos que estamos en producción
  return prodBaseUrl
}

function validateUrl() {
  return getBaseUrl()
}

function handleRequest(url, method, data = null, showAlerts = true) {
  // Ya no se necesita showLoader() ni hideLoader() aquí.

  // Retornamos una nueva Promesa para mantener la compatibilidad con .then() y async/await
  return new Promise((resolve) => {
    // Obtenemos la URL base correcta
    const baseUrl = validateUrl()
    const fullUrl = `${baseUrl}${url}`

    $.ajax({
      url: fullUrl,
      method: method,
      // jQuery es inteligente con los datos. Si 'data' es un objeto, lo formatea
      // como x-www-form-urlencoded por defecto. Si queremos JSON:
      data: data ? JSON.stringify(data) : null,
      contentType: data
        ? 'application/json; charset=utf-8'
        : 'application/x-www-form-urlencoded; charset=UTF-8',
      dataType: 'json', // Le dice a jQuery que espere una respuesta JSON

      success(result) {
        // La lógica de 'success' es el equivalente al bloque 'try' después del 'await'
        console.log(result)

        // Asegura la estructura esperada
        if (result.hasOwnProperty('value')) {
          if (
            showAlerts &&
            result.hasOwnProperty('message') &&
            result.message !== ''
          ) {
            showAlert(result.value, result.message)
          }
          // La promesa se resuelve con el objeto estandarizado
          resolve({
            value: result.value,
            message: result.message || '',
            data: result.data || null,
            labels: result.labels || null,
          })
        } else {
          // Si la estructura no es válida, lo manejamos como un error
          const errorMessage = 'Estructura de respuesta no válida'
          console.error(errorMessage, result)
          if (showAlerts) showAlert(false, errorMessage)
          resolve({
            value: false,
            message: errorMessage,
            data: null,
          })
        }
      },

      error(xhr, status, err) {
        // La lógica de 'error' es el equivalente al bloque 'catch'
        console.error('Error en la petición AJAX:', {
          url: fullUrl,
          status,
          err,
          response: xhr.responseText,
        })
        const errorMessage =
          xhr.responseJSON?.message || err || 'No se pudo procesar la solicitud'

        if (showAlerts) {
          showAlert(false, errorMessage)
        }
        // La promesa también se resuelve en caso de error para no romper la cadena
        resolve({
          value: false,
          message: errorMessage,
          data: null,
          url: url,
        })
      },
    })
  })
}

async function handleRequestFetch(url, method, data = null, showAlerts = true) {
  // showLoader()
  try {
    // Obtenemos la URL base correcta
    const baseUrl = validateUrl()

    // Construimos la URL completa
    const fullUrl = `${baseUrl}${url}`

    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
    }

    if (data) {
      options.body = JSON.stringify(data)
    }

    const response = await fetch(fullUrl, options)

    if (!response.ok) {
      const errorResponse = await response.text() // Evita fallos si no es JSON
      throw {
        status: errorResponse.status,
        statusText: errorResponse.statusText,
        response: errorResponse,
        url: fullUrl,
      }
    }

    // let clone = response.clone()
    // let text = await clone.text()
    // console.log(text)
    // console.log(baseUrl, url)

    const result = await response.json()

    console.log(result)

    // Asegura la estructura esperada
    if (result.hasOwnProperty('value')) {
      if (result.hasOwnProperty('message') && result.message !== '')
        if (showAlerts) showAlert(result.value, result.message)

      return {
        ...result,
        value: result.value,
        message: result.message || '',
        data: result.data || null,
        labels: result.labels || null,
      }
    } else {
      throw new Error('Estructura de respuesta no válida')
    }
  } catch (error) {
    console.log(error)

    if (showAlerts)
      showAlert(false, error.message || 'No se pudo procesar la solicitud')
    return {
      value: false,
      message: error.message || 'Error desconocido',
      data: null,
      url: url,
    }
  } finally {
    // hideLoader()
  }
}

// DASHBOARD DATA

export const getUserDashboardData = async ({
  id_biomarker,
  minDate,
  maxDate,
  status = 'all',
}) => {
  return await handleRequest(
    `/biomarkers/filtered/${id_biomarker}/${minDate}/${maxDate}/${status}`,
    'GET',
  )
}

export const userInOutRange = async ({ id_biomarker, minDate, maxDate }) => {
  return await handleRequest(
    `/biomarkers/in-out-range-percentage_user`,
    'POST',
    {
      id_biomarker,
      min: minDate,
      max: maxDate,
      status: 'all',
    },
  )
}

// USERS

export const getAllUsers = async () => await handleRequest('/users', 'GET')

export const getUserById = async (id) =>
  await handleRequest(`/users/${id}`, 'GET')

export const createUser = async (panelData) =>
  await handleRequest('/users', 'POST', panelData)

export const updateUser = async (id, panelData) =>
  await handleRequest(`/users/${id}`, 'PUT', panelData)

export const deleteUser = async (id) =>
  await handleRequest(`/users/${id}`, 'DELETE')

// Tabpanels

export const getAllTestPanels = async () =>
  await handleRequest('/test-panels', 'GET')

export const getTestPanelById = async (id) =>
  await handleRequest(`/test-panels/${id}`, 'GET')

export const createTestPanel = async (panelData) =>
  await handleRequest('/test-panels', 'POST', panelData)

export const updateTestPanel = async (id, panelData) =>
  await handleRequest(`/test-panels/${id}`, 'PUT', panelData)

export const deleteTestPanel = async (id) =>
  await handleRequest(`/test-panels/${id}`, 'DELETE')

// USERS RECORDS

export const getUserRecords = async (user_id) =>
  await handleRequest(`/test-panels/user-records/${user_id}`, 'GET')

// SPECIALTY
export const getAllSpecialties = async () =>
  await handleRequest('/specialties', 'GET')

export const getSpecialtyById = async (id) =>
  await handleRequest(`/specialties/${id}`, 'GET')

export const createSpecialty = async (specialtyData) =>
  await handleRequest('/specialties', 'POST', specialtyData)

export const updateSpecialty = async (id, specialtyData) =>
  await handleRequest(`/specialties/${id}`, 'PUT', specialtyData)

export const deleteSpecialty = async (id) =>
  await handleRequest(`/specialties/${id}`, 'DELETE')

// COUNTRIES
export const getAllCountries = async () =>
  await handleRequest('/countries', 'GET')

export const getCountryById = async (id) =>
  await handleRequest(`/countries/${id}`, 'GET')

export const createCountry = async (countryData) =>
  await handleRequest('/countries', 'POST', countryData)

export const updateCountry = async (id, countryData) =>
  await handleRequest(`/countries/${id}`, 'PUT', countryData)

export const deleteCountry = async (id) =>
  await handleRequest(`/countries/${id}`, 'DELETE')

// titles
export const getAllTitles = async () => await handleRequest('/titles', 'GET')
export const getTitleById = async (id) =>
  await handleRequest(`/titles/${id}`, 'GET')
export const createTitle = async (data) =>
  await handleRequest('/titles', 'POST', data)
export const updateTitle = async (id, data) =>
  await handleRequest(`/titles/${id}`, 'PUT', data)
export const deleteTitle = async (id) =>
  await handleRequest(`/titles/${id}`, 'DELETE')

// BIOMARKERS

// Records by user and biomarker
export const getRecordsByUser = async (user_id, id_biomarker) => {
  return await handleRequest(
    `/biomarkers/values/${id_biomarker}/${user_id}`,
    'GET',
  )
}

// All biomarkers by user
export const getAllBiomarkersUsers = async (userId) =>
  await handleRequest(`/biomarkers/user_sex/${userId}`, 'GET')

// All biomarkers by user (administrator)

export const usersInOutRange = async ({
  id_biomarker,
  minDate,
  maxDate,
  id_user = null,
  status = 'all',
}) => {
  return await handleRequest(`/biomarkers/in-out-range-percentage`, 'POST', {
    id_biomarker,
    min: minDate,
    max: maxDate,
    id_user,
    status,
  })
}

// Average user by biomarker (administrator)
export const getAvgUserBiomarker = async ({
  id_user,
  id_biomarker,
  minDate,
  maxDate,
  status,
}) => {
  return await handleRequest(`/biomarkers/avg-out-range`, 'POST', {
    id_user,
    id_biomarker,
    min: minDate,
    max: maxDate,
    status,
  })
}

// MOS FRECUENT BIOMARKER BY USER
export const getMostFrequentBiomarker = async (userId, minDate, maxDate) =>
  await handleRequest(
    `/biomarkers/most-frequent`,
    'POST',
    {
      id: userId,
      min: minDate,
      max: maxDate,
    },
    false,
  )
export const getMostFrequentGlobal = async (minDate, maxDate) =>
  await handleRequest(
    `/biomarkers/most-frequent-global`,
    'POST',
    {
      min: minDate,
      max: maxDate,
    },
    false,
  )
export async function getApexLanguage(lang) {
  let response = await fetch(
    `public/assets/libs/apexcharts/locales/${lang}.json`,
  )
  let json = await response.json()

  let { name, options } = json
  return { name, options }
}

// BIOMARKERS CRUD

export const getAllBiomarkers = async () =>
  await handleRequest('/biomarkers/all', 'GET')

export const getBiomarkerById = async (id) =>
  await handleRequest(`/biomarkers/${id}`, 'GET')

export const createBiomarker = async (panelData) =>
  await handleRequest('/biomarkers', 'POST', panelData)

export const updateBiomarker = async (id, panelData) =>
  await handleRequest(`/biomarkers/${id}`, 'PUT', panelData)

export const deleteBiomarker = async (id) =>
  await handleRequest(`/biomarkers/${id}`, 'DELETE')

// COUNTRIES FOR SELECT

export const getCountries = async () =>
  await handleRequestFetch(`/countries/all`, 'GET', null, false)

// notificaciones

// Obtener todas las notificaciones
export async function getAllNotifications() {
  return await handleRequestFetch('/notifications', 'GET')
}

// Obtener notificación por ID
export async function getNotificationById(id) {
  return await handleRequestFetch(`/notifications/${id}`, 'GET')
}

// Crear una notificación (debes pasar un objeto "data" con los datos necesarios)
export async function createNotification(data) {
  return await handleRequestFetch('/notifications', 'POST', data)
}

// Actualizar una notificación por ID (pasas el id y el objeto "data" con los nuevos datos)
export async function updateNotification(id, data) {
  return await handleRequestFetch(`/notifications/${id}`, 'PUT', data)
}

// Eliminar una notificación por ID
export async function deleteNotification(id) {
  return await handleRequestFetch(`/notifications/${id}`, 'DELETE')
}

// Obtener notificaciones por usuario autenticado
export async function getNewNotificationsByUser(page = 1, limit = 10) {
  // Añade page y limit como parámetros

  let offset = (page - 1) * limit

  return await handleRequestFetch(
    `/notifications/active-by-user/1?limit=${limit}&offset=${offset}`, // Pasa limit y offset a la URL
    'GET',
    null,
    false,
  )
}
export async function getReadNotificationsByUser(page = 1, limit = 10) {
  // Añade page y limit como parámetros

  let offset = (page - 1) * limit

  return await handleRequestFetch(
    `/notifications/dismissed-by-user/1?limit=${limit}&offset=${offset}`, // Pasa limit y offset a la URL
    'GET',
    null,
    false,
  )
}

export async function getAllNotificationsByUser(page = 1, limit = 10) {
  // Añade page y limit como parámetros

  let offset = (page - 1) * limit

  return await handleRequestFetch(
    `/notifications/all-by-user/1?limit=${limit}&offset=${offset}`, // Pasa limit y offset a la URL
    'GET',
    null,
    false,
  )
}

// Obtener notificaciones por estado
export async function getNotificationsByStatus(status) {
  return await handleRequestFetch(`/notifications/by-status/${status}`, 'GET')
}

// Obtener notificaciones de alertas activas
export async function getActiveAlertNotifications() {
  return await handleRequestFetch('/notifications/active-alerts', 'GET')
}

// Obtener notificaciones por biomarcador
export async function getNotificationsByBiomarkerId(id_biomarker) {
  return await handleRequestFetch(
    `/notifications/by-biomarker/${id_biomarker}`,
    'GET',
  )
}

// Obtener notificaciones por biomarcador y usuario autenticado
export async function getNotificationsByUserAndBiomarker(id_biomarker) {
  return await handleRequest(
    `/notifications/by-user-biomarker/${id_biomarker}`,
    'GET',
  )
}

// Marcar "no alerta usuario" en notificación
export async function updateNoAlertUser(record_id) {
  await handleRequestFetch(
    '/notifications/no-alert-user',
    'POST',
    {
      record_id,
    },
    false,
  )
}

// Marcar "no alerta admin" en notificación
export async function updateNoAlertAdmin(record_id) {
  // Mismo caso: ajustar a URLSearchParams si el backend lo requiere estrictamente.
  return await handleRequest('/notifications/no-alert-admin', 'POST', {
    record_id,
  })
}

export async function clearUserAlerts() {
  return await handleRequestFetch(
    '/notifications/no-alert-user-all',
    'POST',
    null,
    false,
  )
}
export async function clearNewAlerts() {
  return await handleRequestFetch(
    '/notifications/update-new',
    'POST',
    null,
    false,
  )
}

// AUDIT LOGS

export async function getAuditLogById(id) {
  return await handleRequest(`/auditlog/${id}`, 'GET')
}
export async function getAllAuditLogs() {
  return await handleRequest('/auditlog', 'GET')
}

// SPECIALIST
export async function selectSpecialisties() {
  return await handleRequestFetch('/specialties', 'GET')
}

export async function selectTitles() {
  return await handleRequestFetch('/titles', 'GET')
}
/* ===================== STATES ===================== */

// GET /states
export async function getAllStates() {
  return await handleRequestFetch('/states', 'GET')
}

// GET /states/{id}
export async function getStateById(id) {
  return await handleRequest(`/states/${id}`, 'GET')
}

// POST /states
// payload esperado:
// {
//   country_id, state_name, state_code, iso3166_2,
//   type, timezone, latitude, longitude
// }
export async function createState(payload) {
  // Si tu backend exige URLSearchParams para POST, adapta aquí.
  return await handleRequest('/states', 'POST', payload)
}

// POST /states/{id}
export async function updateState(id, payload) {
  return await handleRequest(`/states/${id}`, 'POST', payload)
}

// DELETE /states/{id}
export async function deleteState(id) {
  return await handleRequest(`/states/${id}`, 'DELETE')
}

/* ===================== CITIES ===================== */

// GET /cities
export async function getAllCities() {
  return await handleRequestFetch('/cities', 'GET')
}

// GET /cities/{id}
export async function getCityById(id) {
  return await handleRequest(`/cities/${id}`, 'GET')
}

// POST /cities
// payload esperado:
// {
//   country_id, state_id, city_name,
//   timezone, latitude, longitude
// }
export async function createCity(payload) {
  return await handleRequest('/cities', 'POST', payload)
}

// POST /cities/{id}
export async function updateCity(id, payload) {
  return await handleRequest(`/cities/${id}`, 'POST', payload)
}

// DELETE /cities/{id}
export async function deleteCity(id) {
  return await handleRequest(`/cities/${id}`, 'DELETE')
}

// ============================================================
// --- PUSH & PREFERENCES ---
// ============================================================
export async function getNotificationPreferences() {
  return await handleRequestFetch('/notifications/preferences', 'GET')
}
export async function updateNotificationPreferences(data) {
  return await handleRequestFetch('/notifications/preferences', 'POST', data)
}
export async function subscribePush(data) {
  return await handleRequestFetch('/push/subscribe', 'POST', data)
}
export async function unsubscribePush(data) {
  return await handleRequestFetch('/push/unsubscribe', 'POST', data)
}
