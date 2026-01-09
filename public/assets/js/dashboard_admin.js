import {
  getAllBiomarkers,
  getMostFrequentGlobal,
  getAvgUserBiomarker,
  usersInOutRange,
  getAllUsers,
} from './apiConfig.js'
import {
  getDeviceType,
  timeFramesRecords,
  updateCentralLabel,
  updateChart,
} from './charts.js'

const d = document

let chart1, chart2

// Datos para resetear data picker
const now = new Date()

const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
const today = now

let dataPicker

let stateValues = {
  dateRange: { min: '', max: '' },
  records: [],
  biomarkers: [],
  users: [],
  donutValues: [],
  id_user: '',
  id_biomarker: '',
  status_range: 'all',
}

export const validateDashboardAdmin = () => {
  barAndLinesChart()

  validateDatePicker()
  validateInputs()

  d.addEventListener('click', async (e) => {
    let resetButton = e.target.closest('a[data-reset]')

    if (resetButton) {
      validateDatePicker()
      $('#id_user').val(null).trigger('change')
      $('#status_range').val(null).trigger('change')

      // RESET GRAPHICS
      updateGraphics()
      resetButtonGraphics()
    }
    validateGraphicRange(e)
  })
}

function resetButtonGraphics() {
  let buttonBar = d.querySelectorAll('[data-toggle-bar]')

  buttonBar.forEach((button) => {
    if (button.classList.contains('active')) {
      button.classList.remove('active')
    }
  })

  let buttondonut = d.querySelectorAll('[data-toggle-donut]')

  buttondonut.forEach((button) => {
    if (button.classList.contains('active')) {
      button.classList.remove('active')
    }
  })
}

const validateDatePicker = async (id) => {
  const formatDate = (date) => {
    return new Date(
      date.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' })
    )
      .toISOString()
      .split('T')[0]
  }

  const min = formatDate(firstDay)
  const max = formatDate(today)

  // Set default date values in stateValues
  stateValues.dateRange.min = min
  stateValues.dateRange.max = max

  if (dataPicker && dataPicker.flatpickr) {
    console.log('El input ya tiene una instancia de Flatpickr.')

    dataPicker.flatpickr().clear()
  }

  dataPicker = d.getElementById('dash-daterange')

  dataPicker.flatpickr({
    altInput: true,
    mode: 'range',
    altFormat: 'F j, Y',
    defaultDate: [firstDay, today],
    onChange: async function (selectedDates, dateStr, instance) {
      if (selectedDates.length === 2) {
        const fechaInicioObj = selectedDates[0]
        const fechaFinObj = selectedDates[1]

        const min = formatDate(fechaInicioObj)
        const max = formatDate(fechaFinObj)

        stateValues.dateRange.min = min
        stateValues.dateRange.max = max

        // OBTENER REGISTROS
        updateGraphics()

        if (typeof window.onDateRangeChange === 'function') {
          window.onDateRangeChange(min, max)
        }
      }
    },
    onClose: function () {
      // Optional: actions when closing the calendar
    },
  })

  if (typeof window.onDateRangeChange === 'function') {
    window.onDateRangeChange(min, max)
  }
}

