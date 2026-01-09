<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';

class ContactPhoneModel
{
    private $db;
    private string $table = 'contact_phones';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /* ===================== Helpers ===================== */

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

    /**
     * Normaliza y valida el tipo de entidad (entity_type).
     * Acepta mayúsculas y plurales comunes (specialists, administrators, users).
     */
    private function normalizeEntityType(string $t): string
    {
        $v = strtolower(trim($t));
        if ($v === 'specialists')    $v = 'specialist';
        if ($v === 'administrators') $v = 'administrator';
        if ($v === 'users')          $v = 'user';

        $allowed = ['administrator', 'specialist', 'user'];
        if (!in_array($v, $allowed, true)) {
            throw new \mysqli_sql_exception("Invalid entity_type '{$t}'. Allowed: " . implode(', ', $allowed));
        }
        return $v;
    }

    /**
     * Normaliza y valida phone_type (si tu columna es ENUM).
     * Devuelve NULL si no se envía (y la columna lo permite).
     */
    private function normalizePhoneType(?string $t): ?string
    {
        if ($t === null || $t === '') return null;
        $v = strtolower(trim($t));
        // alias típicos
        $map = [
            'cell' => 'mobile', 'cel' => 'mobile', 'movil' => 'mobile', 'móvil' => 'mobile',
            'work' => 'office', 'oficina' => 'office',
            'otro' => 'other'
        ];
        $v = $map[$v] ?? $v;

        $allowed = ['mobile', 'office', 'fax', 'other'];
        if (!in_array($v, $allowed, true)) {
            throw new \mysqli_sql_exception("Invalid phone_type '{$t}'. Allowed: " . implode(', ', $allowed));
        }
        return $v;
    }

    private function normalizeCc(?string $cc): ?string
    {
        if ($cc === null)
            return null;
        $cc = preg_replace('/\D+/', '', $cc);
        return $cc === '' ? null : $cc; // guardar sin '+'
    }

    private function normalizeNum(?string $n): ?string
    {
        if ($n === null)
            return null;
        $n = preg_replace('/\D+/', '', $n);
        return $n === '' ? null : $n;
    }

    private function forceSinglePrimary(string $entityType, string $entityId, ?string $excludeId = null): void
    {
        $entityType = $this->normalizeEntityType($entityType);

        $sql = "UPDATE {$this->table}
                SET is_primary = 0
                WHERE entity_type = ? AND entity_id = ? AND deleted_at IS NULL";
        if (!empty($excludeId)) {
            $sql .= " AND contact_phone_id <> ?";
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparando normalización de primario: " . $this->db->error);
        }

        if (!empty($excludeId)) {
            $stmt->bind_param("sss", $entityType, $entityId, $excludeId);
        } else {
            $stmt->bind_param("ss", $entityType, $entityId);
        }

        $stmt->execute();
        $stmt->close();
    }

    private function existsForEntity(string $entityType, string $entityId, string $cc, string $num, ?string $excludeId = null): bool
    {
        $entityType = $this->normalizeEntityType($entityType);

        $sql = "SELECT contact_phone_id FROM {$this->table}
                WHERE entity_type = ? AND entity_id = ? AND country_code = ? AND phone_number = ? AND deleted_at IS NULL";
        if (!empty($excludeId)) {
            $sql .= " AND contact_phone_id <> ?";
        }

        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando verificación: " . $this->db->error);

        if (!empty($excludeId)) {
            $stmt->bind_param("sssss", $entityType, $entityId, $cc, $num, $excludeId);
        } else {
            $stmt->bind_param("ssss", $entityType, $entityId, $cc, $num);
        }

        $stmt->execute();
        $stmt->store_result();
        $ok = $stmt->num_rows > 0;
        $stmt->close();
        return $ok;
    }

    /* ===== Helpers de normalización y auditoría ===== */

    // Solo dígitos
    private function digitsOnly(string $s): string
    {
        return preg_replace('/\D+/', '', $s);
    }

