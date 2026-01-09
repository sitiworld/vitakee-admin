<div class="container-fluid">
  <div class="card-body">
    <h4 class="page-title"><?= $traducciones['cities_page_title'] ?? 'Cities' ?></h4>

    <div id="toolbar-cities" class="d-none">
      <button id="addCityBtn" class="btn btn-add">
        <i class="bi bi-plus"></i>
        <?= $traducciones['add_city_button'] ?? 'Add City' ?>
      </button>
    </div>

    <div class="card">
      <div class="card-body">
        <table id="citiesTable" data-toggle="table" data-search="true" data-show-refresh="true"
          data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true" data-pagination="true"
          data-show-pagination-switch="true" class="table-borderless" data-toolbar="#toolbar-cities"
          data-locale="<?= $locale ?>" data-side-pagination="server" data-query-params="citiesQueryParams"
          data-response-handler="citiesResponseHandler" data-unique-id="city_id">
          <thead>
            <tr>
              <th data-field="city_name" data-sortable="true">
                <?= $traducciones['city_name'] ?? 'City Name' ?>
              </th>
              <th data-field="state_id" data-formatter="stateFormatter" data-sortable="true">
                <?= $traducciones['state'] ?? 'State' ?>
              </th>
              <th data-field="country_id" data-formatter="countryFormatter" data-sortable="true">
                <?= $traducciones['country'] ?? 'Country' ?>
              </th>
              <th data-field="timezone" data-sortable="true">
                <?= $traducciones['timezone'] ?? 'Timezone' ?>
              </th>
              <th data-field="latitude" data-sortable="true">
                <?= $traducciones['latitude'] ?? 'Latitude' ?>
              </th>
              <th data-field="longitude" data-sortable="true">
                <?= $traducciones['longitude'] ?? 'Longitude' ?>
              </th>
              <th data-field="city_id" data-align="center" data-formatter="citiesActionFormatter">
                <?= $traducciones['actions'] ?? 'Action' ?>
              </th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Create/Edit Modal -->
  <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="cityForm">
          <div class="modal-header border-bottom-0">
            <h5 class="modal-title" id="city-modal-label">
              <?= $traducciones['city_modal_title'] ?? 'City' ?>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <!-- Country -->
            <div class="mb-3">
              <label for="city_country_id" class="form-label">
                <?= $traducciones['country'] ?? 'Country' ?>
              </label>
              <select id="city_country_id" name="country_id" class="form-control panels-input"></select>
            </div>

            <!-- State -->
            <div class="mb-3">
              <label for="city_state_id" class="form-label">
                <?= $traducciones['state'] ?? 'State' ?>
              </label>
              <select id="city_state_id" name="state_id" class="form-control panels-input" disabled></select>
            </div>

            <div class="mb-3">
              <label for="city_name" class="form-label">
                <?= $traducciones['city_name'] ?? 'City Name' ?>
              </label>
              <input type="text" id="city_name" name="city_name" class="form-control panels-input">
            </div>

            <!-- TIMEZONE as Select2 -->
            <div class="mb-3">
              <label for="city_timezone" class="form-label">
                <?= $traducciones['timezone'] ?? 'Timezone' ?>
              </label>
              <select name="timezone" id="city_timezone" class="form-control panels-input">
                <?php
                $timezones = DateTimeZone::listIdentifiers();
                foreach ($timezones as $tz) {
                  $dt = new DateTime('now', new DateTimeZone($tz));
                  $offset = $dt->format('P');
                  echo "<option value=\"$tz\">(GMT $offset) $tz</option>";
                }
                ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="city_latitude" class="form-label">
                <?= $traducciones['latitude'] ?? 'Latitude' ?>
              </label>
              <input type="number" step="0.000001" id="city_latitude" name="latitude" class="form-control panels-input">
            </div>

            <div class="mb-3">
              <label for="city_longitude" class="form-label">
                <?= $traducciones['longitude'] ?? 'Longitude' ?>
              </label>
              <input type="number" step="0.000001" id="city_longitude" name="longitude"
                class="form-control panels-input">
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-save text-white" id="city-save">
              <i class="mdi mdi-content-save-outline"></i>
              <?= $traducciones['save'] ?? 'Save' ?>
            </button>
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
              <i class="mdi mdi-cancel"></i>
              <?= $traducciones['cancelButtonText_helper'] ?? 'Cancel' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Modal -->
  <div class="modal fade" id="viewCityModal" tabindex="-1" aria-labelledby="viewCityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-bottom-0">
          <h5 class="modal-title" id="viewCityModalLabel">
            <?= $traducciones['view_city_modal_title'] ?? 'City Details' ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-2"><strong><?= $traducciones['country'] ?? 'Country' ?>:</strong>
            <div id="vc_country"></div>
          </div>
          <div class="mb-2"><strong><?= $traducciones['state'] ?? 'State' ?>:</strong>
            <div id="vc_state"></div>
          </div>
          <div class="mb-2"><strong><?= $traducciones['city_name'] ?? 'City Name' ?>:</strong>
            <div id="vc_city_name"></div>
          </div>
          <div class="mb-2"><strong><?= $traducciones['timezone'] ?? 'Timezone' ?>:</strong>
            <div id="vc_timezone"></div>
          </div>
          <div class="mb-2"><strong><?= $traducciones['latitude'] ?? 'Latitude' ?>:</strong>
            <div id="vc_latitude"></div>
          </div>
          <div class="mb-2"><strong><?= $traducciones['longitude'] ?? 'Longitude' ?>:</strong>
            <div id="vc_longitude"></div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
            <i class="mdi mdi-close-circle-outline"></i>
            <?= $traducciones['close'] ?? 'Close' ?>
          </button>
        </div>
      </div>
    </div>
  </div>

