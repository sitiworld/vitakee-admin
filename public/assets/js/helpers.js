export function procesarDatosParaGrafico({
  year,
  month,
  records,
  range = false,
}) {
  const startOfMonth = dayjs()
    .year(year)
    .month(month - 1)
    .startOf('month')
  const endOfMonth = dayjs()
    .year(year)
    .month(month - 1)
    .endOf('month')

  const minDay = range?.min || 1
  const maxDay = range?.max || endOfMonth.date()

  const daysArray = []
  let currentDay = startOfMonth.clone().date(minDay)

  while (currentDay.isSame(endOfMonth) || currentDay.isBefore(endOfMonth)) {
    if (currentDay.date() <= maxDay) {
      daysArray.push(currentDay.date())
    }
    currentDay = currentDay.add(1, 'day')
    if (currentDay.date() > maxDay) {
      break
    }
  }

  const barChartData = new Array(daysArray.length)
    .fill(null)
    .map((_, index) => ({
      x: daysArray[index],
      y: 0, // Inicializamos en 0, se actualizará con la cantidad de registros
      status: null,
      date: null,
      biomarker: null,
      unit: null,
    }))
  const lineChartData = new Array(daysArray.length).fill(null).map(() => ({
    x: null,
    y: null,
    status: null,
    date: null,
    biomarker: null,
    unit: null,
  }))
  const registrosPorDia = new Array(daysArray.length).fill(0)

  records.forEach((record) => {
    const recordDate = dayjs(record.date)
    const recordYear = recordDate.year()
    const recordMonth = recordDate.month() + 1
    const recordDay = recordDate.date()
    const recordDateFormatted = recordDate.format('YYYY-MM-DD') // Formato para la fecha

    if (
      recordYear === year &&
      recordMonth === month &&
      recordDay >= minDay &&
      recordDay <= maxDay
    ) {
      const dayIndexInRango = daysArray.indexOf(recordDay)

      if (dayIndexInRango !== -1) {
        // Para el gráfico de barras: x es el día, y es el valor, status y date del registro
        barChartData[dayIndexInRango] = {
          x: recordDay,
          y: record.value,
          status: record.status,
          biomarker: record.biomarker,
          unit: record.unit,
          date: recordDateFormatted,
        }
        // Para el gráfico de líneas: x es el día, y es la cantidad de registros, status y date (del último registro del día)
        lineChartData[dayIndexInRango] = {
          x: recordDay,
          y: registrosPorDia[dayIndexInRango] + 1, // Incrementamos la cantidad aquí
          status: record.status,
          unit: record.unit,
          biomarker: record.biomarker,
          date: recordDateFormatted,
        }
        registrosPorDia[dayIndexInRango]++
      }
    }
  })

  return {
    barChart: {
      labels: daysArray, // Los días serán las etiquetas del eje X
      datasets: {
        label: 'Valores de Registros',
        data: barChartData, // 'x' es el día, 'y' es el valor, 'status' y 'date' del registro
      },
    },
    lineChart: {
      labels: daysArray, // Los días serán las etiquetas del eje X
      datasets: {
        label: 'Cantidad de Registros',
        data: barChartData, // 'x' es el día, 'y' es la cantidad, 'status' y 'date' (del último registro)
      },
    },
    dias: daysArray, // Mantenemos el array de días si lo necesitas por separado
  }
}

export function validarRegistrosBiomarker({
  biomarkers,
  records,
  inicio,
  final,
}) {
  let inRange = 0
  let outOfRange = 0

  // Filtrar los registros por rango de fechas si inicio y final están definidos

  const registrosFiltradosPorFecha = records.filter((record) => {
    if (inicio && final) {
      const recordDate = dayjs(record.date).startOf('day')
      const startDate = dayjs(inicio).startOf('day')
      const endDate = dayjs(final).startOf('day')

      return (
        recordDate.isSame(startDate, 'day') ||
        (recordDate.isAfter(startDate, 'day') &&
          recordDate.isBefore(endDate, 'day')) ||
        recordDate.isSame(endDate, 'day')
      )
    }
    return true
  })

  const mappedList = registrosFiltradosPorFecha.map((record) => {
    const biomarker = biomarkers.find(
      (biomarker) => Number(biomarker.id) === Number(record.biomarker_id)
    )

    const value = Number(record.value) // Convertir a número para la comparación
    const referenceMin = Number(biomarker.reference_min)
    const referenceMax = Number(biomarker.reference_max)

    if (value >= referenceMin && value <= referenceMax) {
      inRange++
      return {
        date: record.date,
        value: record.value,
        status: 'in_range',
        biomarker: biomarker.name,
        unit: biomarker.unit,
      }
    } else {
      outOfRange++
      return {
        date: record.date,
        value: record.value,
        status: 'out_of_range',
        biomarker: biomarker.name,
        unit: biomarker.unit,
      }
    }
  })

  const total = inRange + outOfRange

  const inRangePercentage = total > 0 ? ((inRange / total) * 100).toFixed(2) : 0
  const outOfRangePercentage =
    total > 0 ? ((outOfRange / total) * 100).toFixed(2) : 0

  return {
    total,
    inRange,
    outOfRange,
    mappedList,
    percentage: {
      inRange: Number(inRangePercentage),
      outOfRange: Number(outOfRangePercentage),
    },
  }
}

export function procesarInformacionPorRango({ list, year, month, min, max }) {
  const registrosFiltrados = list.filter((registro) => {
    const fechaRegistro = dayjs(registro.date)

    // Asegurémonos de que la fecha del registro sea válida
    if (!fechaRegistro.isValid()) {
      return false
    }

    const registroYear = fechaRegistro.year()
    const registroMonth = fechaRegistro.month() + 1 // dayjs months son de 0 a 11
    const registroDay = fechaRegistro.date()

    // Verificamos que el año y el mes coincidan
    if (registroYear !== year || registroMonth !== month) {
      return false
    }

    // Verificamos que el día esté dentro del rango
    return registroDay >= min && registroDay <= max
  })

  return {
    registrosFiltrados: registrosFiltrados,
  }
}

export function obtenerCantidadRegistrosPorMesYAnio({ list, year, month }) {
  const cantidadRegistros = list.filter((registro) => {
    const fechaRegistro = dayjs(registro.date)
    return (
      fechaRegistro.isValid() &&
      fechaRegistro.year() === year &&
      fechaRegistro.month() + 1 === month // dayjs months son de 0 a 11
    )
  }).length

  return cantidadRegistros
}

export function filtrarObjetos(array, propiedad) {
  const valoresVistos = new Set()
  const objetosUnicos = []

  for (const objeto of array) {
    const valor = objeto[propiedad]
    if (!valoresVistos.has(valor)) {
      valoresVistos.add(valor)
      objetosUnicos.push(objeto)
    }
  }

  return objetosUnicos
}

// Ejemplo de uso:
const datos = [
  { id: 1, nombre: 'Ana', ciudad: 'Caracas' },
  { id: 2, nombre: 'Juan', ciudad: 'Valencia' },
  { id: 3, nombre: 'Ana', ciudad: 'Maracay' },
  { id: 4, nombre: 'Pedro', ciudad: 'Caracas' },
  { id: 5, nombre: 'Juan', ciudad: 'Barquisimeto' },
]

export const months = [
  'Enero',
  'Febrero',
  'Marzo',
  'Abril',
  'Mayo',
  'Junio',
  'Julio',
  'Agosto',
  'Septiembre',
  'Octubre',
  'Noviembre',
  'Diciembre',
]
