/**
 * Dashboard Administrator Module
 *
 * All PHP-dependent values (translations, userId, userRole, language)
 * are injected once from the view via initDashboardAdmin(config).
 */

// ─── Module state ────────────────────────────────────────────────
let _cfg = {}

// ─── Public initialiser ──────────────────────────────────────────
export function initDashboardAdmin(config) {
  _cfg = config

  _initTippys()
  _initGreeting()
  _initKpis()
  _initTopUsers()
  _initTopSpecialists()
  _initCountryCharts()
  _initLegacyDashboard()
  _initPdfExports()
}

// ─── Helpers ─────────────────────────────────────────────────────

function animateCount(spanId, target) {
  const el = document.getElementById(spanId)
  if (!el) return
  let current = 0
  const step = Math.ceil(target / 30)
  const timer = setInterval(() => {
    current = Math.min(current + step, target)
    el.textContent = current.toLocaleString()
    if (current >= target) clearInterval(timer)
  }, 30)
}

function formatearFecha(fecha) {
  const partes = fecha.split('-')
  return `${partes[1]}-${partes[2]}-${partes[0]}`
}

function formatDateToYMD(dateStr) {
  if (!dateStr) return ''
  const parts = dateStr.split('/')
  if (parts.length === 3) {
    const [month, day, year] = parts
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
  }
  return dateStr
}

function formatDateForSearch(dateStr) {
  const [year, month, day] = dateStr.split('-')
  return `${month}/${day}/${year}`
}

function getStatusColor(status) {
  switch (status.toLowerCase()) {
    case 'low':
    case 'bajo':
    case 'high':
    case 'alto':
      return 'red-item'
    case 'ok':
    case 'normal':
      return 'green-item'
    default:
      return 'gray-item'
  }
}

function getStatusColor3(status) {
  switch (status.toLowerCase()) {
    case 'partial':
    case 'parcial':
      return 'yellow-item'
    case 'complete':
    case 'completado':
      return 'green-item'
    default:
      return 'yellow-item'
  }
}

function getRankBadgeClass(index) {
  if (index === 0) return 'bg-primary-app'
  if (index === 1) return 'bg-accent'
  if (index === 2) return 'bg-electric-blue'
  return 'bg-sapphire-blue'
}

// ─── Tippy tooltips ──────────────────────────────────────────────

let _tippy1, _tippy2

function _initTippys() {
  if (typeof tippy === 'undefined') return
  const opts = {
    content: 'mm/dd/yyyy',
    trigger: 'mouseenter',
    placement: 'top',
    theme: 'light-border',
    arrow: true,
    animation: 'shift-away',
    delay: [0, 100],
  }
  _tippy1 = tippy('.data-range', opts)
  _tippy2 = tippy('.data-range2', opts)
}

// ─── User greeting ───────────────────────────────────────────────

function _initGreeting() {
  const greetings = {
    EN: { guest: 'Hello, Guest', hello: 'Hello', age: 'Age' },
    ES: { guest: 'Hola, Invitado', hello: 'Hola', age: 'Edad' },
  }
  const lang =
    _cfg.language === 'ES' || _cfg.language === 'EN' ? _cfg.language : 'EN'
  const el = document.getElementById('user-greeting')
  if (!el) return

  if (!_cfg.userId) {
    el.textContent = greetings[lang].guest
    return
  }

  fetch(`administrator/session/${_cfg.userId}`, {
    method: 'GET',
    headers: { Accept: 'application/json' },
  })
    .then((r) => r.json())
    .then((data) => {
      if (data.value === true) {
        const user = data.data
        let greeting = `${greetings[lang].hello}, ${user.first_name} ${user.last_name}`
        if (_cfg.userRole === 1 && typeof user.age !== 'undefined') {
          greeting += ` | ${greetings[lang].age}: ${user.age}`
        }
        el.textContent = greeting
      } else {
        el.textContent = greetings[lang].guest
      }
    })
    .catch(() => {
      el.textContent = greetings[lang].guest
    })
}

// ─── KPI cards ───────────────────────────────────────────────────

