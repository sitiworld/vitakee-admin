import {
  getAllBiomarkersUsers,
  getMostFrequentBiomarker,
  getUserDashboardData,
  userInOutRange,
} from './apiConfig.js'
import {
  getDeviceType,
  timeFramesRecords,
  updateCentralLabel,
  updateChart,
} from './charts.js'
import { filtrarObjetos } from './helpers.js'

const d = document

let chart1, chart2

// Datos para resetear data picker
const now = new Date()

const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
const today = now

let dataPicker

let dataPickerDay

let initialStateValues = {
  dateRange: { min: '', max: '' },
  records: [],
  biomarkers: [],
  id_biomarker: '',
}
let stateValues = {
  dateRange: { min: '', max: '' },
  records: [],
  donutValues: [],
  biomarkers: [],
  id_biomarker: '',
  status: 'in',
  language: '',
  dateRangeDay: '',
}

export function validateDashboard() {
  // console.log('USER DASHBOARD')

  const dateRange = d.getElementById('dash-daterange')
  if (dateRange) {
    barAndLinesChart()
    // donutChart()
    validateDatePicker('dash-daterange')
    validateDateDayPicker('day-daterange')
    validateInputs()
  }

  d.addEventListener('click', (e) => {
    let resetButton = e.target.closest('a[data-reset]')

    if (resetButton) {
      validateDatePicker()

      updateGraphics()
      resetButtonGraphics()
    }
    validateGraphicRange(e)
  })
}

