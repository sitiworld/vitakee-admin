<?php
$id_test_panel = isset($_GET['id_test_panel']) ? $_GET['id_test_panel'] : 0;
$id_test = isset($_GET['id_test']) ? $_GET['id_test'] : 0;

$idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
$locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
if (!in_array($idioma, ['EN', 'ES'])) {
  $idioma = 'ES'; // valor por defecto
}



?>



<div class="container-fluid">

  <div id="toolbar" class="d-none">
    <?php
    $href = '';
    switch ($id_test_panel) {
      case '7ff39dd8-01e9-443c-b8e6-0d6b429e63a6':
        $href = 'energy_metabolism_view';
        break;
      case '81054d57-92c9-4df8-a6dc-51334c1d82c4':
        $href = 'body_composition';
        break;
      case 'e6861593-7327-4f63-9511-11d56f5398dc':
        $href = 'lipid_profile';
        break;
      case '60819af9-0533-472c-9d5a-24a5df5a83f7':
        $href = 'renal_function';
        break;

    }
    ?>
    <a href="<?= $href ?>" role="button">
      <button class="btn btn-back mb-3"><i class="mdi mdi-arrow-left"></i> <?= $traducciones['back'] ?></button>
    </a>

    <button id="addImageBtn" class="btn btn-add-file mb-3 ">
      <i class="bi bi-plus"></i> <?= $traducciones['add_file_test_image'] ?>
    </button>
  </div>

  <div class="card">
    <div class="card-body">


      <table id="testImagesTable" data-toggle="table" data-search="true" data-show-refresh="true"
        data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true" data-show-no-records="true"
        data-show-columns="true" data-show-pagination-switch="true"
        class="table table-borderless"
        data-locale="<?= $locale ?>" data-toolbar="#toolbar">
        <thead class="">
          <tr>
            <th colspan="3">

            </th>
          </tr>
          <tr>
            <th data-field="name_image" data-sortable="true"><?= $traducciones['name_test_image'] ?></th>
            <th data-field="description" data-sortable="true"><?= $traducciones['description_test_image'] ?>
            </th>
            <th data-field="id" data-formatter="imageActionFormatter" data-align="center">
              <?= $traducciones['actions_test_image'] ?>
            </th>
          </tr>
        </thead>
      </table>

    </div>
  </div>

  <!-- Add / Edit Image Modal -->
  <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="imageForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel"><?= $traducciones['add_file_test_image'] ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="image_id" name="id">
            <input type="hidden" id="hid_panel" name="id_test_panel" value="<?= $id_test_panel ?>">
            <input type="hidden" id="hid_test" name="id_test" value="<?= $id_test ?>">

            <div class="mb-3">
              <label for="name_image" class="form-label"><?= $traducciones['fil_test_image'] ?></label>
              <input type="file" id="name_image" name="name_image" accept=".jpg,.png,.pdf" class="form-control">

            </div>

            <div class="mb-3">
              <label><?= $traducciones['preview_test_image'] ?></label>
              <div>
                <img id="previewImage" class="img-fluid" style="max-height: 300px; display: none;">
              </div>
            </div>

            <!-- Botones para voltear la imagen -->
            <div class="mb-3">
              <button type="button" id="flipHorizontal" class="btn btn-secondary me-2">
                <span class="material-symbols-outlined" style="font-size: 15px;">flip

                </span> <?= $traducciones['flip_horizontal'] ?>
              </button>

              <button type="button" id="flipVertical" class="btn btn-secondary">
                <span class="material-symbols-outlined" style="font-size: 15px;">rotate_right

                </span> <?= $traducciones['flip_vertical'] ?>
              </button>
            </div>


            <div class="mb-3">
              <label for="description" class="form-label"><?= $traducciones['description_test_image'] ?></label>
              <textarea id="description" name="description" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">

            <button type="submit" id="submitImageBtn" class="btn btn-save">
              <span class="spinner-border spinner-border-sm me-1 d-none" role="status" aria-hidden="true"></span>
              <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save_test_image'] ?>
            </button>

            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal"><i class="mdi mdi-cancel"></i>
              <?= $traducciones['cancel_test_image'] ?></button>

          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- View Image Modal -->
  <div class="modal fade" id="viewImageModal" tabindex="-1" aria-labelledby="viewImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewImageModalLabel"><?= $traducciones['preview_test_image'] ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center" id="viewImageContainer">

          <!-- Image or PDF will be injected here -->
        </div>
      </div>
    </div>
  </div>

