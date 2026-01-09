<!DOCTYPE html>
<html lang="en">

<head>
  <?php include 'layouts/head.php' ?>
</head>

<body>
  <?php include 'layouts/header.php' ?>
  <?php include 'layouts/sidebar.php' ?>

  <?php
  $panelId = isset($_GET['id_test_panel']) ? (int) $_GET['id_test_panel'] : 0;
  $testId = isset($_GET['id_test']) ? (int) $_GET['id_test'] : 0;
  ?>
  <?php
  // Asegurar que solo aceptamos 'EN' o 'ES'
  $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
  $locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
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
  <div id="wrapper">
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">

          <div id="toolbar" class="d-none">
            <a href="users_records" class="" role="button"><button class="btn btn-back"><i
                  class="mdi mdi-arrow-left"></i> <?= $traducciones['back'] ?></button></a>
            <button id="addCommentBtn" class="btn btn-add-file">
              <i class="bi bi-plus"></i> <?= $traducciones['comment_modal_title'] ?>
            </button>

          </div>

          <div class="card">
            <div class="card-body">


              <table id="commentsTable" data-toggle="table" data-search="true" data-show-refresh="true"
                data-show-pagination-switch="true" data-page-list="[5,10,20]" data-page-size="5"
                data-show-columns="true" data-pagination="true" class="table table-borderless" data-toolbar="#toolbar"
                data-url="biomarker-comments/${idFields.panelId}/${idFields.testId}" data-locale="<?= $locale ?>">
                <thead class="">
                  <tr>
                    <th colspan="3">

                    </th>
                  </tr>
                  <tr>
                    <th data-field="biomarker_name"><?= $traducciones['comment_modal_label_biomarker'] ?></th>
                    <th data-field="comment"><?= $traducciones['comment_modal_label_comment'] ?></th>
                    <th data-field="id" data-formatter="commentActionFormatter" data-align="center">
                      <?= $traducciones['actions'] ?>
                    </th>
                  </tr>
                </thead>
              </table>

            </div>
          </div>
          <!-- Modal Add/Edit Comment -->
          <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form id="commentForm">
                  <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel"><?= $traducciones['comment_modal_title'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <!-- Contenedor para Add: select + valor -->
                    <div id="biomarkerSelectContainer" class="mb-3">
                      <label for="id_biomarker"
                        class="form-label"><?= $traducciones['table_column_biomarker'] ?></label>
                      <select id="id_biomarker" name="id_biomarker" class="form-control"></select>
                    </div>
                    <!-- Contenedor para Edit: sólo texto -->
                    <div id="biomarkerDisplayContainer" class="mb-3" style="display:none;">
                      <label class="form-label"><?= $traducciones['table_column_biomarker'] ?></label>
                      <p id="biomarkerDisplayName" class="form-control-plaintext fw-bold"></p>
                      <p class="text-muted"><?= $traducciones['value'] ?>: <span id="biomarkerDisplayValue">—</span></p>
                    </div>
                    <div class="mb-3">
                      <label for="comment"
                        class="form-label"><?= $traducciones['comment_modal_label_comment'] ?></label>
                      <textarea id="comment" name="comment" class="form-control" rows="3"></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-save"><i class="mdi mdi-content-save-outline"></i>
                      <?= $traducciones['save'] ?></button>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class=" mdi mdi-cancel">
                      </i> <?= $traducciones['cancel'] ?></button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div><!-- /.container -->
      </div><!-- /.content -->
    </div><!-- /.content-page -->
  </div>
  <script src="public/assets/js/logout.js"></script>
  <?php include 'layouts/footer.php' ?>
  <script type="module">
    import { validateFormFields, clearValidationMessages } from "./public/assets/js/helpers/helpers.js";


    (function () {

      const idioma = '<?= $_SESSION['idioma'] ?? 'EN' ?>'.toUpperCase();

      const messages = {
        EN: {
          success_update: '<?= $traducciones['success_update_comment'] ?>',
          error_update: '<?= $traducciones['error_update_comment'] ?>',
          success_delete: '<?= $traducciones['success_delete_comment'] ?>',
          error_delete: '<?= $traducciones['error_delete_comment'] ?>',
          delete_confirm_title: '<?= $traducciones['delete_confirm_title_comment'] ?>',
          delete_confirm_text: '<?= $traducciones['delete_confirm_text_comment'] ?>',
          delete_confirm_btn: '<?= $traducciones['delete_confirm_btn_comment'] ?>',
          error_loading: '<?= $traducciones['error_loading_comment'] ?>',
          success_create: '<?= $traducciones['success_create_comment'] ?>',
          error_create: '<?= $traducciones['error_create_comment'] ?>',
          select_biomarker: '<?= $traducciones['select_biomarker_comment'] ?>',
          modal_add: '<?= $traducciones['modal_add_comment'] ?>',
          modal_edit: '<?= $traducciones['modal_edit_comment'] ?>',
        },
        ES: {
          success_update: '<?= $traducciones['success_update_comment'] ?>',
          error_update: '<?= $traducciones['error_update_comment'] ?>',
          success_delete: '<?= $traducciones['success_delete_comment'] ?>',
          error_delete: '<?= $traducciones['error_delete_comment'] ?>',
          delete_confirm_title: '<?= $traducciones['delete_confirm_title_comment'] ?>',
          delete_confirm_text: '<?= $traducciones['delete_confirm_text_comment'] ?>',
          delete_confirm_btn: '<?= $traducciones['delete_confirm_btn_comment'] ?>',
          error_loading: '<?= $traducciones['error_loading_comment'] ?>',
          success_create: '<?= $traducciones['success_create_comment'] ?>',
          error_create: '<?= $traducciones['error_create_comment'] ?>',
          select_biomarker: '<?= $traducciones['select_biomarker_comment'] ?>',
          modal_add: '<?= $traducciones['modal_add_comment'] ?>',
          modal_edit: '<?= $traducciones['modal_edit_comment'] ?>',
        }
      }[idioma];

      const idFields = {
        panelId: <?= $panelId ?>,
        testId: <?= $testId ?>,
        commentId: null,
        biomarkersList: []
      };

      window.commentActionFormatter = v => `
    <button class="btn btn-pencil action-icon btn-sm editBtn" data-id="${v}">
      <i class="mdi mdi-pencil-outline"></i>
    </button>
    <button class="btn btn-delete action-icon btn-sm deleteBtn" data-id="${v}">
      <i class="mdi mdi-delete-outline"></i>
    </button>
  `;

      function loadComments() {
        fetch(`biomarker-comments/${idFields.panelId}/${idFields.testId}`)
          .then(r => r.json())
          .then(res => {
            if (!res.value) throw new Error();
            console.log(res);


            const rows = res.data || [];

            document.getElementById('toolbar').classList.remove('d-none');
            $('#commentsTable').bootstrapTable('load', rows);

          })
          .catch(() => Swal.fire('Error', messages.error_loading, 'error'));
      }
      $('#commentsTable').on('refresh.bs.table', function () {
        loadComments();
      });

      function loadBiomarkers(selected = 0) {
        return fetch(`biomarker_value/${idFields.panelId}/${idFields.testId}`)
          .then(r => r.json())
          .then(res => {
            if (!res.value) throw new Error();
            idFields.biomarkersList = res.data;
            const sel = $('#id_biomarker');
            sel.empty().append(new Option(messages.select_biomarker, '', true, true));

            res.data.forEach(b => {
              const displayText = `${b.biomarker_name} — ${b.biomarker_value}`;
              const option = new Option(displayText, b.id_biomarker, false, b.id_biomarker === selected);
              option.dataset.value = b.biomarker_value;
              sel.append(option);
            });

            // Activamos select2 con width 100%
            sel.select2({
              dropdownParent: $('#commentModal .modal-body'),
              width: '100%'
            });


          });
      }


      document.addEventListener('click', e => {
        if (e.target.closest('#addCommentBtn')) {

          clearValidationMessages(document.getElementById('commentForm'));

          idFields.commentId = null;
          document.getElementById('commentForm').reset();
          document.getElementById('commentModalLabel').textContent = messages.modal_add;
          document.getElementById('biomarkerSelectContainer').style.display = '';
          document.getElementById('biomarkerDisplayContainer').style.display = 'none';
          loadBiomarkers().then(() => {

            new bootstrap.Modal(document.getElementById('commentModal')).show();
          });
          return;
        }

        if (e.target.closest('.editBtn')) {

          clearValidationMessages(document.getElementById('commentForm'));

          const id = e.target.closest('.editBtn').dataset.id;
          fetch(`biomarker-comment/${id}`)
            .then(r => r.json())
            .then(res => {
              if (!res.value) throw new Error();
              const d = res.data;
              idFields.commentId = d.id;
              document.getElementById('comment').value = d.comment;
              document.getElementById('commentModalLabel').textContent = messages.modal_edit;
              loadBiomarkers(d.id_biomarker).then(() => {
                const found = idFields.biomarkersList.find(b => +b.id_biomarker === +d.id_biomarker);
                document.getElementById('biomarkerDisplayName').textContent = d.biomarker_name;
                document.getElementById('biomarkerDisplayValue').textContent = found ? found.biomarker_value : '—';
                document.getElementById('biomarkerSelectContainer').style.display = 'none';
                document.getElementById('biomarkerDisplayContainer').style.display = '';
                new bootstrap.Modal(document.getElementById('commentModal')).show();
              });
            })
            .catch(() => Swal.fire('Error', messages.error_update, 'error'));
          return;
        }

        if (e.target.closest('.deleteBtn')) {
          const id = e.target.closest('.deleteBtn').dataset.id;
          Swal.fire({
            title: messages.delete_confirm_title,
            text: messages.delete_confirm_text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: messages.delete_confirm_btn
          }).then(({ isConfirmed }) => {
            if (!isConfirmed) return;
            fetch(`biomarker-comments/${id}`, {
              method: 'delete',
              headers: { 'Content-Type': 'application/json' },
            })
              .then(r => r.json())
              .then(res => {
                console.log(res);

                if (!res.value) throw new Error();
                Swal.fire(messages.success_delete, '', 'success')
                loadComments()
              })
              .catch(() => Swal.fire('Error', messages.error_delete, 'error'));
          });
        }
      });


      document.getElementById('commentForm').addEventListener('submit', e => {
        e.preventDefault();

        let fielList = [
          'id_biomarker',
          'comment'

        ]
        if (!validateFormFields(e.target, fielList, '<?= $traducciones['input_generic_error']; ?>')) {
          return
        }

        const f = e.target, fd = new FormData();
        fd.append('id', idFields.commentId);
        fd.append('id_biomarker', f.id_biomarker.value);
        fd.append('comment', f.comment.value);
        fd.append('id_test_panel', idFields.panelId);
        fd.append('id_test', idFields.testId);

        let url = 'biomarker-comments', method = 'POST';
        if (idFields.commentId) url = `biomarker-comments/${idFields.commentId}`;

        fetch(url, { method, body: fd })
          .then(r => r.json())
          .then(res => {
            if (!res.value) throw new Error();
            bootstrap.Modal.getInstance(document.getElementById('commentModal')).hide();
            Swal.fire(idFields.commentId ? messages.success_update : messages.success_create, '', 'success')
              .then(loadComments);
          })
          .catch(() => Swal.fire('Error', idFields.commentId ? messages.error_update : messages.error_create, 'error'));
      });

      document.addEventListener('DOMContentLoaded', () => {
        $('#commentsTable').bootstrapTable();
        loadComments();
      });
    })();
  </script>


</body>

</html>