const validateInputs = async () => {
  let biomarkersResponse = await getAllBiomarkers()
  console.log(biomarkersResponse)
  let mostFrecuentBiomarkerResponse = await getMostFrequentGlobal(
    userId,
    stateValues.dateRange.min,
    stateValues.dateRange.max
  )

  stateValues.biomarkers = biomarkersResponse.value
    ? biomarkersResponse.data
    : []

  let mostUsedBiomarker = mostFrecuentBiomarkerResponse.data[0]

  let optionsBiomarker = stateValues.biomarkers.map((biomarker) => {
    if (biomarker.biomarker_id == mostUsedBiomarker) {
      stateValues.id_biomarker = mostUsedBiomarker
      return `<option selected value="${biomarker.biomarker_id}">${biomarker.display_name}</option>`
    }
    return `<option value="${biomarker.biomarker_id}">${biomarker.display_name}</option>`
  })

  // //   OBTENER REGISTROS DE TODOS LOS USUARIOS
  // let responseAvg = await getAvgUserBiomarker({
  //   id_biomarker: stateValues.id_biomarker,
  //   minDate: stateValues.dateRange.min,
  //   maxDate: stateValues.dateRange.max,
  //   status: stateValues.status_range,
  //   id_user: stateValues.id_user,
  // })

  // stateValues.records = responseAvg.data

  // let responseDonut = await usersInOutRange({
  //   id_biomarker: stateValues.id_biomarker,
  //   minDate: stateValues.dateRange.min,
  //   maxDate: stateValues.dateRange.max,
  //   status: stateValues.status_range,
  //   id_user: stateValues.id_user,
  // })

  // console.log(responseDonut)

  // stateValues.donutValues = responseDonut.data
  // stateValues.donutLanguage = responseDonut.data.labels

  // console.log(stateValues)

  let users = await getAllUsers()

  stateValues.users = users.data

  let optionsUsers = stateValues.users.map((user) => {
    return `<option value="${user.user_id}">${user.first_name} - ${user.last_name} </option>`
  })

  optionsUsers.unshift(`<option value=''>All users</option>`)

  let selectBiomarker = d.getElementById('id_biomarker')

  selectBiomarker.innerHTML =
    optionsBiomarker.length > 0
      ? optionsBiomarker.join('')
      : `<option value=''>No biomarkers registered</option>`

  let selectUsers = d.getElementById('id_user')

  selectUsers.innerHTML =
    optionsUsers.length > 0
      ? optionsUsers.join('')
      : `<option value=''>No users registered</option>`

  // INICIALIZAR SELECT DE BIOMARCADOR

  $('#id_biomarker')
    .select2()
    .on('change', async function () {
      const valorSeleccionado = $(this).val()
      stateValues.id_biomarker = valorSeleccionado

      updateGraphics()
    })

  $('#id_user')
    .select2()
    .on('change', function () {
      const valorSeleccionado = $(this).val()
      stateValues.id_user = valorSeleccionado === '' ? null : valorSeleccionado

      updateGraphics()
    })

  $('#status_range')
    .select2()
    .on('change', async function () {
      const valorSeleccionado = $(this).val()
      stateValues.status_range =
        valorSeleccionado === '' ? null : valorSeleccionado

      updateGraphics()
    })

  console.log(stateValues)

  updateGraphics()
}

const updateGraphics = async (type, dateRange) => {
  // OBTENER REGISTROS
  let records = await getAvgUserBiomarker({
    id_biomarker: stateValues.id_biomarker,
    minDate: dateRange ? dateRange.min : stateValues.dateRange.min,
    maxDate: dateRange ? dateRange.max : stateValues.dateRange.max,
    id_user: stateValues.id_user,
    stateValues: stateValues.status_range,
  })

  let donutValues = await usersInOutRange({
    id_biomarker: stateValues.id_biomarker,
    minDate: dateRange ? dateRange.min : stateValues.dateRange.min,
    maxDate: dateRange ? dateRange.max : stateValues.dateRange.max,
    id_user: stateValues.id_user,
    status: stateValues.status_range,
  })

  stateValues.donutLanguage = donutValues.data.labels
  stateValues.records = records.data
  stateValues.donutValues = donutValues.data

  if (type === 'donut') {
    updateChart({
      type: 'donut-admin',
      chart: chart2,
      dateRange: dateRange,
      records: stateValues.donutValues,
      biomarkers: stateValues.biomarkers,
      language: stateValues.donutLanguage,
    })
    return
  }

  if (type === 'bar') {
    updateChart({
      type: 'bar-admin',
      chart: chart1,
      dateRange: dateRange,
      records: stateValues.records,
    })
    return
  }

  // FILTRADO DE REGISTROS

  // RESETEAR DATA PICKER

  updateChart({
    type: 'bar-admin',
    chart: chart1,
    dateRange: stateValues.dateRange,
    records: stateValues.records,
  })

  updateChart({
    type: 'donut-admin',
    chart: chart2,
    dateRange: stateValues.dateRange,
    records: stateValues.donutValues,
    biomarkers: stateValues.biomarkers,
    language: stateValues.donutLanguage,
  })

  resetButtonGraphics()
}

