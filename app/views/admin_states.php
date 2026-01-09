
    <div class="container-fluid">
      <div class="card-body">
        <h4 class="page-title"><?= $traducciones['states_page_title'] ?? 'States' ?></h4>

        <div id="toolbar" class="d-none">
          <button id="addStateBtn" class="btn btn-add">
            <i class="bi bi-plus"></i>
            <?= $traducciones['add_state_button'] ?? 'Add State' ?>
          </button>
        </div>

        <div class="card">
          <div class="card-body">
            <table id="statesTable"
data-toggle="table" data-search="true" data-show-refresh="true"
                data-page-list="[5, 10, 20]" data-page-size="5" data-show-columns="true" data-pagination="true"
                data-show-pagination-switch="true" class="table-borderless" 
                data-toolbar="#toolbar"
                   data-locale="<?= $locale ?>">
              <thead>
              <tr>
                <th data-field="state_name" data-sortable="true">
                  <?= $traducciones['state_name'] ?? 'State Name' ?>
                </th>
                <th data-field="country_id" data-formatter="countryFormatter" data-sortable="true">
                  <?= $traducciones['country'] ?? 'Country' ?>
                </th>
                <th data-field="state_code" data-sortable="true">
                  <?= $traducciones['state_code'] ?? 'State Code' ?>
                </th>
                <th data-field="iso3166_2" data-sortable="true">
                  <?= $traducciones['iso3166_2'] ?? 'ISO 3166-2' ?>
                </th>
                <!-- CAMBIO: mostrar type traducido con typeFormatter -->
                <th data-field="type" data-sortable="true" data-formatter="typeFormatter">
                  <?= $traducciones['type'] ?? 'Type' ?>
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
                <th data-field="state_id" data-align="center" data-formatter="statesActionFormatter">
                  <?= $traducciones['actions'] ?? 'Action' ?>
                </th>
              </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div class="modal fade" id="stateModal" tabindex="-1" aria-labelledby="stateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form id="stateForm">
              <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="modal-label">
                  <?= $traducciones['state_modal_title'] ?? 'State' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <div class="mb-3">
                  <label for="country_id" class="form-label">
                    <?= $traducciones['country'] ?? 'Country' ?>
                  </label>
                  <select id="country_id" name="country_id" class="form-control panels-input"></select>
                </div>

                <div class="mb-3">
                  <label for="state_name" class="form-label">
                    <?= $traducciones['state_name'] ?? 'State Name' ?>
                  </label>
                  <input type="text" id="state_name" name="state_name" class="form-control panels-input">
                </div>

                <div class="mb-3">
                  <label for="state_code" class="form-label">
                    <?= $traducciones['state_code'] ?? 'State Code' ?>
                  </label>
                  <input type="text" id="state_code" name="state_code" class="form-control panels-input">
                </div>

                <div class="mb-3">
                  <label for="iso3166_2" class="form-label">
                    <?= $traducciones['iso3166_2'] ?? 'ISO 3166-2' ?>
                  </label>
                  <input type="text" id="iso3166_2" name="iso3166_2" class="form-control panels-input" placeholder="e.g., US-CA">
                </div>

                <!-- TYPE as Select2 -->
                <div class="mb-3">
                  <label for="type" class="form-label">
                    <?= $traducciones['type'] ?? 'Type' ?>
                  </label>
