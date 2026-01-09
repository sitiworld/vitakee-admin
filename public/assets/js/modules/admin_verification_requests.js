import { formatDateTime } from "../helpers/validacionesEspeciales.js";

const currentLang = document.documentElement.lang || 'en';
const traducciones = typeof translations !== 'undefined' ? translations : {};

// Formatters for table
window.statusFormatter = (value, row) => {
    const statusMap = {
        'PENDING': { class: 'bg-white-blue text-sapphire-blue border-sapphire-blue', text: traducciones['verification_status_pending'] || 'Pending' },
        'AWAITING_PAYMENT': { class: 'bg-white-blue text-royal-blue border-royal-blue', text: traducciones['verification_payment_required'] || 'Awaiting Payment' },
        'APPROVED': { class: 'bg-white-blue text-bright-turquoise border-bright-turquoise', text: traducciones['verification_status_approved'] || 'Approved' },
        'REJECTED': { class: 'bg-neutral-light text-gray-dark border-gray', text: traducciones['verification_status_rejected'] || 'Rejected' }
    };
    
    const status = statusMap[value] || { class: 'bg-light text-dark', text: value };
    return `<span class="badge ${status.class} border px-2 py-1">${status.text}</span>`;
};

window.dateFormatter = (value) => {
    if (!value) return '-';
    const date = formatDateTime(value);
    return date;
};

window.actionFormatter = (value, row) => {
    return `
        <button class="btn btn-view action-icon viewBtn" data-id="${row.verification_request_id}" title="${traducciones['view_request'] || 'View Request'}">
            <i class="mdi mdi-eye-outline"></i>
        </button>
    `;
};

// Specialist name formatter
window.specialistFormatter = (value, row) => {
    if (row.specialist) {
        return `${row.specialist.first_name || ''} ${row.specialist.last_name || ''}`;
    }
    return '-';
};

// Specialty formatter
window.specialtyFormatter = (value, row) => {
    return row.specialist?.specialty_display_name || '-';
};

// Title formatter
window.titleFormatter = (value, row) => {
    return row.specialist?.title_display_name || '-';
};

// Load verification requests
function loadVerificationRequests() {
    $.ajax({
        url: 'specialist-verification-requests',
        type: 'GET',
        dataType: 'json',
        success: (response) => {
            if (response.value && Array.isArray(response.data)) {
                $('#verificationRequestsTable').bootstrapTable('load', response.data);
            } else {
                $('#verificationRequestsTable').bootstrapTable('load', []);
            }
        },
        error: () => {
            Swal.fire(
                traducciones['error'] || 'Error',
                traducciones['error_loading_requests'] || 'Error loading verification requests',
                'error'
            );
        }
    });
}

// View request details
$(document).on('click', '.viewBtn', function () {
    const requestId = $(this).data('id');
    
    $.ajax({
        url: `specialist-verification-requests/${requestId}`,
        type: 'GET',
        dataType: 'json',
        success: (response) => {
            if (response.value && response.data) {
                displayRequestDetails(response.data);
            } else {
                Swal.fire(
                    traducciones['error'] || 'Error',
                    response.message || traducciones['error_loading_request'] || 'Error loading request details',
                    'error'
                );
            }
        },
        error: () => {
            Swal.fire(
                traducciones['error'] || 'Error',
                traducciones['error_loading_request'] || 'Error loading request details',
                'error'
            );
        }
    });
});