function _initKpis() {
  fetch('admin-dashboard/kpis', {
    method: 'GET',
    headers: { Accept: 'application/json' },
  })
    .then((r) => r.json())
    .then((res) => {
      if (res.value && res.data) {
        const d = Array.isArray(res.data) ? res.data[0] : res.data
        animateCount('kpi-total-users', parseInt(d.total_users) || 0)
        animateCount(
          'kpi-total-specialists',
          parseInt(d.total_specialists) || 0,
        )
        animateCount(
          'kpi-standard-verif',
          parseInt(d.standard_verifications) || 0,
        )
        animateCount('kpi-plus-verif', parseInt(d.plus_verifications) || 0)
      }
    })
    .catch((err) => {
      console.error('[AdminDashboard] KPI fetch error:', err)
      ;[
        'kpi-total-users',
        'kpi-total-specialists',
        'kpi-standard-verif',
        'kpi-plus-verif',
      ].forEach((id) => {
        const el = document.getElementById(id)
        if (el) el.textContent = '—'
      })
    })
}

// ─── Top Users table ─────────────────────────────────────────────

function _buildTopUserRows(data) {
  return data.map((row, i) => ({
    rank: `<span class="badge ${getRankBadgeClass(i)} text-white">${i + 1}</span>`,
    full_name: row.full_name || '—',
    email: row.email || '—',
    total_exams: row.total_exams || 0,
  }))
}

function _initTopUsers() {
  fetch('admin-dashboard/top-users?limit=20', {
    method: 'GET',
    headers: { Accept: 'application/json' },
  })
    .then((r) => r.json())
    .then((res) => {
      if (!res.value || !Array.isArray(res.data)) return
      $('#allUsersTable').bootstrapTable('load', _buildTopUserRows(res.data))
    })
    .catch((err) =>
      console.error('[AdminDashboard] Top users fetch error:', err),
    )

  // Refresh handler
  $('#allUsersTable').on('refresh.bs.table', function () {
    fetch('admin-dashboard/top-users?limit=20', {
      headers: { Accept: 'application/json' },
    })
      .then((r) => r.json())
      .then((res) => {
        if (!res.value || !Array.isArray(res.data)) return
        $('#allUsersTable').bootstrapTable('load', _buildTopUserRows(res.data))
      })
  })
}

// ─── Top Specialists table ───────────────────────────────────────

function _buildTopSpecRows(data) {
  return data.map((row, i) => ({
    rank: `<span class="badge ${getRankBadgeClass(i)} text-white">${i + 1}</span>`,
    full_name: row.full_name || '—',
    title_display: row.title_display || '—',
    total_consultations: row.total_consultations || 0,
  }))
}

function _initTopSpecialists() {
  fetch('admin-dashboard/top-specialists?limit=20', {
    method: 'GET',
    headers: { Accept: 'application/json' },
  })
    .then((r) => r.json())
    .then((res) => {
      if (!res.value || !Array.isArray(res.data)) return
      $('#topUsersTable').bootstrapTable('load', _buildTopSpecRows(res.data))
    })
    .catch((err) =>
      console.error('[AdminDashboard] Top specialists fetch error:', err),
    )

  // Refresh handler
  $('#topUsersTable').on('refresh.bs.table', function () {
    fetch('admin-dashboard/top-specialists?limit=20', {
      headers: { Accept: 'application/json' },
    })
      .then((r) => r.json())
      .then((res) => {
        if (!res.value || !Array.isArray(res.data)) return
        $('#topUsersTable').bootstrapTable('load', _buildTopSpecRows(res.data))
      })
  })
}

// ─── Country Distribution (donut + bar + modal) ──────────────────