</div> <!-- container -->
<script src="public/assets/js/logout.js"></script>
<script>
  // ======= Variables/formatters GLOBALES =======
  window._countriesMap = window._countriesMap || new Map();
  window._statesMap = window._statesMap || new Map();
  window.language = window.language || {};

  // IMPORTANTES: formatters con fallback a row.*name si viene desde el backend
  window.countryFormatter = function (value, row) {
    if (!value && !row) return '';
    const id = String(value ?? row?.country_id ?? '');
    const label = (row && (row.country_name || row.countryLabel)) ||
      window._countriesMap.get(id) ||
      id;
    return '<span>' + label + '</span>';
  };
  window.stateFormatter = function (value, row) {
    if (!value && !row) return '';
    const id = String(value ?? row?.state_id ?? '');
    const label = (row && (row.state_name || row.stateLabel)) ||
      window._statesMap.get(id) ||
      id;
    return '<span>' + label + '</span>';
  };
  window.citiesActionFormatter = function (value, row) {
    var editTitle = (window.language && window.language['edit_city']) || 'Edit City';
    var deleteTitle = (window.language && window.language['delete_city']) || 'Delete City';
    return (
      '<div class="btn-group d-inline-flex" role="group">' +
      '  <button class="btn btn-view action-icon viewCityBtn p-1" data-id="' + row.city_id + '" title="View City">' +
      '    <i class="mdi mdi-eye-outline"></i>' +
      '  </button>' +
      '  <button class="btn btn-pencil action-icon editCityBtn p-1" data-id="' + row.city_id + '" title="' + editTitle + '">' +
      '    <i class="mdi mdi-pencil-outline"></i>' +
      '  </button>' +
      '  <button class="btn btn-delete action-icon deleteCityBtn p-1" data-id="' + row.city_id + '" title="' + deleteTitle + '">' +
      '    <i class="mdi mdi-delete-outline"></i>' +
      '  </button>' +
      '</div>'
    );
  };

  // === Bootstrap Table -> query params hacia el backend ===
  window.citiesQueryParams = function (params) {
    return {
      limit: params.limit,
      offset: params.offset,
      search: params.search,
      sort: params.sort,
      order: params.order
      // puedes agregar filtros extra aquí: country_id, state_id, etc.
    };
  };

  // === Handler que también completa los mapas si vienen nombres en la respuesta ===
  window.citiesResponseHandler = function (res) {
    try {
      const rows = Array.isArray(res?.rows) ? res.rows : (Array.isArray(res) ? res : []);
      let added = false;
      rows.forEach(r => {
        if (r?.country_id && r?.country_name && !window._countriesMap.has(String(r.country_id))) {
          window._countriesMap.set(String(r.country_id), r.country_name);
          added = true;
        }
        if (r?.state_id && r?.state_name && !window._statesMap.has(String(r.state_id))) {
          window._statesMap.set(String(r.state_id), r.state_name);
          added = true;
        }
      });
      // Si agregamos etiquetas nuevas después de render, refrescamos silenciosamente
      if (added) {
        setTimeout(() => {
          $('#citiesTable').bootstrapTable('refresh', { silent: true });
        }, 0);
      }
    } catch (_) { }
    return res;
  };

  // ===== Helpers robustos para Bootstrap 4/5 (evita _config/backdrop undefined) =====
  function _isBS5() {
    return !!(window.bootstrap && typeof window.bootstrap.Modal === 'function');
  }
  function _isBS4() {
    return !!(window.jQuery && $.fn && typeof $.fn.modal === 'function');
  }
  const _modalCache = new Map();
  function getModalInstanceBS5(modalEl) {
    if (!_isBS5() || !modalEl) return null;
    if (_modalCache.has(modalEl)) return _modalCache.get(modalEl);
    const instance = new bootstrap.Modal(modalEl, {
      backdrop: 'static',
      keyboard: true,
      focus: true
    });
    _modalCache.set(modalEl, instance);
    return instance;
  }
  function openModalById(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (_isBS5()) {
      const inst = getModalInstanceBS5(el);
      inst && inst.show();
      return;
    }
    if (_isBS4()) {
      $(el).modal({ backdrop: 'static', keyboard: true, focus: true, show: true });
      return;
    }
    console.warn('Bootstrap 4/5 no detectado para abrir modal:', id);
  }
  function closeModalById(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (_isBS5()) {
      const inst = getModalInstanceBS5(el);
      inst && inst.hide();
      return;
    }
    if (_isBS4()) {
      $(el).modal('hide');
      return;
    }
  }
