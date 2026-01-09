
  <div id="wrapper">
    <div class="content-page">
      <div class="content">
        <div class="container-fluid">


          <!-- Botón para hacer backup -->


          <h4 class="page-title"><?= $traducciones['backups_page_title'] ?></h4>
          <div id="toolbar" class="d-none">
            <button class="btn btn-add mt-1" id="backupBtn">
              <?= $traducciones['backups_create_button'] ?>
            </button>
          </div>

          <div class="card">
            <div class="card-body">


              <table id="backupTable" data-toggle="table" data-search="true" data-show-refresh="true"
                data-page-list="[5, 10, 20]" data-page-size="5" data-pagination="true" data-show-columns="true"
                data-show-pagination-switch="true" data-url="backups" class="table table-borderless"
                data-toolbar="#toolbar" data-locale="<?= $locale ?>">
                <thead class="">
                  <tr>
                    <th data-field="name" data-sortable="true"><?= $traducciones['backups_table_column_name'] ?></th>
                    <th data-field="date" data-sortable="true" data-formatter="dateFormatterBackup">
                      <?= $traducciones['backups_table_column_date'] ?>
                    </th>
                    <th data-field="id" data-align="center" data-formatter="backupActionFormatter">
                      <?= $traducciones['action'] ?>
                    </th>
                  </tr>
                </thead>
              </table>


            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Right bar overlay-->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>
    <!-- JQuery primero -->
    <!-- Plugins js -->


    <!-- App js -->
    <script src="public/assets/js/logout.js"></script>
    <script>
      $(document).ready(function () {
        const idioma = '<?= $_SESSION["idioma"] ?? "ES" ?>';

        const mensajes = {
          ES: {
            tituloError: '<?= $traducciones['tituloError_backups'] ?>',
            backuperror: '<?= $traducciones['titulobackupError_backups'] ?>',
            tituloExito: '<?= $traducciones['tituloExito_backups'] ?>',
            errorCargarBackups: '<?= $traducciones['errorCargarBackups_backups'] ?>',
            backupCreado: '<?= $traducciones['backupCreado_backups'] ?>',
            restaurado: '<?= $traducciones['restaurado_backups'] ?>',
            restaurarError: '<?= $traducciones['restaurarError_backups'] ?>',
            eliminado: '<?= $traducciones['eliminado_backups'] ?>',
            eliminarError: '<?= $traducciones['eliminarError_backups'] ?>',
            eliminarConfirmacion: '<?= $traducciones['eliminarConfirmacion_backups'] ?>',
            eliminarCancelar: '<?= $traducciones['eliminarCancelar_backups'] ?>',
            eliminarConfirmar: '<?= $traducciones['eliminarConfirmar_backups'] ?>',
          },
          EN: {
            backup_error: '<?= $traducciones['titulobackupError_backups'] ?>',
            tituloError: '<?= $traducciones['tituloError_backups'] ?>',
            tituloExito: '<?= $traducciones['tituloExito_backups'] ?>',
            errorCargarBackups: '<?= $traducciones['errorCargarBackups_backups'] ?>',
            backupCreado: '<?= $traducciones['backupCreado_backups'] ?>',
            restaurado: '<?= $traducciones['restaurado_backups'] ?>',
            restaurarError: '<?= $traducciones['restaurarError_backups'] ?>',
            eliminado: '<?= $traducciones['eliminado_backups'] ?>',
            eliminarError: '<?= $traducciones['eliminarError_backups'] ?>',
            eliminarConfirmacion: '<?= $traducciones['eliminarConfirmacion_backups'] ?>',
            eliminarCancelar: '<?= $traducciones['eliminarCancelar_backups'] ?>',
            eliminarConfirmar: '<?= $traducciones['eliminarConfirmar_backups'] ?>',
          }
        };

        const t = mensajes[idioma];

        function loadBackups() {
          $.ajax({
            url: 'backups',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
              if (response.value) {
                $('#backupTable').bootstrapTable('load', response.data);
              } else {
                $('#backupTable').bootstrapTable('load', []);
              }
            },
            error: function (xhr, status, error) {
              console.error('Error cargando backups:', status, error);
              Swal.fire(t.tituloError, t.errorCargarBackups, 'error');
            }
          });
        }

        $('#backupTable').on('refresh.bs.table', loadBackups);

        $('#backupBtn').click(function () {
          $.ajax({
            url: 'backups',
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
              console.log('Sending AJAX request to backups...');
            },
            success: function (response) {
              if (response.value) {
                Swal.fire({
                  title: t.tituloExito,
                  text: t.backupCreado,
                  icon: 'success',
                  confirmButtonText: 'Okay'
                }).then(() => {
                  loadBackups();
                });
              } else {
                Swal.fire({
                  title: t.tituloError,
                  text: response.message,
                  icon: 'error',
                  confirmButtonText: 'Okay'
                });
              }
            },
            error: function (xhr, status, error) {
              console.log(xhr);
              console.log(status);
              console.log(error);
              Swal.fire({
                title: t.tituloError,
                text: t.backuperror,
                icon: 'error',
                confirmButtonText: 'Okay'
              });
            }
          });
        });

        $(document).on('click', '.restoreBackupBtn', function () {
          const backupId = $(this).data('id');
          $.ajax({
            url: `backups/${backupId}/restore`,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
              if (response.value) {
                Swal.fire({
                  title: t.tituloExito,
                  text: t.restaurado,
                  icon: 'success',
                  confirmButtonText: 'Okay'
                }).then(() => {
                  loadBackups();
                });
              } else {
                Swal.fire({
                  title: t.tituloError,
                  text: t.restaurarError,
                  icon: 'error',
                  confirmButtonText: 'Okay'
                });
              }
            }
          });
        });

        $(document).on('click', '.deleteBackupBtn', function () {
          const backupId = $(this).data('id');
          Swal.fire({
            title: t.eliminarConfirmacion,
            text: t.eliminarConfirmacion,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: t.eliminarConfirmar,
            cancelButtonText: t.eliminarCancelar
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: `backups/${backupId}`,
                type: 'DELETE',
                data: { backup_id: backupId },
                dataType: 'json',
                success: function (response) {
                  if (response.value) {
                    Swal.fire({
                      title: t.eliminado,
                      text: t.eliminado,
                      icon: 'success',
                      confirmButtonText: 'Okay'
                    }).then(() => {
                      location.reload();
                    });
                  } else {
                    Swal.fire({
                      title: t.tituloError,
                      text: t.eliminarError,
                      icon: 'error',
                      confirmButtonText: 'Okay'
                    });
                  }
                }
              });
            }
          });
        });

        document.getElementById('toolbar').classList.remove('d-none');
        loadBackups();
      });
    </script>

    <script>
      // Formatter para acciones
      window.backupActionFormatter = function (value, row) {
        return `
      <button class="btn btn-pencil action-icon btn-sm restoreBackupBtn" data-id="${row.backup_id}">
        <i class="mdi mdi-restore"></i>
      </button>
      <button class="btn btn-delete action-icon btn-sm deleteBackupBtn" data-id="${row.backup_id}">
        <i class="mdi mdi-delete-outline"></i>
      </button>
    `;
      };

      function dateFormatterBackup(value) {
        if (!value) return '';
        const parts = value.split('-');
        if (parts.length !== 3) return value;
        const year = parts[0];
        const month = parts[1].padStart(2, '0');
        const day = parts[2].padStart(2, '0');
        return `${month}/${day}/${year}`;
      }
    </script>

  </div>

</body>

</html>