const formatDate = (date) => {
  return new Date(
    date.toLocaleString('en-US', { timeZone: 'America/Los_Angeles' })
  )
    .toISOString()
    .split('T')[0]
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

function resetFilterByDay() {
  d.getElementById('day-datarange-container').classList.add('invisible')
  d.getElementById('select-by-date').checked = false

  dataPicker.altInput.removeAttribute('disabled')

  if (dataPickerDay && dataPickerDay.flatpickr) {
    dataPickerDay.clear()
  }

  stateValues.filterDayCheck = false
  stateValues.dateRangeDay = ''
}

const validateDateDayPicker = (id) => {
  if (dataPickerDay && dataPickerDay.flatpickr) {
    console.log('El input ya tiene una instancia de Flatpickr.')

    dataPickerDay.clear()
  }

  dataPickerDay = flatpickr(d.getElementById('day-datarange'), {
    altInput: true,

    locale: language.lang,
    altFormat: 'F j, Y',

    defaultDate: [today],
    onChange: function (selectedDate, dateStr, instance) {
      if (selectedDate) {
        const date = formatDate(selectedDate)

        stateValues.dateRangeDay = date

        updateGraphics('bar', {
          min: stateValues.dateRangeDay,
          max: stateValues.dateRangeDay,
        })
      }
    },
    onClose: function () {
      // Optional: actions when closing the calendar
    },
  })

  // stateValues.records = filtrarObjetos(stateValues.records, 'date')

  // Trigger date range change on initial load (even if not manually changed)
}

const validateDatePicker = (id) => {
  const min = formatDate(firstDay)
  const max = formatDate(today)

  // Set default date values in stateValues
  stateValues.dateRange.min = min
  stateValues.dateRange.max = max

  if (dataPicker && dataPicker.flatpickr) {
    console.log('El input ya tiene una instancia de Flatpickr.')

    dataPicker.clear()
  }

  dataPicker = flatpickr(d.getElementById('dash-daterange'), {
    altInput: true,
    mode: 'range',
    locale: language.lang,
    altFormat: 'F j, Y',
    defaultDate: [firstDay, today],
    onChange: function (selectedDates, dateStr, instance) {
      if (selectedDates.length === 2) {
        const fechaInicioObj = selectedDates[0]
        const fechaFinObj = selectedDates[1]

        const min = formatDate(fechaInicioObj)
        const max = formatDate(fechaFinObj)

        stateValues.dateRange.min = min
        stateValues.dateRange.max = max

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

  // stateValues.records = filtrarObjetos(stateValues.records, 'date')

  // Trigger date range change on initial load (even if not manually changed)
  if (typeof window.onDateRangeChange === 'function') {
    window.onDateRangeChange(min, max)
  }
}

// VALIDAR INPUTS Y ACTUALIZAR ESTADO DE FECHA
const validateInputs = async () => {
  let biomarkersResponse = await getAllBiomarkersUsers(userId)

  let mostUsedBiomarkerResponse = await getMostFrequentBiomarker(
    userId,
    stateValues.dateRange.min,
    stateValues.dateRange.max
  )

  stateValues.biomarkers = biomarkersResponse.value
    ? biomarkersResponse.data
    : []

  let mostUsedBiomarker = mostUsedBiomarkerResponse.data[0]

  console.log(stateValues)

  let optionsBiomarker = stateValues.biomarkers.map((biomarker) => {
    if (biomarker.biomarker_id === mostUsedBiomarker) {
      stateValues.id_biomarker = mostUsedBiomarker
      return `<option selected value="${biomarker.biomarker_id}">${biomarker.display_name}</option>`
    }
    return `<option value="${biomarker.biomarker_id}">${biomarker.display_name}</option>`
  })

  let selectBiomarker = d.getElementById('id_biomarker')

  selectBiomarker.innerHTML =
    optionsBiomarker.length > 0
      ? optionsBiomarker.join('')
      : `<option value=''>No biomarkers registered</option>`

  // INICIALIZAR SELECT DE BIOMARCADOR

  $('#id_biomarker')
    .select2()
    .on('change', async function () {
      const valorSeleccionado = $(this).val()
      stateValues.id_biomarker = valorSeleccionado

      resetFilterByDay()
      updateGraphics()
    })

  d.addEventListener('change', (e) => {
    if (e.target.id === 'select-by-date') {
      const dayDateRangeContainer = d.getElementById('day-datarange-container')
      let checked = e.target.checked
      stateValues.filterDayCheck = checked

      if (!stateValues.filterDayCheck) {
        updateGraphics()

        resetFilterByDay()
      } else {
        dataPicker.altInput.setAttribute('disabled', 'true')

        dataPickerDay.element.value = ''

        validateDateDayPicker()

        stateValues.dateRangeDay = formatDate(today)
        updateGraphics('bar', {
          min: stateValues.dateRangeDay,
          max: stateValues.dateRangeDay,
        })

        dayDateRangeContainer.classList.remove('invisible')
      }
    }
  })

  updateGraphics()

  // stateValues.timeFrames = timeFramesRecords(stateValues.records)
}

const updateGraphics = async (type, dateRange, resetButtons = true) => {
  if (type === 'donut') {
    let donutResponse = await userInOutRange({
      id_biomarker: stateValues.id_biomarker,
      minDate: dateRange.min,
      maxDate: dateRange.max,
      status: stateValues.status,
    })

    stateValues.donutValues = donutResponse.data

    updateChart({
      type: 'donut',
      chart: chart1,
      dateRange: dateRange,
      records: stateValues.donutValues,
      biomarkers: stateValues.biomarkers,
      language: donutResponse.data.labels,
    })

    return
  }
  // SI ES TIPO DONUT NO ES NECESARIO
  if (resetButtons) resetButtonGraphics()

  if (type === 'bar') {
    // console.log(dateRange)

    let res = await getUserDashboardData({
      id_biomarker: stateValues.id_biomarker,
      minDate: dateRange.min,
      maxDate: dateRange.max,
      status: 'all',
    })

    stateValues.records = res.data.records

    updateChart({
      type: 'bar',
      chart: chart1,
      dateRange: dateRange,
      records: stateValues.records,
      biomarkers: stateValues.biomarkers,
      id_biomarker: stateValues.id_biomarker,
      status: stateValues.status,
      language: res.data.labels,
    })
    return
  }

  let res = await getUserDashboardData({
    id_biomarker: stateValues.id_biomarker,
    minDate: stateValues.dateRange.min,
    maxDate: stateValues.dateRange.max,
    status: 'all',
  })
  let donutResponse = await userInOutRange({
    id_biomarker: stateValues.id_biomarker,
    minDate: stateValues.dateRange.min,
    maxDate: stateValues.dateRange.max,
    status: stateValues.status,
  })

  stateValues.donutValues = donutResponse.data
  stateValues.records = res.data.records

  updateChart({
    type: 'bar',
    chart: chart1,
    dateRange: stateValues.dateRange,
    records: stateValues.records,
    biomarkers: stateValues.biomarkers,
    id_biomarker: stateValues.id_biomarker,
    status: stateValues.status,
    language: res.data.labels,
  })

  updateChart({
    type: 'donut',
    chart: chart1,
    dateRange: stateValues.dateRange,
    records: stateValues.donutValues,
    biomarkers: stateValues.biomarkers,
    language: donutResponse.data.labels,
  })
}

const getRecordsTotal = () => {
  return stateValues.records.length
}

// RESETEAR

// GRÁFICOS:

export const barAndLinesChart = () => {
  const element = document.querySelector('#barlines-chart')

  if (!element) {
    console.error(
      "El elemento con ID 'barlines-chart' no se encontró en el DOM."
    )
    return
  }
  const colors = ['#1abc9c', '#4a81d4']

  let options = {
    series: [
      {
        name: 'Loading...',
        type: 'column',
        data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16],
      },
      {
        name: 'Loading...',
        type: 'line',
        data: [23, 42, 35, 27, 43, 22, 17, 31, 22, 22, 12, 16],
      },
    ],
    chart: { height: 1, type: 'line', offsetY: 10 },
    stroke: { width: [2, 3] },
    plotOptions: { bar: { columnWidth: '50%' } },
    colors: colors,
    dataLabels: { enabled: true, enabledOnSeries: [1] },
    labels: [],
    xaxis: { type: 'datetime' },
    legend: { offsetY: 7 },
    grid: { padding: { bottom: 20 } },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'light',
        type: 'horizontal',
        shadeIntensity: 0.25,
        // gradientToColors: void 0,
        inverseColors: true,
        opacityFrom: 0.75,
        opacityTo: 0.75,
        stops: [0, 0, 0],
      },
    },
    yaxis: [
      { title: { text: 'In Range' } },
      { opposite: true, title: { text: 'Number of Sales' } },
    ],
  }

  chart1 = new ApexCharts(element, options)
  chart1.render()
}

async function validateGraphicRange(e) {
  if (e.target.dataset.toggleBar) {
    resetFilterByDay()
    const hoy = dayjs()

    let inicio, fin

    let type = e.target.dataset.toggleBar

    if (type === 'day') {
      inicio = hoy.startOf('day').format('YYYY-MM-DD')
      fin = hoy.endOf('day').format('YYYY-MM-DD')
    }

    if (type === 'week') {
      const hoy = dayjs() // hoy

      inicio = hoy.startOf('isoWeek').format('YYYY-MM-DD') // lunes de esta semana
      fin = hoy.format('YYYY-MM-DD') // hoy
    }
    if (type === 'month') {
      inicio = hoy.startOf('month').format('YYYY-MM-DD')
      fin = hoy.endOf('month').format('YYYY-MM-DD')
    }

    updateGraphics('bar', { min: inicio, max: fin }, false)
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
      const hoy = dayjs() // hoy

      inicio = hoy.startOf('isoWeek').format('YYYY-MM-DD') // lunes de esta semana
      fin = hoy.format('YYYY-MM-DD') // hoy

      // console.log(inicio, fin)
    }
    if (type === 'month') {
      inicio = hoy.startOf('month').format('YYYY-MM-DD')
      fin = hoy.endOf('month').format('YYYY-MM-DD')
    }

    updateGraphics('donut', { min: inicio, max: fin }, false)
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

// Ejemplo de función de filtrado sin plugins adicionales:
function filtrarRegistrosPorRangoBasico(registros, inicio, fin) {
  return registros.filter((registro) => {
    const fechaRegistro = dayjs(registro.date)
    const fechaInicio = dayjs(inicio)
    const fechaFin = dayjs(fin)
    return (
      fechaRegistro.isSame(fechaInicio, 'day') ||
      fechaRegistro.isSame(fechaFin, 'day') ||
      (fechaRegistro.isAfter(fechaInicio, 'day') &&
        fechaRegistro.isBefore(fechaFin, 'day'))
    )
  })
}
