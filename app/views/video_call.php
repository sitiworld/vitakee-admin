<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/head.php' ?>

    <style>
        .select2-dropdown--limited-height .select2-results__options {
            max-height: 200px;
            /* Puedes ajustar esta altura según tus necesidades */
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include 'layouts/header.php' ?>
    <?php include 'layouts/sidebar.php' ?>

    <?php
    // Detectar idioma
    $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
    $locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
    if (!in_array($idioma, ['EN', 'ES']))
        $idioma = 'EN';

    $archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';
    if (file_exists($archivo_idioma)) {
        $traducciones = include $archivo_idioma;
    } else {
        die("Archivo de idioma no encontrado: $archivo_idioma");
    }
    ?>

    <div id="wrapper">
        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    <h4 class="page-title"><?= $traducciones['page_title_specialists'] ?? 'Especialistas' ?></h4>
                    <div id="toolbar">
                        <button class="btn btn-add-user" id="btnOpenSpecialistModal">
                            + <?= $traducciones['add_new_specialist'] ?? 'Agregar Nuevo Especialista' ?>
                        </button>
                    </div>

                    <!-- Modal de Videollamada -->
                    <div class="modal fade" id="videoCallModal" tabindex="-1" aria-labelledby="videoCallModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-fullscreen">
                            <div class="modal-content bg-dark text-white">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title" id="videoCallModalLabel">Videollamada en curso</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <div id="video-room"
                                        class="d-flex flex-wrap justify-content-center align-items-center w-100 h-100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include 'layouts/footer.php'; ?>

            <script src="public/assets/js/logout.js"></script>
        </div>
        <div class="rightbar-overlay"></div>
    </div>
    <!-- Script de Twilio Video -->
    <script src="https://sdk.twilio.com/js/video/releases/2.23.1/twilio-video.min.js"></script>
    <script>
        async function joinTwilioVideoCall(meetingToken, roomName) {
            try {
                const modal = new bootstrap.Modal(document.getElementById('videoCallModal'));
                modal.show();

                const room = await Twilio.Video.connect(meetingToken, {
                    name: roomName,
                    audio: true,
                    video: { width: 640 }
                });

                const container = document.getElementById('video-room');
                container.innerHTML = '';

                const localContainer = document.createElement('div');
                localContainer.className = 'p-2';
                container.appendChild(localContainer);

                room.localParticipant.tracks.forEach(publication => {
                    if (publication.track.kind === 'video' || publication.track.kind === 'audio') {
                        localContainer.appendChild(publication.track.attach());
                    }
                });

                room.on('participantConnected', participant => {
                    const remoteContainer = document.createElement('div');
                    remoteContainer.className = 'p-2';
                    container.appendChild(remoteContainer);

                    participant.on('trackSubscribed', track => {
                        remoteContainer.appendChild(track.attach());
                    });
                });

                const modalElement = document.getElementById('videoCallModal');
                modalElement.addEventListener('hidden.bs.modal', () => {
                    room.disconnect();
                    container.innerHTML = '';
                });

            } catch (error) {
                console.error('Error joining Twilio room:', error);
                Swal.fire('Error', 'Could not join the video call.', 'error');
            }
        }

        async function startVideoCallIfApproved(requestId) {
            try {
                const resStatus = await fetch(`/second-opinion/${requestId}/access`);
                const statusData = await resStatus.json();

                if (!statusData || statusData.status !== 'APPROVED') {
                    Swal.fire('Aviso', 'Esta solicitud aún no ha sido aprobada.', 'info');
                    return;
                }

                const res = await fetch('/video-calls/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        request_id: requestId,
                        scheduled_at: new Date().toISOString(),
                        duration_minutes: 30
                    })
                });
                const result = await res.json();

                if (result.value) {
                    joinTwilioVideoCall(result.data.meeting_token, result.data.room_name);
                } else {
                    Swal.fire('Error', result.message, 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'No se pudo iniciar la videollamada.', 'error');
            }
        }
    </script>

    <!-- Botón para iniciar videollamada solo si está aprobada -->
    <button class="btn btn-action-lipid" onclick="startVideoCallIfApproved(123)">
        <i class="mdi mdi-video"></i> Iniciar videollamada
    </button>

</body>

</html>