// Display request details in modal
function displayRequestDetails(data) {
    const statusInfo = {
        'PENDING': { icon: 'mdi-clock-outline', color: 'text-sapphire-blue', text: traducciones['verification_status_pending'] || 'Pending' },
        'AWAITING_PAYMENT': { icon: 'mdi-currency-usd', color: 'text-royal-blue', text: traducciones['verification_payment_required'] || 'Awaiting Payment' },
        'APPROVED': { icon: 'mdi-check-decagram', color: 'text-bright-turquoise', text: traducciones['verification_status_approved'] || 'Approved' },
        'REJECTED': { icon: 'mdi-alert-circle-outline', color: 'text-gray-dark', text: traducciones['verification_status_rejected'] || 'Rejected' }
    };
    
    const status = statusInfo[data.status] || { icon: 'mdi-help-circle', color: 'text-muted', text: data.status };
    
    // Extract specialist data
    const specialist = data.specialist || {};
    const specialistName = `${specialist.first_name || ''} ${specialist.last_name || ''}`.trim() || '-';
    const specialistEmail = specialist.email || '-';
    const titleName = specialist.title_display_name || '-';
    const specialtyName = specialist.specialty_display_name || '-';
    
    // Build certifications HTML
    let certificationsHtml = '';
    if (data.certifications && data.certifications.length > 0) {
        certificationsHtml = data.certifications.map(cert => {
            // Ensure BASE_URL is available and construct full URL
            const baseUrl = window.BASE_URL || '';
            const filePath = cert.file_url || '';  // Changed from file_path to file_url
            const fileUrl = filePath ? `${baseUrl}${filePath}` : null;
            const fileExtension = filePath ? filePath.split('.').pop().toLowerCase() : '';
            
            let preview = '';
            if (fileUrl) {
                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                    preview = `<img src="${fileUrl}" class="img-fluid rounded mb-2" style="max-height: 300px; cursor: pointer;" alt="${cert.title || 'Certificate'}" onclick="window.open('${fileUrl}', '_blank')">`;
                } else if (fileExtension === 'pdf') {
                    preview = `
                        <div class="mb-2">
                            <embed src="${fileUrl}" type="application/pdf" width="100%" height="400px" />
                        </div>
                    `;
                } else {
                    preview = `<p class="text-muted small"><i class="mdi mdi-file-document-outline"></i> ${traducciones['file_type_not_previewable'] || 'File preview not available'}</p>`;
                }
            }
            
            return `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="mdi mdi-certificate me-1"></i>${cert.title || 'Certificate'}</h6>
                        ${cert.description ? `<p class="card-text text-muted small">${cert.description}</p>` : ''}
                        ${preview}
                        ${fileUrl ? `
                            <div class="mt-2">
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm" style="background-color: transparent; color: var(--color-bright-turquoise); border: 1px solid var(--color-bright-turquoise);">
                                    <i class="mdi mdi-download"></i> ${traducciones['download_document'] || 'Download'}
                                </a>
                                <a href="${fileUrl}" target="_blank" class="btn btn-sm" style="background-color: transparent; color: var(--color-electric-blue); border: 1px solid var(--color-electric-blue);">
                                    <i class="mdi mdi-open-in-new"></i> ${traducciones['open_in_new_tab'] || 'Open in New Tab'}
                                </a>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    } else {
        certificationsHtml = `<p class="text-muted">${traducciones['no_certifications'] || 'No certifications uploaded'}</p>`;
    }
    
    const modalHtml = `
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-3 text-uppercase bg-light p-2 rounded">
                    <i class="mdi mdi-account-star me-1"></i> ${traducciones['specialist_info'] || 'Specialist Information'}
                </h5>
                <p><strong>${traducciones['name'] || 'Name'}:</strong> ${specialistName}</p>
                <p><strong>${traducciones['email'] || 'Email'}:</strong> <a href="mailto:${specialistEmail}" class="text-info">${specialistEmail}</a></p>
                <p><strong>${traducciones['title'] || 'Title'}:</strong> ${titleName}</p>
                <p><strong>${traducciones['specialty'] || 'Specialty'}:</strong> ${specialtyName}</p>
                <hr>
                <p><strong>${traducciones['status'] || 'Status'}:</strong> <i class="mdi ${status.icon} ${status.color} me-1"></i><span class="${status.color}">${status.text}</span></p>
                <p><strong>${traducciones['request_date'] || 'Request Date'}:</strong> ${formatDateTime(data.submitted_at || data.created_at)}</p>
                ${data.approved_at ? `<p><strong>${traducciones['approved_date'] || 'Approved Date'}:</strong> ${formatDateTime(data.approved_at)}</p>` : ''}
                
                ${data.status === 'PENDING' ? `
                    <div class="mt-4">
                        <button class="btn btn-save w-100 mb-2 approveRequestBtn" data-id="${data.verification_request_id}">
                            <i class="mdi mdi-check-circle me-1"></i> ${traducciones['approve_request'] || 'Approve Request'}
                        </button>
                        <button class="btn btn-success-dark w-100 rejectRequestBtn" data-id="${data.verification_request_id}">
                            <i class="mdi mdi-close-circle me-1"></i> ${traducciones['reject_request'] || 'Reject Request'}
                        </button>
                    </div>
                ` : ''}
            </div>
            <div class="col-md-8">
                <h5 class="mb-3 text-uppercase bg-light p-2 rounded">
                    <i class="mdi mdi-certificate me-1"></i> ${traducciones['certifications'] || 'Certifications'}
                </h5>
                <div style="max-height: 600px; overflow-y: auto;">
                    ${certificationsHtml}
                </div>
            </div>
        </div>
    `;
    
    $('#viewRequestContainer').html(modalHtml);
    new bootstrap.Modal(document.getElementById('viewRequestModal')).show();
}

// Approve request
$(document).on('click', '.approveRequestBtn', function () {
    const requestId = $(this).data('id');
    
    Swal.fire({
        title: traducciones['confirm_approve_title'] || 'Approve Verification?',
        text: traducciones['confirm_approve_text'] || 'The specialist will be notified to complete payment.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: traducciones['confirm_approve'] || 'Yes, Approve',
        cancelButtonText: traducciones['cancel'] || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `specialist-verification-requests/${requestId}/approve`,
                type: 'PUT',
                dataType: 'json',
                success: (response) => {
                    if (response.value) {
                        Swal.fire(
                            traducciones['success'] || 'Success',
                            response.message || traducciones['request_approved'] || 'Request approved successfully',
                            'success'
                        );
                        loadVerificationRequests();
                        bootstrap.Modal.getInstance(document.getElementById('viewRequestModal')).hide();
                    } else {
                        Swal.fire(
                            traducciones['error'] || 'Error',
                            response.message || traducciones['error_approving'] || 'Error approving request',
                            'error'
                        );
                    }
                },
                error: (xhr) => {
                    Swal.fire(
                        traducciones['error'] || 'Error',
                        xhr.responseJSON?.message || traducciones['error_approving'] || 'Error approving request',
                        'error'
                    );
                }
            });
        }
    });
});

