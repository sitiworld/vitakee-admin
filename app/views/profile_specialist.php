<head>

  <style>
    #profile-image {
      cursor: pointer;
    }

    #preview_cropper {
      max-width: 100%;
      height: auto;
    }
  </style>
</head>

<body>

  <div class="container-fluid">
    <?php
    $idioma = strtoupper($_SESSION['idioma'] ?? 'ES');
    $locale = $idioma === 'ES' ? 'es-ES' : 'en-US';
    if (!in_array($idioma, ['EN', 'ES'])) {
      $idioma = 'EN';
    }

    $langField = $idioma === 'ES' ? 'name_es' : 'name_en';
    require_once __DIR__ . '/../models/SpecialtyModel.php';
    require_once __DIR__ . '/../models/TitleModel.php';
    $specialtyModel = new SpecialtyModel();
    $titleModel = new TitleModel();
    $specialties = $specialtyModel->getAll();
    $titles = $titleModel->getAll();

    $system_type = strtolower($_SESSION['system_type'] ?? 'us');
    ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4">
          <div class="card text-center">
            <div class="card-body">
              <div class="d-flex align-items-center mb-1">
                <img id="profile-image" src="public/assets/images/users/user_boy.jpeg" alt="Profile"
                  class="rounded-circle avatar-lg img-thumbnail">
                <div class="w-100">
                  <h4 class="mt-3" id="profile-name">-</h4>
                  <p class="text-muted font-14 mb-2" id="profile-role">
                    <?= $traducciones['profile_specialist_role'] ?? 'Specialist' ?>
                  </p>
                  <p id="profile-website-link" class="mb-2"></p>
                </div>

              </div>
              <button type="button" class="btn btn-sm btn-add editUserBtn mt-2">
                <i class="mdi mdi-pencil"></i> <?= $traducciones['edit_lipid_profile'] ?? 'Edit' ?>
              </button>

              <div class="text-start mt-3">
                <h5 class="mb-3 mt-4 text-uppercase bg-light p-2">
                  <i class="mdi mdi-account-circle me-1"></i>
                  <?= $traducciones['personal_information'] ?? 'Personal Information' ?>
                </h5>

                <p class="text-muted mb-2 font-13">
                  <strong><?= $traducciones['bio'] ?? 'Biography' ?>:</strong>
                  <span class="ms-2" id="profile-bio">-</span>
                </p>

                <p class="text-muted mb-2 font-13">
                  <strong><?= $traducciones['birthday'] ?? 'Birthday' ?> :</strong>
                  <span class="ms-2" id="profile-birthday">-</span>
                </p>

                <p class="text-muted mb-2 font-13">
                  <strong><?= $traducciones['signup_email_label'] ?? 'Email Address' ?> :</strong>
                  <span class="ms-2" id="profile-email"></span>
                </p>

                <p class="text-muted mb-2 font-13">
                  <strong><?= $traducciones['telephone'] ?? 'Telephone' ?>:</strong>
                  <span class="ms-2" id="profile-telephone"></span>
                </p>

                <p class="text-muted mb-2 font-13">
                  <strong><?= $traducciones['metrical_system'] ?? 'Measurement System' ?> :</strong>
                </p>
                <div class="mb-3">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input system-update-radio" type="radio" name="card_height_system"
                      id="cardHeightAmerican" value="us" />
                    <label class="form-check-label" for="cardHeightAmerican">
                      <?= $traducciones['height_american'] ?? 'Imperial (lb, in)' ?>
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input system-update-radio" type="radio" name="card_height_system"
                      id="cardHeightEuropean" value="eu" />
                    <label class="form-check-label" for="cardHeightEuropean">
                      <?= $traducciones['height_european'] ?? 'Metric (kg, cm)' ?>
                    </label>
                  </div>
                </div>
                <p></p>
                <p class="text-muted mb-1 font-13">
                  <strong><?= $traducciones['profile_specialist_location'] ?? 'Location' ?> :</strong> <span
                    class="ms-2" id="profile-location">-</span>
                </p>
                <p class="text-muted mb-1 font-13">
                  <strong><?= $traducciones['profile_specialist_social_networks'] ?? 'Social Networks' ?>:</strong>
                  <span class="ms-2" id="profile-social-links">
                  </span>
                </p>
              </div>

              <!-- <a href="#"
                class="text-info"><?= $traducciones['profile_specialist_comments_link'] ?? 'Go to comment section' ?>
                <span class="mdi mdi-comment-multiple-outline"></span></a> -->

              <div class="row mt-4">
                <div class="col-4">
                  <div class="mt-3">
                    <h4 id="profile-labs-count">0</h4>
                    <p class="mb-0 text-muted">
                      <?= $traducciones['profile_specialist_reports_evaluated'] ?? 'Lab Reports Evaluated' ?>
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="mt-3">
                    <h4 id="profile-consultations-count">0</h4>
                    <p class="mb-0 text-muted">
                      <?= $traducciones['profile_specialist_consultations_completed'] ?? 'Consultations Completed' ?>
                    </p>
                  </div>
                </div>
                <div class="col-4">
                  <div class="mt-3">
                    <h4 id="profile-rating-count" class="text-secondary-color">0/5</h4>
                    <p class="mb-0 text-muted">
                      <?= $traducciones['profile_specialist_patient_rating'] ?? 'Patient Rating' ?>
                    </p>
                    <div class="text-secondary-color" id="profile-rating-stars"></div>
                  </div>
                </div>
              </div>


            </div>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-3">
                  <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab" role="tablist"
                    aria-orientation="vertical">
                    <a class="nav-link show mb-1 active" id="v-pills-general-tab" data-bs-toggle="pill"
                      href="#v-pills-general" role="tab" aria-controls="v-pills-general" aria-selected="true">
                      <i class="mdi mdi-account-circle-outline me-1"></i>
                      <?= $traducciones['profile_specialist_tab_general'] ?? 'General Information' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-credentials-tab" data-bs-toggle="pill"
                      href="#v-pills-credentials" role="tab" aria-controls="v-pills-credentials" aria-selected="false">
                      <i class="mdi mdi-certificate-outline me-1"></i>
                      <?= $traducciones['profile_specialist_tab_credentials'] ?? 'Credentials & Certifications' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-social-tab" data-bs-toggle="pill" href="#v-pills-social"
                      role="tab" aria-controls="v-pills-social" aria-selected="false">
                      <i class="mdi mdi-share-variant-outline me-1"></i>
                      <?= $traducciones['profile_specialist_tab_socials'] ?? 'Socials' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-about-tab" data-bs-toggle="pill" href="#v-pills-about"
                      role="tab" aria-controls="v-pills-about" aria-selected="false">
                      <i class="mdi mdi-information-outline me-1"></i>
                      <?= $traducciones['profile_specialist_tab_about'] ?? 'About Me' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-locations-tab" data-bs-toggle="pill" href="#v-pills-locations"
                      role="tab" aria-controls="v-pills-locations" aria-selected="false">
                      <i class="mdi mdi-map-marker-outline me-1"></i>
                      <?= $traducciones['profile_specialist_tab_locations'] ?? 'Locations' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-availability-tab" data-bs-toggle="pill"
                      href="#v-pills-availability" role="tab" aria-controls="v-pills-availability"
                      aria-selected="false">
                      <i class="mdi mdi-calendar-clock-outline me-1"></i>
                      <?= $traducciones['availability_label'] ?? 'Availability' ?>
                    </a>
                    <a class="nav-link mb-1" id="v-pills-pricing-tab" data-bs-toggle="pill" href="#v-pills-pricing"
                      role="tab" aria-controls="v-pills-pricing" aria-selected="false">
                      <i class="mdi mdi-currency-usd me-1"></i>
                      <?= $traducciones['profile_specialist_tab_services'] ?? 'Services & Pricing' ?>
                    </a>
                  </div>
                </div>
                <div class="col-sm-9">
                  <div class="tab-content pt-0">
                    <div class="tab-pane fade active show" id="v-pills-general" role="tabpanel"
                      aria-labelledby="v-pills-general-tab">
                    </div>
                    <div class="tab-pane fade" id="v-pills-credentials" role="tabpanel"
                      aria-labelledby="v-pills-credentials-tab">
                      <div id="certifications-view">
                        <h4 class="d-flex justify-content-between align-items-center">
                          <?= $traducciones['profile_specialist_credentials_title'] ?? 'Credentials & Studies' ?>
                          <a href="javascript:void(0);" id="add-certification-btn" class="ms-2"
                            title="<?= $traducciones['profile_specialist_add_certification'] ?? 'Add Certification' ?>"><i
                              class="mdi mdi-plus-circle text-secondary"></i></a>
                        </h4>
                        <div id="certifications-list">
                        </div>
                      </div>
                      <div id="certifications-edit" style="display: none;">
                        <h4 id="certification-form-title">
                          <?= $traducciones['profile_specialist_add_certification'] ?? 'Add Certification' ?>
                        </h4>
                        <form id="certificationForm" enctype="multipart/form-data" data-validation="reactive"
                          novalidate>
                          <input type="hidden" id="certification_id" name="certification_id">
                          <div class="mb-3">
                            <label for="cert_title" class="form-label"><?= $traducciones['title'] ?? 'Title' ?></label>
                            <input type="text" id="cert_title" name="title" class="form-control"
                              data-rules="noVacio|longitudMaxima:100"
                              data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                              data-message-longitud-maxima="<?= $traducciones['validation_max_length_100'] ?? 'This field cannot be longer than {max} characters.' ?>">
                          </div>
                          <div class="mb-3">
                            <label for="cert_description"
                              class="form-label"><?= $traducciones['description'] ?? 'Description' ?></label>
                            <textarea id="cert_description" name="description" class="form-control" rows="3"
                              data-rules="longitudMaxima:500"
                              data-message-longitud-maxima="<?= $traducciones['validation_max_length_500'] ?? 'This field cannot be longer than {max} characters.' ?>"></textarea>
                          </div>
                          <div class="mb-3">
                            <label for="cert_file"
                              class="form-label"><?= $traducciones['profile_specialist_certification_file_label'] ?? 'Certification File (PDF, PNG, JPG)' ?></label>
                            <input type="file" id="cert_file" name="file" class="form-control"
                              accept=".pdf,.png,.jpg,.jpeg"
                              data-rules="esTipoArchivo:application/pdf,image/png,image/jpeg|tamanoMaximoArchivo:5"
                              data-message-es-tipo-archivo="<?= $traducciones['validation_file_type'] ?? 'Invalid file type. Only allowed: {types}.' ?>"
                              data-message-tamano-maximo-archivo="<?= $traducciones['validation_file_size'] ?? 'The file is too large. Maximum size is {maxMB} MB.' ?>">
                          </div>
                          <div id="cert-preview-container" class="mb-3" style="display: none;">
                            <p><strong
                                id="cert-preview-label"><?= $traducciones['profile_specialist_current_document_preview'] ?? 'Current Document Preview:' ?></strong>
                            </p>
                            <img id="cert-preview-image" style="max-width: 100%; max-height: 200px; display: none;"
                              src="" alt="Certification Preview">
                            <embed id="cert-preview-pdf" style="width: 100%; height: 200px; display: none;" src=""
                              type="application/pdf">
                          </div>
                          <div class="mb-3">
                            <label class="form-label"><?= $traducciones['visibility'] ?? 'Visibility' ?></label>
                            <div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="visibility" id="visibilityPublic"
                                  value="PUBLIC" checked>
                                <label class="form-check-label"
                                  for="visibilityPublic"><?= $traducciones['public'] ?? 'Public' ?></label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="visibility" id="visibilityPrivate"
                                  value="PRIVATE">
                                <label class="form-check-label"
                                  for="visibilityPrivate"><?= $traducciones['private'] ?? 'Private' ?></label>
                              </div>
                            </div>
                          </div>
                          <div class="text-end">
                            <button type="button" class="btn btn-cancel"
                              id="cancel-cert-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                            <button type="submit" class="btn btn-save"><?= $traducciones['save'] ?? 'Save' ?></button>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-social" role="tabpanel" aria-labelledby="v-pills-social-tab">
                      <div id="social-links-view">
                        <h4 class="d-flex justify-content-between align-items-center">
                          <?= $traducciones['profile_specialist_social_links_title'] ?? 'Social Links' ?>
                          <a href="javascript:void(0);" id="add-social-link-btn" class="ms-2"
                            title="<?= $traducciones['profile_specialist_add_social_link'] ?? 'Add Social Link' ?>"><i
                              class="mdi mdi-plus-circle text-secondary"></i></a>
                        </h4>
                        <div id="social-links-list">
                        </div>
                      </div>
                      <div id="social-links-edit" style="display: none;">
                        <h4 id="social-form-title">
                          <?= $traducciones['profile_specialist_add_social_link'] ?? 'Add Social Link' ?>
                        </h4>
                        <form id="socialLinksForm" data-validation="reactive" novalidate>
                          <input type="hidden" id="social_link_id" name="social_link_id">
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label for="platform"
                                class="form-label"><?= $traducciones['profile_specialist_platform_label'] ?? 'Platform' ?></label>
                              <div id="platform-container">
                                <select id="platform" name="platform" class="form-select" style="width: 100%"
                                  data-rules="noVacio"
                                  data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                  data-error-container="#platform-container">
                                  <option value="">
                                    <?= $traducciones['profile_specialist_platform_select'] ?? 'Select a platform...' ?>
                                  </option>
                                  <option value="instagram">Instagram</option>
                                  <option value="facebook">Facebook</option>
                                  <option value="tiktok">TikTok</option>
                                  <option value="twitter">X (Twitter)</option>
                                  <option value="linkedin">LinkedIn</option>
                                  <option value="threads">Threads</option>
                                  <option value="telegram">Telegram</option>
                                  <option value="skype">Skype</option>
                                  <option value="whatsapp">WhatsApp</option>
                                  <option value="whatsapp_business">WhatsApp Business</option>
                                </select>

                              </div>
                            </div>
                            <div class="col-md-6 mb-3">
                              <label for="url"
                                class="form-label"><?= $traducciones['profile_specialist_url_user_label'] ?? 'URL or Username' ?></label>
                              <input type="text" id="url" name="url" class="form-control" placeholder="https://..."
                                data-rules="noVacio|esUrlValida"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                data-message-es-url-valida="<?= $traducciones['validation_url'] ?? 'Please enter a valid URL.' ?>">
                            </div>
                          </div>
                          <div class="text-end">
                            <button type="button" class="btn btn-cancel"
                              id="cancel-social-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                            <button type="submit" class="btn btn-save">
                              <i class="mdi mdi-content-save-outline"></i>
                              <?= $traducciones['save'] ?? 'Save' ?>
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-about" role="tabpanel" aria-labelledby="v-pills-about-tab">
                    </div>
                    <div class="tab-pane fade" id="v-pills-locations" role="tabpanel"
                      aria-labelledby="v-pills-locations-tab">
                      <div id="locations-view">
                        <h4 class="d-flex justify-content-between align-items-center">
                          <?= $traducciones['profile_specialist_locations_title'] ?? 'My Locations' ?>
                          <a href="javascript:void(0);" id="add-location-btn" class="ms-2"
                            title="<?= $traducciones['profile_specialist_add_location'] ?? 'Add Location' ?>"><i
                              class="mdi mdi-plus-circle text-secondary"></i></a>
                        </h4>
                        <div id="locations-list">
                        </div>
                      </div>
                      <div id="locations-edit" style="display: none;">
                        <h4 id="location-form-title">
                          <?= $traducciones['profile_specialist_add_location'] ?? 'Add Location' ?>
                        </h4>
                        <form id="locationForm" data-validation="reactive" novalidate>
                          <input type="hidden" id="location_id" name="location_id">
                          <div class="mb-3">
                            <label for="country_id"
                              class="form-label"><?= $traducciones['country'] ?? 'Country' ?></label>
                            <div id="country-id-container">
                              <select id="country_id" name="country_id" class="form-select" data-rules="noVacio"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                data-error-container="#country-id-container"></select>
                            </div>
                          </div>
                          <div class="mb-3">
                            <label for="state_id" class="form-label"><?= $traducciones['state'] ?? 'State' ?></label>
                            <div id="state-id-container">
                              <select id="state_id" name="state_id" class="form-select" disabled data-rules="noVacio"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                data-error-container="#state-id-container"></select>
                            </div>
                          </div>
                          <div class="mb-3">
                            <label for="city_id" class="form-label"><?= $traducciones['city'] ?? 'City' ?></label>
                            <div id="city-id-container">
                              <select id="city_id" name="city_id" class="form-select" disabled data-rules="noVacio"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                data-error-container="#city-id-container"></select>
                            </div>
                          </div>
                          <div class="mb-3">
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary"
                                value="1">
                              <label class="form-check-label"
                                for="is_primary"><?= $traducciones['profile_specialist_primary_location_label'] ?? 'Set as primary location' ?></label>
                            </div>
                          </div>
                          <div class="text-end">
                            <button type="button" class="btn btn-cancel"
                              id="cancel-location-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                            <button type="submit" class="btn btn-save"><?= $traducciones['save'] ?? 'Save' ?></button>
                          </div>
                        </form>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-availability" role="tabpanel"
                      aria-labelledby="v-pills-availability-tab">

                      <ul class="nav nav-tabs nav-bordered mb-3">
                        <li class="nav-item">
                          <a href="#availability-schedule" data-bs-toggle="tab" aria-expanded="true"
                            class="nav-link active">
                            <i
                              class="mdi mdi-calendar-week me-1"></i><?= $traducciones['profile_specialist_availability_schedule_tab'] ?? 'Schedule' ?>
                          </a>
                        </li>
                        <li class="nav-item">
                          <a href="#availability-blocks" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                            <i
                              class="mdi mdi-calendar-remove me-1"></i><?= $traducciones['profile_specialist_availability_blocks_tab'] ?? 'Blocked Events' ?>
                          </a>
                        </li>
                      </ul>

                      <div class="tab-content">
                        <div class="tab-pane show active" id="availability-schedule">
                          <div id="availability-view">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                              <h4>
                                <?= $traducciones['profile_specialist_availability_title'] ?? 'Availability Schedule' ?>
                              </h4>
                              <a href="javascript:void(0);" id="add-availability-btn" class="ms-2"
                                title="<?= $traducciones['profile_specialist_add_availability'] ?? 'Add Availability' ?>"><i
                                  class="mdi mdi-plus-circle text-secondary"></i></a>
                            </div>


                            <div id="availability-list">
                            </div>
                          </div>
                          <div id="availability-edit" style="display: none;">
                            <h4 id="availability-form-title">
                              <?= $traducciones['profile_specialist_add_availability'] ?? 'Add Availability' ?>
                            </h4>
                            <form id="availabilityForm" data-validation="reactive" novalidate>
                              <input type="hidden" id="availability_id" name="availability_id">
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label for="weekday"
                                    class="form-label"><?= $traducciones['profile_specialist_day_of_week_label'] ?? 'Day of the Week' ?></label>
                                  <select id="weekday" name="weekday" class="form-select" data-rules="noVacio"
                                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                    <option value="">
                                      <?= $traducciones['profile_specialist_day_select'] ?? 'Select a day...' ?>
                                    </option>
                                    <option value="Monday">
                                      <?= $traducciones['profile_specialist_day_monday'] ?? 'Monday' ?>
                                    </option>
                                    <option value="Tuesday">
                                      <?= $traducciones['profile_specialist_day_tuesday'] ?? 'Tuesday' ?>
                                    </option>
                                    <option value="Wednesday">
                                      <?= $traducciones['profile_specialist_day_wednesday'] ?? 'Wednesday' ?>
                                    </option>
                                    <option value="Thursday">
                                      <?= $traducciones['profile_specialist_day_thursday'] ?? 'Thursday' ?>
                                    </option>
                                    <option value="Friday">
                                      <?= $traducciones['profile_specialist_day_friday'] ?? 'Friday' ?>
                                    </option>
                                    <option value="Saturday">
                                      <?= $traducciones['profile_specialist_day_saturday'] ?? 'Saturday' ?>
                                    </option>
                                    <option value="Sunday">
                                      <?= $traducciones['profile_specialist_day_sunday'] ?? 'Sunday' ?>
                                    </option>
                                  </select>
                                </div>
                                <!-- Usar SIEMPRE la timezone del especialista (oculta) -->
                                <input type="hidden" id="availabilityTimezone" name="timezone"
                                  value="<?= htmlspecialchars($_SESSION['timezone']) ?>">

                              </div>
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label for="start_time"
                                    class="form-label"><?= $traducciones['profile_specialist_start_time_label'] ?? 'Start Time' ?></label>
                                  <input type="text" id="start_time" name="start_time" class="form-control"
                                    placeholder="HH:MM AM/PM" data-rules="noVacio"
                                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label for="end_time"
                                    class="form-label"><?= $traducciones['profile_specialist_end_time_label'] ?? 'End Time' ?></label>
                                  <input type="text" id="end_time" name="end_time" class="form-control"
                                    placeholder="HH:MM AM/PM" data-rules="noVacio"
                                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label for="buffer_time_minutes"
                                    class="form-label"><?= $traducciones['profile_specialist_buffer_time_label'] ?? 'Time Between Appointments (minutes)' ?></label>
                                  <input type="number" id="buffer_time_minutes" name="buffer_time_minutes"
                                    class="form-control" placeholder="10" min="0" step="5">
                                </div>
                              </div>

                              <div class="text-end">
                                <button type="button" class="btn btn-cancel"
                                  id="cancel-availability-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                                <button type="submit"
                                  class="btn btn-save"><?= $traducciones['save'] ?? 'Save' ?></button>
                              </div>
                            </form>
                          </div>
                        </div>
                        <div class="tab-pane" id="availability-blocks">
                          <div id="blocks-view">
                            <h4 class="d-flex justify-content-between align-items-center">
                              <?= $traducciones['profile_specialist_blocks_title'] ?? 'My Blocked Events' ?>
                              <a href="javascript:void(0);" id="add-block-btn" class="ms-2"
                                title="<?= $traducciones['profile_specialist_add_block'] ?? 'Add Block' ?>"><i
                                  class="mdi mdi-plus-circle text-secondary"></i></a>
                            </h4>
                            <div id="blocks-list">
                            </div>
                          </div>
                          <div id="blocks-edit" style="display: none;">
                            <h4 id="block-form-title">
                              <?= $traducciones['profile_specialist_add_block'] ?? 'Add Block' ?>
                            </h4>
                            <form id="blockForm" data-validation="reactive" novalidate>
                              <input type="hidden" id="second_opinion_id" name="second_opinion_id">
                              <div class="row">
                                <div class="col-md-6 mb-3">
                                  <label for="request_date_to"
                                    class="form-label"><?= $traducciones['profile_specialist_block_start_datetime'] ?? 'Start Date & Time' ?></label>
                                  <input type="text" id="request_date_to" name="request_date_to" class="form-control"
                                    data-rules="noVacio"
                                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                  <label for="request_date_end"
                                    class="form-label"><?= $traducciones['profile_specialist_block_end_datetime'] ?? 'End Date & Time' ?></label>
                                  <input type="text" id="request_date_end" name="request_date_end" class="form-control"
                                    data-rules="noVacio"
                                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                </div>
                              </div>
                              <div class="mb-3">
                                <label for="block_notes"
                                  class="form-label"><?= $traducciones['profile_specialist_block_notes_label'] ?? 'Notes (Optional)' ?></label>
                                <textarea id="block_notes" name="notes" class="form-control" rows="3"
                                  placeholder="<?= $traducciones['profile_specialist_block_notes_placeholder'] ?? 'E.g., Personal appointment' ?>"
                                  data-rules="longitudMaxima:500"
                                  data-message-longitud-maxima="<?= $traducciones['validation_max_length_500'] ?? 'This field cannot be longer than {max} characters.' ?>"></textarea>
                              </div>
                              <div class="text-end">
                                <button type="button" class="btn btn-cancel"
                                  id="cancel-block-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                                <button type="submit"
                                  class="btn btn-save"><?= $traducciones['save'] ?? 'Save' ?></button>
                              </div>
                            </form>
                          </div>
                        </div>

                      </div>
                    </div>
                    <div class="tab-pane fade" id="v-pills-pricing" role="tabpanel"
                      aria-labelledby="v-pills-pricing-tab">
                      <div id="pricing-view">
                        <h4 class="d-flex justify-content-between align-items-center">
                          <?= $traducciones['profile_specialist_services_title'] ?? 'Services & Pricing' ?>
                          <a href="javascript:void(0);" id="add-pricing-btn" class="ms-2"
                            title="<?= $traducciones['profile_specialist_add_service'] ?? 'Add Service' ?>"><i
                              class="mdi mdi-plus-circle text-secondary"></i></a>
                        </h4>
                        <div id="pricing-list">
                        </div>
                      </div>
                      <div id="pricing-edit" style="display: none;">
                        <h4 id="pricing-form-title">
                          <?= $traducciones['profile_specialist_add_service'] ?? 'Add Service' ?>
                        </h4>
                        <form id="pricingForm" data-validation="reactive" novalidate>
                          <input type="hidden" id="pricing_id" name="pricing_id">
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label for="service_type"
                                class="form-label"><?= $traducciones['profile_specialist_service_type_label'] ?? 'Service Type' ?></label>
                              <select id="service_type" name="service_type" class="form-select" data-rules="noVacio"
                                data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
                                <option value="">
                                  <?= $traducciones['profile_specialist_service_select'] ?? 'Select a service...' ?>
                                </option>
                                <option value="CONSULTATION">
                                  <?= $traducciones['CONSULTATION'] ?? 'Consultation' ?>
                                </option>
                                <option value="FOLLOW_UP">
                                  <?= $traducciones['FOLLOW_UP'] ?? 'Follow Up' ?>
                                </option>
                                <option value="REVIEW">
                                  <?= $traducciones['REVIEW'] ?? 'Review' ?>
                                </option>
                              </select>
                            </div>

                            <div class="col-md-6 mb-3" id="duration-container" style="display: none;">
                              <label for="duration_services"
                                class="form-label"><?= $traducciones['profile_specialist_duration_label'] ?? 'Duration (minutes)' ?></label>
                              <input type="number" id="duration_services" name="duration_services"
                                class="form-control number" placeholder="30" min="5" step="5">
                            </div>

                          </div>
                          <div class="row">
                            <div class="col-md-6 mb-3">
                              <label for="price_usd"
                                class="form-label"><?= $traducciones['profile_specialist_price_usd_label'] ?? 'Service Fee' ?></label>
                              <div class="input-group" id="price-usd-container">
                                <span class="input-group-text">$</span>
                                <input type="text" id="price_usd" name="price_usd" class="form-control number"
                                  placeholder="50.00" data-rules="noVacio|formatoMoneda"
                                  data-error-container="#price-usd-container"
                                  data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                                  data-message-formato-moneda="<?= $traducciones['validation_currency'] ?? 'Please enter a valid price.' ?>">
                              </div>
                              <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="is_free_service">
                                <label class="form-check-label" for="is_free_service">
                                  <?= $traducciones['profile_specialist_free_service'] ?? 'This is a free service' ?>
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="mb-3">
                            <label for="pricing_description"
                              class="form-label"><?= $traducciones['description'] ?? 'Description' ?></label>
                            <textarea id="pricing_description" name="description" class="form-control" rows="3"
                              data-rules="longitudMaxima:500"
                              data-message-longitud-maxima="<?= $traducciones['validation_max_length_500'] ?? 'This field cannot be longer than {max} characters.' ?>"></textarea>
                          </div>
                          <div class="mb-3">
                            <div class="form-check form-switch">
                              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                checked>
                              <label class="form-check-label"
                                for="is_active"><?= $traducciones['status_active'] ?? 'Active' ?></label>
                            </div>
                          </div>
                          <div class="text-end">
                            <button type="button" class="btn btn-cancel"
                              id="cancel-pricing-edit-btn"><?= $traducciones['cancel'] ?? 'Cancel' ?></button>
                            <button type="submit" class="btn btn-save"><?= $traducciones['save'] ?? 'Save' ?></button>
                          </div>
                        </form>
                      </div>
                    </div>

                  </div>
                </div>


              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>

  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="editUserForm" autocomplete="off" data-validation="reactive" novalidate>
          <div class="modal-header">
            <h5 class="modal-title"><?= $traducciones['edit_modal_title_profile'] ?? 'Edit Profile' ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"
              aria-label="<?= $traducciones['close'] ?? 'Close' ?>"></button>
          </div>

          <div class="modal-body">
            <input type="hidden" id="user_id" name="user_id">

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label fw-semibold">
                  <?= $traducciones['first_name_label'] ?? 'First Name' ?>
                </label>
                <input type="text" id="first_name" name="first_name" class="form-control" data-rules="noVacio"
                  data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label fw-semibold">
                  <?= $traducciones['last_name_label'] ?? 'Last Name' ?>
                </label>
                <input type="text" id="last_name" name="last_name" class="form-control" data-rules="noVacio"
                  data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">
                  <?= $traducciones['email_label'] ?? 'Email' ?>
                </label>
                <input type="email" id="email" name="email" class="form-control" data-rules="noVacio|email"
                  data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                  data-message-email="<?= $traducciones['validation_email'] ?? 'Please enter a valid email.' ?>"
                  data-validate-duplicate-url="specialist/check-email" data-record-id-selector="#user_id"
                  data-message-duplicado="<?= $traducciones['validation_email_duplicate'] ?? 'This email is already in use.' ?>">
              </div>
              <div class="col-md-6">
                <label for="birthday" class="form-label fw-semibold">
                  <?= $traducciones['birthday_label'] ?? 'Birthday' ?>
                </label>
                <input type="text" id="birthday" name="birthday" class="form-control" placeholder="MM/DD/YYYY">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="title_id" class="form-label fw-semibold">
                  <?= $traducciones['title_label'] ?? 'Title' ?>
                </label>
                <div id="title_id-container">
                  <select id="title_id" name="title_id" class="form-select" data-rules="noVacio"
                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                    data-error-container="#title_id-container">
                    <option value="">...</option>
                    <?php foreach ($titles as $title): ?>
                      <option value="<?= $title['title_id'] ?>">
                        <?= htmlspecialchars($title[$langField]) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label for="specialty_id" class="form-label fw-semibold">
                  <?= $traducciones['specialty_label'] ?? 'Specialty' ?>
                </label>
                <div id="specialty_id-container">
                  <select id="specialty_id" name="specialty_id" class="form-select" data-rules="noVacio"
                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                    data-error-container="#specialty_id-container">
                    <option value="">...</option>
                    <?php foreach ($specialties as $specialty): ?>
                      <option value="<?= $specialty['specialty_id'] ?>">
                        <?= htmlspecialchars($specialty[$langField]) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">
                <?= $traducciones['metrical_system'] ?? 'Measurement System' ?>
              </label>
              <div class="mt-1">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="system_type" id="heightAmerican" value="us">
                  <label class="form-check-label" for="heightAmerican">
                    <?= $traducciones['height_american'] ?? 'Imperial (lb, in)' ?>
                  </label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="system_type" id="heightEuropean" value="eu">
                  <label class="form-check-label" for="heightEuropean">
                    <?= $traducciones['height_european'] ?? 'Metric (kg, cm)' ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold" for="country-select">
                  <?= $traducciones['signup_country'] ?? 'Country' ?>
                </label>
                <div data-phone-select=""></div>
              </div>
              <div class="col-md-6">
                <label for="telephone" class="form-label fw-semibold">
                  <?= $traducciones['telephone_label'] ?? 'Telephone' ?>
                </label>
                <input type="text" id="telephone" name="telephone" class="form-control" data-rules="longitudMinima:6"
                  data-validate-duplicate-url="specialist/check-telephone" data-validate-masked="true"
                  data-message-longitud-minima="<?= $traducciones['validation_phone_min_length']; ?>"
                  data-message-duplicado="<?= $traducciones['validation_duplicate_phone']; ?>">
              </div>
            </div>

            <hr class="my-3">
            <h5 class="mb-3 text-uppercase bg-light p-2 rounded">
              <i class="mdi mdi-card-account-phone-outline me-1"></i>
              <?= $traducciones['additional_contacts'] ?? 'Additional Contacts' ?>
            </h5>

            <div class="row">
              <div class="mb-3 col-lg-6">
                <label class="form-label fw-bold"><?= $traducciones['emails'] ?? 'Emails' ?></label>
                <div id="email-list" class="vstack gap-2"></div>
                <button type="button" class="btn btn-sm btn-add mt-2" id="btn-add-email">
                  <i class="mdi mdi-plus"></i> <?= $traducciones['add_email'] ?? 'Add Email' ?>
                </button>
              </div>
              <div class="mb-3 col-lg-6">
                <label class="form-label fw-bold"><?= $traducciones['telephones'] ?? 'Telephones' ?></label>
                <div id="telephone-list" class="vstack gap-2"></div>
                <button type="button" class="btn btn-sm btn-add mt-2" id="btn-add-telephone">
                  <i class="mdi mdi-plus"></i> <?= $traducciones['add_telephone'] ?? 'Add Telephone' ?>
                </button>
              </div>
            </div>

            <hr class="my-3">

            <div class="mb-3">
              <label for="bio" class="form-label fw-semibold">
                <?= $traducciones['bio'] ?? 'Biography' ?>
              </label>
              <textarea id="bio" name="bio" class="form-control" rows="3" data-rules="longitudMaxima:1000"
                data-message-longitud-maxima="<?= $traducciones['validation_max_length_100'] ?? 'This field cannot be longer than {max} characters.' ?>"></textarea>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="whatsapp_link" class="form-label fw-semibold">
                  <?= $traducciones['whatsapp_link'] ?? 'WhatsApp' ?>:
                </label>
                <input type="url" id="whatsapp_link" name="whatsapp_link" class="form-control"
                  placeholder="https://wa.me/..." data-rules="esUrlValida"
                  data-message-es-url-valida="<?= $traducciones['validation_url'] ?? 'Please enter a valid URL.' ?>">
              </div>
              <div class="col-md-6">
                <label for="website_url" class="form-label fw-semibold">
                  <?= $traducciones['website_url'] ?? 'Website' ?>:
                </label>
                <input type="url" id="website_url" name="website_url" class="form-control" placeholder="https://..."
                  data-rules="esUrlValida"
                  data-message-es-url-valida="<?= $traducciones['validation_url'] ?? 'Please enter a valid URL.' ?>">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="languages" class="form-label fw-semibold">
                  <?= $traducciones['languages'] ?? 'Languages' ?>
                </label>
                <div id="languages-container">
                  <select id="languages" name="languages[]" class="form-select select2" multiple data-rules="noVacio"
                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                    data-error-container="#languages-container">
                    <option value="en"><?= $traducciones['english'] ?? 'English' ?></option>
                    <option value="es"><?= $traducciones['spanish'] ?? 'Spanish' ?></option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label for="timezoneSelect" class="form-label fw-semibold">
                  <?= $traducciones['timezone_label'] ?? 'Timezone' ?>
                </label>
                <div id="timezone-select-container">
                  <select name="timezone" id="timezoneSelect" class="form-control select2" style="width:100%;"
                    data-rules="noVacio"
                    data-message-no-vacio="<?= $traducciones['validation_required'] ?? 'This field is required.' ?>"
                    data-error-container="#timezone-select-container">
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
              </div>
            </div>

            <div class="row mb-3 align-items-end">
              <div class="col-md-6">
                <label for="max_free_consults_per_month" class="form-label fw-semibold">
                  <?= $traducciones['max_free_consults'] ?? 'Max Free Consults/Month' ?>
                </label>
                <input type="text" id="max_free_consults_per_month" name="max_free_consults_per_month"
                  class="form-control number" min="0" data-rules="esEnteroPositivo"
                  data-message-es-entero-positivo="<?= $traducciones['validation_positive_integer'] ?? 'Value must be a positive integer.' ?>">
              </div>
              <div class="col-md-6">
                <div class="form-check mt-3">
                  <input type="checkbox" class="form-check-input" id="available_for_free_consults"
                    name="available_for_free_consults" value="1">
                  <label class="form-check-label" for="available_for_free_consults">
                    <?= $traducciones['available_for_free_consults'] ?? 'Available for Free Consults' ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="password" class="form-label fw-semibold">
                  <?= $traducciones['password_label'] ?? 'Password' ?>
                </label>
                <div class="input-group">
                  <input type="password" id="password" name="password" class="form-control" autocomplete="new-password"
                    data-rules="longitudMinima:8" data-revalidate-targets="#password_confirmation"
                    data-error-container=".input-group"
                    data-message-longitud-minima="<?= $traducciones['validation_min_length_8'] ?? 'Must be at least 8 characters.' ?>">
                </div>
                <small class="text-muted">
                  <?= $traducciones['password_info'] ?? 'Leave blank to keep current password' ?>
                </small>
              </div>
              <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold">
                  <?= $traducciones['signup_confirm_password'] ?? 'Confirm Password' ?>
                </label>
                <div class="input-group">
                  <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                    autocomplete="new-password" data-rules="coincideCon:#password" data-error-container=".input-group"
                    data-message-coincide-con="<?= $traducciones['validation_password_match'] ?? 'Passwords do not match.' ?>">
                </div>
              </div>
            </div>

            <div class="mb-1">
              <label class="form-label fw-semibold d-block mb-2">
                <?= $traducciones['profile_image'] ?? 'Profile Image' ?>
              </label>

              <ul class="nav nav-pills navtab-bg mb-3" id="profileImageTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="tab-upload" data-bs-toggle="tab" data-bs-target="#pane-upload"
                    type="button" role="tab" aria-controls="pane-upload" aria-selected="true">
                    <i class="mdi mdi-upload me-1"></i> <?= $traducciones['upload_image'] ?? 'Profile Image' ?>
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="tab-avatar" data-bs-toggle="tab" data-bs-target="#pane-avatar"
                    type="button" role="tab" aria-controls="pane-avatar" aria-selected="false">
                    <i class="mdi mdi-account-circle-outline me-1"></i>
                    <?= $traducciones['choose_avatar'] ?? 'Avatar' ?>
                  </button>
                </li>
              </ul>

              <div class="tab-content border border-top-0 p-3 rounded-bottom" id="profileImageTabsContent">
                <div class="tab-pane show active" id="pane-upload" role="tabpanel" aria-labelledby="tab-upload">
                  <input type="file" id="profile_image_input" name="profile_image" class="form-control" accept="image/*"
                    data-rules="esTipoArchivo:image/jpeg,image/png,image/gif|tamanoMaximoArchivo:2"
                    data-message-es-tipo-archivo="<?= $traducciones['validation_file_type'] ?? 'Invalid file type. Only allowed: {types}.' ?>"
                    data-message-tamano-maximo-archivo="<?= $traducciones['validation_file_size'] ?? 'The file is too large. Maximum size is {maxMB} MB.' ?>">

                  <div class="mt-2">
                    <img id="preview_cropper" style="display:none; max-width:100%;">
                  </div>
                  <div class="mt-2" id="cropper-buttons" style="display:none;">
                    <button type="button" id="flipHorizontal" class="btn btn-sm btn-add me-2">
                      <?= trim($traducciones['flip_horizontal'] ?? 'Flip Horizontal') ?>
                    </button>
                    <button type="button" id="flipVertical" class="btn btn-sm btn-add">
                      <?= $traducciones['profile_specialist_flip_vertical_button'] ?? 'Flip Vertical' ?>
                    </button>
                  </div>
                </div>

                <div class="tab-pane" id="pane-avatar" role="tabpanel" aria-labelledby="tab-avatar">
                  <input type="hidden" id="avatar_url" name="avatar_url">
                  <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-sm btn-add mt-2" data-bs-toggle="modal"
                      data-bs-target="#avatarPickerModal">
                      <i class="mdi mdi-image-multiple me-1"></i>
                      <?= $traducciones['choose_avatar'] ?? 'Choose Avatar' ?>
                    </button>
                    <div id="avatarPreviewWrapper" class="d-flex align-items-center">
                      <img id="avatarPreview" alt="Avatar preview"
                        style="display:none; width:64px; height:64px; border-radius:50%; object-fit:cover;">
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">
                    <?= $traducciones['avatar_hint'] ?? 'Selecting an avatar will ignore any uploaded file.' ?>
                  </small>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
              <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?? 'Cancel' ?>
            </button>
            <button type="submit" class="btn btn-save">
              <i class="mdi mdi-content-save-outline"></i> <?= $traducciones['save'] ?? 'Save' ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="avatarPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?= $traducciones['choose_avatar'] ?? 'Choose Avatar' ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"
            aria-label="<?= $traducciones['close'] ?? 'Close' ?>"></button>
        </div>
        <div class="modal-body">
          <style>
            .avatar-grid {
              display: grid;
              grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
              gap: 12px
            }

            .avatar-item {
              border: 2px solid transparent;
              border-radius: 12px;
              padding: 6px;
              cursor: pointer;
              background: #f8f9fa;
              transition: transform .1s ease, border-color .1s ease, box-shadow .1s ease
            }

            .avatar-item:hover {
              transform: translateY(-2px);
              box-shadow: 0 6px 16px rgba(0, 0, 0, .06)
            }

            .avatar-item.selected {
              border-color: #0d6efd;
              background: #eef5ff
            }

            .avatar-item img {
              width: 100%;
              height: 100%;
              object-fit: cover;
              border-radius: 8px
            }
          </style>

          <div class="avatar-grid" id="avatarGrid">
            <?php
            $baseAvatarPath = 'public/assets/images/specialist';
            for ($i = 1; $i <= 9; $i++):
              $src = $baseAvatarPath . '/' . $i . '.png';
              ?>
              <div class="avatar-item" data-src="<?= htmlspecialchars($src) ?>" tabindex="0" role="button"
                aria-label="Avatar <?= $i ?>">
                <img src="<?= htmlspecialchars($src) ?>" alt="Avatar <?= $i ?>">
              </div>
            <?php endfor; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-cancel" data-bs-dismiss="modal">
            <i class="mdi mdi-cancel"></i> <?= $traducciones['cancel'] ?? 'Cancel' ?>
          </button>
          <button class="btn btn-save" id="confirmAvatarBtn" disabled>
            <i class="mdi mdi-check"></i> <?= $traducciones['select'] ?? 'Select' ?>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="certificationViewerModal" tabindex="-1" aria-labelledby="certificationViewerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="certificationViewerModalLabel">
            <?= $traducciones['profile_specialist_certification_document_title'] ?? 'Certification Document' ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <iframe id="certViewerIframe" style="width: 100%; height: 75vh; border: none;" src=""></iframe>
          <img id="certViewerImage" style="max-width: 100%; max-height: 75vh; display: none;" src=""
            alt="Certification Image">
        </div>
      </div>
    </div>
  </div>

  </div>

  <script src="public/assets/js/logout.js"></script>

  <script>
    // Create a global object to hold translations and other page-specific data

    const pageData = {
      userId: '<?= $_SESSION['user_id'] ?? '' ?>',
      baseUrl: '<?= BASE_URL ?? '' ?>',
      userImage: '<?= $_SESSION['user_image'] ?? '' ?>'
    };
  </script>
  <script src="public/assets/js/modules/profile_specialist.js" type="module"></script>

</body>