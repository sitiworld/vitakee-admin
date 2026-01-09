<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/head.php' ?>
    <style>
        #security-questions-container {
            max-width: 800px;
            margin: auto;
        }

        .form-edit-view {
            display: none;
        }

        .question-block {
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .question-text {
            font-weight: 500;
            color: #343a40;
        }

        .answer-text {
            color: #495057;
            padding-left: 10px;
        }
    </style>
</head>

<body>
    <?php include 'layouts/header.php' ?>
    <?php include 'layouts/sidebar.php' ?>
    <div id="wrapper">
        <div class="content-page">
            <div class="content" id="security-questions-view">
                <div class="container-fluid">
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
                    ?>

                    <div id="security-questions-container" class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <?= $traducciones['modal_security_title'] ?? 'Security Questions' ?>
                            </h4>

                            <div id="setup-view">
                                <p><?= $traducciones['no_questions_prompt'] ?? 'You have not set up your security questions yet.' ?>
                                </p>
                                <button id="btn-setup" class="btn btn-add">
                                    <i class="mdi mdi-plus"></i>
                                    <?= $traducciones['register_questions'] ?? 'Set up Questions' ?>
                                </button>
                            </div>

                            <div id="display-view" class="d-none">
                                <div class="question-block">
                                    <p class="question-text" id="display_question1"></p>
                                    <p class="answer-text fst-italic" id="display_answer1"></p>
                                </div>
                                <div class="question-block">
                                    <p class="question-text" id="display_question2"></p>
                                    <p class="answer-text fst-italic" id="display_answer2"></p>
                                </div>
                                <button id="btn-edit" class="btn btn-pencil mt-2">
                                    <i class="mdi mdi-pencil-outline"></i> <?= $traducciones['edit'] ?? 'Edit' ?>
                                </button>
                            </div>

                            <div id="form-view" class="d-none">
                                <form id="security-question-form">
                                    <input type="hidden" id="security_id" name="security_id">

                                    <div class="mb-3">
                                        <label for="question1"
                                            class="form-label"><?= $traducciones['question1'] ?? 'Security Question 1' ?></label>
                                        <input class="form-control" type="text" id="question1" name="question1"
                                            placeholder="<?= $traducciones['enter_first_question'] ?? 'Enter your first question' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="answer1"
                                            class="form-label"><?= $traducciones['answer1'] ?? 'Answer 1' ?></label>
                                        <input class="form-control" type="text" id="answer1" name="answer1"
                                            placeholder="<?= $traducciones['enter_answer'] ?? 'Enter answer' ?>">
                                    </div>
                                    <hr class="my-4">
                                    <div class="mb-3">
                                        <label for="question2"
                                            class="form-label"><?= $traducciones['question2'] ?? 'Security Question 2' ?></label>
                                        <input class="form-control" type="text" id="question2" name="question2"
                                            placeholder="<?= $traducciones['enter_second_question'] ?? 'Enter your second question' ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="answer2"
                                            class="form-label"><?= $traducciones['answer2'] ?? 'Answer 2' ?></label>
                                        <input class="form-control" type="text" id="answer2" name="answer2"
                                            placeholder="<?= $traducciones['enter_answer'] ?? 'Enter answer' ?>">
                                    </div>

                                    <div class="mt-4">
                                        <button id="security-submit-btn" type="submit" class="btn btn-save">
                                            <i class="mdi mdi-content-save-outline"></i>
                                            <?= $traducciones['save'] ?? 'Save Questions' ?>
                                        </button>
                                        <button id="btn-cancel-edit" type="button" class="btn btn-cancel">
                                            <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?? 'Cancel' ?>
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div> <?php include 'layouts/footer.php'; ?>
        </div>
    </div>
    <script src="public/assets/js/logout.js"></script>
    <script type="module">
        import { validateFormFields, clearValidationMessages } from "public/assets/js/helpers/helpers.js";

        $(document).ready(function () {
            // Elementos del DOM
            const setupView = $('#setup-view');
            const displayView = $('#display-view');
            const formView = $('#form-view');
            const form = $('#security-question-form');
            const securityIdField = $('#security_id');

            // Traducciones
            const t = {
                success: '<?= $traducciones['tituloExito_security'] ?? "Success" ?>',
                error: '<?= $traducciones['tituloError_security'] ?? "Error" ?>',
                updateSuccess: '<?= $traducciones['updateSuccess_security'] ?? "Questions saved successfully!" ?>',
                updateError: '<?= $traducciones['updateError_security'] ?? "Could not save questions." ?>',
                updateAjaxError: '<?= $traducciones['updateAjaxError_security'] ?? "A server error occurred." ?>',
                input_generic_error: '<?= $traducciones['input_generic_error'] ?? "This field is required." ?>'
            };

            // Función para cambiar entre vistas
            function switchView(viewToShow) {
                setupView.addClass('d-none');
                displayView.addClass('d-none');
                formView.addClass('d-none');
                viewToShow.removeClass('d-none');
            }

            // Cargar y mostrar las preguntas
            function loadSecurityQuestions() {
                $.ajax({
                    url: 'security-questions',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.value && response.data.data) {
                            const data = response.data.data;
                            // Rellenar la vista de visualización
                            $('#display_question1').text(data.question1);
                            $('#display_answer1').text(data.answer1);
                            $('#display_question2').text(data.question2);
                            $('#display_answer2').text(data.answer2);

                            // Rellenar el formulario para edición futura
                            securityIdField.val(data.security_question_id);
                            $('#question1').val(data.question1);
                            $('#answer1').val(data.answer1);
                            $('#question2').val(data.question2);
                            $('#answer2').val(data.answer2);

                            switchView(displayView);
                        } else {
                            // No hay preguntas, mostrar la vista de configuración inicial
                            switchView(setupView);
                        }
                    },
                    error: function () {
                        Swal.fire(t.error, t.updateAjaxError, 'error');
                        switchView(setupView); // Mostrar vista inicial en caso de error
                    }
                });
            }

            // Evento para empezar a configurar las preguntas
            $('#btn-setup').on('click', function () {
                form[0].reset();
                securityIdField.val('');
                clearValidationMessages(form[0]);
                switchView(formView);
            });

            // Evento para entrar en modo edición
            $('#btn-edit').on('click', function () {
                clearValidationMessages(form[0]);
                switchView(formView);
            });

            // Evento para cancelar la edición/creación
            $('#btn-cancel-edit').on('click', function () {
                // Si había un ID, significa que estábamos editando, volvemos a la vista de display
                if (securityIdField.val()) {
                    switchView(displayView);
                } else { // Si no, estábamos creando, volvemos a la vista inicial
                    switchView(setupView);
                }
            });

            // Envío del formulario
            form.submit(function (e) {
                e.preventDefault();

                if (!validateFormFields(e.target, ['question1', 'answer1', 'question2', 'answer2'], t.input_generic_error)) {
                    return;
                }

                const id = securityIdField.val();
                const method = id ? 'PUT' : 'POST';
                const url = id ? `security-questions/${encodeURIComponent(id)}` : 'security-questions';

                const data = {
                    question1: $('#question1').val(),
                    answer1: $('#answer1').val(),
                    question2: $('#question2').val(),
                    answer2: $('#answer2').val()
                };

                $.ajax({
                    url: url,
                    type: method,
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    dataType: 'json',
                    success: function (response) {
                        if (response.value) {
                            Swal.fire(t.success, t.updateSuccess, 'success');
                            loadSecurityQuestions(); // Recargar los datos y mostrar la vista correcta
                        } else {
                            Swal.fire(t.error, response.message || t.updateError, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire(t.error, t.updateAjaxError, 'error');
                    }
                });
            });

            // Carga inicial
            loadSecurityQuestions();
        });
    </script>
</body>

</html>