<?php

require_once __DIR__ . '/../config/Database.php';

class AdministratorModel
{
    private $db;
    private $table = "administrators";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function setIdioma(string $adminId, string $lang): bool
    {
        try {
            $lang = strtoupper(trim($lang));
            if (!in_array($lang, ['EN', 'ES'])) {
                return false;
            }

            $stmt = $this->db->prepare("UPDATE {$this->table} SET interface_language = ? WHERE administrator_id = ?");
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("ss", $lang, $adminId);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        } catch (\Exception $e) {
            error_log("Error setIdioma in {$this->table}: " . $e->getMessage());
            return false;
        }
    }

    public function getAll()
    {
        try {
            $result = $this->db->query("SELECT * FROM {$this->table} WHERE deleted_at IS NULL");
            if (!$result) {
                throw new mysqli_sql_exception("Error fetching administrators: " . $this->db->error);
            }

            $admins = [];
            while ($row = $result->fetch_assoc()) {
                // === Agregar emails y phones relacionados ===
                require_once __DIR__ . '/ContactEmailModel.php';
                require_once __DIR__ . '/ContactPhoneModel.php';

                $emailModel = new ContactEmailModel();
                $phoneModel = new ContactPhoneModel();

                $row['emails'] = $emailModel->getByEntity('administrator', $row['administrator_id']);
                $row['phones'] = $phoneModel->getByEntity('administrator', $row['administrator_id']);

                $admins[] = $row;
            }

            return $admins;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE administrator_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin) {
            // === Agregar emails y phones relacionados ===
            require_once __DIR__ . '/ContactEmailModel.php';
            require_once __DIR__ . '/ContactPhoneModel.php';

            $emailModel = new ContactEmailModel();
            $phoneModel = new ContactPhoneModel();

            $admin['emails'] = $emailModel->getByEntity('administrator', $admin['administrator_id']);
            $admin['phones'] = $phoneModel->getByEntity('administrator', $admin['administrator_id']);

            $imagePathRel = "uploads/administrator/user_" . $admin['administrator_id'] . ".jpg";
            if (file_exists(PROJECT_ROOT . '/' . $imagePathRel)) {
                $admin['profile_image_url'] = $imagePathRel . '?v=' . filemtime(PROJECT_ROOT . '/' . $imagePathRel);
            } else {
                $admin['profile_image_url'] = "public/assets/images/users/user_boy.svg";
            }
        }

        return $admin;
    }


    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("
        SELECT * 
        FROM {$this->table} 
        WHERE email = ? 
          AND deleted_at IS NULL 
        LIMIT 1
    ");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    private function persistEmails(string $administratorId, $emails): void
    {
        require_once __DIR__ . '/ContactEmailModel.php';
        require_once __DIR__ . '/ContactPhoneModel.php';

        $emailModel = new ContactEmailModel();
        $phoneModel = new ContactPhoneModel();

        try {
            // ===== Coerción flexible =====
            if (!is_array($emails)) {
                error_log("[persistEmails][warn] administrator_id={$administratorId} emails no es array; tipo=" . gettype($emails));
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
            error_log("[persistEmails][start] administrator_id={$administratorId} total_in={$totalIn} sample=" . json_encode(array_slice($emails, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $existing = $emailModel->listIdsByEntity('administrator', $administratorId);
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
                    'entity_type' => 'administrator',
                    'entity_id' => $administratorId,
                    'email' => $email,
                    'is_primary' => $isPrimary,
                    'is_active' => $isActive,
                ];

                if ($contactEmailId !== '') {
                    $emailModel->update($contactEmailId, $payload);
                    $updated++;
                    error_log("[persistEmails][update] administrator_id={$administratorId} contact_email_id={$contactEmailId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    $emailModel->create($payload);
                    $created++;
                    error_log("[persistEmails][create] administrator_id={$administratorId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }

            $incomingSet = array_flip($incomingIds);
            foreach ($existing as $id) {
                if (!isset($incomingSet[$id])) {
                    $emailModel->delete($id);
                    $deleted++;
                    error_log("[persistEmails][delete] administrator_id={$administratorId} contact_email_id={$id}");
                }
            }

            if ($totalIn > 0 && ($created + $updated + $deleted) === 0) {
                error_log("[persistEmails][warn] administrator_id={$administratorId} se recibieron {$totalIn} items pero NO se creó/actualizó/eliminó ninguno. Revisa claves del payload.");
            }

            error_log("[persistEmails][summary] administrator_id={$administratorId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

        } catch (\mysqli_sql_exception $e) {
            error_log("[persistEmails][fatal][mysqli] administrator_id={$administratorId} msg={$e->getMessage()}");
            throw $e;
        } catch (\Throwable $e) {
            error_log("[persistEmails][fatal] administrator_id={$administratorId} msg={$e->getMessage()}");
            throw $e;
        }
    }

    private function persistPhones(string $administratorId, $phones): void
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

            // Si viene country_code explícito, úsalo (formato +NNN o NNN)
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

        // helper para obtener prefijo desde country_id si tu clase tiene ese método
        $getCountryPrefix = function (?string $countryId): string {
            if (empty($countryId))
                return '';
            if (method_exists($this, 'getCountryNormalizedPrefix')) {
                return (string) $this->getCountryNormalizedPrefix($countryId); // '+NNN' o ''
            }
            return '';
        };

        try {
            // ===== Coerción flexible =====
            if (!is_array($phones)) {
                error_log("[persistPhones][warn] administrator_id={$administratorId} phones no es array; tipo=" . gettype($phones));
                if (is_string($phones)) {
                    $s = trim($phones);
                    $decoded = (($s !== '' && ($s[0] === '[' || $s[0] === '{')) ? json_decode($s, true) : null);
                    if (is_array($decoded)) {
                        $phones = $decoded;
                    } else {
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
            error_log("[persistPhones][start] administrator_id={$administratorId} total_in={$totalIn} sample=" . json_encode(array_slice($phones, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            $existing = $phoneModel->listIdsByEntity('administrator', $administratorId);
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
                    'entity_type' => 'administrator',
                    'entity_id' => $administratorId,
                    'phone_type' => 'mobile',
                    'country_code' => $countryCode,
                    'phone_number' => $fullNumber,
                    'is_primary' => $isPrimary,
                    'is_active' => $isActive,
                ];

                if ($contactPhoneId !== '') {
                    $phoneModel->update($contactPhoneId, $payload);
                    $updated++;
                    error_log("[persistPhones][update] administrator_id={$administratorId} contact_phone_id={$contactPhoneId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                } else {
                    $phoneModel->create($payload);
                    $created++;
                    error_log("[persistPhones][create] administrator_id={$administratorId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                }
            }

            // Borrado suave de los que no vinieron
            $incomingSet = array_flip($incomingIds);
            foreach ($existing as $id) {
                if (!isset($incomingSet[$id])) {
                    $phoneModel->delete($id);
                    $deleted++;
                    error_log("[persistPhones][delete] administrator_id={$administratorId} contact_phone_id={$id}");
                }
            }

            if ($totalIn > 0 && ($created + $updated + $deleted) === 0) {
                error_log("[persistPhones][warn] administrator_id={$administratorId} se recibieron {$totalIn} items pero NO se creó/actualizó/eliminó ninguno. Revisa claves del payload.");
            }

            error_log("[persistPhones][summary] administrator_id={$administratorId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

        } catch (\mysqli_sql_exception $e) {
            error_log("[persistPhones][fatal][mysqli] administrator_id={$administratorId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        } catch (\Throwable $e) {
            error_log("[persistPhones][fatal] administrator_id={$administratorId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
    }


    public function create(array $data)
    {
        // ===== Log de entrada (no exponer password) =====
        $logSnapshot = [
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'system_type' => strtoupper($data['system_type'] ?? 'US'),
            'timezone' => $data['timezone'] ?? 'America/Los_Angeles',
            'email_in' => isset($data['email']) ? strtolower(trim((string) $data['email'])) : null,
            // Aceptamos 'phone' o 'telephone' como input; en DB la columna es 'phone'
            'phone_in' => isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : null),
            'has_emails_key' => array_key_exists('emails', $data),
            'has_phones_key' => array_key_exists('phones', $data),
            'password_present' => !empty($data['password']),
        ];
        error_log('[admin.create][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->db->begin_transaction();

        try {
            /* ================== Traducciones ================== */
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $t = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            /* ============== Auditoría / Zona Horaria ============== */
            $actorUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $actorUserId);
            (new TimezoneManager($this->db))->applyTimezone();

            $createdAt = $env->getCurrentDatetime();
            $createdBy = $actorUserId;

            /* ================ Datos del administrador ================ */
            $uuid = $this->generateUUIDv4();
            $firstName = trim((string) ($data['first_name'] ?? ''));
            $lastName = trim((string) ($data['last_name'] ?? ''));
            $password = (string) ($data['password'] ?? '');
            $timezone = (string) ($data['timezone'] ?? 'America/Los_Angeles');
            $systemType = strtoupper((string) ($data['system_type'] ?? 'US'));

            // Compat: email/phone simples para columnas de administrators
            $emailIn = isset($data['email']) ? strtolower(trim((string) $data['email'])) : '';
            $phoneIn = isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : '');

            if ($firstName === '' || $lastName === '' || $password === '') {
                throw new \mysqli_sql_exception($t['missing_required_fields'] ?? 'Faltan campos requeridos.');
            }

            // Validar formato email si viene
            if ($emailIn !== '' && !filter_var($emailIn, FILTER_VALIDATE_EMAIL)) {
                throw new \mysqli_sql_exception($t['invalid_email_format'] ?? 'Formato de email inválido.');
            }

            // Validar duplicidad de email si viene
            if ($emailIn !== '') {
                $check = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE email = ? AND deleted_at IS NULL LIMIT 1");
                if (!$check) {
                    error_log('[admin.create][prepare-error] validar email | ' . $this->db->error);
                    throw new \mysqli_sql_exception(($t['error_preparing_select'] ?? 'Error preparando consulta: ') . $this->db->error);
                }
                $check->bind_param("s", $emailIn);
                if (!$check->execute()) {
                    error_log('[admin.create][execute-error] validar email | ' . $check->error);
                    throw new \mysqli_sql_exception(($t['error_executing_select'] ?? 'Error ejecutando consulta: ') . $check->error);
                }
                $check->store_result();
                if ($check->num_rows > 0) {
                    $check->close();
                    error_log("[admin.create][dup-email] email={$emailIn}");
                    throw new \mysqli_sql_exception($t['email_already_exists'] ?? 'Este correo ya está registrado.');
                }
                $check->close();
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            /* ================ Insert en administrators ================ */
            $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (administrator_id, first_name, last_name, email, phone, password, system_type, timezone, interface_language, created_at, created_by)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
            if (!$stmt) {
                error_log('[admin.create][prepare-error] insert admin | ' . $this->db->error);
                throw new \mysqli_sql_exception(($t['error_preparing_insert'] ?? 'Error preparando insert: ') . $this->db->error);
            }

            $interfaceLanguage = strtoupper($data['language'] ?? 'EN');
            $stmt->bind_param(
                "sssssssssss",
                $uuid,
                $firstName,
                $lastName,
                $emailIn,   // scalar email directo a columna
                $phoneIn,   // scalar phone/telephone directo a columna
                $hashedPassword,
                $systemType,
                $timezone,
                $interfaceLanguage,
                $createdAt,
                $createdBy
            );

            if (!$stmt->execute()) {
                error_log('[admin.create][execute-error] insert admin | ' . $stmt->error);
                throw new \mysqli_sql_exception(($t['error_executing_insert'] ?? 'Error ejecutando insert: ') . $stmt->error);
            }
            $stmt->close();

            /* ================ Persistir colecciones (N) ================ */
            // Importante: SOLO si llegan explícitamente 'emails' / 'phones'
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($uuid, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($uuid, $data['phones']);
            }
            $this->db->commit();

            error_log("[admin.create][success] administrator_id={$uuid}");
            return ['administrator_id' => $uuid];

        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            error_log("[admin.create][error] msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
    }







    public function registerAdmin($data)
    {
        $this->db->begin_transaction();
        try {
            // Cargar idioma
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $translations = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            // Validar email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new mysqli_sql_exception($translations['invalid_email_format']);
            }

            // Validar contraseña
            if (strlen($data['password']) < 8) {
                throw new mysqli_sql_exception($translations['password_too_short']);
            }

            // Verificar email duplicado solo en registros activos
            $checkEmail = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE email = ? ");
            if (!$checkEmail) {
                throw new mysqli_sql_exception($translations['error_preparing_email'] . $this->db->error);
            }
            $checkEmail->bind_param("s", $data['email']);
            $checkEmail->execute();
            $checkEmail->store_result();
            if ($checkEmail->num_rows > 0) {
                throw new mysqli_sql_exception($translations['email_already_registered']);
            }
            $checkEmail->close();

            // Verificar teléfono duplicado solo en registros activos
            $checkPhone = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE phone = ? ");
            if (!$checkPhone) {
                throw new mysqli_sql_exception($translations['error_preparing_phone'] . $this->db->error);
            }
            $checkPhone->bind_param("s", $data['phone']);
            $checkPhone->execute();
            $checkPhone->store_result();
            if ($checkPhone->num_rows > 0) {
                throw new mysqli_sql_exception($translations['phone_already_registered']);
            }
            $checkPhone->close();

            // Auditoría y valores automáticos
            $env = new ClientEnvironmentInfo();
            $env->applyAuditContext($this->db, 0);
            (new TimezoneManager($this->db))->applyTimezone();

            $createdAt = $env->getCurrentDatetime();
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $systemType = 'US';
            $timezone = $data['timezone'] ?? 'America/Los_Angeles';

            // Generar UUID
            $uuid = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (administrator_id, first_name, last_name, email, phone, password, system_type, timezone, interface_language, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception($translations['error_preparing_insert'] . $this->db->error);
            }

            $interfaceLanguage = strtoupper($data['language'] ?? 'EN');
            $stmt->bind_param(
                "sssssssssss",
                $uuid,
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'],
                $hashedPassword,
                $systemType,
                $timezone,
                $interfaceLanguage,
                $createdAt,
                $uuid // El mismo UUID como created_by
            );

            $stmt->execute();
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




    public function update(string $id, array $data)
    {
        // ===== Log de entrada (no exponer password) =====
        $logSnapshot = [
            'administrator_id' => $id,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'system_type' => strtoupper($data['system_type'] ?? 'US'),
            'timezone' => $data['timezone'] ?? 'America/Los_Angeles',
            'email_in' => isset($data['email']) ? strtolower(trim((string) $data['email'])) : null,
            // Aceptamos 'phone' o 'telephone' como input; en DB la columna es 'phone'
            'phone_in' => isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : null),
            'has_emails_key' => array_key_exists('emails', $data),
            'has_phones_key' => array_key_exists('phones', $data),
            'password_present' => !empty($data['password']),
            'status_in' => array_key_exists('status', $data) ? (int) ($data['status'] === 1 || $data['status'] === '1') : null,
        ];
        error_log('[admin.update][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $this->db->begin_transaction();

        try {
            /* ================== Traducciones ================== */
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $langPath = PROJECT_ROOT . "/lang/{$lang}.php";
            $t = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . "/lang/EN.php";

            /* ============ Verificar existencia del admin ============ */
            $check = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE administrator_id = ? LIMIT 1");
            if (!$check) {
                error_log('[admin.update][prepare-error] verificar existencia | ' . $this->db->error);
                throw new \mysqli_sql_exception(($t['error_preparing_check'] ?? 'Error preparando verificación: ') . $this->db->error);
            }
            $check->bind_param("s", $id);
            if (!$check->execute()) {
                error_log('[admin.update][execute-error] verificar existencia | ' . $check->error);
                throw new \mysqli_sql_exception(($t['error_executing_check'] ?? 'Error ejecutando verificación: ') . $check->error);
            }
            $check->store_result();
            if ($check->num_rows === 0) {
                $check->close();
                error_log("[admin.update][not-found] administrator_id={$id}");
                throw new \mysqli_sql_exception($t['administrator_not_found'] ?? 'Administrador no encontrado.');
            }
            $check->close();

            /* ============== Auditoría / Zona Horaria ============== */
            $actorUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $actorUserId);
            (new TimezoneManager($this->db))->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $actorUserId;

            /* ================ Datos del administrador ================ */
            $firstName = trim((string) ($data['first_name'] ?? ''));
            $lastName = trim((string) ($data['last_name'] ?? ''));
            $timezone = (string) ($data['timezone'] ?? 'America/Los_Angeles');
            $systemType = strtoupper((string) ($data['system_type'] ?? 'US'));

            // Compat: email/phone simples para columnas de administrators
            $emailIn = isset($data['email']) ? strtolower(trim((string) $data['email'])) : '';
            $phoneIn = isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : '');

            // Validar formato email si viene
            if ($emailIn !== '' && !filter_var($emailIn, FILTER_VALIDATE_EMAIL)) {
                error_log("[admin.update][invalid-email-format] email={$emailIn}");
                throw new \mysqli_sql_exception($t['invalid_email_format'] ?? 'Formato de email inválido.');
            }

            // Validar duplicidad de email si viene (excluyendo el propio id)
            if ($emailIn !== '') {
                $dup = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE email = ? AND administrator_id <> ? AND deleted_at IS NULL LIMIT 1");
                if (!$dup) {
                    error_log('[admin.update][prepare-error] validar email | ' . $this->db->error);
                    throw new \mysqli_sql_exception(($t['error_preparing_select'] ?? 'Error preparando consulta: ') . $this->db->error);
                }
                $dup->bind_param("ss", $emailIn, $id);
                if (!$dup->execute()) {
                    error_log('[admin.update][execute-error] validar email | ' . $dup->error);
                    throw new \mysqli_sql_exception(($t['error_executing_select'] ?? 'Error ejecutando consulta: ') . $dup->error);
                }
                $dup->store_result();
                if ($dup->num_rows > 0) {
                    $dup->close();
                    error_log("[admin.update][dup-email] email={$emailIn}");
                    throw new \mysqli_sql_exception($t['email_already_exists'] ?? 'Este correo ya está registrado.');
                }
                $dup->close();
            }

            /* ============== Update base ============== */
            // status es opcional; si no se envía, no lo modificamos
            $interfaceLanguage = strtoupper($data['language'] ?? 'EN');
            $setParts = [
                "first_name = ?",
                "last_name  = ?",
                "email      = ?",
                "phone      = ?",
                "system_type= ?",
                "timezone   = ?",
                "interface_language = ?",
                "updated_at = ?",
                "updated_by = ?",
            ];
            $types = "sssssssss";
            $params = [
                $firstName,
                $lastName,
                $emailIn,
                $phoneIn,
                $systemType,
                $timezone,
                $interfaceLanguage,
                $updatedAt,
                $updatedBy
            ];

            // password opcional
            if (!empty($data['password'])) {
                $setParts[] = "password = ?";
                $types .= "s";
                $params[] = password_hash((string) $data['password'], PASSWORD_DEFAULT);
            }

            // status opcional (si viene la llave en el payload)
            if (array_key_exists('status', $data)) {
                $setParts[] = "status = ?";
                $types .= "i";
                $params[] = (int) ($data['status'] === 1 || $data['status'] === '1');
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE administrator_id = ?";
            $types .= "s";
            $params[] = $id;

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log('[admin.update][prepare-error] update admin | ' . $this->db->error);
                throw new \mysqli_sql_exception(($t['error_preparing_update_statement'] ?? 'Error preparando update: ') . $this->db->error);
            }

            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                error_log('[admin.update][execute-error] update admin | ' . $stmt->error);
                throw new \mysqli_sql_exception(($t['error_executing_update'] ?? 'Error ejecutando update: ') . $stmt->error);
            }
            $stmt->close();

            /* ================ Persistir colecciones (N) ================ */
            // Importante: SOLO si llegan explícitamente 'emails' / 'phones' (igual que create)
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($id, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($id, $data['phones']);
            }

            $this->db->commit();
            error_log("[admin.update][success] administrator_id={$id}");
            return true;

        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            error_log("[admin.update][error] msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
    }






    public function updateProfile($id, $data)
    {
        // ===== Log de entrada (no exponer password) =====
        $logSnapshot = [
            'administrator_id' => $id,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'system_type' => strtoupper($data['system_type'] ?? 'US'),
            'timezone' => $data['timezone'] ?? 'America/Los_Angeles',
            'email_in' => isset($data['email']) ? strtolower(trim((string) $data['email'])) : null,
            // Aceptamos 'phone' o 'telephone' como input; en DB la columna es 'phone'
            'phone_in' => isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : null),
            'status_in' => $data['status'] ?? null,
            'password_present' => !empty($data['password']),
        ];
        error_log('[admin.updateProfile][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        try {
            $this->db->begin_transaction();

            // ===== Asignar valores base =====
            $this->id = $id;
            $this->first_name = $data['first_name'] ?? '';
            $this->last_name = $data['last_name'] ?? '';
            $this->system_type = strtoupper($data['system_type'] ?? 'US');
            $this->timezone = $data['timezone'] ?? 'America/Los_Angeles';
            $this->email = isset($data['email']) ? trim(strtolower((string) $data['email'])) : '';
            // Preferimos 'phone', pero aceptamos 'telephone' como alias de entrada
            $this->phone = isset($data['phone']) ? trim((string) $data['phone'])
                : (isset($data['telephone']) ? trim((string) $data['telephone']) : '');
            // status es opcional; si no viene, no lo tocamos
            $statusIn = isset($data['status']) ? (string) $data['status'] : null;

            // ===== Auditoría y zona horaria =====
            $actorUserId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $actorUserId);
            (new TimezoneManager($this->db))->applyTimezone();
            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $actorUserId;

            // ===== Validar duplicidad de email (solo si no vacío) =====
            if ($this->email !== '') {
                $check = $this->db->prepare("SELECT administrator_id FROM {$this->table} WHERE email = ? AND administrator_id != ? AND deleted_at IS NULL");
                if (!$check) {
                    error_log('[admin.updateProfile][prepare-error] validar email | ' . $this->db->error);
                    throw new \Exception('Error al preparar validación de email: ' . $this->db->error);
                }
                $check->bind_param("ss", $this->email, $this->id);
                if (!$check->execute()) {
                    error_log('[admin.updateProfile][execute-error] validar email | ' . $check->error);
                    throw new \Exception('Error ejecutando validación de email: ' . $check->error);
                }
                $check->store_result();
                if ($check->num_rows > 0) {
                    $check->close();
                    error_log("[admin.updateProfile][dup-email] administrator_id={$this->id} email={$this->email}");
                    throw new \Exception('Este correo ya está registrado por otro administrador.');
                }
                $check->close();
            }

            // ===== UPDATE (email y phone SIEMPRE incluidos) =====
            $interfaceLanguage = strtoupper($data['language'] ?? 'EN');
            $sql = "UPDATE {$this->table} SET
                    first_name   = ?,
                    last_name    = ?,
                    system_type  = ?,
                    timezone     = ?,
                    interface_language = ?,
                    email        = ?,
                    phone        = ?,
                    updated_at   = ?,
                    updated_by   = ?";

            $params = [
                $this->first_name,
                $this->last_name,
                $this->system_type,
                $this->timezone,
                $interfaceLanguage,
                $this->email,
                $this->phone,
                $updatedAt,
                $updatedBy
            ];
            $types = "sssssssss";

            // status opcional (respetando la estructura de la tabla)
            if ($statusIn !== null) {
                $sql .= ", status = ?";
                $params[] = $statusIn;
                $types .= "s";
            }

            // Contraseña opcional
            if (!empty($data['password'])) {
                $hashedPassword = password_hash((string) $data['password'], PASSWORD_DEFAULT);
                $sql .= ", password = ?";
                $params[] = $hashedPassword;
                $types .= "s";
            }

            // WHERE
            $sql .= " WHERE administrator_id = ?";
            $params[] = $this->id;
            $types .= "s";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                error_log('[admin.updateProfile][prepare-error] update admin | ' . $this->db->error);
                throw new \Exception('Error al preparar consulta: ' . $this->db->error);
            }
            $stmt->bind_param($types, ...$params);
            if (!$stmt->execute()) {
                error_log('[admin.updateProfile][execute-error] update admin | ' . $stmt->error);
                throw new \Exception('No se pudo actualizar el administrador: ' . $stmt->error);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();

            // ===== Persistir colecciones si llegaron (entity = 'administrator') =====
            // Aceptamos 'emails' y 'phones'; si solo viene 'email' o 'telephone/phone' simple, los ignoramos aquí.
            // ===== Persistir colecciones si llegaron =====
            if (array_key_exists('emails', $data)) {
                $this->persistEmails($id, $data['emails']);
            }
            if (array_key_exists('phones', $data)) {
                $this->persistPhones($id, $data['phones']);
            }


            $this->db->commit();

            // ===== Actualizar sesión (desde los valores normalizados) =====
            $_SESSION['first_name'] = $this->first_name;
            $_SESSION['last_name'] = $this->last_name;
            $_SESSION['user_name'] = $this->first_name . ' ' . $this->last_name;
            $_SESSION['system_type'] = $this->system_type;
            $_SESSION['timezone'] = $this->timezone;
            $_SESSION['email'] = $this->email;
            $_SESSION['phone'] = $this->phone; // << usa 'phone' (no 'telephone')

            error_log("[admin.updateProfile][success] administrator_id={$this->id} affected_rows={$affected}");
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            error_log("[admin.updateProfile][error] administrator_id={$id} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
            throw $e;
        }
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
            $checkStmt = $this->db->prepare("SELECT administrator_id  FROM {$this->table} WHERE administrator_id  = ? LIMIT 1");
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
            $stmt = $this->db->prepare("UPDATE {$this->table} SET system_type = ?, updated_at = ?, updated_by = ? WHERE administrator_id  = ?");
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


    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            // Cargar idioma
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $archivo_idioma = PROJECT_ROOT . "/lang/{$lang}.php";
            $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

            $userId = $_SESSION['user_id'] ?? null;

            // Verificar dependencias en security_questions
            $stmtCheck = $this->db->prepare("
            SELECT COUNT(*) as total
            FROM security_questions
            WHERE user_type = 'Administrator' AND user_id_admin = ? AND deleted_at IS NULL
        ");
            if (!$stmtCheck) {
                throw new mysqli_sql_exception("Error preparando la consulta de dependencias: " . $this->db->error);
            }

            $stmtCheck->bind_param("s", $id);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
            $stmtCheck->close();

            if ($row['total'] > 0) {
                $msg = $traducciones['administrator_delete_dependency']
                    ?? "Cannot delete administrator: related security questions exist.";
                throw new mysqli_sql_exception($msg);
            }

            // Auditoría
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $userId;

            // Eliminación lógica
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE administrator_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error eliminando el administrador: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getSessionUserData($adminId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE administrator_id  = ? AND deleted_at IS NULL");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $adminId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [
                    'status' => 'error',
                    'message' => 'Administrador no encontrado'
                ];
            }

            $admin_data = $result->fetch_assoc();

            $imagePathRel = "uploads/administrator/user_" . $admin_data['administrator_id'] . ".jpg";
            if (file_exists(PROJECT_ROOT . '/' . $imagePathRel)) {
                $admin_data['profile_image_url'] = $imagePathRel . '?v=' . filemtime(PROJECT_ROOT . '/' . $imagePathRel);
            } else {
                $admin_data['profile_image_url'] = "public/assets/images/users/user_boy.svg";
            }

            return $admin_data;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
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

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE administrator_id  = ?");
            $stmt->bind_param("ssss", $hashedPassword, $updatedAt, $updatedBy, $userId);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            if ($success) {
                $stmt = $this->db->prepare("SELECT email FROM {$this->table} WHERE administrator_id  = ?");
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

            $stmt = $this->db->prepare("SELECT administrator_id  FROM {$this->table} WHERE email = ?");
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

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE administrator_id  = ?");
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

    public function updateStatus(array $data): bool
    {
        $administratorId = $data['administrator_id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if ($administratorId === null || !in_array($newStatus, [0, 1], true)) {
            return false;
        }

        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $administratorId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $administratorId;

            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = ?, updated_by = ? WHERE administrator_id = ?");
            if (!$stmt) {
                throw new Exception("Error preparing status update: " . $this->db->error);
            }

            $stmt->bind_param("ssss", $newStatus, $updatedAt, $updatedBy, $administratorId);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            return $success;
        } catch (\Throwable $e) {
            error_log("Failed to update status: " . $e->getMessage());
            return false;
        }
    }



    public function authenticate($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                unset($admin['password']);
                return $admin;
            }
        }
        return null;
    }
}