<select id="type" name="type" class="form-control panels-input">
  <option value=""><?= $traducciones['select_type_placeholder'] ?? 'Select a type' ?></option>

  <option value="administered area"><?= $traducciones['type_administered_area'] ?? 'Administered Area' ?></option>
  <option value="administration"><?= $traducciones['type_administration'] ?? 'Administration' ?></option>
  <option value="administrative atoll"><?= $traducciones['type_administrative_atoll'] ?? 'Administrative Atoll' ?></option>
  <option value="administrative region"><?= $traducciones['type_administrative_region'] ?? 'Administrative Region' ?></option>
  <option value="administrative territory"><?= $traducciones['type_administrative_territory'] ?? 'Administrative Territory' ?></option>
  <option value="arctic region"><?= $traducciones['type_arctic_region'] ?? 'Arctic Region' ?></option>
  <option value="area"><?= $traducciones['type_area'] ?? 'Area' ?></option>
  <option value="atoll"><?= $traducciones['type_atoll'] ?? 'Atoll' ?></option>
  <option value="autonomous city"><?= $traducciones['type_autonomous_city'] ?? 'Autonomous City' ?></option>
  <option value="autonomous community"><?= $traducciones['type_autonomous_community'] ?? 'Autonomous Community' ?></option>
  <option value="autonomous district"><?= $traducciones['type_autonomous_district'] ?? 'Autonomous District' ?></option>
  <option value="autonomous municipality"><?= $traducciones['type_autonomous_municipality'] ?? 'Autonomous Municipality' ?></option>
  <option value="autonomous region"><?= $traducciones['type_autonomous_region'] ?? 'Autonomous Region' ?></option>
  <option value="autonomous republic"><?= $traducciones['type_autonomous_republic'] ?? 'Autonomous Republic' ?></option>
  <option value="autonomous territorial unit"><?= $traducciones['type_autonomous_territorial_unit'] ?? 'Autonomous Territorial Unit' ?></option>
  <option value="borough"><?= $traducciones['type_borough'] ?? 'Borough' ?></option>
  <option value="borough council"><?= $traducciones['type_borough_council'] ?? 'Borough Council' ?></option>
  <option value="canton"><?= $traducciones['type_canton'] ?? 'Canton' ?></option>
  <option value="capital"><?= $traducciones['type_capital'] ?? 'Capital' ?></option>
  <option value="capital city"><?= $traducciones['type_capital_city'] ?? 'Capital City' ?></option>
  <option value="capital district"><?= $traducciones['type_capital_district'] ?? 'Capital District' ?></option>
  <option value="capital territory"><?= $traducciones['type_capital_territory'] ?? 'Capital Territory' ?></option>
  <option value="chain"><?= $traducciones['type_chain'] ?? 'Chain' ?></option>
  <option value="city"><?= $traducciones['type_city'] ?? 'City' ?></option>
  <option value="city with county rights"><?= $traducciones['type_city_with_county_rights'] ?? 'City with County Rights' ?></option>
  <option value="commune"><?= $traducciones['type_commune'] ?? 'Commune' ?></option>
  <option value="council area"><?= $traducciones['type_council_area'] ?? 'Council Area' ?></option>
  <option value="country"><?= $traducciones['type_country'] ?? 'Country' ?></option>
  <option value="county"><?= $traducciones['type_county'] ?? 'County' ?></option>
  <option value="decentralized regional entity"><?= $traducciones['type_decentralized_regional_entity'] ?? 'Decentralized Regional Entity' ?></option>
  <option value="department"><?= $traducciones['type_department'] ?? 'Department' ?></option>
  <option value="dependency"><?= $traducciones['type_dependency'] ?? 'Dependency' ?></option>
  <option value="district"><?= $traducciones['type_district'] ?? 'District' ?></option>
  <option value="district council"><?= $traducciones['type_district_council'] ?? 'District Council' ?></option>
  <option value="district municipality"><?= $traducciones['type_district_municipality'] ?? 'District Municipality' ?></option>
  <option value="districts under republic administration"><?= $traducciones['type_districts_under_republic_administration'] ?? 'Districts under Republic Administration' ?></option>
  <option value="division"><?= $traducciones['type_division'] ?? 'Division' ?></option>
  <option value="economic prefecture"><?= $traducciones['type_economic_prefecture'] ?? 'Economic Prefecture' ?></option>
  <option value="emirate"><?= $traducciones['type_emirate'] ?? 'Emirate' ?></option>
  <option value="entity"><?= $traducciones['type_entity'] ?? 'Entity' ?></option>
  <option value="European collectivity"><?= $traducciones['type_european_collectivity'] ?? 'European Collectivity' ?></option>
  <option value="federal capital territory"><?= $traducciones['type_federal_capital_territory'] ?? 'Federal Capital Territory' ?></option>
  <option value="federal dependency"><?= $traducciones['type_federal_dependency'] ?? 'Federal Dependency' ?></option>
  <option value="federal district"><?= $traducciones['type_federal_district'] ?? 'Federal District' ?></option>
  <option value="federal territory"><?= $traducciones['type_federal_territory'] ?? 'Federal Territory' ?></option>
  <option value="free municipal consortium"><?= $traducciones['type_free_municipal_consortium'] ?? 'Free Municipal Consortium' ?></option>
  <option value="geographical region"><?= $traducciones['type_geographical_region'] ?? 'Geographical Region' ?></option>
  <option value="governorate"><?= $traducciones['type_governorate'] ?? 'Governorate' ?></option>
  <option value="indigenous region"><?= $traducciones['type_indigenous_region'] ?? 'Indigenous Region' ?></option>
  <option value="island"><?= $traducciones['type_island'] ?? 'Island' ?></option>
  <option value="island council"><?= $traducciones['type_island_council'] ?? 'Island Council' ?></option>
  <option value="land"><?= $traducciones['type_land'] ?? 'Land' ?></option>
  <option value="local council"><?= $traducciones['type_local_council'] ?? 'Local Council' ?></option>
  <option value="london borough"><?= $traducciones['type_london_borough'] ?? 'London Borough' ?></option>
  <option value="metropolitan administration"><?= $traducciones['type_metropolitan_administration'] ?? 'Metropolitan Administration' ?></option>
  <option value="metropolitan city"><?= $traducciones['type_metropolitan_city'] ?? 'Metropolitan City' ?></option>
  <option value="metropolitan collectivity with special status"><?= $traducciones['type_metropolitan_collectivity_with_special_status'] ?? 'Metropolitan Collectivity with Special Status' ?></option>
  <option value="metropolitan department"><?= $traducciones['type_metropolitan_department'] ?? 'Metropolitan Department' ?></option>
  <option value="metropolitan district"><?= $traducciones['type_metropolitan_district'] ?? 'Metropolitan District' ?></option>
  <option value="metropolitan region"><?= $traducciones['type_metropolitan_region'] ?? 'Metropolitan Region' ?></option>
  <option value="municipality"><?= $traducciones['type_municipality'] ?? 'Municipality' ?></option>
  <option value="oblast"><?= $traducciones['type_oblast'] ?? 'Oblast' ?></option>
  <option value="outlying area"><?= $traducciones['type_outlying_area'] ?? 'Outlying Area' ?></option>
  <option value="overseas collectivity"><?= $traducciones['type_overseas_collectivity'] ?? 'Overseas Collectivity' ?></option>
  <option value="overseas region"><?= $traducciones['type_overseas_region'] ?? 'Overseas Region' ?></option>
  <option value="overseas territory"><?= $traducciones['type_overseas_territory'] ?? 'Overseas Territory' ?></option>
  <option value="parish"><?= $traducciones['type_parish'] ?? 'Parish' ?></option>
  <option value="popularate"><?= $traducciones['type_popularate'] ?? 'Popularate' ?></option>
  <option value="prefecture"><?= $traducciones['type_prefecture'] ?? 'Prefecture' ?></option>
  <option value="province"><?= $traducciones['type_province'] ?? 'Province' ?></option>
  <option value="quarter"><?= $traducciones['type_quarter'] ?? 'Quarter' ?></option>
  <option value="region"><?= $traducciones['type_region'] ?? 'Region' ?></option>
  <option value="regional unit"><?= $traducciones['type_regional_unit'] ?? 'Regional Unit' ?></option>
  <option value="republic"><?= $traducciones['type_republic'] ?? 'Republic' ?></option>
  <option value="sheadings"><?= $traducciones['type_sheadings'] ?? 'Sheadings' ?></option>
  <option value="special administrative region"><?= $traducciones['type_special_administrative_region'] ?? 'Special Administrative Region' ?></option>
  <option value="special city"><?= $traducciones['type_special_city'] ?? 'Special City' ?></option>
  <option value="special island authority"><?= $traducciones['type_special_island_authority'] ?? 'Special Island Authority' ?></option>
  <option value="special municipality"><?= $traducciones['type_special_municipality'] ?? 'Special Municipality' ?></option>
  <option value="special region"><?= $traducciones['type_special_region'] ?? 'Special Region' ?></option>
  <option value="special self-governing city"><?= $traducciones['type_special_self_governing_city'] ?? 'Special Self-Governing City' ?></option>
  <option value="special self-governing province"><?= $traducciones['type_special_self_governing_province'] ?? 'Special Self-Governing Province' ?></option>
  <option value="state"><?= $traducciones['type_state'] ?? 'State' ?></option>
  <option value="state city"><?= $traducciones['type_state_city'] ?? 'State City' ?></option>
  <option value="territorial unit"><?= $traducciones['type_territorial_unit'] ?? 'Territorial Unit' ?></option>
  <option value="territory"><?= $traducciones['type_territory'] ?? 'Territory' ?></option>
  <option value="town council"><?= $traducciones['type_town_council'] ?? 'Town Council' ?></option>
  <option value="two-tier county"><?= $traducciones['type_two_tier_county'] ?? 'Two-Tier County' ?></option>
  <option value="union territory"><?= $traducciones['type_union_territory'] ?? 'Union Territory' ?></option>
  <option value="unitary authority"><?= $traducciones['type_unitary_authority'] ?? 'Unitary Authority' ?></option>
  <option value="urban municipality"><?= $traducciones['type_urban_municipality'] ?? 'Urban Municipality' ?></option>
  <option value="village"><?= $traducciones['type_village'] ?? 'Village' ?></option>
  <option value="voivodship"><?= $traducciones['type_voivodship'] ?? 'Voivodship' ?></option>