function _initCountryCharts() {
  let countryDonutChart = null
  let countryBarChart = null
  let allCountriesBarChart = null

  window._countryFullData = { users: [], specialists: [], all: [] }

  // Paleta ordenada de más oscuro (top 1) a más claro
  const PALETTE = [
    '#223976', // Top 1 – Navy oscuro
    '#0072B8', // Top 2 – Azul medio
    '#06ADD9', // Top 3 – Azul brillante
    '#00ADBF', // Top 4 – Teal
    '#82DDED', // Top 5+ – Azul claro
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
    '#82DDED',
  ]

  const OTHER_COLOR = '#B1B1B1'
  const t = _cfg.translations // shorthand

  // ── Build top 4 + others ──

  function buildTop4PlusOthers(data) {
    if (!data || data.length === 0) return []
    const sorted = [...data].sort((a, b) => b.total - a.total)
    if (sorted.length <= 4) return sorted

    const top4 = sorted.slice(0, 4)
    const rest = sorted.slice(4)

    const othersTotal = rest.reduce((s, d) => s + Number(d.total), 0)
    const othersUsers = rest.reduce((s, d) => s + Number(d.users_count || 0), 0)
    const othersSpecs = rest.reduce(
      (s, d) => s + Number(d.specialists_count || 0),
      0,
    )
    const grandTotal = sorted.reduce((s, d) => s + Number(d.total), 0)

    const others = {
      flag: '🌍',
      country_name: t.otherNationalities,
      total: othersTotal,
      users_count: othersUsers,
      specialists_count: othersSpecs,
      percentage:
        grandTotal > 0 ? ((othersTotal / grandTotal) * 100).toFixed(1) : '0.0',
      _isOthers: true,
      _otherCountries: rest,
    }
    return [...top4, others]
  }

  // ── Load country data ──

  function loadCountryData(type = 'users') {
    fetch(`admin-dashboard/country-distribution?limit=100&type=${type}`, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    })
      .then((r) => r.json())
      .then((res) => {
        const donutEl = document.getElementById('donut-chart-admin')
        if (!res.value || !Array.isArray(res.data) || res.data.length === 0) {
          if (donutEl)
            donutEl.innerHTML =
              '<p class="text-muted text-center py-4"><i class="mdi mdi-information-outline"></i> Sin datos</p>'
          return
        }

        window._countryFullData[type] = res.data
        if (donutEl) donutEl.innerHTML = ''

        const data = buildTop4PlusOthers(res.data)
        const grandAll = res.data.reduce((s, d) => s + Number(d.total), 0)

        const labels = data.map((d) => `${d.flag} ${d.country_name}`)
        const users = data.map((d) => Number(d.users_count || 0))
        const specs = data.map((d) => Number(d.specialists_count || 0))

        // ── DONUT (C3) ──
        const colorsMap = {}
        const columns = data.map((d, i) => {
          const lbl = `${d.flag} ${d.country_name}`
          colorsMap[lbl] = d._isOthers
            ? OTHER_COLOR
            : PALETTE[i % PALETTE.length]
          return [lbl, Number(d.total)]
        })

        if (countryDonutChart) donutEl.innerHTML = ''
        countryDonutChart = c3.generate({
          bindto: '#donut-chart-admin',
          data: { columns, type: 'donut', colors: colorsMap },
          donut: {
            title: `${grandAll} total`,
            width: 22,
            label: { show: false },
          },
          tooltip: {
            format: {
              value: (value, ratio) =>
                `${value} (${(ratio * 100).toFixed(1)}%)`,
            },
          },
          size: { height: 220 },
        })

        // ── LEGEND LIST ──
        const legendEl = document.getElementById('country-donut-legend')
        if (legendEl) {
          legendEl.innerHTML = data
            .map((d, i) => {
              const color = d._isOthers
                ? OTHER_COLOR
                : PALETTE[i % PALETTE.length]
              const tooltip =
                d._isOthers && d._otherCountries
                  ? `title="${d._otherCountries.map((c) => c.country_name).join(', ')}"`
                  : ''
              return `
              <li class="d-flex align-items-center justify-content-between py-1 border-bottom" ${tooltip}>
                <span>
                  <span style="display:inline-block;width:10px;height:10px;border-radius:50%;
                    background:${color};margin-right:5px;"></span>
                  ${d.flag} ${d.country_name}
                  ${d._isOthers ? '<i class="mdi mdi-information-outline text-muted ms-1" style="font-size:.8rem"></i>' : ''}
                </span>
                <span class="fw-bold">${d.percentage}%
                  <small class="text-muted">(${d.total})</small>
                </span>
              </li>`
            })
            .join('')
        }

        // ── BAR (ApexCharts) ──
        const barEl = document.getElementById('barlines-chart-admin')
        if (!barEl) return

        if (countryBarChart) {
          try {
            countryBarChart.destroy()
          } catch (e) {}
          countryBarChart = null
        }
        barEl.innerHTML = ''
        const barInner = document.createElement('div')
        barEl.appendChild(barInner)

        const seriesData = []
        const barColors = []
        if (type === 'all' || type === 'users') {
          seriesData.push({ name: t.usersLabel, data: users })
          barColors.push('#223976')
        }
        if (type === 'all' || type === 'specialists') {
          seriesData.push({ name: t.specialistsLabel, data: specs })
          barColors.push(type === 'specialists' ? '#223976' : '#0072B8')
        }

        const isSingleSeries = seriesData.length === 1
        const distributedColors = data.map((d, i) =>
          d._isOthers ? OTHER_COLOR : PALETTE[i % PALETTE.length],
        )

        countryBarChart = new ApexCharts(barInner, {
          series: seriesData,
          chart: {
            type: 'bar',
            height: 340,
            stacked: false,
            toolbar: { show: false },
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '55%',
              borderRadius: 3,
              grouped: true,
              distributed: isSingleSeries,
            },
          },
          colors: isSingleSeries ? distributedColors : barColors,
          dataLabels: { enabled: false },
          stroke: { show: true, width: 2, colors: ['transparent'] },
          xaxis: {
            categories: labels,
            labels: { rotate: -20, style: { fontSize: '11px' } },
          },
          yaxis: {
            title: { text: t.quantityLabel },
            min: 0,
            labels: { formatter: (val) => Math.round(val) },
          },
          legend: {
            show: !isSingleSeries,
            position: 'top',
            horizontalAlign: 'right',
          },
          tooltip: {
            shared: !isSingleSeries,
            intersect: isSingleSeries,
            y: { formatter: (val) => `${val} personas` },
          },
          grid: { padding: { bottom: 10 } },
          fill: { type: 'solid' },
          annotations: {
            xaxis: data
              .map((d, i) =>
                d._isOthers
                  ? {
                      x: labels[i],
                      strokeDashArray: 0,
                      borderColor: OTHER_COLOR,
                      label: {
                        borderColor: OTHER_COLOR,
                        style: {
                          color: '#fff',
                          background: OTHER_COLOR,
                          fontSize: '10px',
                        },
                        text: '',
                      },
                    }
                  : null,
              )
              .filter(Boolean),
          },
        })
        countryBarChart.render()
      })
      .catch((err) => {
        console.error('[AdminDashboard] Country distribution fetch error:', err)
        const el = document.getElementById('donut-chart-admin')
        if (el)
          el.innerHTML =
            '<p class="text-danger text-center py-3"><i class="mdi mdi-alert-circle-outline"></i> Error al cargar datos</p>'
      })
  }

  // ── Modal: All Countries ──

  window.openAllCountriesModal = function () {
    const modal = new bootstrap.Modal(
      document.getElementById('all-countries-modal'),
    )
    modal.show()

    const activeRadio = document.querySelector('.country-filter-radio:checked')
    const type = activeRadio ? activeRadio.value : 'users'

    const modalRadio = document.querySelector(
      `input[name="modal_country_filter"][value="${type}"]`,
    )
    if (modalRadio) modalRadio.checked = true

    renderAllCountriesModal(type)
  }

  function renderAllCountriesModal(type) {
    const loadingEl = document.getElementById('all-countries-loading')
    const contentEl = document.getElementById('all-countries-content')
    const tbodyEl = document.getElementById('all-countries-tbody')
    const chartEl = document.getElementById('all-countries-bar-chart')

    if (loadingEl) loadingEl.style.display = 'block'
    if (contentEl) contentEl.style.display = 'none'

    const cached = window._countryFullData[type]
    if (cached && cached.length > 0) {
      _renderModalContent(cached, type, loadingEl, contentEl, tbodyEl, chartEl)
    } else {
      fetch(`admin-dashboard/country-distribution?limit=100&type=${type}`, {
        method: 'GET',
        headers: { Accept: 'application/json' },
      })
        .then((r) => r.json())
        .then((res) => {
          if (!res.value || !Array.isArray(res.data)) return
          window._countryFullData[type] = res.data
          _renderModalContent(
            res.data,
            type,
            loadingEl,
            contentEl,
            tbodyEl,
            chartEl,
          )
        })
        .catch((err) => {
          console.error('[Modal] Error fetching all countries:', err)
          if (loadingEl)
            loadingEl.innerHTML =
              '<p class="text-danger text-center"><i class="mdi mdi-alert-circle-outline"></i> Error al cargar</p>'
        })
    }
  }

  function _renderModalContent(
    data,
    type,
    loadingEl,
    contentEl,
    tbodyEl,
    chartEl,
  ) {
    const sorted = [...data].sort((a, b) => b.total - a.total)
    const grandTotal = sorted.reduce((s, d) => s + Number(d.total), 0)

    if (tbodyEl) {
      tbodyEl.innerHTML = sorted
        .map((d, i) => {
          const pct =
            grandTotal > 0
              ? ((Number(d.total) / grandTotal) * 100).toFixed(1)
              : '0.0'
          const barWidth =
            grandTotal > 0
              ? Math.max(3, Math.round((Number(d.total) / grandTotal) * 100))
              : 0
          const color = PALETTE[i % PALETTE.length]
          return `
          <tr>
            <td><span class="badge" style="background:${color};color:#fff">${i + 1}</span></td>
            <td>
              <span style="font-size:1.1rem;">${d.flag}</span>
              <strong class="ms-1">${d.country_name}</strong>
            </td>
            <td class="text-center">${Number(d.users_count || 0).toLocaleString()}</td>
            <td class="text-center">${Number(d.specialists_count || 0).toLocaleString()}</td>
            <td class="text-center fw-bold">${Number(d.total).toLocaleString()}</td>
            <td class="text-center" style="min-width:120px">
              <div class="d-flex align-items-center gap-1">
                <div style="flex:1;background:#e9ecef;border-radius:4px;height:8px;">
                  <div style="height:8px;border-radius:4px;width:${barWidth}%;background:${color};transition:width .4s"></div>
                </div>
                <small class="fw-bold" style="min-width:40px">${pct}%</small>
              </div>
            </td>
          </tr>`
        })
        .join('')
    }

    if (loadingEl) loadingEl.style.display = 'none'
    if (contentEl) contentEl.style.display = 'block'

    if (chartEl) {
      if (allCountriesBarChart) {
        try {
          allCountriesBarChart.destroy()
        } catch (e) {}
        allCountriesBarChart = null
      }
      chartEl.innerHTML = ''

      const chartLabels = sorted.map((d) => `${d.flag} ${d.country_name}`)
      const usersData = sorted.map((d) => Number(d.users_count || 0))
      const specsData = sorted.map((d) => Number(d.specialists_count || 0))

      const modalSeriesData = []
      const modalColors = []
      if (type === 'all' || type === 'users') {
        modalSeriesData.push({ name: t.usersLabel, data: usersData })
        modalColors.push('#223976')
      }
      if (type === 'all' || type === 'specialists') {
        modalSeriesData.push({ name: t.specialistsLabel, data: specsData })
        modalColors.push('#0072B8')
      }

      const chartHeight = Math.max(300, sorted.length * 28)
      const chartOptions = {
        series: modalSeriesData,
        chart: {
          type: 'bar',
          height: chartHeight,
          stacked: false,
          toolbar: { show: false },
          animations: { enabled: true, speed: 400 },
        },
        plotOptions: {
          bar: { horizontal: true, barHeight: '60%', borderRadius: 3 },
        },
        colors: modalColors,
        dataLabels: {
          enabled: true,
          formatter: (val) => (val > 0 ? val : ''),
          style: { fontSize: '11px' },
        },
        xaxis: {
          categories: chartLabels,
          labels: { style: { fontSize: '11px' } },
        },
        yaxis: { labels: { style: { fontSize: '11px' } } },
        legend: { position: 'top' },
        tooltip: {
          shared: true,
          intersect: false,
          y: { formatter: (val) => `${val} personas` },
        },
        grid: { padding: { left: 10 } },
      }

      requestAnimationFrame(() => {
        const modalChartInner = document.createElement('div')
        chartEl.appendChild(modalChartInner)
        allCountriesBarChart = new ApexCharts(modalChartInner, chartOptions)
        allCountriesBarChart.render()
      })
    }
  }

  // ── Event listeners ──

  document
    .querySelectorAll('input[name="modal_country_filter"]')
    .forEach((radio) => {
      radio.addEventListener('change', (e) => {
        if (e.target.checked) renderAllCountriesModal(e.target.value)
      })
    })

  loadCountryData('users')

  document.querySelectorAll('.country-filter-radio').forEach((radio) => {
    radio.addEventListener('change', (e) => {
      if (e.target.checked) loadCountryData(e.target.value)
    })
  })
}

