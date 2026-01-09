<div class="container-fluid">
    <h4 class="page-title"><?= $traducciones['session_log_view_title'] ?? 'Session Audit Log' ?></h4>
    <div id="toolbar">
        <button class="btn btn-add-user" id="btnSessionConfig">
            <i class="mdi mdi-cog-outline"></i>
            <?= $traducciones['session_config'] ?? 'Session Config' ?>
        </button>
        <button class="btn btn-action-lipid" id="btnExportCSV">
            <i class="mdi mdi-file-export-outline"></i>
            <?= $traducciones['export_csv_button'] ?? 'Export to CSV' ?>
        </button>


    </div>

    <div class="card">
        <div class="card-body">
            <table id="sessionAuditTable" class="table table-borderless" data-toggle="table" data-search="true"
                data-show-refresh="true" data-page-list="[5, 10, 20, 50, 100]" data-page-size="5" data-pagination="true"
                data-show-columns="true" data-show-pagination-switch="true" data-toolbar="#toolbar"
                data-locale="<?= $locale ?>">


                <thead>
                    <tr>
                        <th data-field="user_name"><?= $traducciones['user_name'] ?? 'Username' ?></th>
                        <th data-field="user_type"><?= $traducciones['user_type'] ?? 'User Type' ?></th>
                        <th data-field="full_name"><?= $traducciones['full_name'] ?? 'Full Name' ?></th>
                        <th data-field="login_time"><?= $traducciones['login_time'] ?? 'Login Time' ?>
                        </th>
                        <th data-field="logout_time">
                            <?= $traducciones['logout_time'] ?? 'Logout Time' ?>
                        </th>
                        <th data-field="actions" data-formatter="detalleFormatter" data-align="center">
                            <?= $traducciones['audit_table_column_actions'] ?? 'Details' ?>
                        </th>
                    </tr>
                </thead>


            </table>
        </div>
    </div>

</div>

<!-- Modal de Detalles -->
<!-- Modal de Detalles -->
<div class="modal fade" id="sessionDetailModal" tabindex="-1" aria-labelledby="sessionDetailModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="sessionDetailModalLabel">
                    <!-- Título dinámico desde JS -->
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row" id="sessionDetailContent">
                    <!-- Aquí se insertan los detalles dinámicos -->
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> <?= $traducciones['close'] ?? 'Close' ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sessionConfigModal" tabindex="-1" aria-labelledby="sessionConfigLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title" id="sessionConfigLabel">
                    <?= $traducciones['session_config'] ?? 'Session Configuration' ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sessionConfigForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inactivityTime" class="form-label">
                            <?= $traducciones['inactivity_time'] ?? 'Inactivity Time (min)' ?> <span
                                class="text-danger">*</span>
                        </label>
                        <input type="text" min="1" max="99999" class="form-control number" id="inactivityTime"
                            name="inactivityTime" required>
                    </div>
                    <div class="text-muted fst-italic small">
                        <?= $traducciones['required_fields_note'] ?? '* Required fields' ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline"></i>
                        <?= $traducciones['save'] ?></button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class="mdi mdi-cancel"></i>
                        <?= $traducciones['cancel'] ?></button>

                </div>
            </form>
        </div>
    </div>
</div>