</select>


                </div>

                <!-- TIMEZONE as Select2 (con GMT offset) -->
                <div class="mb-3">
                  <label for="timezone" class="form-label">
                    <?= $traducciones['timezone'] ?? 'Timezone' ?>
                  </label>
                  <select name="timezone" id="timezone" class="form-control panels-input">
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
                  <label for="latitude" class="form-label">
                    <?= $traducciones['latitude'] ?? 'Latitude' ?>
                  </label>
                  <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control panels-input">
                </div>

                <div class="mb-3">
                  <label for="longitude" class="form-label">
                    <?= $traducciones['longitude'] ?? 'Longitude' ?>
                  </label>
                  <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control panels-input">
                </div>
              </div>

              <div class="modal-footer">
                <button type="submit" class="btn btn-save text-white" id="state-save">
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
      <div class="modal fade" id="viewStateModal" tabindex="-1" aria-labelledby="viewStateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header border-bottom-0">
              <h5 class="modal-title" id="viewStateModalLabel">
                <?= $traducciones['view_state_modal_title'] ?? 'State Details' ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-2"><strong><?= $traducciones['country'] ?? 'Country' ?>:</strong> <div id="v_country"></div></div>
              <div class="mb-2"><strong><?= $traducciones['state_name'] ?? 'State Name' ?>:</strong> <div id="v_state_name"></div></div>
              <div class="mb-2"><strong><?= $traducciones['state_code'] ?? 'State Code' ?>:</strong> <div id="v_state_code"></div></div>
              <div class="mb-2"><strong><?= $traducciones['iso3166_2'] ?? 'ISO 3166-2' ?>:</strong> <div id="v_iso3166_2"></div></div>
              <div class="mb-2"><strong><?= $traducciones['type'] ?? 'Type' ?>:</strong> <div id="v_type"></div></div>
              <div class="mb-2"><strong><?= $traducciones['timezone'] ?? 'Timezone' ?>:</strong> <div id="v_timezone"></div></div>
              <div class="mb-2"><strong><?= $traducciones['latitude'] ?? 'Latitude' ?>:</strong> <div id="v_latitude"></div></div>
              <div class="mb-2"><strong><?= $traducciones['longitude'] ?? 'Longitude' ?>:</strong> <div id="v_longitude"></div></div>
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