    /**
     * Normaliza el código de país:
     * - Solo dígitos
     * - Si viene vacío, intenta inferirlo desde rawNumber (p.ej. "+58..." o "(+1)...")
     * - Si empieza por "1", fuerza "1" (NANP)
     * - Si > 3 dígitos, recorta a 3 (máximo ITU)
     * - Si todo falla, usa "1"
     */
    private function sanitizeCountryCode(string $ccRaw, string $rawNumber = ''): string
    {
        $cc = $this->digitsOnly($ccRaw);

        // Intentar inferir desde número crudo si no vino cc
        if ($cc === '' && $rawNumber !== '') {
            [$infCc,] = $this->normalizeRawPhone($rawNumber);
            $cc = $this->digitsOnly($infCc);
        }

        if ($cc === '')
            return '1';
        if ($cc[0] === '1')
            return '1'; // NANP
        if (strlen($cc) > 3)
            $cc = substr($cc, 0, 3);

        return $cc;
    }

    /**
     * Si el número viene con cc al frente (p.ej. "+1(111)111-1111" o "(+1) (111)111-1111"),
     * lo separa usando el cc esperado y devuelve SOLO el número nacional en dígitos.
     */
    private function stripLeadingCcFromNumber(string $rawNumber, string $countryCode): string
    {
        $digits = $this->digitsOnly($rawNumber);
        $cc = $this->digitsOnly($countryCode);

        if ($cc !== '' && strpos($digits, $cc) === 0 && strlen($digits) > strlen($cc) + 5) {
            return substr($digits, strlen($cc));
        }
        return $digits; // ya era nacional o no hay cc detectable
    }

    /**
     * Construye el display a partir de cc + número nacional (en dígitos).
     * Para cc=1 y 10 dígitos: "(+1) (AAA) BBB-CCCC"
     * Para el resto: "(+cc) NNNNN..."
     */
    private function formatDisplay(string $countryCode, string $nationalDigits): string
    {
        $cc = $this->digitsOnly($countryCode);
        $n = $this->digitsOnly($nationalDigits);

        if ($cc === '1' && strlen($n) === 10) {
            return sprintf(
                "(+1) (%s)%s-%s",
                substr($n, 0, 3),
                substr($n, 3, 3),
                substr($n, 6)
            );
        }
        return sprintf("(+%s) %s", $cc, $n);
    }

    // (Compatibilidad; no se usa para construir display desde crudo)
    private function normalizeDisplayPhone(string $phone): string
    {
        $phone = trim((string) $phone);
        $phone = preg_replace('/\s+/', ' ', $phone);
        $phone = preg_replace('/^\(\+(\d{1,3})\)\s*(\()/', '(+$1) $2', $phone);
        $phone = preg_replace('/\)\s*(\d)/', ') $1', $phone);
        $phone = preg_replace('/\)\s*\(/', ') (', $phone);
        return $phone;
    }

    // Para comparación por dígitos
    private function normalizeForCompare(string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    // Aplica contexto de auditoría y timezone (devuelve [$env, $userId])
    private function applyAuditContext(): array
    {
        $userId = $_SESSION['user_id'] ?? null;
        $geoDbPath = defined('PROJECT_ROOT')
            ? PROJECT_ROOT . '/app/config/geolite.mmdb'
            : (__DIR__ . '/../config/geolite.mmdb');

        $env = new ClientEnvironmentInfo($geoDbPath);
        $env->applyAuditContext($this->db, $userId);
        (new TimezoneManager($this->db))->applyTimezone();

        return [$env, $userId];
    }

    public function listIdsByEntity(string $entityType, string $entityId): array
    {
        $entityType = $this->normalizeEntityType($entityType);

        $sql = "SELECT contact_phone_id
                FROM {$this->table}
                WHERE entity_type = ?
                  AND entity_id   = ?
                  AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparing listIdsByEntity: " . $this->db->error);
        }
        $stmt->bind_param('ss', $entityType, $entityId);
        $stmt->execute();
        $res = $stmt->get_result();

        $ids = [];
        while ($row = $res->fetch_assoc()) {
            $id = trim((string) ($row['contact_phone_id'] ?? ''));
            if ($id !== '') {
                $ids[] = $id;
            }
        }
        $stmt->close();
        return $ids;
    }