// ─── Legacy dashboard functions (old specialist-style cards) ─────

function _initLegacyDashboard() {
  if (!_cfg.userId) return

  const t = _cfg.translations
  const lang =
    _cfg.language === 'ES' || _cfg.language === 'EN' ? _cfg.language : 'EN'

  function loadBiomarkerDashboard(minDate = '', maxDate = '') {
    $.ajax({
      url: `users/count/${_cfg.userId}`,
      method: 'GET',
      dataType: 'json',
      success(resp) {
        if (
          !resp.value ||
          !resp.data ||
          typeof resp.data.total === 'undefined'
        ) {
          $('#bm2-out-range').text('0')
        } else {
          $('#bm2-out-range').text(resp.data.total)
        }
      },
      error() {
        $('#bm2-out-range').text('0')
      },
    })

    $.ajax({
      url: `biomarkers/today-count/1?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
      method: 'GET',
      dataType: 'json',
      success(resp) {
        if (
          !resp.value ||
          !resp.data ||
          typeof resp.data.total === 'undefined'
        ) {
          $('#bm2-finish').text('0')
        } else {
          $('#bm2-finish').text(resp.data.total)
          if (_tippy1 && _tippy1[0])
            _tippy1[0].setContent(
              `${formatearFecha(minDate).replaceAll('-', '/')} - ${formatearFecha(maxDate).replaceAll('-', '/')}`,
            )
        }
      },
      error() {
        $('#bm2-out-range').text('0')
      },
    })

    $.ajax({
      url: `biomarkers/out-streak/${_cfg.userId}?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
      method: 'GET',
      dataType: 'json',
      success(resp) {
        if (
          !resp.value ||
          !resp.data ||
          typeof resp.data.total === 'undefined'
        ) {
          $('#bm2-this-month').text('0.00')
        } else {
          $('#bm2-this-month').text(resp.data.total)
        }
      },
      error() {
        $('#bm2-this-month').text('0.00')
      },
    })

    $.ajax({
      url: `biomarkers/in-range-percentage/${_cfg.userId}?minDate=${encodeURIComponent(minDate)}&maxDate=${encodeURIComponent(maxDate)}`,
      method: 'GET',
      dataType: 'json',
      success(resp) {
        if (
          !resp.value ||
          !resp.data ||
          typeof resp.data.percentage === 'undefined'
        ) {
          $('#bm2-in-range').text('0.00')
        } else {
          $('#bm2-in-range').text(resp.data.percentage.toFixed(2))
        }
      },
      error() {
        $('#bm2-in-range').text('0.00')
      },
    })
  }

  // Alert details delegation
  $(document).on('click', '.show-details-btn', function () {
    const userId = $(this).data('id')
    const user =
      typeof topUsersData !== 'undefined'
        ? topUsersData.find((item) => item.id_user === userId)
        : null

    if (user && user.alert_details && user.alert_details.length > 0) {
      showAlertDetailsModal(user.alert_details)
    } else {
      alert(t.noAlertDetails)
    }
  })

  function showAlertDetailsModal(alertDetails) {
    const $modalBody = $('#alert-details-modal-body').empty()
    alertDetails.forEach((alertItem) => {
      const formattedDate = formatDateForSearch(alertItem.date)
      $modalBody.append(`
        <div class="alert red-item">
          <strong>${t.biomarker}:</strong> ${alertItem.biomarker}<br>
          <strong>${t.value}:</strong> ${alertItem.value}<br>
          <strong>${t.referenceRange}:</strong> ${alertItem.reference_min} - ${alertItem.reference_max}<br>
          <strong>${t.date}:</strong> ${formattedDate}
        </div>
      `)
    })
    $('#alert-details-modal').modal('show')
  }

  // Date range change hook
  window.onDateRangeChange = (minDate, maxDate) => {
    const formattedMin = formatDateToYMD(minDate)
    const formattedMax = formatDateToYMD(maxDate)
    loadBiomarkerDashboard(formattedMin, formattedMax)
  }

  // Initial load
  const now = new Date()
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
  const fmt = (d) => d.toISOString().split('T')[0]
  window.onDateRangeChange(fmt(firstDay), fmt(now))
}