<!-- ======= Helpers globales / formatters ======= -->
<script>
  // Mapa global (country_id -> country_name)
  window._countriesMap = new Map();

  // Exponer traducciones
  window.language = window.language || {};

  // Formatter país
  window.countryFormatter = function (value) {
    if (!value) return '';
    var label = window._countriesMap.get(String(value)) || value;
    return '<span>' + label + '</span>';
  };

  // Helper: traducir type según keys language['type_xxx']
  window.translateType = function (val) {
    if (!val) return '';
    try {
      var key = String(val).trim().toLowerCase().replace(/[^\w]+/g, '_'); // ej: "capital district" -> "capital_district"
      var t = (window.language && window.language['type_' + key]) || null;
      if (t) return t;

      // Fallback: Title Case del valor original
      return String(val).replace(/\w\S*/g, function (w) {
        return w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();
      });
    } catch (e) {
      return String(val);
    }
  };

  // Formatter para columna "type"
  window.typeFormatter = function (value) {
    return '<span>' + window.translateType(value) + '</span>';
  };

  // Formatter acciones
  window.statesActionFormatter = function (value, row) {
    var editTitle   = (window.language && window.language['edit_state'])   || 'Edit State';
    var deleteTitle = (window.language && window.language['delete_state']) || 'Delete State';

    return (
      '<div class="btn-group d-inline-flex" role="group">' +
      '  <button class="btn btn-view action-icon viewBtn p-1" data-id="' + row.state_id + '" title="View State">' +
      '    <i class="mdi mdi-eye-outline"></i>' +
      '  </button>' +
      '  <button class="btn btn-pencil action-icon editBtn p-1" data-id="' + row.state_id + '" title="' + editTitle + '">' +
      '    <i class="mdi mdi-pencil-outline"></i>' +
      '  </button>' +
      '  <button class="btn btn-delete action-icon deleteBtn p-1" data-id="' + row.state_id + '" title="' + deleteTitle + '">' +
      '    <i class="mdi mdi-delete-outline"></i>' +
      '  </button>' +
      '</div>'
    );
  };