</script>

<script type="module">
  import {
    getCityById, deleteCity,
    getAllStates, getStateById, getAllCountries
  } from './public/assets/js/apiConfig.js'

  import { clearValidationMessages, validateFormFields } from './public/assets/js/helpers/helpers.js'

  const d = document
  const traducciones = <?= json_encode($traducciones) ?>;
  const language = traducciones
  window.language = language

  const countriesMap = window._countriesMap
  const statesMap = window._statesMap

  const cityFieldList = {
    city_id: '', state_id: '', country_id: '',
    city_name: '', timezone: '', latitude: '', longitude: ''
  }

  const cityForm = d.getElementById('cityForm')
  const cityModalLabel = d.getElementById('city-modal-label')

  d.getElementById('toolbar-cities').classList.remove('d-none')
  cityForm.addEventListener('submit', e => e.preventDefault())
  d.addEventListener('click', handleCityClick)

  initCountrySelect(); initStateSelect(); initTimezoneSelect();

  // === Inicialización controlada para evitar carreras con la tabla ===
  await init();

  async function init() {
    // 1) Cargar países
    await loadCountries();
    // 2) Pre-cargar TODOS los estados para los formatters
    await preloadAllStatesForFormatter();
    // 3) Ahora sí, activar la URL de la tabla (evita que cargue antes de tener mapas)
    $('#citiesTable').bootstrapTable('refreshOptions', { url: 'cities/all' });
  }

  /* =================== Selects =================== */
  async function loadCountries() {
    try {
      const res = await getAllCountries()
      const rows = (res?.data ?? [])
      const $sel = $('#city_country_id')

      $sel.empty().append(
        new Option(language.select_country_placeholder || 'Select a country', '', true, false)
      )

      rows.forEach(c => {
        const id = String(c.country_id)
        const label = c.country_name ?? c.name ?? c.code ?? id
        countriesMap.set(id, label)
        $sel.append(new Option(label, id, false, false))
      })
      $sel.trigger('change')
    } catch (e) { console.error('[cities:countries] error', e) }
  }

  // Pre-carga para mapear nombres de todos los estados (para stateFormatter del grid)
  async function preloadAllStatesForFormatter() {
    try {
      const res = await getAllStates()
      const rows = (res?.data ?? [])
      rows.forEach(s => {
        const id = String(s.state_id)
        const label = s.state_name ?? s.name ?? id
        if (!statesMap.has(id)) statesMap.set(id, label)
      })
    } catch (e) { console.error('[cities:preload states] error', e) }
  }

  // API para traer estados filtrados por país
  async function fetchStatesByCountry(countryId) {
    const url = countryId ? `states?country_id=${encodeURIComponent(countryId)}` : 'states'
    const res = await fetch(url, { method: 'GET' }).then(r => r.json())
    return res?.data ?? res ?? []
  }

  // Pobla el select de estados con base en un país (sin limpiar el mapa global)
  async function populateStateSelect(countryId = null, selectedStateId = '') {
    try {
      const rows = await fetchStatesByCountry(countryId)
      const $sel = $('#city_state_id')

      $sel.prop('disabled', true)
      $sel.empty().append(
        new Option(language.select_state_placeholder || 'Select a state', '', true, false)
      )

      rows.forEach(s => {
        const id = String(s.state_id)
        const label = s.state_name ?? s.name ?? id
        // Mantenemos el mapa global para el formatter del grid
        if (!statesMap.has(id)) statesMap.set(id, label)
        $sel.append(new Option(label, id, false, false))
      })

      if (selectedStateId) {
        $sel.val(String(selectedStateId))
      } else {
        $sel.val('')
      }
      $sel.trigger('change')
      $sel.prop('disabled', false)
    } catch (e) {
      console.error('[cities:populateStateSelect] error', e)
      $('#city_state_id').prop('disabled', false)
    }
  }

  function initCountrySelect() {
    if (window.jQuery && $.fn.select2) {
      $('#city_country_id').select2({
        width: '100%',
        placeholder: language.select_country_placeholder || 'Select a country',
        dropdownParent: $('#cityModal'),
        allowClear: true
      })
      // Filtrado de estados por país
      $('#city_country_id').on('change', async function () {
        const cid = $(this).val() || null
        // Limpia y deshabilita mientras carga
        $('#city_state_id')
          .empty()
          .append(new Option(language.select_state_placeholder || 'Select a state', '', true, false))
          .val('').trigger('change')
          .prop('disabled', true)
        await populateStateSelect(cid)
      })
    }
  }
  function initStateSelect() {
    if (window.jQuery && $.fn.select2) {
      $('#city_state_id').select2({
        width: '100%',
        placeholder: language.select_state_placeholder || 'Select a state',
        dropdownParent: $('#cityModal'),
        allowClear: true
      })
    }
  }
  function initTimezoneSelect() {
    if (window.jQuery && $.fn.select2) {
      $('#city_timezone').select2({
        width: '100%',
        placeholder: language.select_timezone_placeholder || 'Select a timezone',
        dropdownParent: $('#cityModal')
      })
    }
  }

  function refreshCitiesTable(silent = true) {
    $('#citiesTable').bootstrapTable('refresh', { silent });
  }

  function fillCityViewModal(city) {
    d.getElementById('vc_country').textContent = countriesMap.get(String(city.country_id)) || city.country_name || city.country_id || ''
    d.getElementById('vc_state').textContent = statesMap.get(String(city.state_id)) || city.state_name || city.state_id || ''
    d.getElementById('vc_city_name').textContent = city.city_name || ''
    d.getElementById('vc_timezone').textContent = city.timezone || ''
    d.getElementById('vc_latitude').textContent = (city.latitude ?? '') === null ? '' : city.latitude
    d.getElementById('vc_longitude').textContent = (city.longitude ?? '') === null ? '' : city.longitude
  }

  // Editar: set país -> poblar estados de ese país -> seleccionar estado
  async function fillCityEditForm(city) {
    cityFieldList.city_id = city.city_id
    $('#city_country_id').val(city.country_id ?? '').trigger('change')
    await populateStateSelect(city.country_id ?? null, city.state_id ?? '')
    d.getElementById('city_name').value = city.city_name ?? ''
    $('#city_timezone').val(city.timezone ?? '').trigger('change')
    d.getElementById('city_latitude').value = city.latitude ?? ''
    d.getElementById('city_longitude').value = city.longitude ?? ''
  }

  /* ================= Click handlers ================= */
  async function handleCityClick(e) {
    // Nuevo
    if (e.target.closest('#addCityBtn')) {
      clearValidationMessages(cityForm)
      cityForm.reset()
      cityFieldList.city_id = ''

      $('#city_country_id').val('').trigger('change')

      // Estado deshabilitado hasta elegir país
      $('#city_state_id')
        .empty()
        .append(new Option(language.select_state_placeholder || 'Select a state', '', true, false))
        .val('').trigger('change')
        .prop('disabled', true)

      $('#city_timezone').val('').trigger('change')

      cityModalLabel.textContent = language.add_new_city || 'Add City'
      openModalById('cityModal')
    }

    // Ver
    if (e.target.closest('.viewCityBtn')) {
      const id = e.target.closest('.viewCityBtn').dataset.id
      const res = await getCityById(id)
      if (!res?.value) return
      fillCityViewModal(res.data)
      openModalById('viewCityModal')
    }

    // Editar
    if (e.target.closest('.editCityBtn')) {
      const id = e.target.closest('.editCityBtn').dataset.id
      const res = await getCityById(id)
      if (!res?.value) return
      clearValidationMessages(cityForm)
      await fillCityEditForm(res.data)
      cityModalLabel.textContent = language.edit_city || 'Edit City'
      openModalById('cityModal')
    }

    // Eliminar
    if (e.target.closest('.deleteCityBtn')) {
      const id = e.target.closest('.deleteCityBtn').dataset.id
      Swal.fire({
        title: language.delete_confirm_title_city || 'Confirm deletion?',
        text: language.delete_confirm_text_city || 'This action cannot be undone.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: language.delete_confirm_btn_city || 'Delete',
        cancelButtonText: language.cancel || 'Cancel'
      }).then(async (result) => {
        if (!result.isConfirmed) return
        try {
          const res = await deleteCity(id)
          if (res?.value) {
            Swal.fire({
              icon: 'success',
              title: language.titleSuccess_city || 'City deleted',
              text: language.success_delete_city || 'The city was deleted successfully.'
            })
            refreshCitiesTable()
          } else {
            Swal.fire({
              icon: 'error',
              title: language.titleError_city || 'Delete error',
              text: res?.message || language.error_delete_city || 'Failed to delete city.'
            })
          }
        } catch (err) {
          Swal.fire({
            icon: 'error',
            title: language.titleError_city || 'Delete error',
            text: err?.message || language.error_delete_city || 'Failed to delete city.'
          })
        }
      })
    }

    // Guardar (crear/editar)
    if (e.target.closest('#city-save')) {
      const required = ['country_id', 'state_id', 'city_name', 'timezone', 'latitude', 'longitude']
      const ok = validateFormFields(cityForm, required, language.input_generic_error)
      if (!ok) return

      const payload = {
        country_id: $('#city_country_id').val(),
        state_id: $('#city_state_id').val(),
        city_name: d.getElementById('city_name').value?.trim(),
        timezone: $('#city_timezone').val() || '',
        latitude: d.getElementById('city_latitude').value === '' ? '' : String(parseFloat(d.getElementById('city_latitude').value)),
        longitude: d.getElementById('city_longitude').value === '' ? '' : String(parseFloat(d.getElementById('city_longitude').value))
      }

      try {
        let res
        if (cityFieldList.city_id) {
          // Update
          res = await fetch(`cities/${cityFieldList.city_id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams(payload)
          }).then(r => r.json())
          if (res?.value) {
            closeModalById('cityModal')
            Swal.fire(language.titleSuccess_city || 'Success',
              language.success_update_city || 'City updated successfully.',
              'success')
            refreshCitiesTable()
          } else {
            Swal.fire('Error', res?.message || language.error_update_city || 'Failed to update city.', 'error')
          }
        } else {
          // Create
          res = await fetch('cities', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
            body: new URLSearchParams(payload)
          }).then(r => r.json())
          if (res?.value) {
            closeModalById('cityModal')
            Swal.fire(language.titleSuccess_city || 'Success',
              language.success_create_city || 'City created successfully.',
              'success')
            refreshCitiesTable(false)
          } else {
            Swal.fire('Error', res?.message || language.error_create_city || 'Failed to create city.', 'error')
          }
        }
      } catch (e) {
        Swal.fire('Error', e?.message || language.generic_error || 'Unexpected error.', 'error')
      }
    }
  }
</script>