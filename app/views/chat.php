<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/head.php' ?>
</head>

<body>
    <?php include 'layouts/header.php' ?>
    <?php include 'layouts/sidebar.php' ?>

    <div class="content-page">
        <div class="content">
            <?php
            // Asegurar que solo aceptamos 'EN' o 'ES'
            $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
            if (!in_array($idioma, ['EN', 'ES'])) {
                $idioma = 'EN'; // valor por defecto
            }

            $archivo_idioma = PROJECT_ROOT . '/lang/' . $idioma . '.php';

            if (file_exists($archivo_idioma)) {
                $traducciones = include $archivo_idioma;
            } else {
                die("Archivo de idioma no encontrado: $archivo_idioma");
            }

            ?><!-- Chat Modal -->
            <div class="modal fade" id="chatModal" tabindex="-1" aria-labelledby="chatModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="chatModalLabel">Chat with James Zavel</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">

                                    <div class="card mb-0 border-0 shadow-none">
                                        <div class="card-body py-2 px-3 border-bottom border-light">
                                            <div class="row justify-content-between py-1">
                                                <div class="col-sm-7">
                                                    <div class="d-flex align-items-start">
                                                        <img src="assets/images/users/user-5.jpg"
                                                            class="me-2 rounded-circle" height="36" alt="Brandon Smith">
                                                        <div>
                                                            <h5 class="mt-0 mb-0 font-15">
                                                                <a href="contacts-profile.html" class="text-reset">James
                                                                    Zavel</a>
                                                            </h5>
                                                            <p class="mt-1 mb-0 text-muted font-12">
                                                                <small class="mdi mdi-circle text-success"></small>
                                                                Online
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div id="tooltips-container">
                                                        <a href="javascript:void(0);"
                                                            class="text-reset font-19 py-1 px-2 d-inline-block">
                                                            <i class="fe-phone-call" data-bs-toggle="tooltip"
                                                                title="Voice Call"></i>
                                                        </a>
                                                        <a href="javascript:void(0);"
                                                            class="text-reset font-19 py-1 px-2 d-inline-block">
                                                            <i class="fe-video" data-bs-toggle="tooltip"
                                                                title="Video Call"></i>
                                                        </a>
                                                        <a href="javascript:void(0);"
                                                            class="text-reset font-19 py-1 px-2 d-inline-block">
                                                            <i class="fe-user-plus" data-bs-toggle="tooltip"
                                                                title="Add Users"></i>
                                                        </a>
                                                        <a href="javascript:void(0);"
                                                            class="text-reset font-19 py-1 px-2 d-inline-block">
                                                            <i class="fe-trash-2" data-bs-toggle="tooltip"
                                                                title="Delete Chat"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <ul class="conversation-list" data-simplebar style="max-height: 460px;">
                                                <li class="clearfix odd">
                                                    <div class="chat-avatar">
                                                        <img src="assets/images/users/user-1.jpg" alt="Geneva M"
                                                            class="rounded" />
                                                        <i>10:05</i>
                                                    </div>
                                                    <div class="conversation-text">
                                                        <div class="ctext-wrap">
                                                            <i>Geneva M</i>
                                                            <p>
                                                                3pm it is. Sure, let's discuss about presentation
                                                                format, it would be great to finalize today. I am
                                                                attaching the last year format and assets here...
                                                            </p>
                                                        </div>
                                                        <div class="card mt-2 mb-1 shadow-none border text-start">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-primary rounded">
                                                                                .ZIP
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:void(0);"
                                                                            class="text-muted fw-bold">UBold-sketch.zip</a>
                                                                        <p class="mb-0">2.3 MB</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link btn-lg text-muted">
                                                                            <i class="dripicons-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="conversation-actions dropdown">
                                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical font-16"></i></button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#">Copy Message</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix odd">
                                                    <div class="chat-avatar">
                                                        <img src="assets/images/users/user-1.jpg" alt="Geneva M"
                                                            class="rounded" />
                                                        <i>10:05</i>
                                                    </div>
                                                    <div class="conversation-text">
                                                        <div class="ctext-wrap">
                                                            <i>Geneva M</i>
                                                            <p>
                                                                3pm it is. Sure, let's discuss about presentation
                                                                format, it would be great to finalize today. I am
                                                                attaching the last year format and assets here...
                                                            </p>
                                                        </div>
                                                        <div class="card mt-2 mb-1 shadow-none border text-start">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-primary rounded">
                                                                                .ZIP
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:void(0);"
                                                                            class="text-muted fw-bold">UBold-sketch.zip</a>
                                                                        <p class="mb-0">2.3 MB</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link btn-lg text-muted">
                                                                            <i class="dripicons-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="conversation-actions dropdown">
                                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical font-16"></i></button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#">Copy Message</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix odd">
                                                    <div class="chat-avatar">
                                                        <img src="assets/images/users/user-1.jpg" alt="Geneva M"
                                                            class="rounded" />
                                                        <i>10:05</i>
                                                    </div>
                                                    <div class="conversation-text">
                                                        <div class="ctext-wrap">
                                                            <i>Geneva M</i>
                                                            <p>
                                                                3pm it is. Sure, let's discuss about presentation
                                                                format, it would be great to finalize today. I am
                                                                attaching the last year format and assets here...
                                                            </p>
                                                        </div>
                                                        <div class="card mt-2 mb-1 shadow-none border text-start">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-primary rounded">
                                                                                .ZIP
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:void(0);"
                                                                            class="text-muted fw-bold">UBold-sketch.zip</a>
                                                                        <p class="mb-0">2.3 MB</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link btn-lg text-muted">
                                                                            <i class="dripicons-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="conversation-actions dropdown">
                                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical font-16"></i></button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#">Copy Message</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix odd">
                                                    <div class="chat-avatar">
                                                        <img src="assets/images/users/user-1.jpg" alt="Geneva M"
                                                            class="rounded" />
                                                        <i>10:05</i>
                                                    </div>
                                                    <div class="conversation-text">
                                                        <div class="ctext-wrap">
                                                            <i>Geneva M</i>
                                                            <p>
                                                                3pm it is. Sure, let's discuss about presentation
                                                                format, it would be great to finalize today. I am
                                                                attaching the last year format and assets here...
                                                            </p>
                                                        </div>
                                                        <div class="card mt-2 mb-1 shadow-none border text-start">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-primary rounded">
                                                                                .ZIP
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:void(0);"
                                                                            class="text-muted fw-bold">UBold-sketch.zip</a>
                                                                        <p class="mb-0">2.3 MB</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link btn-lg text-muted">
                                                                            <i class="dripicons-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="conversation-actions dropdown">
                                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical font-16"></i></button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#">Copy Message</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="clearfix odd">
                                                    <div class="chat-avatar">
                                                        <img src="assets/images/users/user-1.jpg" alt="Geneva M"
                                                            class="rounded" />
                                                        <i>10:05</i>
                                                    </div>
                                                    <div class="conversation-text">
                                                        <div class="ctext-wrap">
                                                            <i>Geneva M</i>
                                                            <p>
                                                                3pm it is. Sure, let's discuss about presentation
                                                                format, it would be great to finalize today. I am
                                                                attaching the last year format and assets here...
                                                            </p>
                                                        </div>
                                                        <div class="card mt-2 mb-1 shadow-none border text-start">
                                                            <div class="p-2">
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="avatar-sm">
                                                                            <span
                                                                                class="avatar-title bg-primary rounded">
                                                                                .ZIP
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col ps-0">
                                                                        <a href="javascript:void(0);"
                                                                            class="text-muted fw-bold">UBold-sketch.zip</a>
                                                                        <p class="mb-0">2.3 MB</p>
                                                                    </div>
                                                                    <div class="col-auto">
                                                                        <a href="javascript:void(0);"
                                                                            class="btn btn-link btn-lg text-muted">
                                                                            <i class="dripicons-download"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="conversation-actions dropdown">
                                                        <button class="btn btn-sm btn-link" data-bs-toggle="dropdown"
                                                            aria-expanded="false"><i
                                                                class="mdi mdi-dots-vertical font-16"></i></button>
                                                        <div class="dropdown-menu">
                                                            <a class="dropdown-item" href="#">Copy Message</a>
                                                            <a class="dropdown-item" href="#">Edit</a>
                                                            <a class="dropdown-item" href="#">Delete</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>

                                            <div class="row">
                                                <div class="col">
                                                    <div class="mt-2 bg-light p-3 rounded">
                                                        <form class="needs-validation" novalidate id="chat-form">
                                                            <div class="row">
                                                                <div class="col mb-2 mb-sm-0">
                                                                    <input type="text" class="form-control border-0"
                                                                        placeholder="Enter your text" required />
                                                                    <div class="invalid-feedback">
                                                                        Please enter your message
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-auto">
                                                                    <div class="btn-group">
                                                                        <a href="#" class="btn btn-light"><i
                                                                                class="fe-paperclip"></i></a>
                                                                        <button type="submit"
                                                                            class="btn btn-success chat-send w-100"><i
                                                                                class="fe-send"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div> <!-- end card-body -->
                                    </div> <!-- end card -->

                                </div>
                            </div>
                        </div> <!-- end modal-body -->

                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#chatModal">
                Abrir Chat
            </button>




            <!-- Footer Start -->

            <!-- Footer Start -->
            <?php
            include 'layouts/footer.php';
            ?>
            <!-- end Footer -->
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>