</script>

<!-- Solo letras para state_name -->
<script>
  document.getElementById('state_name').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/g, '');
  });
</script>

<!-- ======= Lógica principal (ES6 module) ======= -->
<script type="module">
  import {
    getAllStates, getStateById, /* createState, updateState, */ deleteState,
    getAllCountries
  } from './public/assets/js/apiConfig.js'

  import { clearValidationMessages, validateFormFields } from './public/assets/js/helpers/helpers.js'

  const d = document
  const traducciones = <?= json_encode($traducciones) ?>;
  const language = traducciones
  window.language = language

  const countriesMap = window._countriesMap

  const fieldList = {
    state_id: '', country_id: '', state_name: '', state_code: '',
    iso3166_2: '', type: '', timezone: '', latitude: '', longitude: ''
  }

  const modal = new bootstrap.Modal(d.getElementById('stateModal'))
  const form = d.getElementById('stateForm')
  const modalLabel = d.getElementById('modal-label')

  d.getElementById('toolbar').classList.remove('d-none')
  form.addEventListener('submit', e => e.preventDefault())
  d.addEventListener('click', handleClick)
  $('#statesTable').on('refresh.bs.table', loadStatesList)

  initCountrySelect(); initTypeSelect(); initTimezoneSelect();
  await Promise.all([loadCountries(), loadStatesList()])

  /* ============== helpers ============== */

  async function loadCountries() {
    try {
      const res  = await getAllCountries()
      const rows = (res?.data ?? [])
      const $sel = $('#country_id')

      $sel.empty().append(
        new Option(language.select_country_placeholder || 'Select a country', '', true, false)
      )

      rows.forEach(c => {
        const id    = String(c.country_id)
        const label = c.country_name ?? c.name ?? c.code ?? id
        countriesMap.set(id, label)
        $sel.append(new Option(label, id, false, false))
      })
      $sel.trigger('change')
    } catch (e) { console.error('[countries] error', e) }
  }

  function initCountrySelect() {
    if (window.jQuery && $.fn.select2) {
      $('#country_id').select2({
        width: '100%',
        placeholder: language.select_country_placeholder || 'Select a country',
        dropdownParent: $('#stateModal')
      })
    }
  }
  function initTypeSelect() {
    if (window.jQuery && $.fn.select2) {
      $('#type').select2({
        width: '100%',
        placeholder: language.select_type_placeholder || 'Select a type',
        dropdownParent: $('#stateModal'),
        allowClear: true
      })
    }
  }
  function initTimezoneSelect() {
    if (window.jQuery && $.fn.select2) {
      $('#timezone').select2({
        width: '100%',
        placeholder: language.select_timezone_placeholder || 'Select a timezone',
        dropdownParent: $('#stateModal')
      })
    }
  }

  // (por si otro script no lo definió)
  window.countryFormatter ||= (value) => {
    if (!value) return ''
    const label = countriesMap.get(String(value)) || value
    return `<span>${label}</span>`
  }
  window.typeFormatter ||= (value) => `<span>${window.translateType(value) || ''}</span>`
  window.statesActionFormatter ||= (value, row) => {
    const editTitle   = language['edit_state']   || 'Edit State'
    const deleteTitle = language['delete_state'] || 'Delete State'
    return `
      <div class="btn-group d-inline-flex" role="group">
        <button class="btn btn-view action-icon viewBtn p-1" data-id="${row.state_id}" title="View State">
          <i class="mdi mdi-eye-outline"></i>
        </button>
        <button class="btn btn-pencil action-icon editBtn p-1" data-id="${row.state_id}" title="${editTitle}">
          <i class="mdi mdi-pencil-outline"></i>
        </button>
        <button class="btn btn-delete action-icon deleteBtn p-1" data-id="${row.state_id}" title="${deleteTitle}">
          <i class="mdi mdi-delete-outline"></i>
        </button>
      </div>`
  }

  async function loadStatesList() {
    try {
      const res = await getAllStates()
      $('#statesTable').bootstrapTable('load', res?.data || [])
    } catch (e) { console.error('[states] load error', e) }
  }

  function fillViewModal(state) {
    d.getElementById('v_country').textContent    = countriesMap.get(String(state.country_id)) || state.country_id || ''
    d.getElementById('v_state_name').textContent = state.state_name || ''
    d.getElementById('v_state_code').textContent = state.state_code || ''
    d.getElementById('v_iso3166_2').textContent  = state.iso3166_2 || ''
    // CAMBIO: traducción de type en modal
    d.getElementById('v_type').textContent       = window.translateType(state.type) || ''
    d.getElementById('v_timezone').textContent   = state.timezone || ''
    d.getElementById('v_latitude').textContent   = (state.latitude  ?? '') === null ? '' : state.latitude
    d.getElementById('v_longitude').textContent  = (state.longitude ?? '') === null ? '' : state.longitude
  }

  function fillEditForm(state) {
    fieldList.state_id = state.state_id
    $('#country_id').val(state.country_id ?? '').trigger('change')
    d.getElementById('state_name').value = state.state_name ?? ''
    d.getElementById('state_code').value = state.state_code ?? ''
    d.getElementById('iso3166_2').value  = state.iso3166_2 ?? ''
    $('#type').val(state.type ?? '').trigger('change')
    $('#timezone').val(state.timezone ?? '').trigger('change')
    d.getElementById('latitude').value   = state.latitude ?? ''
    d.getElementById('longitude').value  = state.longitude ?? ''
  }

  // POST x-www-form-urlencoded
  async function postFormEncoded(url, dataObj) {
    const body = new URLSearchParams();
    Object.entries(dataObj).forEach(([k, v]) => {
      if (v === undefined || v === null) return;
      body.append(k, String(v));
    });

    const resp = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
      body
    });
    const contentType = resp.headers.get('content-type') || '';
    if (contentType.includes('application/json')) {
      return await resp.json();
    }
    return { value: resp.ok, message: resp.ok ? '' : 'HTTP ' + resp.status, data: [] };
  }

  /* ================= Click handlers ================= */

  async function handleClick(e) {
    if (e.target.closest('#addStateBtn')) {
      clearValidationMessages(form)
      form.reset()
      fieldList.state_id = ''
      $('#country_id').val('').trigger('change')
      $('#type').val('').trigger('change')
      $('#timezone').val('').trigger('change')
      modalLabel.textContent = language.add_new_state || 'Add State'
      modal.show()
    }

    if (e.target.closest('.viewBtn')) {
      const id  = e.target.closest('.viewBtn').dataset.id
      const res = await getStateById(id)
      if (!res?.value) return
      fillViewModal(res.data)
      new bootstrap.Modal(d.getElementById('viewStateModal')).show()
    }

    if (e.target.closest('.editBtn')) {
      const id  = e.target.closest('.editBtn').dataset.id
      const res = await getStateById(id)
      if (!res?.value) return
      clearValidationMessages(form)
      fillEditForm(res.data)
      modalLabel.textContent = language.edit_state || 'Edit State'
      modal.show()
    }

    if (e.target.closest('.deleteBtn')) {
      const id = e.target.closest('.deleteBtn').dataset.id
      Swal.fire({
        title: language.delete_confirm_title_state || 'Confirm deletion?',
        text:  language.delete_confirm_text_state  || 'This action cannot be undone.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
        confirmButtonText: language.delete_confirm_btn_state || 'Delete',
        cancelButtonText:  language.cancel || 'Cancel'
      }).then(async (result) => {
        if (!result.isConfirmed) return
        try {
          const res = await deleteState(id)
          if (res?.value) {
            Swal.fire({ icon:'success',
              title: language.titleSuccess_state || 'State deleted',
              text:  language.success_delete_state || 'The state was deleted successfully.' })
            loadStatesList()
          } else {
            Swal.fire({ icon:'error',
              title: language.titleError_state || 'Delete error',
              text:  res?.message || language.error_delete_state || 'Failed to delete state.' })
          }
        } catch (err) {
          Swal.fire({ icon:'error',
            title: language.titleError_state || 'Delete error',
            text:  err?.message || language.error_delete_state || 'Failed to delete state.' })
        }
      })
    }

    // Guardar (crear/editar) — x-www-form-urlencoded
    if (e.target.closest('#state-save')) {
      const required = ['country_id', 'state_name', 'state_code', 'iso3166_2', 'type', 'timezone', 'latitude', 'longitude']
      if (!validateFormFields(form, required, language.input_generic_error)) return

      const payload = {
        country_id : $('#country_id').val(),
        state_name : d.getElementById('state_name').value?.trim(),
        state_code : d.getElementById('state_code').value?.trim(),
        iso3166_2  : d.getElementById('iso3166_2').value?.trim(),
        type       : $('#type').val() || '',
        timezone   : $('#timezone').val() || '',
        latitude   : d.getElementById('latitude').value  === '' ? '' : String(parseFloat(d.getElementById('latitude').value)),
        longitude  : d.getElementById('longitude').value === '' ? '' : String(parseFloat(d.getElementById('longitude').value))
      }

      try {
        let res
        if (fieldList.state_id) {
          res = await postFormEncoded(`states/${fieldList.state_id}`, payload)
          if (res?.value) {
            modal.hide()
            Swal.fire(
              language.titleSuccess_state || 'Success',
              language.success_update_state || 'State updated successfully.',
              'success'
            )
            await loadStatesList()
          } else {
            Swal.fire('Error', res?.message || language.error_update_state || 'Failed to update state.', 'error')
          }
        } else {
          res = await postFormEncoded('states', payload)
          if (res?.value) {
            modal.hide()
            Swal.fire(
              language.titleSuccess_state || 'Success',
              language.success_create_state || 'State created successfully.',
              'success'
            )
            await loadStatesList()
          } else {
            Swal.fire('Error', res?.message || language.error_create_state || 'Failed to create state.', 'error')
          }
        }
      } catch (e) {
        Swal.fire('Error', e?.message || language.generic_error || 'Unexpected error.', 'error')
      }
    }
  }
</script>
