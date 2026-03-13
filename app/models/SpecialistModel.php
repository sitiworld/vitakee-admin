<?php

require_once __DIR__ . '/../config/Database.php';

class SpecialistModel
{
    private $db;
    private $table = "specialists";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function setIdioma(string $specialistId, string $lang): bool
    {
        try {
            $lang = strtoupper(trim($lang));
            if (!in_array($lang, ['EN', 'ES'])) {
                return false;
            }

            $stmt = $this->db->prepare("UPDATE {$this->table} SET interface_language = ? WHERE specialist_id = ?");
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("ss", $lang, $specialistId);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        } catch (\Exception $e) {
            error_log("Error setIdioma in {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    // Dentro de tu SpecialistModel (o el modelo donde viven estas funciones)
    /**
     * Verifica si existe una imagen para un especialista específico.
     * Busca archivos que coincidan con el patrón "user_{specialistId}.*"
     * dentro del directorio de uploads.
     *
     * @param string $specialistId El ID del especialista a verificar.
     * @return bool True si se encuentra al menos una imagen, false en caso contrario.
     */
    private function specialistImageExists(string $specialistId): bool
    {
        // 1. Define la RUTA del sistema de archivos, no una URL.
        // Usamos $_SERVER['DOCUMENT_ROOT'] para obtener la raíz del sitio.
        $uploadDir = APP_ROOT . '/uploads/specialist/';

        // 2. Creamos un patrón de búsqueda para glob().
        // El asterisco (*) actúa como comodín para cualquier extensión (jpg, png, etc.)
        $pattern = $uploadDir . 'user_' . $specialistId . '.*';

        // 3. Usamos glob() para buscar archivos que coincidan con el patrón.
        // glob() devuelve un array con los archivos encontrados o un array vacío si no hay coincidencias.
        $foundFiles = glob($pattern);

        // 4. Si el array no está vacío, significa que se encontró al menos un archivo.
        return !empty($foundFiles);
    }
    public function getAll()
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langField = $idioma === 'ES' ? 'name_es' : 'name_en';

            require_once __DIR__ . '/SpecialtyModel.php';
            require_once __DIR__ . '/TitleModel.php';

            // Relacionados
            require_once __DIR__ . '/SpecialistVerificationRequestsModel.php';
            require_once __DIR__ . '/SpecialistSocialLinksModel.php';
            require_once __DIR__ . '/SpecialistReviewsModel.php';
            require_once __DIR__ . '/SpecialistPricingModel.php';
            require_once __DIR__ . '/SpecialistLocationsModel.php';
            require_once __DIR__ . '/SpecialistCertificationsModel.php';
            require_once __DIR__ . '/SpecialistAvailabilityModel.php';
            require_once __DIR__ . '/TransactionsModel.php';        // <-- agregado previamente
            require_once __DIR__ . '/ContactEmailModel.php';        // <-- agregado previamente
            require_once __DIR__ . '/ContactPhoneModel.php';        // <-- agregado previamente
            require_once __DIR__ . '/SecondOpinionRequestsModel.php'; // <-- NUEVO (para traer blocks)

            $specialtyModel = new SpecialtyModel();
            $titleModel = new TitleModel();
            $verifModel = new SpecialistVerificationRequestsModel();
            $socialModel = new SpecialistSocialLinksModel();
            $reviewsModel = new SpecialistReviewsModel();
            $pricingModel = new SpecialistPricingModel();
            $locationsModel = new SpecialistLocationsModel();
            $certsModel = new SpecialistCertificationsModel();
            $availabilityModel = new SpecialistAvailabilityModel();
            $transactionsModel = new TransactionsModel();          // <-- agregado previamente
            $emailModel = new ContactEmailModel();          // <-- agregado previamente
            $phoneModel = new ContactPhoneModel();          // <-- agregado previamente
            $secondReqModel = new SecondOpinionRequestsModel(); // <-- NUEVO

            $asList = function ($val): array {
                if (empty($val))
                    return [];
                if (is_array($val)) {
                    $isList = array_keys($val) === range(0, count($val) - 1);
                    return $isList ? $val : [$val];
                }
                return [$val];
            };

            // Edad calculada
            $result = $this->db->query("
            SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
            FROM {$this->table} s
            WHERE s.deleted_at IS NULL
        ");
            if (!$result) {
                throw new \mysqli_sql_exception("Error fetching specialists: " . $this->db->error);
            }

            $specialists = [];
            while ($row = $result->fetch_assoc()) {
                $specId = $row['specialist_id'] ?? null;

                // nombre de especialidad / título según idioma
                $specialty = $specialtyModel->getById($row['specialty_id']);
                $row['specialty_display_name'] = $specialty[$langField] ?? '';

                $title = $titleModel->getById($row['title_id']);
                $row['title_display_name'] = $title[$langField] ?? '';

                // colecciones relacionadas
                $row['verification_requests'] = $asList($specId ? $verifModel->getByIdSpecialist($specId) : []);
                $row['social_links'] = $asList($specId ? $socialModel->getByIdSpecialist($specId) : []);
                $row['reviews'] = $asList($specId ? $reviewsModel->getByIdSpecialist($specId) : []);
                $row['pricing'] = $asList($specId ? $pricingModel->getByIdSpecialist($specId) : []);
                $row['locations'] = $asList($specId ? $locationsModel->getByIdSpecialist($specId) : []);
                $row['certifications'] = $asList($specId ? $certsModel->getByIdSpecialist($specId) : []);
                $row['availability'] = $asList($specId ? $availabilityModel->getByIdSpecialist($specId) : []);
                $row['transactions'] = $asList($specId ? $transactionsModel->getByIdSpecialist($specId) : []);

                // emails y phones
                $row['emails'] = $asList($specId ? $emailModel->getByEntity('specialist', $specId) : []);
                $row['phones'] = $asList($specId ? $phoneModel->getByEntity('specialist', $specId) : []);



                // métricas de reviews
                $row['review_count'] = count($row['reviews']);
                if ($row['review_count'] > 0) {
                    $sum = 0.0;
                    foreach ($row['reviews'] as $rv) {
                        $sum += (float) ($rv['rating'] ?? 0);
                    }
                    $row['average_rating'] = round($sum / $row['review_count'], 2);
                } else {
                    $row['average_rating'] = null;
                }

                // specialist_image
                $row['specialist_image'] = $specId ? (bool) $this->specialistImageExists($specId) : false;

                $specialists[] = $row;
            }

            return $specialists;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function getOneWithRelations(string $specialistId, bool $includeFreePricing): ?array
    {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $langField = $idioma === 'ES' ? 'name_es' : 'name_en';

        require_once __DIR__ . '/SpecialtyModel.php';
        require_once __DIR__ . '/TitleModel.php';
        require_once __DIR__ . '/SpecialistVerificationRequestsModel.php';
        require_once __DIR__ . '/SpecialistSocialLinksModel.php';
        require_once __DIR__ . '/SpecialistReviewsModel.php';
        require_once __DIR__ . '/SpecialistPricingModel.php';
        require_once __DIR__ . '/SpecialistLocationsModel.php';
        require_once __DIR__ . '/SpecialistCertificationsModel.php';
        require_once __DIR__ . '/SpecialistAvailabilityModel.php';
        require_once __DIR__ . '/TransactionsModel.php';
        require_once __DIR__ . '/ContactEmailModel.php';
        require_once __DIR__ . '/ContactPhoneModel.php';

        $specialtyModel = new SpecialtyModel();
        $titleModel = new TitleModel();
        $verifModel = new SpecialistVerificationRequestsModel();
        $socialModel = new SpecialistSocialLinksModel();
        $reviewsModel = new SpecialistReviewsModel();
        $pricingModel = new SpecialistPricingModel();
        $locationsModel = new SpecialistLocationsModel();
        $certsModel = new SpecialistCertificationsModel();
        $availabilityModel = new SpecialistAvailabilityModel();
        $transactionsModel = new TransactionsModel();
        $emailModel = new ContactEmailModel();
        $phoneModel = new ContactPhoneModel();

        $stmt = $this->db->prepare("
        SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
        FROM {$this->table} s
        WHERE s.deleted_at IS NULL AND s.specialist_id = ?
        LIMIT 1
    ");
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if (!$row)
            return null;

        $asList = function ($val): array {
            if (empty($val))
                return [];
            if (is_array($val)) {
                $isList = array_keys($val) === range(0, count($val) - 1);
                return $isList ? $val : [$val];
            }
            return [$val];
        };

        // Nombres traducidos
        $specialty = $specialtyModel->getById($row['specialty_id']);
        $row['specialty_display_name'] = $specialty[$langField] ?? '';

        $title = $titleModel->getById($row['title_id']);
        $row['title_display_name'] = $title[$langField] ?? '';

        // Relaciones
        $row['verification_requests'] = $asList($verifModel->getByIdSpecialist($specialistId));
        $row['social_links'] = $asList($socialModel->getByIdSpecialist($specialistId));
        $row['reviews'] = $asList($reviewsModel->getByIdSpecialist($specialistId));
        $row['locations'] = $asList($locationsModel->getByIdSpecialist($specialistId));
        $row['certifications'] = $asList($certsModel->getByIdSpecialist($specialistId));
        $row['availability'] = $asList($availabilityModel->getByIdSpecialist($specialistId));
        $row['transactions'] = $asList($transactionsModel->getByIdSpecialist($specialistId));
        $row['emails'] = $asList($emailModel->getByEntity('specialist', $specialistId));
        $row['phones'] = $asList($phoneModel->getByEntity('specialist', $specialistId));



        // Pricing con filtro dinámico de GRATIS
        $pricingAll = $asList($pricingModel->getByIdSpecialist($specialistId));
        if ($includeFreePricing) {
            $row['pricing'] = $pricingAll;
        } else {
            // Filtramos filas cuyo costo sea 0 (revisamos varias posibles columnas)
            $row['pricing'] = array_values(array_filter($pricingAll, function ($p) {
                $candidates = ['cost_request', 'cost', 'price_usd', 'base_cost', 'amount'];
                foreach ($candidates as $k) {
                    if (array_key_exists($k, $p)) {
                        return (float) $p[$k] > 0;
                    }
                }
                // Si no hay columna de costo identificable, conservamos la fila (o exclúyela si prefieres)
                return true;
            }));
        }

        // Métricas de reviews
        $row['review_count'] = count($row['reviews']);
        $row['average_rating'] = $row['review_count'] > 0
            ? round(array_sum(array_map(fn($r) => (float) ($r['rating'] ?? 0), $row['reviews'])) / $row['review_count'], 2)
            : null;

        // Imagen
        $row['specialist_image'] = (bool) $this->specialistImageExists($specialistId);



        return $row;
    }

    public function getAllForCards(): array
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $titleCol = $idioma === 'ES' ? 't.name_es' : 't.name_en'; // @Therapist / @Terapeuta

            $sql = "
        SELECT
            s.specialist_id,
            CONCAT(s.first_name, ' ', s.last_name)        AS full_name,
            $titleCol                                     AS title_display,
            s.website_url,
            s.avatar_url,

            COALESCE(SUM(CASE WHEN tr.type_request = 'VERIFICATION' AND tr.status = 'COMPLETED' THEN 1 ELSE 0 END), 0) AS lab_reports_evaluated,
            COALESCE(SUM(CASE WHEN tr.type_request = 'CONSULTATION' AND tr.status = 'COMPLETED' THEN 1 ELSE 0 END), 0) AS consultations_completed,
            ROUND(AVG(NULLIF(sr.rating, 0)), 2)                                                 AS avg_rating,
            COUNT(sr.review_id)                                                                 AS reviews_count
        FROM specialists s
        LEFT JOIN specialists_titles t
               ON t.title_id = s.title_id
        LEFT JOIN transactions tr
               ON tr.specialist_id = s.specialist_id
              AND tr.deleted_at IS NULL
        LEFT JOIN specialist_reviews sr
               ON sr.specialist_id = s.specialist_id
              AND sr.deleted_at IS NULL
        WHERE s.deleted_at IS NULL
        GROUP BY s.specialist_id, full_name, title_display, s.website_url, s.avatar_url
        ORDER BY s.created_at DESC
        ";

            $res = $this->db->query($sql);
            if (!$res) {
                throw new \mysqli_sql_exception("Query error: " . $this->db->error);
            }

            $cards = [];
            while ($r = $res->fetch_assoc()) {
                $handle = $r['title_display'] ? '@' . $r['title_display'] : null;
                $avgRating = $r['avg_rating'] !== null ? (float) $r['avg_rating'] : null;
                $ratingText = $avgRating !== null ? ($avgRating . '/5') : null;

                $specId = $r['specialist_id'];
                $hasImg = $specId ? (bool) $this->specialistImageExists($specId) : false;

                $cards[] = [
                    'specialist_id' => $specId,
                    'full_name' => $r['full_name'],
                    'handle' => $handle,
                    'website_url' => $r['website_url'],
                    'avatar_url' => $r['avatar_url'],
                    'lab_reports_evaluated' => (int) $r['lab_reports_evaluated'],
                    'consultations_completed' => (int) $r['consultations_completed'],
                    'avg_rating' => $avgRating,
                    'rating_text' => $ratingText,
                    'reviews_count' => (int) $r['reviews_count'],
                    'specialist_image' => $hasImg, // <-- agregado
                ];
            }

            return $cards;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }
    public function getCardById(string $specialistId): ?array
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $titleCol = $idioma === 'ES' ? 't.name_es' : 't.name_en';

            // ✅ Detecta la PK de second_opinion_requests si tienes el helper;
            // si no, usa un fallback robusto que minimiza duplicados.
            $sorPk = method_exists($this, 'getSorPkColumn') ? $this->getSorPkColumn() : '';
            $sorPkExpr = $sorPk !== ''
                ? "sor.`$sorPk`"
                : "CONCAT_WS('|', sor.specialist_id, sor.type_request, DATE(sor.created_at), sor.user_id)";

            $sql = "
            SELECT
                s.specialist_id,
                CONCAT(s.first_name, ' ', s.last_name) AS full_name,
                $titleCol                              AS title_display,
                s.website_url,
                s.avatar_url,

                /* ✅ KPIs desde second_opinion_requests (solo COMPLETED) y sin duplicados */
                COUNT(DISTINCT CASE 
                    WHEN sor.type_request = 'document_review' THEN $sorPkExpr
                END) AS lab_reports_evaluated,

                COUNT(DISTINCT CASE 
                    WHEN sor.type_request = 'appointment_request' THEN $sorPkExpr
                END) AS consultations_completed,

                /* ✅ Evitar sesgo por duplicados de JOINs en reviews */
                ROUND(AVG(DISTINCT NULLIF(sr.rating, 0)), 2) AS avg_rating,
                COUNT(DISTINCT sr.review_id)                 AS reviews_count

            FROM specialists s
            LEFT JOIN specialists_titles t
                ON t.title_id = s.title_id
            LEFT JOIN second_opinion_requests sor
                ON sor.specialist_id = s.specialist_id
               AND sor.deleted_at IS NULL
               AND sor.status = 'COMPLETED'
            LEFT JOIN specialist_reviews sr
                ON sr.specialist_id = s.specialist_id
               AND sr.deleted_at IS NULL

            WHERE s.deleted_at IS NULL
              AND s.specialist_id = ?

            GROUP BY 
                s.specialist_id, full_name, title_display, s.website_url, s.avatar_url
            LIMIT 1
        ";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("s", $specialistId);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $handle = $row['title_display'] ? '@' . $row['title_display'] : null;
                $avgRating = $row['avg_rating'] !== null ? (float) $row['avg_rating'] : null;
                $ratingText = $avgRating !== null ? ($avgRating . '/5') : null;
                $hasImg = $this->specialistImageExists($row['specialist_id']);

                return [
                    'specialist_id' => $row['specialist_id'],
                    'full_name' => $row['full_name'],
                    'handle' => $handle,
                    'website_url' => $row['website_url'],
                    'avatar_url' => $row['avatar_url'],
                    'lab_reports_evaluated' => (int) $row['lab_reports_evaluated'],
                    'consultations_completed' => (int) $row['consultations_completed'],
                    'avg_rating' => $avgRating,
                    'rating_text' => $ratingText,
                    'reviews_count' => (int) $row['reviews_count'],
                    'specialist_image' => $hasImg,
                ];
            }

            return null;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function getById($id)
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langField = $idioma === 'ES' ? 'name_es' : 'name_en';

            require_once __DIR__ . '/SpecialtyModel.php';
            require_once __DIR__ . '/TitleModel.php';

            // Relacionados
            require_once __DIR__ . '/SpecialistVerificationRequestsModel.php';
            require_once __DIR__ . '/SpecialistSocialLinksModel.php';
            require_once __DIR__ . '/SpecialistReviewsModel.php';
            require_once __DIR__ . '/SpecialistPricingModel.php';
            require_once __DIR__ . '/SpecialistLocationsModel.php';
            require_once __DIR__ . '/SpecialistCertificationsModel.php';
            require_once __DIR__ . '/SpecialistAvailabilityModel.php';
            require_once __DIR__ . '/TransactionsModel.php';
            require_once __DIR__ . '/ContactEmailModel.php';
            require_once __DIR__ . '/ContactPhoneModel.php';
            require_once __DIR__ . '/SecondOpinionRequestsModel.php';

            $specialtyModel = new SpecialtyModel();
            $titleModel = new TitleModel();
            $verifModel = new SpecialistVerificationRequestsModel();
            $socialModel = new SpecialistSocialLinksModel();
            $reviewsModel = new SpecialistReviewsModel();
            $pricingModel = new SpecialistPricingModel();
            $locationsModel = new SpecialistLocationsModel();
            $certsModel = new SpecialistCertificationsModel();
            $availabilityModel = new SpecialistAvailabilityModel();
            $transactionsModel = new TransactionsModel();
            $emailModel = new ContactEmailModel();
            $phoneModel = new ContactPhoneModel();
            $secondReqModel = new SecondOpinionRequestsModel();

            $asList = function ($val): array {
                if (empty($val))
                    return [];
                if (is_array($val)) {
                    $isList = array_keys($val) === range(0, count($val) - 1);
                    return $isList ? $val : [$val];
                }
                return [$val];
            };

            // Edad calculada
            $stmt = $this->db->prepare("
            SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
            FROM {$this->table} s
            WHERE s.specialist_id = ? AND s.deleted_at IS NULL
        ");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $specialist = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($specialist) {
                $specId = $specialist['specialist_id'] ?? null;

                // nombres visuales
                $specialty = $specialtyModel->getById($specialist['specialty_id']);
                $specialist['specialty_display_name'] = $specialty[$langField] ?? '';

                $title = $titleModel->getById($specialist['title_id']);
                $specialist['title_display_name'] = $title[$langField] ?? '';

                // relacionados
                $specialist['verification_requests'] = $asList($specId ? $verifModel->getByIdSpecialist($specId) : []);
                $specialist['social_links'] = $asList($specId ? $socialModel->getByIdSpecialist($specId) : []);
                $specialist['reviews'] = $asList($specId ? $reviewsModel->getByIdSpecialist($specId) : []);
                $specialist['languages'] = json_decode($specialist['languages'] ?? '[]', true) ?: [];
                $specialist['pricing'] = $asList($specId ? $pricingModel->getByIdSpecialist($specId) : []);
                $specialist['locations'] = $asList($specId ? $locationsModel->getByIdSpecialist($specId) : []);
                $specialist['certifications'] = $asList($specId ? $certsModel->getByIdSpecialist($specId) : []);
                $specialist['availability'] = $asList($specId ? $availabilityModel->getByIdSpecialist($specId) : []);
                $specialist['other_requests'] = $asList($specId ? $secondReqModel->getSimpleRequestDataBySpecialistId($specId) : []);




                // === blocks (second-opinion type_request_request=block) ===
                $allRequestsForSpec = $specId ? $secondReqModel->getBlockRequestForSpecialist($specId) : [];
                $specialist['blocks'] = $asList($allRequestsForSpec);

                // === emails y phones relacionados ===
                $specialist['emails'] = $asList($specId ? $emailModel->getByEntity('specialist', $specId) : []);
                $specialist['phones'] = $asList($specId ? $phoneModel->getByEntity('specialist', $specId) : []);

                // métricas de reviews
                $specialist['review_count'] = count($specialist['reviews']);
                if ($specialist['review_count'] > 0) {
                    $sum = 0.0;
                    foreach ($specialist['reviews'] as $rv) {
                        $sum += (float) ($rv['rating'] ?? 0);
                    }
                    $specialist['average_rating'] = round($sum / $specialist['review_count'], 2);
                } else {
                    $specialist['average_rating'] = null;
                }

                // imagen
                $specialist['specialist_image'] = $specId ? (bool) $this->specialistImageExists($specId) : false;
            }

            return $specialist;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function getTimezoneSpecialistById($specialistId)
    {
        $stmt = $this->db->prepare("SELECT timezone FROM {$this->table} WHERE specialist_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
        }
        $stmt->bind_param("s", $specialistId);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($row = $res->fetch_assoc()) {
            return $row['timezone'] ?? null;
        }
        return null;
    }


    public function showByIdSecondOpinion($id)
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langField = $idioma === 'ES' ? 'name_es' : 'name_en';

            require_once __DIR__ . '/SpecialtyModel.php';
            require_once __DIR__ . '/TitleModel.php';

            // Relacionados
            require_once __DIR__ . '/SpecialistVerificationRequestsModel.php';
            require_once __DIR__ . '/SpecialistSocialLinksModel.php';
            require_once __DIR__ . '/SpecialistReviewsModel.php';
            require_once __DIR__ . '/SpecialistPricingModel.php';
            require_once __DIR__ . '/SpecialistLocationsModel.php';
            require_once __DIR__ . '/SpecialistCertificationsModel.php';
            require_once __DIR__ . '/SpecialistAvailabilityModel.php';
            require_once __DIR__ . '/TransactionsModel.php';     // opcional
            require_once __DIR__ . '/ContactEmailModel.php';
            require_once __DIR__ . '/ContactPhoneModel.php';
            require_once __DIR__ . '/SecondOpinionRequestsModel.php';

            $specialtyModel = new SpecialtyModel();
            $titleModel = new TitleModel();
            $verifModel = new SpecialistVerificationRequestsModel();
            $socialModel = new SpecialistSocialLinksModel();
            $reviewsModel = new SpecialistReviewsModel();
            $pricingModel = new SpecialistPricingModel();
            $locationsModel = new SpecialistLocationsModel();
            $certsModel = new SpecialistCertificationsModel();
            $availabilityModel = new SpecialistAvailabilityModel();
            $transactionsModel = new TransactionsModel();
            $emailModel = new ContactEmailModel();
            $phoneModel = new ContactPhoneModel();
            $secondReqModel = new SecondOpinionRequestsModel();

            $asList = function ($val): array {
                if (empty($val))
                    return [];
                if (is_array($val)) {
                    $isList = array_keys($val) === range(0, count($val) - 1);
                    return $isList ? $val : [$val];
                }
                return [$val];
            };

            // ====== Cargar especialista con edad calculada ======
            $stmt = $this->db->prepare("
            SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
            FROM {$this->table} s
            WHERE s.specialist_id = ? AND s.deleted_at IS NULL
        ");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $specialist = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($specialist) {
                $specId = $specialist['specialist_id'] ?? null;

                // ====== Datos descriptivos ======
                $specialty = $specialtyModel->getById($specialist['specialty_id']);
                $specialist['specialty_display_name'] = $specialty[$langField] ?? '';

                $title = $titleModel->getById($specialist['title_id']);
                $specialist['title_display_name'] = $title[$langField] ?? '';

                $specialist['verification_requests'] = $asList($specId ? $verifModel->getByIdSpecialist($specId) : []);
                $specialist['social_links'] = $asList($specId ? $socialModel->getByIdSpecialist($specId) : []);
                $specialist['reviews'] = $asList($specId ? $reviewsModel->getByIdSpecialist($specId) : []);
                $specialist['languages'] = json_decode($specialist['languages'] ?? '[]', true) ?: [];

                $specialist['locations'] = $asList($specId ? $locationsModel->getByIdSpecialist($specId) : []);
                $specialist['certifications'] = $asList($specId ? $certsModel->getByIdSpecialist($specId) : []);
                $specialist['availability'] = $asList($specId ? $availabilityModel->getByIdSpecialist($specId) : []);

                // ====== Convertir disponibilidad a zona del usuario (si aplica) ======
                $specialistTimezone = $specialist['timezone'] ?? null;
                $userTimezone = $_SESSION['timezone'] ?? 'UTC';
                if ($specialistTimezone && $userTimezone && $specialistTimezone !== $userTimezone) {
                    foreach ($specialist['availability'] as &$avail) {
                        try {
                            $startTimeObj = new \DateTime("today {$avail['start_time']}", new \DateTimeZone($specialistTimezone));
                            $endTimeObj = new \DateTime("today {$avail['end_time']}", new \DateTimeZone($specialistTimezone));
                            $startTimeObj->setTimezone(new \DateTimeZone($userTimezone));
                            $endTimeObj->setTimezone(new \DateTimeZone($userTimezone));

                            $avail['user_start_time'] = $startTimeObj->format('H:i:s');
                            $avail['user_end_time'] = $endTimeObj->format('H:i:s');
                            $avail['user_timezone'] = $userTimezone;
                        } catch (\Exception $e) {
                            $avail['user_start_time'] = null;
                            $avail['user_end_time'] = null;
                            $avail['user_timezone'] = 'Translation Error';
                        }
                    }
                    unset($avail);
                }

                // ====== Pricing base ======
                $specialist['pricing'] = $asList($specId ? $pricingModel->getByIdSpecialist($specId) : []);

                

                // Normalizar service_type por si el modelo usa otro nombre (pricing_type / type_request / type)
                if (!empty($specialist['pricing'])) {
                    foreach ($specialist['pricing'] as &$p) {
                        if (!isset($p['service_type']) || $p['service_type'] === '' || $p['service_type'] === null) {
                            $p['service_type'] = $p['service_type']
                                ?? ($p['pricing_type'] ?? $p['type_request'] ?? $p['type'] ?? null);
                        }
                        if (is_string($p['service_type'])) {
                            $p['service_type'] = strtolower(trim($p['service_type']));
                        }
                    }
                    unset($p);
                }

                // Helper para obtener precio consistente con tu esquema (price_usd)
                $getPrice = static function (array $p): float {
                    return (float) ($p['price_usd'] ?? $p['price'] ?? $p['amount'] ?? 0);
                };

                // ====== Remover FOLLOW_UP si no existe una consulta base ======
                $hasConsultation = false;
                foreach ($specialist['pricing'] as $p) {
                    if (($p['service_type'] ?? '') === 'appointment_request') {
                        $hasConsultation = true;
                        break;
                    }
                }


                if (!$hasConsultation) {
                    $specialist['pricing'] = array_values(array_filter(
                        $specialist['pricing'],
                        fn($p) => ($p['service_type'] ?? '') !== 'FOLLOW_UP'
                    ));
                }

                // ====== Reglas para servicios GRATIS (price_usd = 0) con cupo mensual ======
                $offersFree = (int) ($specialist['available_for_free_consults'] ?? 0) === 1;
                $maxFree = (int) ($specialist['max_free_consults_per_month'] ?? 0);

                $monthStart = (new \DateTime('first day of this month 00:00:00'))->format('Y-m-d H:i:s');
                $monthEnd = (new \DateTime('last day of this month 23:59:59'))->format('Y-m-d H:i:s');

                if (!$offersFree || $maxFree <= 0) {
                    // Si no ofrece o no tiene cupo, ocultar todos los precios 0
                    $specialist['pricing'] = array_values(array_filter(
                        $specialist['pricing'],
                        fn($p) => $getPrice($p) > 0
                    ));
                } else {
                    // Tipos de servicio con precio == 0
                    $freeServices = array_values(array_filter(
                        $specialist['pricing'],
                        fn($p) => $getPrice($p) == 0.0
                    ));
                    $serviceTypes = array_unique(array_map(
                        fn($p) => strtolower($p['service_type'] ?? ''),
                        $freeServices
                    ));
                    $serviceTypes = array_values(array_filter($serviceTypes, fn($x) => $x !== ''));

                    // Contar consultas del mes actual por tipo (completed, pending, awaiting_payment)
                    // IMPORTANTE: transactions NO tiene service_type -> JOIN con specialist_pricing por pricing_id
// Contar consultas del mes actual por tipo (completed, pending, awaiting_payment)
// Nota: 'service_type' está en specialist_pricing, no en transactions → usamos JOIN
                    $allowedStatuses = ['completed', 'pending', 'awaiting_payment'];
                    $countsByType = [];

                    if (!empty($serviceTypes)) {
                        $sqlCnt = "
        SELECT sp.service_type AS stype, COUNT(*) AS cnt
        FROM transactions t
        JOIN specialist_pricing sp ON sp.pricing_id = t.pricing_id
        WHERE t.specialist_id = ?
          AND sp.service_type = ?
          AND t.status IN (?, ?, ?)
          AND t.created_at BETWEEN ? AND ?
          AND t.deleted_at IS NULL
          AND sp.deleted_at IS NULL
    ";

                        $stmtCnt = $this->db->prepare($sqlCnt);
                        if ($stmtCnt) {
                            foreach ($serviceTypes as $stype) {
                                $stmtCnt->bind_param(
                                    'sssssss',   // 7 parámetros: specId, stype, 3 estados, fecha inicio, fecha fin
                                    $specId,
                                    $stype,
                                    $allowedStatuses[0],
                                    $allowedStatuses[1],
                                    $allowedStatuses[2],
                                    $monthStart,
                                    $monthEnd
                                );
                                $stmtCnt->execute();
                                $row = $stmtCnt->get_result()->fetch_assoc();
                                $countsByType[strtolower($stype)] = (int) ($row['cnt'] ?? 0);
                            }
                            $stmtCnt->close();
                        } else {
                            // fallback: asumimos 0 si falla el prepare
                            foreach ($serviceTypes as $stype) {
                                $countsByType[strtolower($stype)] = 0;
                            }
                        }
                    }


                    // Filtrar: mostrar gratis solo si usados < maxFree
                    $specialist['pricing'] = array_values(array_filter(
                        $specialist['pricing'],
                        function ($p) use ($countsByType, $maxFree, $getPrice) {
                            $price = $getPrice($p);
                            if ($price > 0)
                                return true; // servicios pagos siempre visibles
                            $stype = strtolower($p['service_type'] ?? '');
                            $used = (int) ($countsByType[$stype] ?? 0);
                            return $used < $maxFree;
                        }
                    ));
                }

                // ====== emails y phones ======
                $specialist['emails'] = $asList($specId ? $emailModel->getByEntity('specialist', $specId) : []);
                $specialist['phones'] = $asList($specId ? $phoneModel->getByEntity('specialist', $specId) : []);

                // ====== rating ======
                $specialist['review_count'] = count($specialist['reviews']);
                if ($specialist['review_count'] > 0) {
                    $sum = 0.0;
                    foreach ($specialist['reviews'] as $rv) {
                        $sum += (float) ($rv['rating'] ?? 0);
                    }
                    $specialist['average_rating'] = round($sum / $specialist['review_count'], 2);
                } else {
                    $specialist['average_rating'] = null;
                }

                // ====== specialist_image ======
                $specialist['specialist_image'] = $specId ? (bool) $this->specialistImageExists($specId) : false;
            }

            return $specialist;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }





    public function searchByName(string $q, array $opts = []): array
    {
        $q = trim($q ?? '');
        if ($q === '') {
            return [];
        }

        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $titleCol = $idioma === 'ES' ? 't.name_es' : 't.name_en';

        $params = [];
        $types = "";

        $joins = [];
        $joins[] = "LEFT JOIN specialists_titles t ON t.title_id = s.title_id";
        // ⬇️ Reemplazamos transactions por second_opinion_requests (sor)
        $joins[] = "LEFT JOIN second_opinion_requests sor 
                ON sor.specialist_id = s.specialist_id 
               AND sor.deleted_at IS NULL 
               AND sor.status = 'COMPLETED'";
        $joins[] = "LEFT JOIN specialist_reviews sr 
                ON sr.specialist_id = s.specialist_id 
               AND sr.deleted_at IS NULL";
        $joins[] = "LEFT JOIN specialist_pricing sp 
                ON sp.specialist_id = s.specialist_id 
               AND sp.deleted_at IS NULL 
               AND sp.service_type = 'CONSULTATION'";

        $wheres = ["s.deleted_at IS NULL"];

        // Búsqueda por nombre completo
        $tokens = array_values(array_filter(preg_split('/\s+/', $q), fn($t) => $t !== ''));
        $likeParts = [];

        $likeParts[] = "CONCAT(s.first_name, ' ', s.last_name) LIKE ?";
        $params[] = '%' . $q . '%';
        $types .= 's';

        $likeParts[] = "CONCAT(s.last_name, ' ', s.first_name) LIKE ?";
        $params[] = '%' . $q . '%';
        $types .= 's';

        foreach ($tokens as $tk) {
            $likeParts[] = "CONCAT_WS(' ', s.first_name, s.last_name) LIKE ?";
            $params[] = '%' . $tk . '%';
            $types .= 's';
        }

        $wheres[] = '(' . implode(' AND ', $likeParts) . ')';
        $innerSql = "
    SELECT
        s.specialist_id,
        CONCAT(s.first_name, ' ', s.last_name) AS full_name,
        $titleCol                              AS title_display,
        s.website_url,
        s.avatar_url,

        /* ✅ KPIs solo COMPLETED, sin duplicados por otros JOINs */
        COUNT(DISTINCT CASE 
            WHEN sor.status = 'COMPLETED' AND sor.type_request = 'document_review' 
            THEN sor.second_opinion_id  
        END) AS lab_reports_evaluated,

        COUNT(DISTINCT CASE 
            WHEN sor.status = 'COMPLETED' AND sor.type_request = 'appointment_request' 
            THEN sor.second_opinion_id  
        END) AS consultations_completed,

        /* ✅ Evita sesgo por duplicados de filas */
        ROUND(AVG(DISTINCT NULLIF(sr.rating, 0)), 2) AS avg_rating,
        COUNT(DISTINCT sr.review_id)                 AS reviews_count,

        MIN(sp.price_usd) AS min_consult_price_for_filter,
        MAX(s.created_at) AS created_at_max
    FROM specialists s
    " . implode("\n", $joins) . "
    WHERE " . implode(" AND ", $wheres) . "
    GROUP BY 
        s.specialist_id, 
        full_name, 
        title_display, 
        s.website_url, 
        s.avatar_url
";



        $sql = "SELECT * FROM ( $innerSql ) AS q WHERE 1=1";

        $order = $opts['order'] ?? null;
        if ($order === 'rating_cost') {
            $sql .= " ORDER BY (q.avg_rating IS NULL), q.avg_rating DESC,
                          (q.min_consult_price_for_filter IS NULL), q.min_consult_price_for_filter ASC";
        } else {
            $sql .= " ORDER BY q.created_at_max DESC";
        }

        $useLimit = isset($opts['limit']) && is_numeric($opts['limit']) && (int) $opts['limit'] > 0;
        $useOffset = isset($opts['offset']) && is_numeric($opts['offset']) && (int) $opts['offset'] >= 0;
        if ($useLimit) {
            $sql .= " LIMIT ?";
            $params[] = (int) $opts['limit'];
            $types .= "i";
            if ($useOffset) {
                $sql .= " OFFSET ?";
                $params[] = (int) $opts['offset'];
                $types .= "i";
            }
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();

        $cards = [];
        while ($r = $res->fetch_assoc()) {
            $handle = $r['title_display'] ? '@' . $r['title_display'] : null;
            $avgRating = $r['avg_rating'] !== null ? (float) $r['avg_rating'] : null;
            $ratingText = $avgRating !== null ? ($avgRating . '/5') : null;

            $specId = $r['specialist_id'];
            $hasImg = $specId ? (bool) $this->specialistImageExists($specId) : false;

            $cards[] = [
                'specialist_id' => $specId,
                'full_name' => $r['full_name'],
                'handle' => $handle,
                'website_url' => $r['website_url'],
                'avatar_url' => $r['avatar_url'],
                'lab_reports_evaluated' => (int) $r['lab_reports_evaluated'],
                'consultations_completed' => (int) $r['consultations_completed'],
                'avg_rating' => $avgRating,
                'rating_text' => $ratingText,
                'reviews_count' => (int) $r['reviews_count'],
                'specialist_image' => $hasImg,
            ];
        }
        $stmt->close();

        return $cards;
    }

public function searchByFilters(array $f = []): array
{
    $f = is_array($f) ? $f : [];

    $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
    $titleCol = $idioma === 'ES' ? 't.name_es' : 't.name_en';
    $specialtyCol = $idioma === 'ES' ? 'spc.name_es' : 'spc.name_en';

    $joins = [];
    $wheres = ["s.deleted_at IS NULL"];
    $params = [];
    $types = "";

    /* ======== JOINS base ======== */
    $joins[] = "LEFT JOIN specialists_titles t
           ON t.title_id = s.title_id";
    $joins[] = "LEFT JOIN specialty spc
           ON spc.specialty_id = s.specialty_id";
    // Solo COMPLETED para no contar otros estados
    $joins[] = "LEFT JOIN second_opinion_requests sor
           ON sor.specialist_id = s.specialist_id
          AND sor.deleted_at IS NULL
          AND sor.status = 'COMPLETED'";
    $joins[] = "LEFT JOIN specialist_reviews sr
           ON sr.specialist_id = s.specialist_id
          AND sr.deleted_at IS NULL";
    $joins[] = "LEFT JOIN specialist_pricing sp
           ON sp.specialist_id = s.specialist_id
          AND sp.deleted_at IS NULL
          AND sp.service_type = 'CONSULTATION'";

    /* ======== Filtro por nombre (opcional) ======== */
    $q = trim((string) ($f['q'] ?? $f['name'] ?? ''));
    if ($q !== '') {
        $tokens = array_values(array_filter(preg_split('/\s+/', $q) ?: [], fn($t) => $t !== ''));
        $likeOrs = [];

        $likeOrs[] = "CONCAT(s.first_name, ' ', s.last_name) LIKE ?";
        $params[] = '%' . $q . '%';  $types .= 's';

        $likeOrs[] = "CONCAT(s.last_name, ' ', s.first_name) LIKE ?";
        $params[] = '%' . $q . '%';  $types .= 's';

        foreach ($tokens as $tk) {
            $likeOrs[] = "CONCAT_WS(' ', s.first_name, s.last_name) LIKE ?";
            $params[] = '%' . $tk . '%';  $types .= 's';
        }
        $wheres[] = '(' . implode(' OR ', $likeOrs) . ')';
    }

    /* ======== Filtros opcionales ======== */
    // verified
    if (array_key_exists('verified', $f)) {
        $joins[] = "LEFT JOIN specialist_verification_requests svr
               ON svr.specialist_id = s.specialist_id
              AND svr.deleted_at IS NULL";
        if (!empty($f['verified'])) {
            $wheres[] = "(s.verified_status = 'VERIFIED' OR svr.status = 'APPROVED')";
        } else {
            $wheres[] = "(s.verified_status <> 'VERIFIED' AND (svr.status IS NULL OR svr.status <> 'APPROVED'))";
        }
    }

    // specialty_ids (array de UUIDs)
    if (!empty($f['specialty_ids']) && is_array($f['specialty_ids'])) {
        $ids = array_values(array_filter($f['specialty_ids'], fn($x) => is_string($x) && $x !== ''));
        if ($ids) {
            $wheres[] = "s.specialty_id IN (" . implode(',', array_fill(0, count($ids), '?')) . ")";
            foreach ($ids as $id) { $params[] = $id; $types .= "s"; }
        }
    }

    // languages (array de códigos)
    if (!empty($f['languages']) && is_array($f['languages'])) {
        $langs = array_values(array_filter($f['languages'], fn($x) => is_string($x) && $x !== ''));
        if ($langs) {
            $parts = [];
            foreach ($langs as $lg) { $parts[] = "JSON_CONTAINS(s.languages, JSON_QUOTE(?))"; $params[] = $lg; $types .= "s"; }
            $wheres[] = "(" . implode(" OR ", $parts) . ")";
        }
    }

    // availability (date | weekdays[], time_start/time_end)
    if (!empty($f['availability']) && is_array($f['availability'])) {
        $av = $f['availability'];
        $joins[] = "JOIN specialist_availability sa
               ON sa.specialist_id = s.specialist_id
              AND sa.deleted_at IS NULL";

        $weekdayConds = [];
        $date = $av['date'] ?? null;
        if ($date) {
            $phpDow = date('l', strtotime($date)); // Monday, Tuesday, ...
            $weekdayConds[] = "sa.weekday = ?";
            $params[] = $phpDow;  $types .= "s";
        } elseif (!empty($av['weekdays']) && is_array($av['weekdays'])) {
            $wds = array_values(array_filter($av['weekdays'], fn($x) => is_string($x) && $x !== ''));
            if ($wds) {
                $weekdayConds[] = "sa.weekday IN (" . implode(",", array_fill(0, count($wds), "?")) . ")";
                foreach ($wds as $wd) { $params[] = $wd; $types .= "s"; }
            }
        }
        if ($weekdayConds) {
            $wheres[] = "(" . implode(" OR ", $weekdayConds) . ")";
        }

        $start = $av['time_start'] ?? null;
        $end   = $av['time_end'] ?? null;
        if ($start && $end) {
            $wheres[] = "(sa.start_time <= ? AND sa.end_time >= ?)";
            $params[] = $end;   $types .= "s";
            $params[] = $start; $types .= "s";
        } elseif ($start) {
            $wheres[] = "(sa.end_time >= ?)";
            $params[] = $start; $types .= "s";
        } elseif ($end) {
            $wheres[] = "(sa.start_time <= ?)";
            $params[] = $end;   $types .= "s";
        }
    }

    /* ======== Subquery con agregados (sin duplicados) ======== */
    $innerSql = "
SELECT
    s.specialist_id,
    s.specialty_id,
    CONCAT(s.first_name, ' ', s.last_name) AS full_name,
    $titleCol       AS title_display,
    $specialtyCol   AS specialty_display,
    s.website_url,
    s.avatar_url,

    /* KPIs desde second_opinion_requests (solo COMPLETED) y sin duplicados */
    COUNT(DISTINCT CASE WHEN sor.type_request = 'document_review'    THEN sor.second_opinion_id END) AS lab_reports_evaluated,
    COUNT(DISTINCT CASE WHEN sor.type_request = 'appointment_request' THEN sor.second_opinion_id END) AS consultations_completed,

    /* Evitar sesgo por duplicados de JOINs */
    ROUND(AVG(DISTINCT NULLIF(sr.rating, 0)), 2) AS avg_rating,
    COUNT(DISTINCT sr.review_id)                 AS reviews_count,

    MIN(sp.price_usd) AS min_consult_price_for_filter,
    MAX(s.created_at) AS created_at_max
FROM specialists s
" . implode("\n", $joins) . "
WHERE " . implode(" AND ", $wheres) . "
GROUP BY
    s.specialist_id,
    s.specialty_id,
    full_name,
    title_display,
    specialty_display,
    s.website_url,
    s.avatar_url
";

    $sql = "SELECT * FROM ( $innerSql ) AS q WHERE 1=1";

    /* ======== Filtros post-aggregado ======== */
    $minCost = $f['min_cost'] ?? null;

    /**
     * SOLO min_rating:
     * - Si es entero 1..5 => bucket:
     *   1 → [1.00,2.00) ; 2 → [2.00,3.00) ; 3 → [3.00,4.00) ; 4 → [4.00,5.00) ; 5 → [5.00,5.00]
     * - Si tiene decimales o está fuera de 1..5 => comportamiento clásico (>= min_rating).
     */
    $minRatingRaw = $f['min_rating'] ?? null;
    if ($minRatingRaw !== null && $minRatingRaw !== '' && is_numeric($minRatingRaw)) {
        $mr = (float) $minRatingRaw;
        $rounded = (int) round($mr);
        $isIntBucket = (abs($mr - $rounded) < 1e-9) && $rounded >= 1 && $rounded <= 5;

        if ($isIntBucket) {
            $sql .= " AND q.avg_rating IS NOT NULL";
            if ($rounded < 5) {
                // [n, n+1) → incluye n.00 hasta (n+1)-0.01 (p.ej. 3.00–3.99)
                $sql .= " AND q.avg_rating >= ? AND q.avg_rating < ?";
                $params[] = (float) $rounded;       $types .= "d";
                $params[] = (float) ($rounded + 1); $types .= "d";
            } else {
                // 5 exacto
                $sql .= " AND q.avg_rating >= ? AND q.avg_rating <= ?";
                $params[] = 5.00; $types .= "d";
                $params[] = 5.00; $types .= "d";
            }
        } else {
            // Comportamiento clásico: >= min_rating
            $sql .= " AND COALESCE(q.avg_rating, 0) >= ?";
            $params[] = $mr;  $types .= "d";
        }
    }

    $minEvaluations   = $f['min_evaluations'] ?? null;
    $minConsultations = $f['min_consultations'] ?? null;

    if ($minCost !== null && $minCost !== '' && is_numeric($minCost)) {
        $sql .= " AND q.min_consult_price_for_filter IS NOT NULL AND q.min_consult_price_for_filter >= ?";
        $params[] = (float) $minCost;  $types .= "d";
    }
    if ($minEvaluations !== null && $minEvaluations !== '' && is_numeric($minEvaluations)) {
        $sql .= " AND q.reviews_count >= ?";
        $params[] = (int) $minEvaluations;  $types .= "i";
    }
    if ($minConsultations !== null && $minConsultations !== '' && is_numeric($minConsultations)) {
        $sql .= " AND q.consultations_completed >= ?";
        $params[] = (int) $minConsultations;  $types .= "i";
    }

    /* ======== Orden ======== */
    $order = $f['order'] ?? null;
    if ($order === 'rating_cost') {
        $sql .= " ORDER BY (q.avg_rating IS NULL), q.avg_rating DESC,
                 (q.min_consult_price_for_filter IS NULL), q.min_consult_price_for_filter ASC";
    } else {
        $sql .= " ORDER BY q.created_at_max DESC";
    }

    /* ======== Paginación ======== */
    $limit  = $f['limit']  ?? null;
    $offset = $f['offset'] ?? null;

    if ($limit !== null && $limit !== '' && is_numeric($limit) && (int) $limit > 0) {
        $sql .= " LIMIT ?";
        $params[] = (int) $limit;  $types .= "i";

        if ($offset !== null && $offset !== '' && is_numeric($offset) && (int) $offset >= 0) {
            $sql .= " OFFSET ?";
            $params[] = (int) $offset;  $types .= "i";
        }
    }

    /* ======== Ejecutar ======== */
    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();

    $cards = [];
    while ($r = $res->fetch_assoc()) {
        $handle = $r['title_display'] ? '@' . $r['title_display'] : null;
        $avgRating = $r['avg_rating'] !== null ? (float) $r['avg_rating'] : null;
        $ratingText = $avgRating !== null ? ($avgRating . '/5') : null;

        $specId = $r['specialist_id'];
        $hasImg = $specId ? (bool) $this->specialistImageExists($specId) : false;

        $cards[] = [
            'specialist_id' => $specId,
            'full_name' => $r['full_name'],
            'handle' => $handle,
            'website_url' => $r['website_url'],
            'avatar_url' => $r['avatar_url'],
            'lab_reports_evaluated' => (int) $r['lab_reports_evaluated'],
            'consultations_completed' => (int) $r['consultations_completed'],
            'avg_rating' => $avgRating,
            'rating_text' => $ratingText,
            'reviews_count' => (int) $r['reviews_count'],
            'specialist_image' => $hasImg,
            'specialty_id' => $r['specialty_id'] ?? null,
            'specialty_display' => $r['specialty_display'] ?? null,
        ];
    }
    $stmt->close();

    return $cards;
}




    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("
        SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
        FROM {$this->table} s
        WHERE s.email = ?
          AND s.deleted_at IS NULL 
        LIMIT 1
    ");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }


    private function persistEmails(string $specialistId, $emails): void
    {
        require_once __DIR__ . '/ContactEmailModel.php';
        require_once __DIR__ . '/ContactPhoneModel.php';

        $emailModel = new ContactEmailModel();
        $phoneModel = new ContactPhoneModel();

        try {
            // ===== Coerción flexible =====
            if (!is_array($emails)) {
                error_log("[persistEmails][warn] specialist_id={$specialistId} emails no es array; tipo=" . gettype($emails));
                if (is_string($emails)) {
                    $s = trim($emails);
                    if ($s !== '' && ($s[0] === '[' || $s[0] === '{')) {
                        $decoded = json_decode($s, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $emails = $decoded;
                        }
                    }
                    if (!is_array($emails)) {
                        $s = trim((string) $emails);
                        if ($s !== '' && strpos($s, '@') !== false) {
                            $emails = [['email' => $s, 'is_primary' => 1, 'is_active' => 1]];
                        } else {
                            $emails = [];
                        }
                    }
                } else {
                    $emails = [];
                }
            }

            if (is_array($emails) && isset($emails['email'])) {
                $emails = [$emails];
            }
            if (is_array($emails) && !empty($emails) && is_string(reset($emails))) {
                $emails = array_values(array_filter(array_map(function ($e) {
                    $e = trim((string) $e);
                    if ($e !== '' && strpos($e, '@') !== false) {
                        return ['email' => $e, 'is_primary' => 0, 'is_active' => 1];
                    }
                    return null;
                }, $emails)));
            }

            $totalIn = count($emails);
            error_log("[persistEmails][start] specialist_id={$specialistId} total_in={$totalIn} sample=" . json_encode(array_slice($emails, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $existing = $emailModel->listIdsByEntity('specialist', $specialistId);
            $incomingIds = [];

            $created = 0;
            $updated = 0;
            $deleted = 0;
            $skipped = 0;

            foreach ($emails as $idx => $item) {
                if (!is_array($item)) {
                    $skipped++;
                    error_log("[persistEmails][skip] idx={$idx} motivo=item no es array");
                    continue;
                }

                $contactEmailId = trim((string) ($item['contact_email_id'] ?? ''));
                $email = isset($item['email']) ? trim((string) $item['email']) : '';

                $isPrimary = isset($item['is_primary']) ? (int) ((string) $item['is_primary'] === '1' || $item['is_primary'] === 1 || $item['is_primary'] === true) : 0;
                $isActive = isset($item['is_active']) ? (int) ((string) $item['is_active'] === '1' || $item['is_active'] === 1 || $item['is_active'] === true) : 1;

                if ($contactEmailId !== '')
                    $incomingIds[] = $contactEmailId;

                if ($email === '') {
                    $skipped++;
                    error_log("[persistEmails][skip] idx={$idx} motivo=email vacío | item=" . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    continue;
                }

                $payload = [
                    'entity_type' => 'specialist',
                    'entity_id' => $specialistId,
                    'email' => $email,
                    'is_primary' => $isPrimary,
                    'is_active' => $isActive,
                ];

                if ($contactEmailId !== '') {
                    $emailModel->update($contactEmailId, $payload);
                    $updated++;
                    error_log("[persistEmails][update] specialist_id={$specialistId} contact_email_id={$contactEmailId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    $emailModel->create($payload);
                    $created++;
                    error_log("[persistEmails][create] specialist_id={$specialistId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }

            $incomingSet = array_flip($incomingIds);
            foreach ($existing as $id) {
                if (!isset($incomingSet[$id])) {
                    $emailModel->delete($id);
                    $deleted++;
                    error_log("[persistEmails][delete] specialist_id={$specialistId} contact_email_id={$id}");
                }
            }

            error_log("[persistEmails][summary] specialist_id={$specialistId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

        } catch (\Throwable $e) {
            error_log("[persistEmails][fatal] specialist_id={$specialistId} msg={$e->getMessage()}");
            throw $e;
        }
    }

    private function persistPhones(string $specialistId, $phones): void
    {
        require_once __DIR__ . '/ContactEmailModel.php';
        require_once __DIR__ . '/ContactPhoneModel.php';

        $emailModel = new ContactEmailModel();
        $phoneModel = new ContactPhoneModel();

        // --- helper local para normalizar y extraer prefijo ---
        $parsePhone = function (string $raw, ?string $givenCountryCode = null): array {
            $raw = trim($raw);
            $raw = preg_replace('/\s+/', ' ', $raw); // normalizar espacios

            $countryCode = '';
            $national = '';

            // Si viene country_code explícito, úsalo (formato +NNN)
            if (!empty($givenCountryCode)) {
                $cc = trim($givenCountryCode);
                if (preg_match('/^\+?\d{1,3}$/', $cc)) {
                    $countryCode = (str_starts_with($cc, '+') ? $cc : ('+' . $cc));
                }
            }

            // Si NO vino country_code, intenta extraerlo del número
            if ($countryCode === '') {
                $candidate = $raw;

                // Quitar "(+NNN) " al inicio si existe
                $candidate = preg_replace('/^\((\+?\d{1,3})\)\s*/', '$1 ', $candidate);

                // +NNN 123..., 00NNN 123..., +NNN-123..., etc.
                if (preg_match('/^\+?(\d{1,3})[^\d]*([0-9][0-9\-\.\s]+)$/', $candidate, $m)) {
                    $countryCode = '+' . $m[1];
                    $national = preg_replace('/\D+/', '', $m[2]);
                } elseif (preg_match('/^00(\d{1,3})[^\d]*([0-9][0-9\-\.\s]+)$/', $candidate, $m2)) {
                    $countryCode = '+' . $m2[1];
                    $national = preg_replace('/\D+/', '', $m2[2]);
                }
            }

            // Si ya tenemos countryCode pero no nacional, sácalo del raw
            if ($countryCode !== '' && $national === '') {
                $noCC = preg_replace('/^\+?' . preg_quote(ltrim($countryCode, '+'), '/') . '[^\d]*/', '', $raw);
                $noCC = preg_replace('/^\(\\+' . preg_quote(ltrim($countryCode, '+'), '/') . '\)\s*/', '', $noCC);
                $national = preg_replace('/\D+/', '', $noCC);
                if ($national === '') {
                    $national = preg_replace('/\D+/', '', $raw);
                }
            }

            // Asegurar nacional con dígitos si sigue vacío
            if ($national === '') {
                $national = preg_replace('/\D+/', '', $raw);
            }

            return [$countryCode, $national];
        };

        // helper para obtener prefijo desde country_id si existe un método global del modelo
        $getCountryPrefix = function (?string $countryId): string {
            if (empty($countryId))
                return '';
            if (method_exists($this, 'getCountryNormalizedPrefix')) {
                return (string) $this->getCountryNormalizedPrefix($countryId); // debe devolver '+NNN' o ''
            }
            return ''; // fallback silencioso
        };

        try {
            // ===== Coerción flexible =====
            if (!is_array($phones)) {
                error_log("[persistPhones][warn] specialist_id={$specialistId} phones no es array; tipo=" . gettype($phones));
                if (is_string($phones)) {
                    $s = trim($phones);
                    $decoded = (($s !== '' && ($s[0] === '[' || $s[0] === '{')) ? json_decode($s, true) : null);
                    if (is_array($decoded)) {
                        $phones = $decoded;
                    } else {
                        $s = trim((string) $phones);
                        $phones = ($s !== '') ? [
                            [
                                'telephone' => $s, // aceptamos string simple
                                'is_primary' => 1,
                                'is_active' => 1,
                            ]
                        ] : [];
                    }
                } else {
                    $phones = [];
                }
            }

            // Envolver objeto simple
            if (is_array($phones) && (isset($phones['telephone']) || isset($phones['phone_number']))) {
                $phones = [$phones];
            }
            // Array de strings -> mapear
            if (is_array($phones) && !empty($phones) && is_string(reset($phones))) {
                $phones = array_values(array_filter(array_map(function ($p) {
                    $p = trim((string) $p);
                    return $p !== '' ? ['telephone' => $p, 'is_primary' => 0, 'is_active' => 1] : null;
                }, $phones)));
            }

            $totalIn = count($phones);
            error_log("[persistPhones][start] specialist_id={$specialistId} total_in={$totalIn} sample=" . json_encode(array_slice($phones, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $existing = $phoneModel->listIdsByEntity('specialist', $specialistId);
            $incomingIds = [];

            $created = 0;
            $updated = 0;
            $deleted = 0;
            $skipped = 0;

            foreach ($phones as $idx => $item) {
                if (!is_array($item)) {
                    $skipped++;
                    error_log("[persistPhones][skip] idx={$idx} motivo=item no es array");
                    continue;
                }

                $contactPhoneId = trim((string) ($item['contact_phone_id'] ?? ''));

                // Obtener número bruto desde cualquiera de las llaves
                $rawNumber = '';
                if (isset($item['telephone']))
                    $rawNumber = (string) $item['telephone'];
                elseif (isset($item['phone_number']))
                    $rawNumber = (string) $item['phone_number'];
                $rawNumber = trim($rawNumber);

                // Flags
                $isPrimary = isset($item['is_primary']) ? (int) ((string) $item['is_primary'] === '1' || $item['is_primary'] === 1 || $item['is_primary'] === true) : 0;
                $isActive = isset($item['is_active']) ? (int) ((string) $item['is_active'] === '1' || $item['is_active'] === 1 || $item['is_active'] === true) : 1;

                // country_code explícito o derivado por country_id (si llega partido)
                $countryCodeGiven = '';
                if (!empty($item['country_code'])) {
                    $countryCodeGiven = trim((string) $item['country_code']);
                } elseif (!empty($item['country_id'])) {
                    $countryCodeGiven = $getCountryPrefix(trim((string) $item['country_id'])); // '+NNN' o ''
                }

                // === Normalización / parsing ===
                [$countryCode, $national] = $parsePhone($rawNumber, $countryCodeGiven);

                if ($contactPhoneId !== '')
                    $incomingIds[] = $contactPhoneId;

                if ($national === '') {
                    $skipped++;
                    error_log("[persistPhones][skip] idx={$idx} motivo=phone_number vacío tras normalizar | raw={$rawNumber} item=" . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    continue;
                }

                // Último intento de extraer código si aún está vacío
                if ($countryCode === '') {
                    $digits = preg_replace('/\D+/', '', ($rawNumber[0] === '+' ? $rawNumber : '+' . $rawNumber));
                    if (preg_match('/^\+?(\d{1,3})(\d{5,})$/', '+' . $digits, $mm)) {
                        $countryCode = '+' . $mm[1];
                        $national = $mm[2];
                    }
                }
                if ($countryCode === '') {
                    $skipped++;
                    error_log("[persistPhones][skip] idx={$idx} motivo=country_code no detectable | raw={$rawNumber} item=" . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    continue;
                }

                // === Construir phone_number combinado: "(+NNN) NNNNN..." ===
                $fullNumber = sprintf("(+%s) %s", ltrim($countryCode, '+'), $national);

                $payload = [
                    'entity_type' => 'specialist',
                    'entity_id' => $specialistId,
                    'phone_type' => 'mobile',
                    'country_code' => $countryCode, // requerido por el modelo
                    'phone_number' => $fullNumber,  // guardamos combinado
                    'is_primary' => $isPrimary,
                    'is_active' => $isActive,
                ];

                if ($contactPhoneId !== '') {
                    $phoneModel->update($contactPhoneId, $payload);
                    $updated++;
                    error_log("[persistPhones][update] specialist_id={$specialistId} contact_phone_id={$contactPhoneId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    $phoneModel->create($payload);
                    $created++;
                    error_log("[persistPhones][create] specialist_id={$specialistId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }

            // Borrado suave de los que no vinieron
            $incomingSet = array_flip($incomingIds);
            foreach ($existing as $id) {
                if (!isset($incomingSet[$id])) {
                    $phoneModel->delete($id);
                    $deleted++;
                    error_log("[persistPhones][delete] specialist_id={$specialistId} contact_phone_id={$id}");
                }
            }

            if ($totalIn > 0 && ($created + $updated + $deleted) === 0) {
                error_log("[persistPhones][warn] specialist_id={$specialistId} se recibieron {$totalIn} items pero NO se creó/actualizó/eliminó ninguno. Revisa claves del payload.");
            }

            error_log("[persistPhones][summary] specialist_id={$specialistId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

        } catch (\mysqli_sql_exception $e) {
            error_log("[persistPhones][fatal][mysqli] specialist_id={$specialistId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        } catch (\Throwable $e) {
            error_log("[persistPhones][fatal] specialist_id={$specialistId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
    }



    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            /* ======= Traducciones ======= */
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $t = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            /* ======= Auditoría / TZ ======= */
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();
            $createdAt = $env->getCurrentDatetime();
            $createdBy = $userId;

            /* ======= Dependencias Contacto ======= */
            require_once __DIR__ . '/ContactEmailModel.php';
            require_once __DIR__ . '/ContactPhoneModel.php';
            $emailModel = new ContactEmailModel();
            $phoneModel = new ContactPhoneModel();

            /* ======= Datos base Specialist ======= */
            $uuid = $this->generateUUIDv4();
            $systemType = 'US';
            $timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $birthday = $data['birthday'] ?? null; // YYYY-MM-DD
            $hashedPass = password_hash($data['password'], PASSWORD_DEFAULT);

            /* ======= Construir arrays emails/phones (NUEVO MANEJO) ======= */
            // ---------- Emails ----------
            $emailsIn = [];

            // 1) email plano
            if (isset($data['email']) && is_string($data['email'])) {
                $e = strtolower(trim($data['email']));
                if ($e !== '') {
                    $emailsIn[] = ['email' => $e, 'is_primary' => 1, 'is_active' => 1];
                }
            }

            // 2) emails (array) puede venir como strings o arrays
            if (!empty($data['emails']) && is_array($data['emails'])) {
                foreach ($data['emails'] as $e) {
                    if (is_string($e)) {
                        $v = strtolower(trim($e));
                        if ($v !== '') {
                            $emailsIn[] = ['email' => $v, 'is_primary' => 0, 'is_active' => 1];
                        }
                    } elseif (is_array($e)) {
                        $emailsIn[] = [
                            'contact_email_id' => trim((string) ($e['contact_email_id'] ?? '')),
                            'email' => strtolower(trim((string) ($e['email'] ?? ''))),
                            'is_primary' => (int) !!($e['is_primary'] ?? 0),
                            'is_active' => (int) !!($e['is_active'] ?? 1),
                        ];
                    }
                }
            }

            // limpiar vacíos y deduplicar por email
            $emailsIn = array_values(array_filter($emailsIn, fn($it) => !empty($it['email'])));
            $seen = [];
            $emails = [];
            foreach ($emailsIn as $it) {
                $key = $it['email'];
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $emails[] = $it;
                }
            }
            // asegurar un único primary
            $hasPrimary = false;
            foreach ($emails as &$it) {
                if (!$hasPrimary && !empty($it['is_primary'])) {
                    $hasPrimary = true;
                } else {
                    $it['is_primary'] = 0;
                }
            }
            unset($it);
            if (!$hasPrimary && !empty($emails)) {
                $emails[0]['is_primary'] = 1;
            }

            // ---------- Phones ----------
            $phonesIn = [];

            // 1) phone plano
            if (isset($data['phone']) && is_string($data['phone'])) {
                $raw = trim($data['phone']);
                if ($raw !== '') {
                    $phonesIn[] = ['raw' => $raw, 'is_primary' => 1, 'is_active' => 1];
                }
            }

            // 2) phones (array) puede venir como strings o arrays
            if (!empty($data['phones']) && is_array($data['phones'])) {
                foreach ($data['phones'] as $p) {
                    if (is_string($p)) {
                        $v = trim($p);
                        if ($v !== '') {
                            $phonesIn[] = ['raw' => $v, 'is_primary' => 0, 'is_active' => 1];
                        }
                    } elseif (is_array($p)) {
                        $phonesIn[] = [
                            'contact_phone_id' => trim((string) ($p['contact_phone_id'] ?? '')),
                            'country_code' => $p['country_code'] ?? null,
                            'phone_number' => $p['phone_number'] ?? null,
                            'phone_type' => trim((string) ($p['phone_type'] ?? '')),
                            'is_primary' => (int) !!($p['is_primary'] ?? 0),
                            'is_active' => (int) !!($p['is_active'] ?? 1),
                            'raw' => isset($p['raw']) ? trim((string) $p['raw']) : null,
                        ];
                    }
                }
            }

            // completar country_code/phone_number desde raw si faltan (soporta "(+58) ...")
            $parseRaw = function (string $raw): array {
                $s = trim($raw);
                if ($s === '')
                    return [null, null];

                // 00... -> +...
                if (preg_match('/^\s*00\d+/', $s)) {
                    $s = preg_replace('/^\s*00/', '+', $s);
                }

                // Detectar '+' en cualquier posición (aun dentro de paréntesis)
                $plusPos = strpos($s, '+');
                if ($plusPos !== false) {
                    $digits = preg_replace('/\D+/', '', substr($s, $plusPos + 1));
                    if ($digits === '')
                        return [null, null];

                    foreach ([3, 2, 1] as $len) {
                        if (strlen($digits) > $len + 5) {
                            return [substr($digits, 0, $len), substr($digits, $len)];
                        }
                    }
                    return [substr($digits, 0, 1), substr($digits, 1)];
                }

                // Sin '+'
                $digits = preg_replace('/\D+/', '', $s);
                if ($digits === '')
                    return [null, null];

                foreach ([3, 2, 1] as $len) {
                    if (strlen($digits) > $len + 5) {
                        return [substr($digits, 0, $len), substr($digits, $len)];
                    }
                }
                return ['1', $digits]; // default US
            };

            foreach ($phonesIn as $i => $p) {
                $cc = $p['country_code'] ?? null;
                $num = $p['phone_number'] ?? null;

                // Si phone_number viene como "(+58) 123..." normalizamos a dígitos
                if (is_string($num) && $num !== '' && !preg_match('/^\d+$/', $num)) {
                    $phonesIn[$i]['phone_number'] = preg_replace('/\D+/', '', $num);
                    $num = $phonesIn[$i]['phone_number'];
                }

                if ((!$cc || !$num) && !empty($p['raw'])) {
                    [$cc2, $num2] = $parseRaw($p['raw']);
                    if ($cc2 && $num2) {
                        $phonesIn[$i]['country_code'] = $cc2;
                        $phonesIn[$i]['phone_number'] = preg_replace('/\D+/', '', $num2);
                    }
                }
            }

            // deduplicar por (country_code, phone_number) o por raw si no hay cc/num
            $seenP = [];
            $phones = [];
            foreach ($phonesIn as $it) {
                $key = ($it['country_code'] ?? '') . '|' . ($it['phone_number'] ?? '') . '|' . ($it['raw'] ?? '');
                if (!isset($seenP[$key])) {
                    $seenP[$key] = true;
                    $phones[] = $it;
                }
            }
            // asegurar un único primary
            $hasPPrimary = false;
            foreach ($phones as &$it) {
                if (!$hasPPrimary && !empty($it['is_primary'])) {
                    $hasPPrimary = true;
                } else {
                    $it['is_primary'] = 0;
                }
            }
            unset($it);
            if (!$hasPPrimary && !empty($phones)) {
                $phones[0]['is_primary'] = 1;
            }

            /* ======= Compat columns (email/phone) ======= */
            $compatEmail = isset($emails[0]['email']) ? $emails[0]['email'] : ($data['email'] ?? '');
            if ($compatEmail !== '' && !filter_var($compatEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \mysqli_sql_exception($t['invalid_email_format'] ?? 'Formato de email inválido.');
            }

            // Guardar phone TAL CUAL si viene en $data; si no, lo formateamos desde phones[0]
            $compatPhone = $data['phone'] ?? '';
            if ($compatPhone === '' && !empty($phones)) {
                $p0 = $phones[0];
                if (!empty($p0['country_code']) && !empty($p0['phone_number'])) {
                    $cc = ltrim((string) $p0['country_code'], '+');
                    $num = preg_replace('/\D+/', '', (string) $p0['phone_number']);
                    $compatPhone = '(+' . $cc . ') ' . $num; // Formato (+CC) NNN...
                } elseif (!empty($p0['raw'])) {
                    $compatPhone = $p0['raw'];
                }
            }

            /* ======= INSERT specialist (sin chequeos de duplicado directo) ======= */
            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (specialist_id, first_name, last_name, email, phone, password, specialty_id, title_id, bio, whatsapp_link, website_url, avatar_url,
             verified_status, languages, available_for_free_consults, max_free_consults_per_month,
             system_type, timezone, birthday, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new \mysqli_sql_exception(($t['error_preparing_insert'] ?? 'Error preparando insert: ') . $this->db->error);
            }

                $verifiedStatus = $data['verified_status'] ?? 'PENDING';

            $stmt->bind_param(
                "ssssssssssssssiisssss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $compatEmail,
                $compatPhone,
                $hashedPass,
                $data['specialty_id'],
                $data['title_id'],
                $data['bio'],
                $data['whatsapp_link'],
                $data['website_url'],
                $data['avatar_url'],
                $verifiedStatus,
                $data['languages'],
                $data['available_for_free_consults'],
                $data['max_free_consults_per_month'],
                $systemType,
                $timezone,
                $birthday,
                $createdAt,
                $createdBy
            );
            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception(($t['error_executing_insert'] ?? 'Error ejecutando insert: ') . $stmt->error);
            }
            $stmt->close();

            /* ======= Persistir contactos N (entity = 'specialist') ======= */
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($uuid, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($uuid, $data['phones']);
            }

            $this->db->commit();
            return true;

        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            /* ======= Traducciones ======= */
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $t = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            /* ======= Verificación existencia + timezone actual ======= */
            $check = $this->db->prepare("SELECT timezone FROM {$this->table} WHERE specialist_id = ? LIMIT 1");
            if (!$check) {
                throw new \mysqli_sql_exception(($t['error_preparing_check'] ?? 'Error preparando verificación: ') . $this->db->error);
            }
            $check->bind_param("s", $id);
            $check->execute();
            $check->store_result();
            if ($check->num_rows === 0) {
                $check->close();
                throw new \mysqli_sql_exception($t['specialist_not_found'] ?? 'Especialista no encontrado.');
            }
            $check->bind_result($oldTimezone);
            $check->fetch();
            $check->close();

            /* ======= Auditoría / TZ ======= */
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $userId;

            /* ======= Dependencias Contacto ======= */
            require_once __DIR__ . '/ContactEmailModel.php';
            require_once __DIR__ . '/ContactPhoneModel.php';
            $emailModel = new ContactEmailModel();
            $phoneModel = new ContactPhoneModel();

            /* ======= Construir arrays emails/phones (NUEVO MANEJO) ======= */
            // ---------- Emails ----------
            $emailsIn = [];

            // 1) email plano
            if (isset($data['email']) && is_string($data['email'])) {
                $e = strtolower(trim($data['email']));
                if ($e !== '') {
                    $emailsIn[] = ['email' => $e, 'is_primary' => 1, 'is_active' => 1];
                }
            }

            // 2) emails (array)
            if (!empty($data['emails']) && is_array($data['emails'])) {
                foreach ($data['emails'] as $e) {
                    if (is_string($e)) {
                        $v = strtolower(trim($e));
                        if ($v !== '') {
                            $emailsIn[] = ['email' => $v, 'is_primary' => 0, 'is_active' => 1];
                        }
                    } elseif (is_array($e)) {
                        $emailsIn[] = [
                            'contact_email_id' => trim((string) ($e['contact_email_id'] ?? '')),
                            'email' => strtolower(trim((string) ($e['email'] ?? ''))),
                            'is_primary' => (int) !!($e['is_primary'] ?? 0),
                            'is_active' => (int) !!($e['is_active'] ?? 1),
                        ];
                    }
                }
            }

            // limpiar vacíos y deduplicar por email
            $emailsIn = array_values(array_filter($emailsIn, fn($it) => !empty($it['email'])));
            $seenEmails = [];
            $emails = [];
            foreach ($emailsIn as $it) {
                $key = $it['email'];
                if (!isset($seenEmails[$key])) {
                    $seenEmails[$key] = true;
                    $emails[] = $it;
                }
            }
            // asegurar un único primary
            $hasPrimaryEmail = false;
            foreach ($emails as &$it) {
                if (!$hasPrimaryEmail && !empty($it['is_primary'])) {
                    $hasPrimaryEmail = true;
                } else {
                    $it['is_primary'] = 0;
                }
            }
            unset($it);
            if (!$hasPrimaryEmail && !empty($emails)) {
                $emails[0]['is_primary'] = 1;
            }

            // ---------- Phones ----------
            $phonesIn = [];

            // 1) phone plano
            if (isset($data['phone']) && is_string($data['phone'])) {
                $raw = trim($data['phone']);
                if ($raw !== '') {
                    $phonesIn[] = ['raw' => $raw, 'is_primary' => 1, 'is_active' => 1];
                }
            }

            // 2) phones (array)
            if (!empty($data['phones']) && is_array($data['phones'])) {
                foreach ($data['phones'] as $p) {
                    if (is_string($p)) {
                        $v = trim($p);
                        if ($v !== '') {
                            $phonesIn[] = ['raw' => $v, 'is_primary' => 0, 'is_active' => 1];
                        }
                    } elseif (is_array($p)) {
                        $phonesIn[] = [
                            'contact_phone_id' => trim((string) ($p['contact_phone_id'] ?? '')),
                            'country_code' => $p['country_code'] ?? null,
                            'phone_number' => $p['phone_number'] ?? null,
                            'phone_type' => trim((string) ($p['phone_type'] ?? '')),
                            'is_primary' => (int) !!($p['is_primary'] ?? 0),
                            'is_active' => (int) !!($p['is_active'] ?? 1),
                            'raw' => isset($p['raw']) ? trim((string) $p['raw']) : null,
                        ];
                    }
                }
            }

            // completar country_code/phone_number desde raw si faltan (soporta "(+58) ...")
            $parseRaw = function (string $raw): array {
                $s = trim($raw);
                if ($s === '')
                    return [null, null];

                // Normalizar 00... a +...
                if (preg_match('/^\s*00\d+/', $s)) {
                    $s = preg_replace('/^\s*00/', '+', $s);
                }

                // Detectar '+' en cualquier posición
                $plusPos = strpos($s, '+');
                if ($plusPos !== false) {
                    $digits = preg_replace('/\D+/', '', substr($s, $plusPos + 1));
                    if ($digits === '')
                        return [null, null];

                    foreach ([3, 2, 1] as $len) {
                        if (strlen($digits) > $len + 5) {
                            return [substr($digits, 0, $len), substr($digits, $len)];
                        }
                    }
                    return [substr($digits, 0, 1), substr($digits, 1)];
                }

                // Sin '+': default US (1) si parece internacional
                $digits = preg_replace('/\D+/', '', $s);
                if ($digits === '')
                    return [null, null];

                foreach ([3, 2, 1] as $len) {
                    if (strlen($digits) > $len + 5) {
                        return [substr($digits, 0, $len), substr($digits, $len)];
                    }
                }
                return ['1', $digits];
            };

            foreach ($phonesIn as $i => $p) {
                $cc = $p['country_code'] ?? null;
                $num = $p['phone_number'] ?? null;

                // Normaliza phone_number a dígitos
                if (is_string($num) && $num !== '' && !preg_match('/^\d+$/', $num)) {
                    $phonesIn[$i]['phone_number'] = preg_replace('/\D+/', '', $num);
                    $num = $phonesIn[$i]['phone_number'];
                }

                if ((!$cc || !$num) && !empty($p['raw'])) {
                    [$cc2, $num2] = $parseRaw($p['raw']);
                    if ($cc2 && $num2) {
                        $phonesIn[$i]['country_code'] = $cc2;
                        $phonesIn[$i]['phone_number'] = preg_replace('/\D+/', '', $num2);
                    }
                }
            }

            // deduplicar por (country_code, phone_number) o por raw
            $seenPhones = [];
            $phones = [];
            foreach ($phonesIn as $it) {
                $key = ($it['country_code'] ?? '') . '|' . ($it['phone_number'] ?? '') . '|' . ($it['raw'] ?? '');
                if (!isset($seenPhones[$key])) {
                    $seenPhones[$key] = true;
                    $phones[] = $it;
                }
            }
            // asegurar un único primary
            $hasPrimaryPhone = false;
            foreach ($phones as &$it) {
                if (!$hasPrimaryPhone && !empty($it['is_primary'])) {
                    $hasPrimaryPhone = true;
                } else {
                    $it['is_primary'] = 0;
                }
            }
            unset($it);
            if (!$hasPrimaryPhone && !empty($phones)) {
                $phones[0]['is_primary'] = 1;
            }

            /* ======= Compat email/phone ======= */
            $compatEmail = isset($emails[0]['email']) ? $emails[0]['email'] : ($data['email'] ?? '');
            if ($compatEmail !== '' && !filter_var($compatEmail, FILTER_VALIDATE_EMAIL)) {
                throw new \mysqli_sql_exception($t['invalid_email_format'] ?? 'Formato de email inválido.');
            }

            // Guardar phone TAL CUAL si viene en $data; si no, reconstruir
            $compatPhone = $data['phone'] ?? '';
            if ($compatPhone === '' && !empty($phones)) {
                $p0 = $phones[0];
                if (!empty($p0['country_code']) && !empty($p0['phone_number'])) {
                    $cc = ltrim((string) $p0['country_code'], '+');
                    $num = preg_replace('/\D+/', '', (string) $p0['phone_number']);
                    $compatPhone = '(+' . $cc . ') ' . $num;
                } elseif (!empty($p0['raw'])) {
                    $compatPhone = $p0['raw'];
                }
            }

            /* ======= Campos base ======= */
            $timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $birthday = $data['birthday'] ?? null; // YYYY-MM-DD
            $status = isset($data['status']) && (int) $data['status'] === 1 ? 1 : 0;

            $sql = "UPDATE {$this->table} SET 
            first_name = ?, last_name = ?, email = ?, phone = ?, 
            specialty_id = ?, title_id = ?, system_type = ?, timezone = ?, 
            status = ?, birthday = ?, updated_at = ?, updated_by = ?";

            $params = [
                $data['first_name'],
                $data['last_name'],
                $compatEmail,
                $compatPhone,
                $data['specialty_id'],
                $data['title_id'],
                strtoupper($data['system_type'] ?? 'US'),
                $timezone,
                $status,
                $birthday,
                $updatedAt,
                $updatedBy
            ];
            $types = "ssssssssisss";

            if (array_key_exists('verified_status', $data)) {
                 $sql .= ", verified_status = ?";
                 $params[] = $data['verified_status'];
                 $types .= "s";
            }

            if (!empty($data['password'])) {
                $sql .= ", password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
                $types .= "s";
            }

            $sql .= " WHERE specialist_id = ?";
            $params[] = $id;
            $types .= "s";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \mysqli_sql_exception(($t['error_preparing_update_statement'] ?? 'Error preparando update: ') . $this->db->error);
            }
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                throw new \mysqli_sql_exception(($t['error_executing_update'] ?? 'Error ejecutando update: ') . $stmt->error);
            }
            $stmt->close();

            /* ======= Actualizar availability.timezone si cambió ======= */
            if ($oldTimezone !== $timezone) {
                $stmtAv = $this->db->prepare("
                UPDATE specialist_availability 
                   SET timezone = ?, updated_at = ?, updated_by = ?
                 WHERE specialist_id = ?
            ");
                if (!$stmtAv) {
                    throw new \mysqli_sql_exception(($t['error_preparing_update_statement'] ?? 'Error preparando update: ') . $this->db->error);
                }
                $stmtAv->bind_param("ssss", $timezone, $updatedAt, $updatedBy, $id);
                if (!$stmtAv->execute()) {
                    throw new \mysqli_sql_exception(($t['error_executing_update'] ?? 'Error ejecutando update: ') . $stmtAv->error);
                }
                $stmtAv->close();
            }

            /* ======= Persistir emails/phones si vinieron explícitos ======= */
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($id, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($id, $data['phones']);
            }

            $this->db->commit();
            return true;

        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }








    public function getByTelephone(string $telephone)
    {
        try {
            // El teléfono ya viene limpio desde el frontend, como "584249173469"
            $normalizedTelephone = $telephone;

            $query = "
            SELECT * 
            FROM {$this->table} 
            WHERE 
                phone = ?
              AND deleted_at IS NULL
            LIMIT 1
        ";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $normalizedTelephone);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }


    public function registerSpecialist($data)
    {
        $this->db->begin_transaction();
        try {
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            $checkEmail = $this->db->prepare("SELECT specialist_id FROM {$this->table} WHERE email = ? ");
            if (!$checkEmail) {
                throw new mysqli_sql_exception($translations['error_preparing_email_check'] . $this->db->error);
            }
            $checkEmail->bind_param("s", $data['email']);
            $checkEmail->execute();
            $checkEmail->store_result();
            if ($checkEmail->num_rows > 0) {
                throw new mysqli_sql_exception($translations['email_already_registered']);
            }
            $checkEmail->close();

            $checkPhone = $this->db->prepare("SELECT specialist_id FROM {$this->table} WHERE phone = ? ");
            if (!$checkPhone) {
                throw new mysqli_sql_exception($translations['error_preparing_phone_check'] . $this->db->error);
            }
            $checkPhone->bind_param("s", $data['phone']);
            $checkPhone->execute();
            $checkPhone->store_result();
            if ($checkPhone->num_rows > 0) {
                throw new mysqli_sql_exception($translations['phone_already_registered']);
            }
            $checkPhone->close();

            $userId = 0;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();

            $createdAt = $env->getCurrentDatetime();
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $uuid = $this->generateUUIDv4();
            $timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $birthday = $data['birthday'] ?? null; // YYYY-MM-DD

            // birthday antes de auditoría
            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
        (specialist_id, first_name, last_name, email, phone, password, specialty_id, title_id, system_type, timezone, birthday, created_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'US', ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception($translations['error_preparing_insert'] . $this->db->error);
            }

            // +1 's' por birthday respecto a tu versión previa
            $stmt->bind_param(
                "ssssssssssss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $hashedPassword,
                $data['specialty_id'],
                $data['title_id'],
                $timezone,
                $birthday, // <-- NUEVO
                $createdAt,
                $uuid      // created_by = specialist_id
            );

            if (!$stmt->execute()) {
                throw new mysqli_sql_exception($translations['error_executing_insert'] . $stmt->error);
            }

            $stmt->close();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }



    private function generateUUIDv4(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }



    public function updatePassword(array $data): bool
    {
        $newPassword = $data['newPassword'] ?? null;
        $userId = $data['userId'] ?? null;
        $token = $data['token'] ?? null;

        if (empty($newPassword)) {
            return false;
        }

        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();

        $updatedAt = $env->getCurrentDatetime();
        $updatedBy = $userId;

        // === CASO 1: Se está usando userId (respuestas de seguridad)
        if (!empty($userId)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE specialist_id  = ?");
            $stmt->bind_param("ssss", $hashedPassword, $updatedAt, $updatedBy, $userId);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            if ($success) {
                $stmt = $this->db->prepare("SELECT email FROM {$this->table} WHERE specialist_id  = ?");
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();

                if ($user && isset($user['email'])) {
                    $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
                    $stmt->bind_param("s", $user['email']);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            return $success;
        }

        // === CASO 2: Se está usando token (enlace por email)
        if (!empty($token)) {
            $stmt = $this->db->prepare("SELECT email, created_at FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            $reset = $result->fetch_assoc();
            $stmt->close();

            if (!$reset) {
                return false;
            }

            // Verificar expiración del token (10 min)
            $createdAt = new DateTime($reset['created_at']);
            $now = new DateTime();
            $diffSeconds = $now->getTimestamp() - $createdAt->getTimestamp();

            if ($diffSeconds > 600) {
                $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->close();
                return false;
            }

            $stmt = $this->db->prepare("SELECT specialist_id  FROM {$this->table} WHERE email = ?");
            $stmt->bind_param("s", $reset['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                return false;
            }

            $userIdFromToken = $user['id'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE specialist_id  = ?");
            $stmt->bind_param("ssss", $hashedPassword, $updatedAt, $userIdFromToken, $userIdFromToken);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            if ($success) {
                $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                $stmt->close();
            }

            return $success;
        }

        return false;
    }
    public function updateSystemTypeByUserId($userId, $systemType)
    {
        try {
            $this->db->begin_transaction();

            $this->system_type = strtoupper($systemType ?? 'US');

            // Auditoría
            $sessionUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $sessionUserId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $sessionUserId;

            // Validar si el registro existe por user_id
            $checkStmt = $this->db->prepare("SELECT specialist_id  FROM {$this->table} WHERE specialist_id  = ? LIMIT 1");
            if (!$checkStmt) {
                throw new Exception("Error preparando la consulta: " . $this->db->error);
            }
            $checkStmt->bind_param("s", $userId);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows === 0) {
                throw new Exception("El registro no existe.");
            }
            $checkStmt->close();

            // Actualizar solo el system_type basado en user_id
            $stmt = $this->db->prepare("UPDATE {$this->table} SET system_type = ?, updated_at = ?, updated_by = ? WHERE specialist_id  = ?");
            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . $this->db->error);
            }

            $stmt->bind_param("ssss", $this->system_type, $updatedAt, $updatedBy, $userId);
            if (!$stmt->execute()) {
                throw new Exception('No se pudo actualizar el system_type: ' . $stmt->error);
            }

            $this->db->commit();

            // Actualizar variable de sesión si corresponde
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
                $_SESSION['system_type'] = $this->system_type;
            }

            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateProfile($id, $data)
    {
        // ===== Log de entrada (no exponer password) =====
        $logSnapshot = [
            'specialist_id' => $id,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'system_type' => strtoupper($data['system_type'] ?? 'US'),
            'timezone' => $data['timezone'] ?? 'America/Los_Angeles',
            'specialty_id' => $data['specialty_id'] ?? null,
            'title_id' => $data['title_id'] ?? null,
            'email_in' => isset($data['email']) ? strtolower(trim((string) $data['email'])) : null,
            'telephone_in' => isset($data['telephone']) ? trim((string) $data['telephone']) : null,
            'has_emails' => is_array($data['emails'] ?? null),
            'has_phones' => is_array($data['phones'] ?? null),
            'birthday' => $data['birthday'] ?? null,
            'password_present' => !empty($data['password']),
        ];
        error_log('[specialist.updateProfile][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        try {
            $this->db->begin_transaction();

            /* ====== Obtener timezone actual del especialista ====== */
            $stmtTz = $this->db->prepare("SELECT timezone FROM {$this->table} WHERE specialist_id = ? AND deleted_at IS NULL LIMIT 1");
            if (!$stmtTz) {
                throw new \Exception('Error preparando consulta de timezone actual: ' . $this->db->error);
            }
            $stmtTz->bind_param("s", $id);
            if (!$stmtTz->execute()) {
                $stmtTz->close();
                throw new \Exception('Error ejecutando consulta de timezone actual: ' . $this->db->error);
            }
            $stmtTz->bind_result($oldTimezone);
            if (!$stmtTz->fetch()) {
                $stmtTz->close();
                throw new \Exception('Especialista no encontrado o eliminado.');
            }
            $stmtTz->close();

            /* ====== Cargar datos base del formulario ====== */
            $this->specialist_id = $id;
            $this->first_name = $data['first_name'] ?? '';
            $this->last_name = $data['last_name'] ?? '';
            $this->password = $data['password'] ?? '';
            $this->system_type = strtoupper($data['system_type'] ?? 'US');
            $this->timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $this->specialty_id = $data['specialty_id'] ?? null;
            $this->title_id = $data['title_id'] ?? null;
            $this->bio = $data['bio'] ?? '';
            $this->whatsapp_link = $data['whatsapp_link'] ?? '';
            $this->website_url = $data['website_url'] ?? '';
            $this->avatar_url = $data['avatar_url'] ?? null;

            if (isset($data['languages']) && is_array($data['languages'])) {
                $this->languages = json_encode($data['languages']);
            } else {
                $this->languages = $data['languages'] ?? '["en"]';
            }

            $this->available_for_free_consults = (int) ($data['available_for_free_consults'] ?? 0);
            $this->max_free_consults_per_month = (int) ($data['max_free_consults_per_month'] ?? 0);
            $this->birthday = $data['birthday'] ?? null; // YYYY-MM-DD

            // Normalización directa (igual que en user)
            $this->email = isset($data['email']) ? trim(strtolower((string) $data['email'])) : '';
            $this->telephone = isset($data['telephone']) ? trim((string) $data['telephone']) : '';

            /* ====== Auditoría ====== */
            $actorId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $actorId);
            (new TimezoneManager($this->db))->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $actorId;

            /* ====== Validar duplicidad de email (solo si no vacío) ====== */
            if ($this->email !== '') {
                $check = $this->db->prepare("SELECT specialist_id FROM {$this->table} WHERE email = ? AND specialist_id != ? AND deleted_at IS NULL");
                if (!$check) {
                    error_log('[specialist.updateProfile][prepare-error] validar email | ' . $this->db->error);
                    throw new \Exception('Error al preparar validación de email: ' . $this->db->error);
                }
                $check->bind_param("ss", $this->email, $this->specialist_id);
                if (!$check->execute()) {
                    error_log('[specialist.updateProfile][execute-error] validar email | ' . $check->error);
                    throw new \Exception('Error ejecutando validación de email: ' . $check->error);
                }
                $check->store_result();
                if ($check->num_rows > 0) {
                    $check->close();
                    error_log("[specialist.updateProfile][dup-email] specialist_id={$this->specialist_id} email={$this->email}");
                    throw new \Exception('Este correo ya está registrado por otro especialista.');
                }
                $check->close();
            }

            /* ====== UPDATE specialists (email y phone SIEMPRE, phone = telephone) ====== */
            $sql = "UPDATE {$this->table} SET 
            first_name = ?, 
            last_name = ?, 
            email = ?, 
            phone = ?, 
            specialty_id = ?, 
            title_id = ?, 
            bio = ?, 
            whatsapp_link = ?, 
            website_url = ?, 
            avatar_url = ?, 
            languages = ?, 
            available_for_free_consults = ?, 
            max_free_consults_per_month = ?, 
            system_type = ?, 
            timezone = ?, 
            birthday = ?, 
            updated_at = ?, 
            updated_by = ?";

            $params = [
                $this->first_name,
                $this->last_name,
                $this->email,
                $this->telephone, // <- igual que en user (usa telephone para la columna maestra)
                $this->specialty_id,
                $this->title_id,
                $this->bio,
                $this->whatsapp_link,
                $this->website_url,
                $this->avatar_url,
                $this->languages,
                $this->available_for_free_consults,
                $this->max_free_consults_per_month,
                $this->system_type,
                $this->timezone,
                $this->birthday,
                $updatedAt,
                $updatedBy
            ];

            // Tipos: 11s + 2i + 5s => total 18
            $types = "sssssssssss" . "ii" . "sssss";

            if (!empty($this->password)) {
                $sql .= ", password = ?";
                $params[] = password_hash($this->password, PASSWORD_DEFAULT);
                $types .= "s";
            }

            $sql .= " WHERE specialist_id = ?";
            $params[] = $this->specialist_id;
            $types .= "s";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log('[specialist.updateProfile][prepare-error] update | ' . $this->db->error);
                throw new \Exception('Error al preparar consulta: ' . $this->db->error);
            }
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                error_log('[specialist.updateProfile][execute-error] update | ' . $stmt->error);
                throw new \Exception('No se pudo actualizar el especialista: ' . $stmt->error);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();

            /* ====== Actualizar specialist_availability.timezone si cambió ====== */
            if ($oldTimezone !== $this->timezone) {
                $stmtAv = $this->db->prepare("
                UPDATE specialist_availability
                   SET timezone = ?, updated_at = ?, updated_by = ?
                 WHERE specialist_id = ?
            ");
                if (!$stmtAv) {
                    throw new \Exception('Error preparando actualización de availability: ' . $this->db->error);
                }
                $stmtAv->bind_param("ssss", $this->timezone, $updatedAt, $updatedBy, $this->specialist_id);
                if (!$stmtAv->execute()) {
                    $stmtAv->close();
                    throw new \Exception('Error ejecutando actualización de availability: ' . $stmtAv->error);
                }
                $stmtAv->close();
                error_log("[specialist.updateProfile][availability-timezone-updated] from={$oldTimezone} to={$this->timezone} specialist_id={$this->specialist_id}");
            }

            /* ====== Persistir colecciones si llegaron las claves (igual que user) ====== */
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($id, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($id, $data['phones']); // <- pasa tal cual llegó (sin normalizar)
            }

            $this->db->commit();

            /* ====== Refrescar sesión (igual que user: desde normalizados) ====== */
            $_SESSION['first_name'] = $this->first_name;
            $_SESSION['last_name'] = $this->last_name;
            $_SESSION['user_name'] = $this->first_name . ' ' . $this->last_name;
            $_SESSION['system_type'] = $this->system_type;
            $_SESSION['timezone'] = $this->timezone;
            $_SESSION['email'] = $this->email;
            $_SESSION['phone'] = $this->telephone; // mantiene el mismo origen
            $_SESSION['avatar_url'] = $this->avatar_url;
            $_SESSION['specialty_id'] = $this->specialty_id;
            $_SESSION['title_id'] = $this->title_id;

            error_log("[specialist.updateProfile][success] specialist_id={$this->specialist_id} affected_rows={$affected}");
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("[specialist.updateProfile][error] specialist_id={$id} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
    }








    public function getSessionUserData($specialistId)
    {
        try {
            if (empty($specialistId)) {
                return ['status' => 'error', 'message' => 'Specialist ID is required'];
            }

            $stmt = $this->db->prepare("
            SELECT s.*, TIMESTAMPDIFF(YEAR, s.birthday, CURDATE()) AS age_years
            FROM {$this->table} s
            WHERE s.specialist_id = ? AND s.deleted_at IS NULL
        ");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param("s", $specialistId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return ['status' => 'error', 'message' => 'Specialist not found'];
            }

            $data = $result->fetch_assoc();

            $lang = strtoupper($_SESSION['idioma'] ?? $_SESSION['language'] ?? 'EN');
            $lang_field = ($lang === 'ES') ? 'name_es' : 'name_en';

            require_once __DIR__ . '/SpecialtyModel.php';
            $specialtyModel = new SpecialtyModel();
            $specialty = $specialtyModel->getById($data['specialty_id']);
            $data['specialty_name'] = is_array($specialty) ? ($specialty[$lang_field] ?? null) : null;

            require_once __DIR__ . '/TitleModel.php';
            $titleModel = new TitleModel();
            $title = $titleModel->getById($data['title_id']);
            $data['title_name'] = is_array($title) ? ($title[$lang_field] ?? null) : null;

            return $data;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }




    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            // Cargar idioma
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $archivo_idioma = PROJECT_ROOT . "/lang/{$lang}.php";
            $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

            $userId = $_SESSION['user_id'] ?? null;

            // ===== Verificación de dependencias =====
            // Nota: En tu esquema, casi todas estas tablas tienen deleted_at.
            // comment_biomarker NO necesariamente la tiene, por eso su query no la usa.
            $checks = [
                [
                    'label' => 'security_questions',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM security_questions
                            WHERE user_type = 'Specialist' AND user_id_specialist = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_security_questions']
                        ?? "Cannot delete specialist: related security questions exist."
                ],
                [
                    'label' => 'specialist_availability',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_availability
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_availability']
                        ?? "Cannot delete specialist: related availability slots exist."
                ],
                [
                    'label' => 'specialist_certifications',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_certifications
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_certifications']
                        ?? "Cannot delete specialist: related certifications exist."
                ],
                [
                    'label' => 'specialist_locations',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_locations
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_locations']
                        ?? "Cannot delete specialist: related locations exist."
                ],
                [
                    'label' => 'specialist_pricing',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_pricing
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_pricing']
                        ?? "Cannot delete specialist: related pricing exist."
                ],
                [
                    'label' => 'specialist_reviews',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_reviews
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_reviews']
                        ?? "Cannot delete specialist: related reviews exist."
                ],
                [
                    'label' => 'specialist_social_links',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_social_links
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_social_links']
                        ?? "Cannot delete specialist: related social links exist."
                ],
                [
                    'label' => 'specialist_verification_requests',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM specialist_verification_requests
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_verification_requests']
                        ?? "Cannot delete specialist: related verification requests exist."
                ],
                [
                    'label' => 'second_opinion_requests',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM second_opinion_requests
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_second_opinion']
                        ?? "Cannot delete specialist: related second opinion requests exist."
                ],
                [
                    'label' => 'transactions',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM transactions
                            WHERE specialist_id = ? AND deleted_at IS NULL",
                    'msg' => $traducciones['specialist_delete_dependency_transactions']
                        ?? "Cannot delete specialist: related transactions exist."
                ],
                [
                    'label' => 'comment_biomarker',
                    'sql' => "SELECT COUNT(*) AS total
                            FROM comment_biomarker
                            WHERE id_specialist = ?",
                    'msg' => $traducciones['specialist_delete_dependency_comments']
                        ?? "Cannot delete specialist: related biomarker comments exist."
                ],
            ];

            $found = [];
            foreach ($checks as $chk) {
                $stmt = $this->db->prepare($chk['sql']);
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparando verificación de {$chk['label']}: " . $this->db->error);
                }
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();
                $stmt->close();

                if ((int) ($row['total'] ?? 0) > 0) {
                    $found[] = [
                        'table' => $chk['label'],
                        'count' => (int) $row['total'],
                        'msg' => $chk['msg']
                    ];
                }
            }

            if (!empty($found)) {
                // Construir mensaje compuesto indicando tablas con bloqueo
                $detalles = array_map(function ($f) {
                    return "{$f['table']} ({$f['count']})";
                }, $found);

                $prefijo = $traducciones['specialist_delete_dependency']
                    ?? "Cannot delete specialist: related records exist.";
                $mensaje = $prefijo . ' [' . implode(', ', $detalles) . ']';

                // Lanza con el primer msg específico como detalle
                $primero = $found[0]['msg'];
                throw new mysqli_sql_exception($mensaje . " | " . $primero);
            }

            // ===== Auditoría =====
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $userId;

            // ===== Eliminación lógica =====
            $stmt = $this->db->prepare("UPDATE {$this->table}
                                    SET deleted_at = ?, deleted_by = ?
                                    WHERE specialist_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error eliminando el especialista: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateStatus(array $data): bool
    {
        $specialistId = $data['specialist_id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if (!in_array($newStatus, [0, 1], true) || empty($specialistId)) {
            return false;
        }

        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $specialistId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $specialistId;

            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = ?, updated_by = ? WHERE specialist_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("isss", $newStatus, $updatedAt, $updatedBy, $specialistId);
            $stmt->execute();

            $success = $stmt->affected_rows > 0;
            $stmt->close();

            return $success;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }



    public function authenticate($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $specialist = $result->fetch_assoc();
            if (password_verify($password, $specialist['password'])) {
                unset($specialist['password']);
                return $specialist;
            }
        }
        return null;
    }
}
