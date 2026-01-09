<?php

require_once __DIR__ . '/../config/Database.php';
// === nuevos: modelos de contacto ===

class UserModel
{
    private $db;
    private $table = "users";

    public function __construct()
    {
        $this->db = Database::getInstance();

    }

    public function getAll()
    {
        try {
            $query = "SELECT * 
                  FROM {$this->table} 
                  WHERE deleted_at IS NULL";

            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener usuarios: " . $this->db->error);
            }

            $users = [];
            while ($row = $result->fetch_assoc()) {
                // === Agregar emails y phones relacionados ===
                require_once __DIR__ . '/ContactEmailModel.php';
                require_once __DIR__ . '/ContactPhoneModel.php';

                $emailModel = new ContactEmailModel();
                $phoneModel = new ContactPhoneModel();

                $row['emails'] = $emailModel->getByEntity('user', $row['user_id']);
                $row['phones'] = $phoneModel->getByEntity('user', $row['user_id']);
                $users[] = $row;
            }

            return $users;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }



    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $id);
            $stmt->execute();

            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            if ($data) {
                // === Agregar emails y phones relacionados ===
                require_once __DIR__ . '/ContactEmailModel.php';
                require_once __DIR__ . '/ContactPhoneModel.php';

                $emailModel = new ContactEmailModel();
                $phoneModel = new ContactPhoneModel();

                $data['emails'] = $emailModel->getByEntity('user', $data['user_id']);
                $data['phones'] = $phoneModel->getByEntity('user', $data['user_id']);
            }


            return $data;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

        public function getUserBy($column, $value)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $column = ? LIMIT 1");
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
public function getUserByEmail($email)
{
    try {
        $query = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (mysqli_sql_exception $e) {
        throw $e;
    }
}

    public function updateStatus(array $data): bool
    {
        $userId = $data['user_id'] ?? null;
        $newStatus = $data['status'] ?? null;

        if (!in_array($newStatus, [0, 1], true) || empty($userId)) {
            return false;
        }

        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();
            $updatedBy = $userId;

            $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("isss", $newStatus, $updatedAt, $updatedBy, $userId);
            $stmt->execute();

            $success = $stmt->affected_rows > 0;
            $stmt->close();

            return $success;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }


public function getUserByTelephone($telephone)
{
    try {
        // El teléfono ya viene limpio del frontend, como "584249173469"
        $normalizedTelephone = $telephone;

        // Reemplazar en la consulta los caracteres especiales
        $query = "
            SELECT * FROM {$this->table} 
            WHERE 
                REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(telephone, '(', ''), ')', ''
                                ), '-', ''
                            ), ' ', ''
                        ), '+', ''
                    ), '.', ''
                ) = ?
                AND deleted_at IS NULL
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

    public function countUsers()
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM {$this->table}";
            $result = $this->db->query($sql);

            if (!$result) {
                throw new \mysqli_sql_exception("Error al contar usuarios: " . $this->db->error);
            }

            $row = $result->fetch_assoc();
            return (int) $row['total'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getAllCountries(): array
    {
        try {
            $sql = "SELECT * FROM countries";
            $result = $this->db->query($sql);

            if (!$result) {
                throw new \mysqli_sql_exception("Error al obtener países: " . $this->db->error);
            }

            $countries = [];
            while ($row = $result->fetch_assoc()) {
                $countries[] = $row;
            }

            return $countries;
        } catch (\Exception $e) {
            return [];
        }
    }


    public function setIdioma(string $lang): bool
    {
        try {
            $lang = strtoupper(trim($lang));
            if (!in_array($lang, ['EN', 'ES'])) {
                throw new \InvalidArgumentException("Idioma no válido: $lang");
            }

            $_SESSION['idioma'] = $lang;
            return true;
        } catch (\Exception $e) {
            // Puedes loguear el error si es necesario: error_log($e->getMessage());
            return false;
        }
    }

     private function getCountryNormalizedPrefix(?string $countryId): ?string
    {
        $countryId = trim((string)$countryId);
        if ($countryId === '') return null;

        $sql = "SELECT normalized_prefix 
                FROM countries 
                WHERE country_id = ? AND deleted_at IS NULL 
                LIMIT 1";

        if (!$stmt = $this->db->prepare($sql)) {
            return null;
        }

        $stmt->bind_param("s", $countryId);
        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $stmt->bind_result($prefix);
        $found = $stmt->fetch();
        $stmt->close();

        return $found ? (string)$prefix : null;
    }

    /**
 * Intenta decodificar JSON de manera segura.
 */
private function decodeJsonSafely(?string $str)
{
    if ($str === null) return null;
    $str = trim($str);
    if ($str === '') return null;

    // Sólo intentamos si "parece" JSON
    $first = $str[0];
    if ($first !== '{' && $first !== '[') return null;

    $decoded = json_decode($str, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
}

/**
 * Coerce emails input a un array de items con clave 'email', etc.
 * Acepta:
 *   - array de items
 *   - objeto/array simple con 'email'
 *   - string JSON (array u objeto)
 *   - string simple tipo "user@example.com"
 */
private function coerceEmailsInput($emails): array
{
    // 1) Si es string, prueba como JSON; si no es JSON y parece email, envuélvelo
    if (is_string($emails)) {
        $maybe = $this->decodeJsonSafely($emails);
        if ($maybe !== null) {
            $emails = $maybe;
        } else {
            // ¿Es un email simple?
            $s = trim($emails);
            if ($s !== '' && strpos($s, '@') !== false) {
                return [['email' => $s, 'is_primary' => 1, 'is_active' => 1]];
            }
            return []; // string no útil
        }
    }

    // 2) Si es objeto tipo array asociativo con 'email', envuélvelo
    if (is_array($emails) && isset($emails['email'])) {
        return [$emails];
    }

    // 3) Si es array pero sus elementos no son arrays, intenta mapearlos
    if (is_array($emails)) {
        // Si el primer elemento es string (lista de emails separados por coma, etc.)
        if (!empty($emails) && is_string(reset($emails))) {
            $out = [];
            foreach ($emails as $e) {
                $e = trim((string)$e);
                if ($e !== '' && strpos($e, '@') !== false) {
                    $out[] = ['email' => $e, 'is_primary' => 0, 'is_active' => 1];
                }
            }
            return $out;
        }
        return $emails; // ya es array de items
    }

    return [];
}

/**
 * Coerce phones input a array de items con 'phone_number'/'telephone' y 'country_code' (o derivable).
 * Acepta:
 *   - array de items
 *   - objeto/array simple con 'telephone'/'phone_number'
 *   - string JSON (array u objeto)
 *   - string simple tipo "+1 555-0000" (se guardará con country_code = '' si no se puede inferir)
 */
private function coercePhonesInput($phones): array
{
    if (is_string($phones)) {
        $maybe = $this->decodeJsonSafely($phones);
        if ($maybe !== null) {
            $phones = $maybe;
        } else {
            $s = trim($phones);
            if ($s !== '') {
                // Podemos aceptar un único número; sin country_code si no lo trae:
                return [[
                    'telephone'    => $s,
                    'country_code' => '', // se intentará completar luego si viene country_id
                    'is_primary'   => 1,
                    'is_active'    => 1,
                ]];
            }
            return [];
        }
    }

    if (is_array($phones) && (isset($phones['telephone']) || isset($phones['phone_number']))) {
        return [$phones];
    }

    if (is_array($phones)) {
        if (!empty($phones) && is_string(reset($phones))) {
            $out = [];
            foreach ($phones as $p) {
                $p = trim((string)$p);
                if ($p !== '') {
                    $out[] = [
                        'telephone'    => $p,
                        'country_code' => '',
                        'is_primary'   => 0,
                        'is_active'    => 1,
                    ];
                }
            }
            return $out;
        }
        return $phones;
    }

    return [];
}


private function persistEmails(string $userId, $emails): void
{
    require_once __DIR__ . '/ContactEmailModel.php';
    require_once __DIR__ . '/ContactPhoneModel.php';

    $emailModel = new ContactEmailModel();
    $phoneModel = new ContactPhoneModel();

    try {
        // ===== Coerción flexible =====
        if (!is_array($emails)) {
            error_log("[persistEmails][warn] user_id={$userId} emails no es array; tipo=" . gettype($emails));
            // Intentar decodificar JSON si "parece" JSON
            if (is_string($emails)) {
                $s = trim($emails);
                if ($s !== '' && ($s[0] === '[' || $s[0] === '{')) {
                    $decoded = json_decode($s, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $emails = $decoded;
                    }
                }
                // Si tras intentar JSON sigue sin ser array: caso email simple
                if (!is_array($emails)) {
                    $s = trim((string)$emails);
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

        // Si vino un objeto asociativo con 'email', envolverlo
        if (is_array($emails) && isset($emails['email'])) {
            $emails = [$emails];
        }
        // Si vino un array de strings, mapearlos a items
        if (is_array($emails) && !empty($emails) && is_string(reset($emails))) {
            $emails = array_values(array_filter(array_map(function ($e) {
                $e = trim((string)$e);
                if ($e !== '' && strpos($e, '@') !== false) {
                    return ['email' => $e, 'is_primary' => 0, 'is_active' => 1];
                }
                return null;
            }, $emails)));
        }

        $totalIn = count($emails);
        error_log("[persistEmails][start] user_id={$userId} total_in={$totalIn} sample=" . json_encode(array_slice($emails, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $existing    = $emailModel->listIdsByEntity('user', $userId);
        $incomingIds = [];

        $created = 0; $updated = 0; $deleted = 0; $skipped = 0;

        foreach ($emails as $idx => $item) {
            if (!is_array($item)) {
                $skipped++;
                error_log("[persistEmails][skip] idx={$idx} motivo=item no es array");
                continue;
            }

            $contactEmailId = trim((string)($item['contact_email_id'] ?? ''));
            $email          = isset($item['email']) ? trim((string)$item['email']) : '';

            $isPrimary = isset($item['is_primary']) ? (int)((string)$item['is_primary'] === '1' || $item['is_primary'] === 1 || $item['is_primary'] === true) : 0;
            $isActive  = isset($item['is_active'])  ? (int)((string)$item['is_active']  === '1' || $item['is_active']  === 1 || $item['is_active']  === true) : 1;

            if ($contactEmailId !== '') $incomingIds[] = $contactEmailId;

            if ($email === '') {
                $skipped++;
                error_log("[persistEmails][skip] idx={$idx} motivo=email vacío | item=" . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                continue;
            }

            $payload = [
                'entity_type' => 'user',
                'entity_id'   => $userId,
                'email'       => $email,
                'is_primary'  => $isPrimary,
                'is_active'   => $isActive,
            ];

            if ($contactEmailId !== '') {
                $emailModel->update($contactEmailId, $payload);
                $updated++;
                error_log("[persistEmails][update] user_id={$userId} contact_email_id={$contactEmailId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } else {
                $emailModel->create($payload);
                $created++;
                error_log("[persistEmails][create] user_id={$userId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }

        $incomingSet = array_flip($incomingIds);
        foreach ($existing as $id) {
            if (!isset($incomingSet[$id])) {
                $emailModel->delete($id);
                $deleted++;
                error_log("[persistEmails][delete] user_id={$userId} contact_email_id={$id}");
            }
        }

        if ($totalIn > 0 && ($created + $updated + $deleted) === 0) {
            error_log("[persistEmails][warn] user_id={$userId} se recibieron {$totalIn} items pero NO se creó/actualizó/eliminó ninguno. Revisa claves del payload.");
        }

        error_log("[persistEmails][summary] user_id={$userId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

    } catch (\mysqli_sql_exception $e) {
        error_log("[persistEmails][fatal][mysqli] user_id={$userId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    } catch (\Throwable $e) {
        error_log("[persistEmails][fatal] user_id={$userId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    }
}
private function persistPhones(string $userId, $phones): void
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
        $national    = '';

        // Si viene country_code explícito, úsalo (formato +NNN)
        if (!empty($givenCountryCode)) {
            $cc = trim($givenCountryCode);
            if (preg_match('/^\+?\d{1,3}$/', $cc)) {
                $countryCode = (str_starts_with($cc, '+') ? $cc : ('+'.$cc));
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
                $national    = preg_replace('/\D+/', '', $m[2]);
            } elseif (preg_match('/^00(\d{1,3})[^\d]*([0-9][0-9\-\.\s]+)$/', $candidate, $m2)) {
                $countryCode = '+' . $m2[1];
                $national    = preg_replace('/\D+/', '', $m2[2]);
            }
        }

        // Si ya tenemos countryCode pero no nacional, sácalo del raw
        if ($countryCode !== '' && $national === '') {
            $noCC = preg_replace('/^\+?'.preg_quote(ltrim($countryCode, '+'), '/').'[^\d]*/', '', $raw);
            $noCC = preg_replace('/^\(\\+'.preg_quote(ltrim($countryCode, '+'), '/').'\)\s*/', '', $noCC);
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

    try {
        // ===== Coerción flexible =====
        if (!is_array($phones)) {
            error_log("[persistPhones][warn] user_id={$userId} phones no es array; tipo=" . gettype($phones));
            if (is_string($phones)) {
                $s = trim($phones);
                $decoded = ( ($s !== '' && ($s[0] === '[' || $s[0] === '{')) ? json_decode($s, true) : null );
                if (is_array($decoded)) {
                    $phones = $decoded;
                } else {
                    $s = trim((string)$phones);
                    $phones = ($s !== '') ? [[
                        'telephone'    => $s, // aceptamos string simple
                        'is_primary'   => 1,
                        'is_active'    => 1,
                    ]] : [];
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
                $p = trim((string)$p);
                return $p !== '' ? ['telephone' => $p, 'is_primary' => 0, 'is_active' => 1] : null;
            }, $phones)));
        }

        $totalIn = count($phones);
        error_log("[persistPhones][start] user_id={$userId} total_in={$totalIn} sample=" . json_encode(array_slice($phones, 0, 2), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $existing    = $phoneModel->listIdsByEntity('user', $userId);
        $incomingIds = [];

        $created = 0; $updated = 0; $deleted = 0; $skipped = 0;

        foreach ($phones as $idx => $item) {
            if (!is_array($item)) {
                $skipped++; error_log("[persistPhones][skip] idx={$idx} motivo=item no es array");
                continue;
            }

            $contactPhoneId = trim((string)($item['contact_phone_id'] ?? ''));

            // Obtener número bruto desde cualquiera de las llaves
            $rawNumber = '';
            if (isset($item['telephone']))         $rawNumber = (string)$item['telephone'];
            elseif (isset($item['phone_number']))  $rawNumber = (string)$item['phone_number'];
            $rawNumber = trim($rawNumber);

            // Flags
            $isPrimary = isset($item['is_primary']) ? (int)((string)$item['is_primary'] === '1' || $item['is_primary'] === 1 || $item['is_primary'] === true) : 0;
            $isActive  = isset($item['is_active'])  ? (int)((string)$item['is_active']  === '1' || $item['is_active']  === 1 || $item['is_active']  === true) : 1;

            // country_code explícito o derivado por country_id (si llega partido)
            $countryCodeGiven = '';
            if (!empty($item['country_code'])) {
                $countryCodeGiven = trim((string)$item['country_code']);
            } elseif (!empty($item['country_id'])) {
                $countryCodeGiven = $this->getCountryNormalizedPrefix(trim((string)$item['country_id'])); // '+NNN' o ''
            }

            // === Normalización / parsing ===
            [$countryCode, $national] = $parsePhone($rawNumber, $countryCodeGiven);

            if ($contactPhoneId !== '') $incomingIds[] = $contactPhoneId;

            if ($national === '') {
                $skipped++;
                error_log("[persistPhones][skip] idx={$idx} motivo=phone_number vacío tras normalizar | raw={$rawNumber} item=" . json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                continue;
            }

            // Último intento de extraer código si aún está vacío
            if ($countryCode === '') {
                $digits = preg_replace('/\D+/', '', ($rawNumber[0] === '+' ? $rawNumber : '+'.$rawNumber));
                if (preg_match('/^\+?(\d{1,3})(\d{5,})$/', '+'.$digits, $mm)) {
                    $countryCode = '+' . $mm[1];
                    $national    = $mm[2];
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
                'entity_type'  => 'user',
                'entity_id'    => $userId,
                'phone_type'   => 'mobile',
                'country_code' => $countryCode, // requerido por el modelo
                'phone_number' => $fullNumber,  // ← guardamos combinado
                'is_primary'   => $isPrimary,
                'is_active'    => $isActive,
            ];

            if ($contactPhoneId !== '') {
                $phoneModel->update($contactPhoneId, $payload);
                $updated++;
                error_log("[persistPhones][update] user_id={$userId} contact_phone_id={$contactPhoneId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } else {
                $phoneModel->create($payload);
                $created++;
                error_log("[persistPhones][create] user_id={$userId} payload=" . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }

        // Borrado suave de los que no vinieron
        $incomingSet = array_flip($incomingIds);
        foreach ($existing as $id) {
            if (!isset($incomingSet[$id])) {
                $phoneModel->delete($id);
                $deleted++;
                error_log("[persistPhones][delete] user_id={$userId} contact_phone_id={$id}");
            }
        }

        if ($totalIn > 0 && ($created + $updated + $deleted) === 0) {
            error_log("[persistPhones][warn] user_id={$userId} se recibieron {$totalIn} items pero NO se creó/actualizó/eliminó ninguno. Revisa claves del payload.");
        }

        error_log("[persistPhones][summary] user_id={$userId} total_in={$totalIn} created={$created} updated={$updated} deleted={$deleted} skipped={$skipped}");

    } catch (\mysqli_sql_exception $e) {
        error_log("[persistPhones][fatal][mysqli] user_id={$userId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    } catch (\Throwable $e) {
        error_log("[persistPhones][fatal] user_id={$userId} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    }
}



   public function create(array $data)
{
    // ===== Log de entrada (no exponer password) =====
    $logSnapshot = [
        'first_name'        => $data['first_name'] ?? null,
        'last_name'         => $data['last_name'] ?? null,
        'sex_biological'               => $data['sex_biological'] ?? null,
        'birthday'          => $data['birthday'] ?? null,
        'system_type'       => strtoupper($data['system_type'] ?? 'US'),
        'timezone'          => $data['timezone'] ?? 'America/Los_Angeles',
        'email_in'          => isset($data['email']) ? strtolower(trim((string)$data['email'])) : null,
        'telephone_in'      => isset($data['telephone']) ? trim((string)$data['telephone']) : null,
        'height_present'    => isset($data['height']),
        'password_present'  => !empty($data['password']),
        'has_emails_array'  => array_key_exists('emails', $data),
        'has_phones_array'  => array_key_exists('phones', $data) || array_key_exists('telephones', $data),
    ];
    error_log('[user.create][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    $this->db->begin_transaction();
    try {
        // ===== Normalización base =====
        $uuid               = $this->generateUUIDv4();
        $firstName          = $data['first_name'] ?? '';
        $lastName           = $data['last_name'] ?? '';
        $sex_biological                = $data['sex_biological'] ?? '';
        $birthday           = $data['birthday'] ?? '';
        $systemType         = strtoupper($data['system_type'] ?? 'US');
        $timezone           = $data['timezone'] ?? 'America/Los_Angeles';

        // Email/telephone SIEMPRE como texto plano normalizado (igual a updateProfile)
        $email              = isset($data['email']) ? trim(strtolower((string)$data['email'])) : '';
        $telephone          = isset($data['telephone']) ? trim((string)$data['telephone']) : '';

        // ===== Altura =====
        $rawHeight = trim($data['height'] ?? '');
        if ($systemType === 'EU') {
            $cm           = intval($rawHeight);
            $totalInches  = (int) round($cm / 2.54);
            $feet         = intdiv($totalInches, 12);
            $inches       = $totalInches % 12;
            $height       = sprintf("%d'%02d\"", $feet, $inches);
        } else {
            $height       = $rawHeight;
        }

        // ===== Password (requerido) =====
        if (empty($data['password'])) {
            throw new \mysqli_sql_exception('La contraseña es obligatoria.');
        }
        $hashedPassword = password_hash((string)$data['password'], PASSWORD_DEFAULT);

        // ===== Auditoría y zona horaria =====
        $actorUserId = $_SESSION['user_id'] ?? null;
        $env         = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $actorUserId);
        (new TimezoneManager($this->db))->applyTimezone();
        $createdAt   = $env->getCurrentDatetime();
        $createdBy   = $actorUserId;

        // ===== Validar duplicidad de email SOLO si vino no-vacío =====
        if ($email !== '') {
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
            if (!$check) {
                error_log('[user.create][prepare-error] validar email | ' . $this->db->error);
                throw new \mysqli_sql_exception('Error preparando la validación de email: ' . $this->db->error);
            }
            $check->bind_param('s', $email);
            if (!$check->execute()) {
                error_log('[user.create][execute-error] validar email | ' . $check->error);
                throw new \mysqli_sql_exception('Error ejecutando la validación de email: ' . $check->error);
            }
            $check->store_result();
            if ($check->num_rows > 0) {
                $check->close();
                error_log("[user.create][dup-email] email={$email}");
                throw new \mysqli_sql_exception('Este correo ya está registrado.');
            }
            $check->close();
        }

        // ===== INSERT (email y telephone incluidos como columnas planas) =====
        $sql = "INSERT INTO {$this->table}
                (user_id, first_name, last_name, sex_biological, birthday, height, email, telephone, password, system_type, timezone, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('[user.create][prepare-error] insert usuario | ' . $this->db->error);
            throw new \mysqli_sql_exception('Error preparando inserción: ' . $this->db->error);
        }
        $stmt->bind_param(
            'sssssssssssss',
            $uuid,
            $firstName,
            $lastName,
            $sex_biological,
            $birthday,
            $height,
            $email,
            $telephone,
            $hashedPassword,
            $systemType,
            $timezone,
            $createdAt,
            $createdBy
        );
        if (!$stmt->execute()) {
            error_log('[user.create][execute-error] insert usuario | ' . $stmt->error);
            throw new \mysqli_sql_exception('Error al crear usuario: ' . $stmt->error);
        }
        $stmt->close();

        // ===== Persistir colecciones si llegaron =====
        // Aceptamos 'phones' (preferido) o 'telephones' (compat)
        if (array_key_exists('emails', $data)) {
            $this->persistEmails($uuid, $data['emails']);
        }
        if (array_key_exists('phones', $data)) {
            $this->persistPhones($uuid, $data['phones']);
        } 
        $this->db->commit();

        error_log("[user.create][success] user_id={$uuid}");
        return true;
    } catch (\mysqli_sql_exception $e) {
        $this->db->rollback();
        error_log("[user.create][error] sqlx msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("[user.create][error] ex msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
        throw $e;
    }
}

 public function update(string $id, array $data)
{
    // ===== Log de entrada (no exponer password) =====
    $logSnapshot = [
        'user_id'         => $id,
        'first_name'      => $data['first_name'] ?? null,
        'last_name'       => $data['last_name'] ?? null,
        'sex_biological'             => $data['sex_biological'] ?? null,
        'birthday'        => $data['birthday'] ?? null,
        'system_type'     => strtoupper($data['system_type'] ?? 'US'),
        'timezone'        => $data['timezone'] ?? 'America/Los_Angeles',
        'email_in'        => isset($data['email']) ? strtolower(trim((string)$data['email'])) : null,
        'telephone_in'    => isset($data['telephone']) ? trim((string)$data['telephone']) : null,
        'status_in'       => isset($data['status']) ? (int)$data['status'] : null,
        'height_present'  => isset($data['height']),
        'password_present'=> !empty($data['password']),
        'has_emails_array'=> array_key_exists('emails', $data),
        'has_phones_array'=> array_key_exists('phones', $data),
    ];
    error_log('[user.update][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

    try {
        $this->db->begin_transaction();

        // ===== Verificar existencia =====
        $checkExist = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE user_id = ? AND deleted_at IS NULL");
        if (!$checkExist) {
            error_log('[user.update][prepare-error] check exist | ' . $this->db->error);
            throw new \Exception('Error preparando verificación de existencia: ' . $this->db->error);
        }
        $checkExist->bind_param('s', $id);
        if (!$checkExist->execute()) {
            error_log('[user.update][execute-error] check exist | ' . $checkExist->error);
            throw new \Exception('Error ejecutando verificación de existencia: ' . $checkExist->error);
        }
        $checkExist->store_result();
        if ($checkExist->num_rows === 0) {
            $checkExist->close();
            throw new \Exception('Usuario no encontrado.');
        }
        $checkExist->close();

        // ===== Asignar / normalizar =====
        $this->id          = $id;
        $this->first_name  = $data['first_name'] ?? '';
        $this->last_name   = $data['last_name'] ?? '';
        $this->sex_biological         = $data['sex_biological'] ?? '';
        $this->birthday    = $data['birthday'] ?? '';
        $this->system_type = strtoupper($data['system_type'] ?? 'US');
        $this->timezone    = $data['timezone'] ?? 'America/Los_Angeles';
        $this->status      = isset($data['status']) ? (int)$data['status'] : 1;

        // Normalización directa (texto plano)
        $this->email       = isset($data['email']) ? trim(strtolower((string)$data['email'])) : '';
        $this->telephone   = isset($data['telephone']) ? trim((string)$data['telephone']) : '';

        // ===== Altura =====
        $rawHeight = trim($data['height'] ?? '');
        if ($this->system_type === 'EU') {
            $cm          = (int)$rawHeight;
            $totalInches = (int)round($cm / 2.54);
            $feet        = intdiv($totalInches, 12);
            $inches      = $totalInches % 12;
            $this->height = sprintf("%d'%02d\"", $feet, $inches);
        } else {
            $this->height = $rawHeight;
        }

        // ===== Auditoría / TZ =====
        $actorUserId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $actorUserId);
        (new TimezoneManager($this->db))->applyTimezone();
        $updatedAt = $env->getCurrentDatetime();
        $updatedBy = $actorUserId;

        // ===== Validar duplicado email (solo si no vacío) =====
        if ($this->email !== '') {
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND user_id != ? AND deleted_at IS NULL");
            if (!$check) {
                error_log('[user.update][prepare-error] dup email | ' . $this->db->error);
                throw new \Exception('Error al preparar validación de email: ' . $this->db->error);
            }
            $check->bind_param('ss', $this->email, $this->id);
            if (!$check->execute()) {
                error_log('[user.update][execute-error] dup email | ' . $check->error);
                throw new \Exception('Error ejecutando validación de email: ' . $check->error);
            }
            $check->store_result();
            if ($check->num_rows > 0) {
                $check->close();
                error_log("[user.update][dup-email] user_id={$this->id} email={$this->email}");
                throw new \Exception('Este correo ya está registrado por otro usuario.');
            }
            $check->close();
        }

        // ===== Armado SQL (incluye timezone y password opcional) =====
        $sql = "UPDATE {$this->table} SET
                    first_name  = ?,
                    last_name   = ?,
                    sex_biological         = ?,
                    birthday    = ?,
                    height      = ?,
                    system_type = ?,
                    timezone    = ?,
                    email       = ?,
                    telephone   = ?,
                    status      = ?,
                    updated_at  = ?,
                    updated_by  = ?";

        $params = [
            $this->first_name,
            $this->last_name,
            $this->sex_biological,
            $this->birthday,
            $this->height,
            $this->system_type,
            $this->timezone,
            $this->email,
            $this->telephone,
            $this->status,
            $updatedAt,
            $updatedBy
        ];
        $types = "ssssssssssss"; // s*s*s*s*s*s*s*s*i*s*s = 12 parámetros

        // Contraseña opcional
        if (!empty($data['password'])) {
            $hashed = password_hash((string)$data['password'], PASSWORD_DEFAULT);
            $sql   .= ", password = ?";
            $params[] = $hashed;
            $types   .= "s";
        }

        // WHERE
        $sql .= " WHERE user_id = ?";
        $params[] = $this->id;
        $types   .= "s";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log('[user.update][prepare-error] update | ' . $this->db->error);
            throw new \Exception('Error al preparar la consulta de actualización: ' . $this->db->error);
        }
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            error_log('[user.update][execute-error] update | ' . $stmt->error);
            throw new \Exception('No se pudo actualizar el usuario: ' . $stmt->error);
        }

        $affected = $stmt->affected_rows;
        $stmt->close();

        // ===== Persistir colecciones si llegaron =====
        if (array_key_exists('emails', $data)) {
            $this->persistEmails($id, $data['emails']);
        }
        if (array_key_exists('phones', $data)) {
            $this->persistPhones($id, $data['phones']);
        }

        $this->db->commit();
        error_log("[user.update][success] user_id={$this->id} affected_rows={$affected}");
        return true;

    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("[user.update][error] user_id={$id} msg={$e->getMessage()} code={$e->getCode()} file={$e->getFile()} line={$e->getLine()}");
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





    public function getSessionUserData($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return [
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ];
            }

            $user_data = $result->fetch_assoc();

            // Transformar el valor de 'sex_biological'
            $user_data['sex_biological'] = ($user_data['sex_biological'] === 'm') ? 'Male' : (($user_data['sex_biological'] === 'f') ? 'Female' : 'Other');

            // Calcular edad y formatear cumpleaños
            $birthday = new \DateTime($user_data['birthday']);
            $user_data['birthday'] = $birthday->format('m-d-Y');
            $today = new \DateTime();
            $age = $today->diff($birthday)->y;
            $user_data['age'] = $age;



            return $user_data;
        } catch (mysqli_sql_exception $e) {
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
            $checkStmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE user_id = ? LIMIT 1");
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
            $stmt = $this->db->prepare("UPDATE {$this->table} SET system_type = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
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
        'user_id'      => $id,
        'first_name'   => $data['first_name'] ?? null,
        'last_name'    => $data['last_name'] ?? null,
        'sex_biological' => $data['sex_biological'] ?? null,
        'birthday'     => $data['birthday'] ?? null,
        'system_type'  => strtoupper($data['system_type'] ?? 'US'),
        'timezone'     => $data['timezone'] ?? 'America/Los_Angeles',
        'email_in'     => isset($data['email']) ? strtolower(trim((string)$data['email'])) : null,
        'telephone_in' => isset($data['telephone']) ? trim((string)$data['telephone']) : null,
        'height_present'   => isset($data['height']),
        'password_present' => !empty($data['password']),
    ];
    error_log('[updateProfile][start] ' . json_encode($logSnapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    try {
        $this->db->begin_transaction();

        // ===== Idioma para mensajes =====
        $idioma = strtoupper($_SESSION['idioma'] ?? $_SESSION['lang'] ?? 'EN');
        $isES = $idioma === 'ES';

        // ===== Asignar valores base =====
        $this->id          = $id;
        $this->first_name  = $data['first_name'] ?? '';
        $this->last_name   = $data['last_name'] ?? '';
        $this->sex_biological = $data['sex_biological'] ?? '';
        $this->birthday    = $data['birthday'] ?? '';
        $this->system_type = strtoupper($data['system_type'] ?? 'US');
        $this->timezone    = $data['timezone'] ?? 'America/Los_Angeles';

        $this->email     = isset($data['email']) ? trim(strtolower((string)$data['email'])) : '';
        $this->telephone = isset($data['telephone']) ? trim((string)$data['telephone']) : '';

        // ===== Procesar altura =====
        $rawHeight = trim($data['height'] ?? '');
        if ($this->system_type === 'EU') {
            $cm = intval($rawHeight);
            $totalInches = (int)round($cm / 2.54);
            $feet   = intdiv($totalInches, 12);
            $inches = $totalInches % 12;
            $this->height = sprintf("%d'%02d\"", $feet, $inches);
        } else {
            $this->height = $rawHeight;
        }

        // ===== Auditoría y zona horaria =====
        $userId = $_SESSION['user_id'] ?? null;
        $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        $tzManager = new TimezoneManager($this->db);
        $tzManager->applyTimezone();
        $updatedAt = $env->getCurrentDatetime();
        $updatedBy = $userId;

        // ===== Validar duplicidad de email =====
        if ($this->email !== '') {
            $check = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ? AND user_id != ? AND deleted_at IS NULL");
            if (!$check) {
                throw new \Exception($isES ? 'Error al preparar la validación de correo electrónico.' : 'Error preparing email validation.');
            }
            $check->bind_param("ss", $this->email, $this->id);
            if (!$check->execute()) {
                throw new \Exception($isES ? 'Error al ejecutar la validación de correo electrónico.' : 'Error executing email validation.');
            }
            $check->store_result();
            if ($check->num_rows > 0) {
                $check->close();
                throw new \Exception($isES ? 'Este correo ya está registrado por otro usuario.' : 'This email is already registered by another user.');
            }
            $check->close();
        }

        // ===== Validar cambio de sexo biológico =====
        $curStmt = $this->db->prepare("SELECT sex_biological FROM {$this->table} WHERE user_id = ? FOR UPDATE");
        if (!$curStmt) {
            throw new \Exception($isES ? 'Error al preparar la lectura del usuario.' : 'Error preparing user read statement.');
        }
        $curStmt->bind_param("s", $this->id);
        if (!$curStmt->execute()) {
            $err = $curStmt->error;
            $curStmt->close();
            throw new \Exception($isES ? 'No se pudo obtener la información del usuario.' : 'Could not retrieve user information.');
        }
        $res = $curStmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $curStmt->close();

        $currentSex = strtolower(trim((string)($row['sex_biological'] ?? '')));
        $newSex     = strtolower(trim((string)$this->sex_biological));

        if ($newSex !== '' && $newSex !== $currentSex) {
            $q = "
                SELECT COUNT(1) AS c
                FROM second_opinion_requests
                WHERE user_id = ?
                  AND deleted_at IS NULL
                  AND UPPER(status) NOT IN ('completed','rejected','cancelled')
            ";
            $s = $this->db->prepare($q);
            if (!$s) {
                throw new \Exception($isES ? 'Error al preparar la validación de solicitudes de segunda opinión.' : 'Error preparing second-opinion validation.');
            }
            $s->bind_param("s", $this->id);
            if (!$s->execute()) {
                $err = $s->error;
                $s->close();
                throw new \Exception($isES ? 'Error al verificar las solicitudes de segunda opinión.' : 'Error checking second-opinion requests.');
            }
            $countRow = $s->get_result()->fetch_assoc();
            $s->close();

            if ((int)($countRow['c'] ?? 0) > 0) {
                throw new \Exception(
                    $isES
                        ? 'No puede modificar su sexo biológico mientras tenga una solicitud de segunda opinión en progreso.'
                        : 'You cannot change your biological sex while you have a second opinion request in progress.'
                );
            }
        }

        // ===== UPDATE principal =====
        $sql = "UPDATE {$this->table} SET 
                    first_name  = ?, 
                    last_name   = ?, 
                    sex_biological = ?, 
                    birthday    = ?, 
                    height      = ?, 
                    system_type = ?, 
                    timezone    = ?, 
                    email       = ?, 
                    telephone   = ?, 
                    updated_at  = ?, 
                    updated_by  = ?";

        $params = [
            $this->first_name,
            $this->last_name,
            $this->sex_biological,
            $this->birthday,
            $this->height,
            $this->system_type,
            $this->timezone,
            $this->email,
            $this->telephone,
            $updatedAt,
            $updatedBy
        ];
        $types = "sssssssssss";

        if (!empty($data['password'])) {
            $hashedPassword = password_hash((string)$data['password'], PASSWORD_DEFAULT);
            $sql .= ", password = ?";
            $params[] = $hashedPassword;
            $types .= "s";
        }

        $sql .= " WHERE user_id = ?";
        $params[] = $this->id;
        $types .= "s";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception($isES ? 'Error al preparar la actualización del usuario.' : 'Error preparing user update statement.');
        }
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            throw new \Exception($isES ? 'No se pudo actualizar el usuario.' : 'Failed to update user.');
        }

        $affected = $stmt->affected_rows;
        $stmt->close();

        // ===== Persistir colecciones =====
        if (array_key_exists('emails', $data)) {
            $this->persistEmails($id, $data['emails']);
        }
        if (array_key_exists('phones', $data)) {
            $this->persistPhones($id, $data['phones']);
        }

        $this->db->commit();

        // ===== Actualizar sesión =====
        $_SESSION['first_name']  = $this->first_name;
        $_SESSION['last_name']   = $this->last_name;
        $_SESSION['user_name']   = $this->first_name . ' ' . $this->last_name;
        $_SESSION['system_type'] = $this->system_type;
        $_SESSION['timezone']    = $this->timezone;
        $_SESSION['birthday']    = $this->birthday;
        $_SESSION['sex_biological'] = $this->sex_biological;
        $_SESSION['height']      = $this->height;
        $_SESSION['email']       = $this->email;
        $_SESSION['telephone']   = $this->telephone;

        error_log("[updateProfile][success] user_id={$this->id} affected_rows={$affected}");
        return true;

    } catch (\Exception $e) {
        $this->db->rollback();
        error_log("[updateProfile][error] user_id={$id} msg={$e->getMessage()}");
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

            // Verificar existencia
            $checkExist = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE user_id = ?");
            $checkExist->bind_param("s", $id);
            $checkExist->execute();
            $checkExist->store_result();
            if ($checkExist->num_rows === 0) {
                throw new mysqli_sql_exception($traducciones['user_not_found'] ?? "User not found.");
            }
            $checkExist->close();

            // Verificar dependencias
            $relatedTables = [
                'body_composition' => ['user_id', true],
                'lipid_profile_record' => ['user_id', true],
                'renal_function' => ['user_id', true],
                'energy_metabolism' => ['user_id', true],
                'notifications' => ['user_id', false],
                'security_questions' => ['user_id_user', false]
            ];

            foreach ($relatedTables as $table => [$field, $hasDeletedAt]) {
                if ($hasDeletedAt) {
                    $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$table} WHERE {$field} = ? AND deleted_at IS NULL");
                } else {
                    $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$table} WHERE {$field} = ?");
                }
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparing dependency check for $table: " . $this->db->error);
                }
                $stmt->bind_param("s", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();

                if ($row['total'] > 0) {
                    $msg = $traducciones['user_delete_dependency'] ?? "Cannot delete user: related records exist in table '{$table}'.";
                    throw new mysqli_sql_exception(str_replace('{table}', $table, $msg));
                }
            }

            // Auditoría
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $userId;

            // Eliminación lógica
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE user_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing delete statement: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error deleting user: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function authenticate($email, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    unset($user['password']); // Nunca devolver el hash
                    return $user;
                }
            }
            return null;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function verifySecurityAnswers(int $userId, string $answer1, string $answer2): bool
    {
        $stmt = $this->db->prepare("SELECT user_id FROM security_questions WHERE user_id = ? AND answer1 = ? AND answer2 = ?");
        $stmt->bind_param("sss", $userId, $answer1, $answer2);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
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

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
            $stmt->bind_param("ssss", $hashedPassword, $updatedAt, $updatedBy, $userId);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();

            if ($success) {
                $stmt = $this->db->prepare("SELECT email FROM {$this->table} WHERE user_id = ?");
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

            $stmt = $this->db->prepare("SELECT user_id FROM {$this->table} WHERE email = ?");
            $stmt->bind_param("s", $reset['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user) {
                return false;
            }

            $userIdFromToken = $user['user_id'];
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
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








}