</div> <!-- container -->


<!-- /Right-bar -->
<script src="public/assets/js/logout.js"></script>
<!-- Right bar overlay-->
<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>
<!-- JQuery primero -->
<?php
?>
<script type="module">
  import { validateFormFields } from "./public/assets/js/helpers/helpers.js";

  $(function () {
    const idioma = '<?= $_SESSION['idioma'] ?? 'ES' ?>';


    const texts = {
      ES: {
        errorTitle: '<?= $traducciones['errorTitle_test_image'] ?>',
        errorLoadImages: '<?= $traducciones['errorLoadImages_test_image'] ?>',
        errorServer: '<?= $traducciones['errorServer_test_image'] ?>',
        errorDescriptionRequired: '<?= $traducciones['errorDescriptionRequired_test_image'] ?>',
        updated: '<?= $traducciones['updated_test_image'] ?>',
        updatedSuccess: '<?= $traducciones['updatedSuccess_test_image'] ?>',
        saved: '<?= $traducciones['saved_test_image'] ?>',
        savedSuccess: '<?= $traducciones['savedSuccess_test_image'] ?>',
        confirmDelete: '<?= $traducciones['confirmDelete_test_image'] ?>',
        yesDelete: '<?= $traducciones['yesDelete_test_image'] ?>',
        cancel: '<?= $traducciones['cancel_test_image'] ?>',
        deleted: '<?= $traducciones['deleted_test_image'] ?>',
        deleteSuccess: '<?= $traducciones['deleteSuccess_test_image'] ?>',
        deleteError: '<?= $traducciones['deleteError_test_image'] ?>',
        deleteConnectionError: '<?= $traducciones['deleteConnectionError_test_image'] ?>',
        createLabel: '<?= $traducciones['createLabel_test_image'] ?>',
        editLabel: '<?= $traducciones['editLabel_test_image'] ?>',
        cannotPreview: '<?= $traducciones['cannotPreview_test_image'] ?>',
        uploadingTitle: '<?= $traducciones['uploading_title_test_image'] ?>',
        uploadingText: '<?= $traducciones['uploading_text_test_image'] ?>',
      },
      EN: {
        errorTitle: '<?= $traducciones['errorTitle_test_image'] ?>',
        errorLoadImages: '<?= $traducciones['errorLoadImages_test_image'] ?>',
        errorServer: '<?= $traducciones['errorServer_test_image'] ?>',
        errorDescriptionRequired: '<?= $traducciones['errorDescriptionRequired_test_image'] ?>',
        updated: '<?= $traducciones['updated_test_image'] ?>',
        updatedSuccess: '<?= $traducciones['updatedSuccess_test_image'] ?>',
        saved: '<?= $traducciones['saved_test_image'] ?>',
        savedSuccess: '<?= $traducciones['savedSuccess_test_image'] ?>',
        confirmDelete: '<?= $traducciones['confirmDelete_test_image'] ?>',
        yesDelete: '<?= $traducciones['yesDelete_test_image'] ?>',
        cancel: '<?= $traducciones['cancel_test_image'] ?>',
        deleted: '<?= $traducciones['deleted_test_image'] ?>',
        deleteSuccess: '<?= $traducciones['deleteSuccess_test_image'] ?>',
        deleteError: '<?= $traducciones['deleteError_test_image'] ?>',
        deleteConnectionError: '<?= $traducciones['deleteConnectionError_test_image'] ?>',
        createLabel: '<?= $traducciones['createLabel_test_image'] ?>',
        editLabel: '<?= $traducciones['editLabel_test_image'] ?>',
        cannotPreview: '<?= $traducciones['cannotPreview_test_image'] ?>',
        uploadingTitle: '<?= $traducciones['uploading_title_test_image'] ?>',
        uploadingText: '<?= $traducciones['uploading_text_test_image'] ?>',
      }
    }[idioma];

    const panelId = '<?= $id_test_panel ?>';
    const testId = '<?= $id_test ?>';

    window.imageActionFormatter = function (value, row) {
      return `
        <button class="btn btn-view viewBtn action-icon" data-file="${row.name_image}">
          <i class="mdi mdi-eye-outline"></i>
        </button>
        <button class="btn btn-pencil editBtn action-icon" data-id="${row.test_documents_id}">
          <i class="mdi mdi-pencil-outline"></i>
        </button>
        <button class="btn btn-delete deleteBtn action-icon" data-id="${row.test_documents_id}">
          <i class="mdi mdi-delete-outline"></i>
        </button>
      `;
    };

    $('#testImagesTable').bootstrapTable();

    function loadImages() {
      const url = `test-documents/panel/${panelId}/test/${testId}`;
      $.ajax({
        url,
        method: 'GET',
        dataType: 'json',
        success(res) {
          console.log("Respuesta de loadImages:", res);  // <-- depuración
          if (res.value !== true) {
            return Swal.fire(texts.errorTitle, texts.errorLoadImages, 'error');
          }
          if (!res.data || res.data.length === 0) {
            document.getElementById('toolbar').classList.remove('d-none');
            $('#testImagesTable').bootstrapTable('removeAll');
          } else {
            document.getElementById('toolbar').classList.remove('d-none');
            $('#testImagesTable').bootstrapTable('load', res.data);
          }
        },
        error(err) {
          console.error("Error en loadImages:", err);  // <-- depuración
          Swal.fire(texts.errorTitle, texts.errorServer, 'error');
        }
      });
    }


    loadImages();

    $(document).on('click', '.viewBtn', function () {
      const filename = $(this).data('file');
      const ext = filename.split('.').pop().toLowerCase();
      let html;
      if (['jpg', 'png'].includes(ext)) {
        html = `<img src="uploads/${filename}" class="img-fluid">`;
      } else if (ext === 'pdf') {
        html = `<embed src="uploads/${filename}" type="application/pdf" width="100%" height="600px" />`;
      } else {
        html = `<p>${texts.cannotPreview}</p>`;
      }
      $('#viewImageContainer').html(html);
      new bootstrap.Modal($('#viewImageModal')[0]).show();
    });

    $('#addImageBtn').click(function () {
      $('#imageForm')[0].reset();
      $('#image_id').val('');
      $('#currentFileContent').html('');
      $('#currentFilePreview').addClass('d-none');
      $('#imageModal').modal('show');
      $('#imageModalLabel').text(texts.createLabel);
    });

    $(document).on('click', '.editBtn', function () {
      const id = $(this).data('id');
      $.getJSON(`test-documents/${id}`, function (res) {
        if (res.value !== true) {
          return Swal.fire(texts.errorTitle, texts.errorLoadImages, 'error');
        }
        $('#image_id').val(res.data.test_documents_id);
        $('#description').val(res.data.description);
        $('#imageModalLabel').text(texts.editLabel);

        const file = res.data.name_image;
        const ext = file.split('.').pop().toLowerCase();
        let preview = '';
        if (['jpg', 'jpeg', 'png'].includes(ext)) {
          preview = `<img src="uploads/${file}" class="img-fluid" alt="Preview">`;
        } else if (ext === 'pdf') {
          preview = `<embed src="uploads/${file}" type="application/pdf" width="100%" height="400px">`;
        } else {
          preview = `<a href="uploads/${file}" target="_blank">${file}</a>`;
        }

        $('#currentFileContent').html(preview);
        $('#currentFilePreview').removeClass('d-none');
        $('#imageModal').modal('show');
      });
    });

    $('#imageForm').submit(function (e) {
      e.preventDefault();



      if (!validateFormFields(document.getElementById('imageForm'), ['description', 'name_image'], '<?= $traducciones['input_generic_error'] ?>')) {
        return
      }


      const id = $('#image_id').val();
      const description = $('#description').val().trim();
      const idTestPanel = $('#hid_panel').val();
      const idTest = $('#hid_test').val();
      const fileInput = $('#name_image')[0];
      const hasNewFile = fileInput.files.length > 0;

      if (!description) {
        return Swal.fire(texts.errorTitle, texts.errorDescriptionRequired, 'error');
      }

      // Mostrar loader con SweetAlert
      Swal.fire({
        title: texts.uploadingTitle || 'Uploading...',
        text: texts.uploadingText || 'Please wait while the image is being uploaded.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const hideLoader = () => {
        Swal.close();
      };

      const submitWithFormData = (formData, url, method = 'POST') => {
        $.ajax({
          url,
          method,
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success(res) {
            hideLoader();
            if (res.value) {
              $('#imageModal').modal('hide');
              Swal.fire(texts.saved, texts.savedSuccess, 'success').then(loadImages);
            } else {
              Swal.fire(texts.errorTitle, texts.errorServer, 'error');
            }
          },
          error() {
            hideLoader();
            Swal.fire(texts.errorTitle, texts.errorServer, 'error');
          }
        });
      };

      if (id && !hasNewFile) {
        const jsonData = { id, description, id_test_panel: idTestPanel, id_test: idTest };
        $.ajax({
          url: `test-documents/${id}`,
          type: 'PUT',
          data: JSON.stringify(jsonData),
          contentType: 'application/json',
          dataType: 'json',
          success(res) {
            hideLoader();
            if (res.value) {
              $('#imageModal').modal('hide');
              Swal.fire(texts.updated, texts.updatedSuccess, 'success').then(loadImages);
            } else {
              Swal.fire(texts.errorTitle, texts.errorServer, 'error');
            }
          },
          error() {
            hideLoader();
            Swal.fire(texts.errorTitle, texts.errorServer, 'error');
          }
        });
      } else {
        const url = id ? `test-documents/${id}` : 'test-documents';
        const formElement = $('#imageForm')[0];
        const formData = new FormData(formElement);
        formData.set('id_test_panel', idTestPanel);
        formData.set('id_test', idTest);
        if (id) formData.append('_method', 'PUT');

        if (cropper) {
          cropper.getCroppedCanvas().toBlob(function (blob) {
            formData.set('name_image', blob, 'cropped-image.jpg');
            submitWithFormData(formData, url);
          });
        } else {
          submitWithFormData(formData, url);
        }
      }
    });



    $(document).on('click', '.deleteBtn', function () {
      const id = $(this).data('id');

      Swal.fire({
        title: texts.confirmDelete,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: texts.yesDelete,
        cancelButtonText: texts.cancel
      }).then(({ isConfirmed }) => {
        if (!isConfirmed) return;

        $.ajax({
          url: `test-documents/${id}`,
          method: 'DELETE',
          dataType: 'json',
          data: { id: id },
          success(res) {
            if (res && res.value) {
              Swal.fire(texts.deleted, texts.deleteSuccess, 'success').then(loadImages);
            } else {
              Swal.fire(texts.errorTitle, texts.deleteError, 'error');
            }
          },
          error() {
            Swal.fire(texts.errorTitle, texts.deleteConnectionError, 'error');
          }
        });
      });
    });
    let cropper;

    const inputImage = document.getElementById('name_image');
    const imagePreview = document.getElementById('previewImage');

    const flipHorizontalBtn = document.getElementById('flipHorizontal');
    const flipVerticalBtn = document.getElementById('flipVertical');

    let scaleX = 1;
    let rotation = 0;

    function initializeCropper() {
      cropper = new Cropper(imagePreview, {
        aspectRatio: NaN, // proporción libre
        viewMode: 0,      // modo más libre
        autoCropArea: 1,  // recorte amplio
        responsive: true,
        background: false,
        restore: false,
        rotatable: true,
        scalable: true,
        zoomable: false,
        movable: true,
        cropBoxResizable: true,
        cropBoxMovable: true,
        ready() {
          cropper.rotateTo(rotation);
          cropper.scaleX(scaleX);

          // Ajuste automático para que se vea toda la imagen
          const imageData = cropper.getImageData();
          const containerData = cropper.getContainerData();

          const scaleRatio = Math.min(
            containerData.width / imageData.naturalWidth,
            containerData.height / imageData.naturalHeight
          );

          const newWidth = imageData.naturalWidth * scaleRatio;
          const newHeight = imageData.naturalHeight * scaleRatio;

          cropper.setCanvasData({
            left: (containerData.width - newWidth) / 2,
            top: (containerData.height - newHeight) / 2,
            width: newWidth,
            height: newHeight
          });
        }
      });
    }

    inputImage.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (file && /^image\//.test(file.type)) {
        const reader = new FileReader();
        reader.onload = function (event) {
          imagePreview.src = event.target.result;
          imagePreview.style.display = 'block';

          if (cropper) {
            cropper.destroy();
            cropper = null;
          }

          scaleX = 1;
          rotation = 0;

          imagePreview.onload = function () {
            initializeCropper();
          };
        };
        reader.readAsDataURL(file);
      } else {
        imagePreview.style.display = 'none';
        if (cropper) {
          cropper.destroy();
          cropper = null;
        }
      }
    });

    flipHorizontalBtn.addEventListener('click', function () {
      if (cropper) {
        scaleX = -scaleX;
        cropper.scaleX(scaleX); // ¡ya no reinicializa!
      }
    });

    flipVerticalBtn.addEventListener('click', function () {
      if (cropper) {
        rotation = (rotation + 90) % 360;
        cropper.rotateTo(rotation); // ¡ya no reinicializa!
      }
    });




  });
</script>