// ─── PDF Exports ─────────────────────────────────────────────────

function _initPdfExports() {
  window.printBiomarkerReport = function () {
    const printContents = document.getElementById('printable-section').innerHTML
    const originalContents = document.body.innerHTML
    document.body.innerHTML = printContents
    window.print()
    document.body.innerHTML = originalContents
    location.reload()
  }

  window.exportTopUsersPDF = function () {
    const { jsPDF } = window.jspdf
    const doc = new jsPDF()
    const table = document.getElementById('allUsersTable')
    const rows = table.querySelectorAll('tr')
    let yPos = 10

    doc.setFontSize(16)
    doc.text('Reporte de Registros Recientes', 20, yPos)
    yPos += 10

    const headerCols = [
      'Fecha',
      'Usuario',
      'Biomarcador',
      'Valor',
      'Estado',
      'Acciones',
    ]
    doc.setFontSize(12)
    doc.text(headerCols.join(' | '), 20, yPos)
    yPos += 10

    rows.forEach((row, index) => {
      if (index === 0) return
      const cols = row.querySelectorAll('td')
      const rowData = []
      cols.forEach((col) => rowData.push(col.innerText))
      doc.setFontSize(10)
      doc.text(rowData.join(' | '), 20, yPos)
      yPos += 10
      if (yPos > 270) {
        doc.addPage()
        yPos = 10
      }
    })
    doc.save('reporte_registros_recientes.pdf')
  }

  window.exportTopSpecialistsPDF = function () {
    const { jsPDF } = window.jspdf
    const doc = new jsPDF()
    const table = document.getElementById('revenueTable')
    if (!table) return
    const rows = table.querySelectorAll('tr')
    let yPos = 10

    doc.setFontSize(16)
    doc.text('Reporte de Alertas', 20, yPos)
    yPos += 10

    rows.forEach((row, index) => {
      if (index === 0) return
      const cols = row.querySelectorAll('td')
      const rowData = []
      cols.forEach((col) => rowData.push(col.innerText))
      doc.setFontSize(12)
      doc.text(rowData.join(' | '), 20, yPos)
      yPos += 10
    })
    doc.save('reporte_alertas.pdf')
  }

  // BootstrapTable formatters (need to be global)
  window.statusFormatter = function (value) {
    return `<span class="badge ${getStatusColor(value)}">${value}</span>`
  }

  window.actionFormatterTop = function (value) {
    return `<button class="btn btn-view action-icon show-details-btn" data-id="${value}"><i class="mdi mdi-eye-outline"></i></button>`
  }

  window.actionFormatter = function (value, row) {
    let url = ''
    const panel = row.panel
    const id = row.record_id
    const biomarker_search = row.biomarker_key

    if (panel === '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6') {
      url = `component_energy_metabolism?id=${id}&select=${biomarker_search}`
    } else if (panel === '81054d57-92c9-4df8-a6dc-51334c1d82c4') {
      url = `component_body_composition?id=${id}&select=${biomarker_search}`
    } else if (panel === 'e6861593-7327-4f63-9511-11d56f5398dc') {
      url = `component_lipid?id=${id}&select=${biomarker_search}`
    } else if (panel === '60819af9-0533-472c-9d5a-24a5df5a83f7') {
      url = `component_renal?id=${id}&select=${biomarker_search}`
    }

    return `
      <a href="${url}">
        <button class="btn btn-view action-icon">
          <i class="mdi mdi-eye-outline"></i>
        </button>
      </a>`
  }
}