<script>
    const mensajes2 = {
        <?= $_SESSION['lang'] ?>: <?= json_encode($traducciones, JSON_UNESCAPED_UNICODE) ?>
    };

    document.addEventListener('DOMContentLoaded', () => {
        const masks = document.querySelectorAll('.form-control.number');

        masks.forEach(input => {
            // Al teclear
            input.addEventListener('input', () => {
                // Elimina todo lo que no sea dígito, punto o coma
                input.value = input.value.replace(/[^0-9\.,]/g, '');
            });

            // Al pegar
            input.addEventListener('paste', (e) => {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (/[^0-9\.,]/.test(paste)) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
<script src="public/assets/js/logout.js"></script>
<script type="module">
    function hideAllModals() {
        // Oculta cualquier modal abierto
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modalEl => {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        });

        // Elimina cualquier backdrop colgado
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Limpia scroll del body si quedó bloqueado
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    const tableId = '#sessionAuditTable';
    const exportBtnId = 'btnExportCSV';

    document.addEventListener('DOMContentLoaded', () => {
        inicializarTablaAuditoria();
        configurarExportarCSV();
    });

    function inicializarTablaAuditoria() {
        $(tableId).bootstrapTable({
            columns: [
                { field: 'user_name', title: language?.user_name || 'Username', sortable: true },
                {
                    field: 'user_type',
                    title: language?.user_type || 'User Type',
                    formatter: tipo => tipo.charAt(0).toUpperCase() + tipo.slice(1), sortable: true
                },
                { field: 'full_name', title: language?.full_name || 'Full Name', sortable: true },
                {
                    field: 'login_time',
                    title: language?.login_time || 'Login Time',
                    sortable: true,
                    formatter: formatDateTime
                },
                {
                    field: 'logout_time',
                    title: language?.logout_time || 'Logout Time',
                    sortable: true,
                    formatter: formatDateTime
                },


                {
                    field: 'actions',
                    title: language?.audit_table_column_actions || 'Details',
                    align: 'center',
                    formatter: detalleFormatter,
                    events: {
                        'click .btn-view-details': (e, value, row) => mostrarDetalle(row),
                        'click .btn-kick-session': (e, value, row) => expulsarSesion(row)
                    }
                }
            ],
            locale: idioma === 'ES' ? 'es-ES' : 'en-US',
            search: true,
            pagination: true,
            pageSize: 5,
            pageList: [5, 10, 20, 50, 100],
            showRefresh: true,
            showColumns: true,
            url: 'session-audit',
            responseHandler: res => res.data || []
        });
    }
    document.getElementById('btnSessionConfig')?.addEventListener('click', () => {
        const modal = new bootstrap.Modal(document.getElementById('sessionConfigModal'));
        modal.show();
    });

    document.getElementById('btnSessionConfig')?.addEventListener('click', async () => {
        hideAllModals(); // 🔧 Limpia cualquier modal anterior

        const modal = new bootstrap.Modal(document.getElementById('sessionConfigModal'));

        try {
            const res = await fetch('<?= BASE_URL ?>/session-config');
            const json = await res.json();

            if (json.value && json.data) {
                const { timeout_minutes } = json.data;
                document.getElementById('inactivityTime').value = timeout_minutes || '';
            } else {
                document.getElementById('inactivityTime').value = '';
            }
        } catch (error) {
            console.error('❌ Error al obtener configuración de sesión:', error);
            document.getElementById('inactivityTime').value = '';
            Swal.fire({
                icon: 'error',
                title: language.session_config_error_title || 'Error',
                text: language.session_config_error_text || 'Could not load session config.'
            });
        }

        modal.show();
    });


    document.getElementById('sessionConfigForm')?.addEventListener('submit', async function (e) {
        e.preventDefault();

        const timeout = parseInt(document.getElementById('inactivityTime').value);
        if (isNaN(timeout) || timeout < 1) {
            return Swal.fire({
                icon: 'warning',
                title: language.invalid_timeout_title || 'Invalid Value',
                text: language.invalid_timeout_text || 'Please enter a valid number greater than 0.'
            });
        }

        Swal.fire({
            title: language.saving || 'Saving...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const response = await fetch('<?= BASE_URL ?>/session-config', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ timeout_minutes: timeout })
            });

            const result = await response.json();
            Swal.close();

            if (result.value) {
                Swal.fire({
                    icon: 'success',
                    title: language.update_success_title || 'Updated!',
                    text: language.update_success_text || 'Session timeout updated successfully.'
                });

                const modal = bootstrap.Modal.getInstance(document.getElementById('sessionConfigModal'));
                modal.hide();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: language.update_error_title || 'Error updating',
                    text: result.message || language.update_error_text || 'Could not update session timeout.'
                });
            }

        } catch (error) {
            let errorMessage = language.update_error_text || 'Could not update session timeout.';

            try {
                const errData = await error.response?.json?.();
                if (errData?.message) {
                    errorMessage = errData.message;
                }
            } catch (_) { }

            Swal.fire({
                icon: 'error',
                title: language.update_error_title || 'Update Error',
                text: errorMessage
            });

            console.error('❌ Error al actualizar configuración de sesión:', error);
        }
    });



    function detalleFormatter(value, row) {
        const isActive = row.session_status === 'active';

        return `
        <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
            <button class="btn btn-view action-icon btn-sm btn-view-details" title="View">
                <i class="mdi mdi-eye-outline"></i>
            </button>
            <button class="btn btn-delete action-icon btn-sm btn-kick-session ${!isActive ? 'invisible pe-none' : ''}" title="Kick" ${!isActive ? 'disabled' : ''}>
                <i class="mdi mdi-logout"></i>
            </button>
        </div>
    `;
    }
    function statusBadgeFormatter(value) {
        let label = '';
        let colorClass = '';

        switch ((value || '').toLowerCase()) {
            case 'active':
                label = language?.status_active || 'Active';
                colorClass = 'badge bg-info-subtle text-info';
                break;
            case 'expired':
                label = language?.status_expired || 'Expired';
                colorClass = 'badge bg-danger-subtle text-danger';
                break;
            case 'kicked':
                label = language?.status_kicked || 'Kicked';
                colorClass = 'badge bg-warning-subtle text-warning';
                break;
            default:
                label = language?.status_unknown || 'Unknown';
                colorClass = 'badge bg-secondary-subtle text-muted';
        }

        return `<span class="${colorClass}">${label}</span>`;
    }

    function mobileBadgeFormatter(value) {
        const isMobile = value === true || value === 1 || value === '1';
        const label = isMobile
            ? language?.is_mobile_yes || 'Mobile'
            : language?.is_mobile_no || 'Desktop';
        const colorClass = isMobile
            ? 'badge bg-success-subtle text-success'
            : 'badge bg-primary-subtle text-primary';

        return `<span class="${colorClass}">${label}</span>`;
    }


    function mostrarDetalle(row) {
        const modalLabel = document.getElementById('sessionDetailModalLabel');
        modalLabel.textContent = language?.session_detail_modal_title || 'Session Details';

        const labels = {
            session_id: language?.session_id || 'Session ID',
            user_id: language?.user_id || 'User ID',
            user_name: language?.user_name || 'Username',
            user_type: language?.user_type || 'User Type',
            full_name: language?.full_name || 'Full Name',
            login_time: language?.login_time || 'Login Time',
            logout_time: language?.logout_time || 'Logout Time',
            inactivity_duration: language?.inactivity_duration || 'Inactivity Duration',
            login_success: language?.login_success || 'Login Success',
            failure_reason: language?.failure_reason || 'Failure Reason',
            session_status: language?.session_status || 'Session Status',
            ip_address: language?.ip_address || 'IP Address',
            city: language?.city || 'City',
            region: language?.region || 'Region',
            country: language?.country || 'Country',
            zipcode: language?.audit_modal_field_client_zipcode || 'Zipcode',
            coordinates: language?.coordinates || 'Coordinates',
            hostname: language?.hostname || 'Hostname',
            os: language?.os || 'OS',
            browser: language?.browser || 'Browser',
            user_agent: language?.user_agent || 'User Agent',
            device_id: language?.device_id || 'Device ID',
            device_type: language?.device_type || 'Device Type',
            created_at: language?.created_at || 'Created At'
        };

        // 🔧 Aquí defines el orden exacto de los campos a mostrar:
        const camposOrdenados = [
            'session_id', 'user_id', 'user_type', 'full_name', 'user_name',
            'login_time', 'logout_time', 'inactivity_duration', 'login_success',
            'failure_reason', 'session_status', 'ip_address', 'city', 'region',
            'country', 'zipcode', 'coordinates', 'hostname', 'os', 'browser',
            'user_agent', 'device_id', 'device_type', 'created_at'
        ];

        let html = '';
        let coords = null;

        camposOrdenados.forEach(key => {
            const label = labels[key] || key;
            const value = row[key] ?? '-';

            if (key === 'coordinates' && typeof value === 'string' && value.includes(',')) {
                coords = value.split(',').map(coord => parseFloat(coord.trim()));
                if (!isNaN(coords[0]) && !isNaN(coords[1])) {
                    html += `
                    <dt class="col-sm-4">${label}</dt>
                    <dd class="col-sm-8">
                        ${value}
                        <div id="session_map_container" style="width: 100%; height: 300px; border-radius: 5px; margin-top: 10px; display: none;"></div>
                    </dd>
                `;
                    return; // evitar duplicado
                }
            }

            let badgeHtml = '';

            if (key === 'session_status') {
                const val = (value || '').toLowerCase();
                const label = language?.['status_' + val] || value;

                let colorClass = 'badge-soft-secondary';
                if (val === 'active') colorClass = 'badge-soft-info';
                else if (val === 'expired') colorClass = 'badge-soft-danger';
                else if (val === 'kicked') colorClass = 'badge-soft-warning';
                else if (val === 'failed') colorClass = 'badge-soft-danger';
                else if (val === 'closed') colorClass = 'badge-soft-danger';

                html += `<dt class="col-sm-4">${labels[key]}</dt><dd class="col-sm-8">
        <span class="badge ${colorClass} rounded-pill px-2 py-1 text-capitalize">${label}</span>
    </dd>`;
            }

            else if (key === 'device_type') {
                const isMobile = value === true || value === 1 || value === '1';
                const label = isMobile
                    ? language?.is_mobile_yes || 'Mobile'
                    : language?.is_mobile_no || 'Desktop';
                const colorClass = isMobile
                    ? 'badge-soft-success'
                    : 'badge-soft-primary';

                html += `<dt class="col-sm-4">${labels[key]}</dt><dd class="col-sm-8">
        <span class="badge ${colorClass} rounded-pill px-2 py-1">${label}</span>
    </dd>`;
            }

            else if (key === 'login_success') {
                const isSuccess = value === true || value === 1 || value === '1';
                const label = isSuccess
                    ? language?.login_success_yes || 'Successful'
                    : language?.login_success_no || 'Failed';
                const colorClass = isSuccess
                    ? 'badge-soft-success'
                    : 'badge-soft-danger';

                html += `<dt class="col-sm-4">${labels[key]}</dt><dd class="col-sm-8">
        <span class="badge ${colorClass} rounded-pill px-2 py-1">${label}</span>
    </dd>`;
            }

            else if (key === 'failure_reason') {
                const reasonMap = {
                    'user_not_found': language?.failure_user_not_found || 'User not found',
                    'invalid_password': language?.failure_invalid_password || 'Invalid password',
                    'account_inactive': language?.failure_account_inactive || 'Account inactive',
                    'too_many_attempts': language?.failure_too_many_attempts || 'Too many attempts',
                    'ip_blocked': language?.failure_ip_blocked || 'IP blocked',
                    'user_blocked': language?.failure_user_blocked2 || 'User blocked'
                };

                const label = reasonMap[value] || value || '-';
                html += `<dt class="col-sm-4">${labels[key]}</dt><dd class="col-sm-8">
        <span class="badge badge-soft-danger rounded-pill px-2 py-1">${label}</span>
    </dd>`;
            }

            else if (key === 'coordinates' && typeof value === 'string' && value.includes(',')) {
                coords = value.split(',').map(coord => parseFloat(coord.trim()));
                if (!isNaN(coords[0]) && !isNaN(coords[1])) {
                    html += `<dt class="col-sm-4">${labels[key]}</dt>
        <dd class="col-sm-8">
            ${value}
            <div id="session_map_container" style="width: 100%; height: 300px; border-radius: 5px; margin-top: 10px; display: none;"></div>
        </dd>`;
                    return;
                }
            }

            else {
                let displayValue = value;

                if (['login_time', 'logout_time', 'created_at'].includes(key)) {
                    displayValue = formatDateTime(value);
                }

                // 🔧 Mostrar unidad "segundos" para inactivity_duration
                if (key === 'inactivity_duration') {
                    const unidad = idioma === 'ES' ? 'segundos' : 'seconds';
                    displayValue = `${value} ${unidad}`;
                }

                html += `<dt class="col-sm-4">${labels[key]}</dt><dd class="col-sm-8">${displayValue}</dd>`;
            }




        });

        document.getElementById('sessionDetailContent').innerHTML = html;

        const modalEl = document.getElementById('sessionDetailModal');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        modalEl.addEventListener('shown.bs.modal', () => {
            if (
                Array.isArray(coords) &&
                coords.length === 2 &&
                !isNaN(coords[0]) &&
                !isNaN(coords[1]) &&
                coords[0] !== 0 &&
                coords[1] !== 0
            ) {
                const mapContainer = document.getElementById('session_map_container');
                if (mapContainer) {
                    mapContainer.style.display = 'block';
                    const map = L.map(mapContainer).setView(coords, 13);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);
                    L.marker(coords).addTo(map);
                    setTimeout(() => map.invalidateSize(), 200);
                }
            }
        }, { once: true });
    }



    function expulsarSesion(row) {
        Swal.fire({
            title: language?.confirmKickTitle || 'Expel user?',
            text: language?.confirmKickText || `Force logout for ${row.full_name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: language?.confirmYes || 'Yes, kick',
            cancelButtonText: language?.confirmCancel || 'Cancel',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/session-audit/kick/1`,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ session_id: row.session_id }),
                    success: function (res) {
                        console.log("✅ Respuesta expulsarSesion:", res);
                        if (res.value) {
                            Swal.fire({
                                icon: 'success',
                                title: language?.kickSuccessTitle || 'Session ended',
                                text: language?.kickSuccessText || 'User has been logged out.'
                            });
                            $(tableId).bootstrapTable('refresh');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: language?.kickErrorTitle || 'Error',
                                text: res.message || 'Could not force logout.'
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("❌ Error al expulsar sesión:");
                        console.warn("↪ Status:", status);
                        console.warn("↪ Error:", error);
                        console.warn("↪ Respuesta:", xhr.responseText);
                        Swal.fire('Error', 'Internal server error. Check console.', 'error');
                    }
                });
            }
        });
    }
    function formatDateTime(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;

        const pad = n => n.toString().padStart(2, '0');
        const MM = pad(date.getMonth() + 1);
        const DD = pad(date.getDate());
        const YYYY = date.getFullYear();
        const hh = pad(date.getHours());
        const mm = pad(date.getMinutes());
        const ss = pad(date.getSeconds());

        return `${MM}/${DD}/${YYYY} ${hh}:${mm}:${ss}`;
    }

    function configurarExportarCSV() {
        const exportBtn = document.getElementById(exportBtnId);
        if (!exportBtn) return;

        exportBtn.addEventListener('click', async () => {
            Swal.fire({
                title: language?.exportLoadingTitle || 'Exporting...',
                text: language?.exportLoadingText || 'Generating file, please wait...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch('session-audit/export/1');
                const contentType = response.headers.get("Content-Type");

                if (contentType && contentType.includes("text/csv")) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `${language?.csvFilenamePrefix_session_audit || 'session_audit'}.csv`;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    Swal.close();
                } else {
                    const res = await response.json();
                    Swal.fire({
                        icon: 'info',
                        title: language?.noRecordsTitle || 'No Records',
                        text: language?.noRecordsText || 'No data available to export.'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: language?.exportErrorTitle || 'Export Error',
                    text: language?.exportErrorText || 'An error occurred while exporting.'
                });
            }
        });
    }
</script>