export const barAndLinesChart = () => {
  const element = document.querySelector('#barlines-chart-admin')

  if (!element) {
    console.error(
      `No se encontró el elemento con el selector: #barlines-chart-admin`
    )
    return
  }

  const opcionesIniciales = {
    series: [
      {
        name: '',
        type: 'column',
        data: [],
      },
      {
        name: '',
        type: 'column',
        data: [],
      },
      {
        name: '',
        type: 'line',
        data: [],
      },
    ],
    chart: {
      type: 'bar',
      height: 350,
    },

    yaxis: [
      {
        title: {
          text: '',
        },
      },
      {
        opposite: true,
        title: {
          text: '',
        },
      },
    ],

    colors: ['#a5dfb4', '#ebbcbe'],
    stroke: {
      show: true, // Mostrar el borde (true por defecto)
      width: 3,
      opacity: 0.3, // Opacidad del borde (0 a 1)
    },

    legend: { offsetY: 7 },
    grid: { padding: { bottom: 20 } },
    tooltip: {
      shared: true,
      intersect: false,
      y: {
        formatter: function (value, { seriesIndex, dataPointIndex, w }) {
          if (seriesIndex === 0) {
            return value + ' (Avg)'
          } else if (seriesIndex === 1) {
            return value + ' (Out-of-Range)'
          }
          return value
        },
      },
    },
  }

  chart1 = new ApexCharts(element, opcionesIniciales)
  chart1.render()
}

async function validateGraphicRange(e) {
  if (e.target.dataset.toggleBar) {
    const hoy = dayjs()

    let inicio, fin

    let type = e.target.dataset.toggleBar

    if (type === 'day') {
      inicio = hoy.startOf('day').format('YYYY-MM-DD')
      fin = hoy.endOf('day').format('YYYY-MM-DD')
    }

    if (type === 'week') {
      inicio = hoy.subtract(3, 'day').format('YYYY-MM-DD') // Hoy menos 3 días
      fin = hoy.add(3, 'day').format('YYYY-MM-DD') // Hoy más 3 días
    }
    if (type === 'month') {
      inicio = hoy.startOf('month').format('YYYY-MM-DD')
      fin = hoy.endOf('month').format('YYYY-MM-DD')
    }

    // updateChart(
    //   'bar-admin',
    //   chart1,
    //   { min: inicio, max: fin },
    //   stateValues.records,
    //   stateValues.biomarkers
    // )
    updateGraphics('bar', { min: inicio, max: fin })
  }

  if (e.target.dataset.toggleDonut) {
    const hoy = dayjs()

    let inicio, fin

    let type = e.target.dataset.toggleDonut

    if (type === 'day') {
      inicio = hoy.startOf('day').format('YYYY-MM-DD')
      fin = hoy.endOf('day').format('YYYY-MM-DD')
    }

    if (type === 'week') {
      inicio = hoy.subtract(3, 'day').format('YYYY-MM-DD') // Hoy menos 3 días
      fin = hoy.add(3, 'day').format('YYYY-MM-DD') // Hoy más 3 días
    }
    if (type === 'month') {
      inicio = hoy.startOf('month').format('YYYY-MM-DD')
      fin = hoy.endOf('month').format('YYYY-MM-DD')
    }

    updateGraphics('donut', { min: inicio, max: fin })
  }
  validateActiveButtons(e)
}

function validateActiveButtons(e) {
  if (e.target.dataset.toggleBar) {
    let buttonBar = d.querySelectorAll('[data-toggle-bar]')

    buttonBar.forEach((button) => {
      if (button.classList.contains('active')) {
        button.classList.remove('active')
      }
    })

    e.target.classList.add('active')
  }
  if (e.target.dataset.toggleDonut) {
    let buttondonut = d.querySelectorAll('[data-toggle-donut]')

    buttondonut.forEach((button) => {
      if (button.classList.contains('active')) {
        button.classList.remove('active')
      }
    })
    e.target.classList.add('active')
  }
}