// Reject request
$(document).on('click', '.rejectRequestBtn', function () {
    const requestId = $(this).data('id');
    
    Swal.fire({
        title: traducciones['confirm_reject_title'] || 'Reject Verification?',
        text: traducciones['confirm_reject_text'] || 'The specialist will be notified of the rejection.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: traducciones['confirm_reject'] || 'Yes, Reject',
        cancelButtonText: traducciones['cancel'] || 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `specialist-verification-requests/${requestId}/reject`,
                type: 'PUT',
                dataType: 'json',
                success: (response) => {
                    if (response.value) {
                        Swal.fire(
                            traducciones['success'] || 'Success',
                            response.message || traducciones['request_rejected'] || 'Request rejected successfully',
                            'success'
                        );
                        loadVerificationRequests();
                        bootstrap.Modal.getInstance(document.getElementById('viewRequestModal')).hide();
                    } else {
                        Swal.fire(
                            traducciones['error'] || 'Error',
                            response.message || traducciones['error_rejecting'] || 'Error rejecting request',
                            'error'
                        );
                    }
                },
                error: (xhr) => {
                    Swal.fire(
                        traducciones['error'] || 'Error',
                        xhr.responseJSON?.message || traducciones['error_rejecting'] || 'Error rejecting request',
                        'error'
                    );
                }
            });
        }
    });
});

// Refresh table
$('#verificationRequestsTable').on('refresh.bs.table', loadVerificationRequests);

// Initial load
loadVerificationRequests();