    // Convierte valores "boolean-like" a 0/1
    private function toIntBool($v): int
    {
        if (is_bool($v))
            return $v ? 1 : 0;
        $s = strtolower(trim((string) $v));
        return in_array($s, ['1', 'true', 'yes', 'y', 'on'], true) ? 1 : 0;
    }

    // Crea un "set" seguro de IDs: ['id1' => true, ...]
    private function safeFlipIds(array $maybeIds): array
    {
        $set = [];
        foreach ($maybeIds as $item) {
            $id = null;
            if (is_array($item)) {
                $id = $item['contact_email_id'] ?? $item['contact_phone_id'] ?? $item['id'] ?? null;
            } else {
                $id = $item;
            }
            if ($id !== null) {
                $id = trim((string) $id);
                if ($id !== '') {
                    $set[$id] = true;
                }
            }
        }
        return $set;
    }

    /* ===================== Queries ===================== */

    public function getByTelephone(string $telephone, ?string $entityType = null): ?array
    {
        // Compara por dígitos para que funcione sin importar el formato guardado
        $cmp = preg_replace('/\D+/', '', $telephone);

        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                COALESCE(phone_number,''), '(', ''), ')', ''), '-', ''), ' ', ''), '+', ''), '.', '') = ?
        ";

        $types = 's';
        $params = [$cmp];

        if (!empty($entityType)) {
            $entityType = $this->normalizeEntityType($entityType);
            $sql .= " AND entity_type = ?";
            $types .= 's';
            $params[] = $entityType;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception('Error al preparar la consulta: ' . $this->db->error);
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();

        return $row;
    }

    public function showByTelephone(string $telephone, ?string $entityType = null): ?array
    {
        // Compara por dígitos para que funcione sin importar el formato guardado
        $cmp = preg_replace('/\D+/', '', $telephone);

        $sql = "
        SELECT *
        FROM {$this->table}
        WHERE deleted_at IS NULL
          AND phone_number = ?
        ";

        $types = 's';
        $params = [$cmp];

        if (!empty($entityType)) {
            $entityType = $this->normalizeEntityType($entityType);
            $sql .= " AND entity_type = ?";
            $types .= 's';
            $params[] = $entityType;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception('Error al preparar la consulta: ' . $this->db->error);
        }

        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();

        return $row;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";
        $res = $this->db->query($sql);
        if (!$res)
            throw new mysqli_sql_exception("Query error: " . $this->db->error);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE contact_phone_id = ? AND deleted_at IS NULL");
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function getByEntity(string $entityType, string $entityId): array
    {
        $entityType = $this->normalizeEntityType($entityType);

        $stmt = $this->db->prepare("SELECT * FROM {$this->table}
                                    WHERE entity_type = ? AND entity_id = ? AND deleted_at IS NULL
                                    ORDER BY is_primary DESC, created_at DESC");
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        $stmt->bind_param("ss", $entityType, $entityId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /* ===================== Mutations ===================== */

    private function mustExist(string $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE contact_phone_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new mysqli_sql_exception("Contact phone not found.");
        }
        return $row;
    }

    public function create(array $data): bool
    {
        $this->db->begin_transaction();
        try {
            [$env, $userId] = $this->applyAuditContext();
            $now = $env->getCurrentDatetime();
            $uuid = $this->generateUUIDv4();

            $entityType     = $this->normalizeEntityType((string) ($data['entity_type'] ?? ''));
            $entityId       = trim((string) ($data['entity_id'] ?? ''));
            $phoneType      = array_key_exists('phone_type', $data) ? $this->normalizePhoneType((string)$data['phone_type']) : null;

            $countryCodeRaw = (string) ($data['country_code'] ?? '');
            $phoneNumberRaw = (string) ($data['phone_number'] ?? '');
            $isPrimary      = isset($data['is_primary']) ? (int) $data['is_primary'] : 0;
            $isActive       = isset($data['is_active']) ? (int) $data['is_active'] : 1;

            if ($entityType === '' || $entityId === '' || $phoneNumberRaw === '') {
                throw new mysqli_sql_exception("entity_type, entity_id and phone_number are required.");
            }

            // Normaliza/infiera CC de forma robusta ANTES de usarlo
            $countryCode = $this->sanitizeCountryCode($countryCodeRaw, $phoneNumberRaw);

            // 1) Quitar cc del número si vino incluido (quedarse solo con dígitos nacionales)
            $nationalDigits = $this->stripLeadingCcFromNumber($phoneNumberRaw, $countryCode);

            // 2) Display consistente
            $phoneNumberDisplay = $this->formatDisplay($countryCode, $nationalDigits);

            // Duplicado (comparando por dígitos)
            $cmp = $nationalDigits;
            $sqlDup = "
                SELECT contact_phone_id, deleted_at
                FROM {$this->table}
                WHERE entity_type = ?
                  AND entity_id    = ?
                  AND country_code = ?
                  AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        COALESCE(phone_number,''), '(', ''), ')', ''), '-', ''), ' ', ''), '+', ''), '.', '') = ?
                LIMIT 1";
            $stmtDup = $this->db->prepare($sqlDup);
            if (!$stmtDup)
                throw new mysqli_sql_exception("Error preparing duplicate check: " . $this->db->error);
            $stmtDup->bind_param('ssss', $entityType, $entityId, $countryCode, $cmp);
            $stmtDup->execute();
            $dup = $stmtDup->get_result()->fetch_assoc() ?: null;
            $stmtDup->close();

            if ($dup && $dup['deleted_at'] !== null) {
                // Revivir registro (y además actualizar display)
                $sql = "UPDATE {$this->table}
                        SET is_active = ?, phone_number = ?, deleted_at = NULL, deleted_by = NULL, updated_at = ?, updated_by = ?
                        WHERE contact_phone_id = ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt)
                    throw new mysqli_sql_exception("Error preparing revive: " . $this->db->error);
                $stmt->bind_param("issss", $isActive, $phoneNumberDisplay, $now, $userId, $dup['contact_phone_id']);
                $stmt->execute();
                $stmt->close();
                $this->db->commit();
                return true;
            }
            if ($dup && $dup['deleted_at'] === null) {
                throw new mysqli_sql_exception("Duplicate phone for this entity (uk_contact_phone).");
            }

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
                (contact_phone_id, entity_type, entity_id, phone_type, country_code, phone_number,
                 is_primary, is_active, created_at, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing insert: " . $this->db->error);

            // Tipos: 6 's' + 2 'i' + 2 's' => 'ssssssiiss'
            $types = 'ssssssiiss';
            $stmt->bind_param(
                $types,
                $uuid,
                $entityType,
                $entityId,
                $phoneType,
                $countryCode,
                $phoneNumberDisplay, // guardar display consistente
                $isPrimary,
                $isActive,
                $now,
                $userId
            );
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            error_log("[CREATE ERROR] {$e->getMessage()} | Code: {$e->getCode()} | File: {$e->getFile()} | Line: {$e->getLine()}");
            if ($this->db->errno === 1062 || str_contains($e->getMessage(), 'uk_contact_phone')) {
                throw new mysqli_sql_exception("Duplicate entry for (entity_type, entity_id, country_code, phone_number).", 1062);
            }
            throw $e;
        }
    }

    public function update(string $id, array $data): bool
    {
        $this->db->begin_transaction();
        try {
            $this->mustExist($id);
            [$env, $userId] = $this->applyAuditContext();
            $now = $env->getCurrentDatetime();

            $stmtCur = $this->db->prepare("SELECT entity_type, entity_id, country_code, phone_number FROM {$this->table} WHERE contact_phone_id = ? LIMIT 1");
            if (!$stmtCur)
                throw new mysqli_sql_exception("Error preparing current fetch: " . $this->db->error);
            $stmtCur->bind_param("s", $id);
            $stmtCur->execute();
            $current = $stmtCur->get_result()->fetch_assoc();
            $stmtCur->close();
            if (!$current)
                throw new mysqli_sql_exception("Contact phone not found.");

            $entityType = array_key_exists('entity_type', $data)
                ? $this->normalizeEntityType((string) $data['entity_type'])
                : $current['entity_type'];

            $entityId = array_key_exists('entity_id', $data)
                ? trim((string) $data['entity_id'])
                : $current['entity_id'];

            $phoneType = array_key_exists('phone_type', $data)
                ? $this->normalizePhoneType((string) $data['phone_type'])
                : null;

            // Saneamos cc tanto si viene en payload como si no
            if (array_key_exists('country_code', $data)) {
                $countryCode = $this->sanitizeCountryCode((string) $data['country_code'], (string) ($data['phone_number'] ?? ''));
            } else {
                $countryCode = $this->sanitizeCountryCode((string) $current['country_code'], (string) ($data['phone_number'] ?? ''));
            }

            // Si viene phone_number en el payload, normalizarlo con base en countryCode
            if (array_key_exists('phone_number', $data)) {
                $rawNumber = (string) $data['phone_number'];
                $nationalDigits = $this->stripLeadingCcFromNumber($rawNumber, $countryCode);
                $phoneNumber = $this->formatDisplay($countryCode, $nationalDigits);
            } else {
                $phoneNumber = $current['phone_number'];
            }

            $isPrimary = array_key_exists('is_primary', $data) ? (int) $data['is_primary'] : null;
            $isActive  = array_key_exists('is_active', $data) ? (int) $data['is_active'] : null;

            $keyChanged = (
                $entityType  !== $current['entity_type'] ||
                $entityId    !== $current['entity_id']   ||
                $countryCode !== $current['country_code']||
                $phoneNumber !== $current['phone_number']
            );

            if ($keyChanged) {
                // Para duplicado, comparar por dígitos del display candidate
                $cmp = $this->normalizeForCompare($phoneNumber);
                $sqlDup = "
                    SELECT contact_phone_id, deleted_at
                    FROM {$this->table}
                    WHERE entity_type = ?
                      AND entity_id    = ?
                      AND country_code = ?
                      AND REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                            COALESCE(phone_number,''), '(', ''), ')', ''), '-', ''), ' ', ''), '+', ''), '.', '') = ?
                    LIMIT 1";
                $stmtDup = $this->db->prepare($sqlDup);
                if (!$stmtDup)
                    throw new mysqli_sql_exception("Error preparing duplicate check: " . $this->db->error);
                $stmtDup->bind_param('ssss', $entityType, $entityId, $countryCode, $cmp);
                $stmtDup->execute();
                $dup = $stmtDup->get_result()->fetch_assoc() ?: null;
                $stmtDup->close();

                if ($dup && $dup['contact_phone_id'] !== $id && $dup['deleted_at'] === null) {
                    throw new mysqli_sql_exception("Duplicate phone for this entity (uk_contact_phone).");
                }
            }

            $fields = [];
            $params = [];
            $types = '';

            if (array_key_exists('entity_type', $data)) {
                $fields[] = "entity_type = ?";
                $params[] = $entityType;
                $types .= 's';
            }
            if (array_key_exists('entity_id', $data)) {
                $fields[] = "entity_id = ?";
                $params[] = $entityId;
                $types .= 's';
            }
            if (array_key_exists('phone_type', $data)) {
                $fields[] = "phone_type = ?";
                $params[] = $phoneType;
                $types .= 's';
            }
            if (array_key_exists('country_code', $data)) {
                $fields[] = "country_code = ?";
                $params[] = $countryCode;
                $types .= 's';
            }
            if (array_key_exists('phone_number', $data)) {
                $fields[] = "phone_number = ?";
                $params[] = $phoneNumber;
                $types .= 's';
            }
            if ($isPrimary !== null) {
                $fields[] = "is_primary = ?";
                $params[] = $isPrimary;
                $types .= 'i';
            }
            if ($isActive !== null) {
                $fields[] = "is_active  = ?";
                $params[] = $isActive;
                $types .= 'i';
            }

            $fields[] = "updated_at = ?";
            $params[] = $now;
            $types .= 's';
            $fields[] = "updated_by = ?";
            $params[] = $userId;
            $types .= 's';

            $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE contact_phone_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing update: " . $this->db->error);
            $types .= 's';
            $params[] = $id;
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            error_log("[UPDATE ERROR] {$e->getMessage()} | Code: {$e->getCode()} | File: {$e->getFile()} | Line: {$e->getLine()}");
            if ($this->db->errno === 1062 || str_contains($e->getMessage(), 'uk_contact_phone')) {
                throw new mysqli_sql_exception("Duplicate entry for (entity_type, entity_id, country_code, phone_number).", 1062);
            }
            throw $e;
        }
    }

    public function persistPhones(string $entityId, $phones, string $entityType = 'administrator'): void
    {
        $entityType = $this->normalizeEntityType($entityType);

        if (!is_array($phones))
            $phones = [];

        // IDs actuales en BD
        $existingIds = $this->listIdsByEntity($entityType, $entityId);
        $incomingIds = [];  // IDs que vienen en payload (existentes)
        $sawPrimary = false;

        foreach ($phones as $item) {
            if (!is_array($item))
                continue;

            $contactPhoneId = trim((string) ($item['contact_phone_id'] ?? ''));

            // Puede venir (country_code, phone_number) o 'raw'
            $countryCode = isset($item['country_code']) ? trim((string) $item['country_code']) : null;
            $phoneNumber = isset($item['phone_number']) ? trim((string) $item['phone_number']) : null;
            $raw = isset($item['raw']) ? trim((string) $item['raw']) : null;

            // Normalización mínima desde 'raw' si faltan partes
            if ((!$countryCode || !$phoneNumber) && $raw) {
                [$countryCode, $phoneNumber] = $this->normalizeRawPhone($raw);
            }

            // Saneamos CC de forma robusta
            if ($countryCode !== null) {
                $countryCode = $this->sanitizeCountryCode($countryCode, $phoneNumber ?? $raw ?? '');
            }

            // Limpiar símbolos del número (solo dígitos)
            if ($phoneNumber !== null) {
                $phoneNumber = preg_replace('/\D+/', '', $phoneNumber);
            }

            // Requisitos mínimos
            if (!$countryCode || !$phoneNumber)
                continue;

            $phoneType = array_key_exists('phone_type', $item) ? $this->normalizePhoneType((string)$item['phone_type']) : null;
            $isPrimary = $this->toIntBool($item['is_primary'] ?? 0);
            $isActive  = $this->toIntBool($item['is_active'] ?? 1);

            // Registrar ID entrante si vino (para protegerlo de borrado)
            if ($contactPhoneId !== '') {
                $incomingIds[] = $contactPhoneId;
            }

            $payload = [
                'entity_type'  => $entityType,
                'entity_id'    => $entityId,
                'phone_type'   => $phoneType,
                'country_code' => $countryCode,
                'phone_number' => $phoneNumber, // dígitos; create/update construyen display internamente
                'is_primary'   => $isPrimary,
                'is_active'    => $isActive,
            ];

            if ($contactPhoneId !== '') {
                $this->update($contactPhoneId, $payload);
            } else {
                $this->create($payload);
            }

            if ($isPrimary === 1)
                $sawPrimary = true;
        }

        // Eliminar los que ya no vienen
        $incomingSet = $this->safeFlipIds($incomingIds);
        foreach ($existingIds as $id) {
            if (!isset($incomingSet[$id])) {
                $this->delete($id); // soft-delete o delete según tu implementación
            }
        }

        // Garantiza un único primario si en el payload hubo alguno marcado
        if ($sawPrimary) {
            $this->forceSinglePrimary($entityType, $entityId, null);
        }
    }

    /**
     * Normalizador tolerante de teléfonos crudos (p.ej. "+58 412-123.45.67" o "(+1) (626)423-8682")
     * Devuelve [country_code, phone_number] con solo dígitos.
     */
    private function normalizeRawPhone(string $raw): array
    {
        $s = trim((string) $raw);
        // Quita espacios
        $s = preg_replace('/\s+/', '', $s);

        // 00-prefijo → '+'
        if (strpos($s, '00') === 0) {
            $s = '+' . substr($s, 2);
        }

        // Soporta "(+cc)..."
        if (preg_match('/^\(\+(\d{1,3})\)(.*)$/', $s, $m)) {
            $cc = $m[1];
            $rest = $m[2]; // "(626)423-8682" o "6264238682"
            $digits = preg_replace('/\D+/', '', $rest);
            return [$cc, $digits];
        }

        // Caso "+cc...."
        if (strpos($s, '+') === 0) {
            $digits = preg_replace('/\D+/', '', substr($s, 1));
            foreach ([3, 2, 1] as $len) {
                if (strlen($digits) > $len + 5) {
                    $cc = substr($digits, 0, $len);
                    $num = substr($digits, $len);
                    return [$cc, $num];
                }
            }
            // Fallback
            $cc = substr($digits, 0, 1);
            $num = substr($digits, 1);
            return [$cc, $num];
        }

        // Sin '+' ni "(+cc)": intenta detectar cc al inicio
        $digits = preg_replace('/\D+/', '', $s);
        foreach ([3, 2, 1] as $len) {
            if (strlen($digits) > $len + 5) {
                $cc = substr($digits, 0, $len);
                $num = substr($digits, $len);
                return [$cc, $num];
            }
        }

        // Por defecto: CC = 1 (US)
        return ['1', $digits];
    }

    public function delete(string $id): bool
    {
        $this->db->begin_transaction();
        try {
            $exists = $this->getById($id);
            if (!$exists)
                throw new mysqli_sql_exception("Registro no encontrado.");

            $userId = $_SESSION['user_id'] ?? null;

            $geoDbPath = defined('PROJECT_ROOT')
                ? PROJECT_ROOT . '/app/config/geolite.mmdb'
                : (__DIR__ . '/../config/geolite.mmdb');

            $env = new ClientEnvironmentInfo($geoDbPath);
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();

            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $userId;

            $stmt = $this->db->prepare("UPDATE {$this->table}
                                        SET deleted_at = ?, deleted_by = ?
                                        WHERE contact_phone_id = ? AND deleted_at IS NULL");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparando delete: " . $this->db->error);
            $stmt->bind_param("sss", $deletedAt, $deletedBy, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error ejecutando delete: " . $stmt->error);
            }
            $ok = $stmt->affected_rows > 0;
            $stmt->close();

            $this->db->commit();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function setPrimary(string $id): bool
    {
        $this->db->begin_transaction();
        try {
            $row = $this->getById($id);
            if (!$row)
                throw new mysqli_sql_exception("Registro no encontrado.");

            $this->forceSinglePrimary($row['entity_type'], $row['entity_id'], $id);

            $stmt = $this->db->prepare("UPDATE {$this->table}
                                        SET is_primary = 1
                                        WHERE contact_phone_id = ? AND deleted_at IS NULL");
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparando setPrimary: " . $this->db->error);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $ok = $stmt->affected_rows > 0;
            $stmt->close();

            $this->db->commit();
            return $ok;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
