<?php
require_once __DIR__ . '/../config/Database.php';


class ContactEmailModel
{
    private $db;
    private string $table = 'contact_emails';


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

    private function normalizeEntity(string $entityType): string
    {
        // Normaliza capitalización sin restringir a lista cerrada
        return trim($entityType);
    }

    private function forceSinglePrimary(string $entityType, string $entityId, ?string $excludeId = null): void
    {
        // Pone is_primary=0 para los demás correos del mismo entity
        $sql = "UPDATE {$this->table}
                SET is_primary = 0
                WHERE entity_type = ? AND entity_id = ? AND deleted_at IS NULL";
        if (!empty($excludeId)) {
            $sql .= " AND contact_email_id <> ?";
        }
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando normalización de primario: " . $this->db->error);

        if (!empty($excludeId)) {
            $stmt->bind_param("sss", $entityType, $entityId, $excludeId);
        } else {
            $stmt->bind_param("ss", $entityType, $entityId);
        }
        $stmt->execute();
        $stmt->close();
    }

    private function emailExistsForEntity(string $entityType, string $entityId, string $email, ?string $excludeId = null): bool
    {
        $sql = "SELECT contact_email_id FROM {$this->table}
                WHERE entity_type = ? AND entity_id = ? AND email = ? AND deleted_at IS NULL";
        if (!empty($excludeId)) {
            $sql .= " AND contact_email_id <> ?";
        }
        $stmt = $this->db->prepare($sql);
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando verificación: " . $this->db->error);

        if (!empty($excludeId)) {
            $stmt->bind_param("ssss", $entityType, $entityId, $email, $excludeId);
        } else {
            $stmt->bind_param("sss", $entityType, $entityId, $email);
        }
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /* ===================== Queries ===================== */
    public function getByEmail(string $email, string $entityType = ''): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL";
        $types = "s";
        $params = [$email];

        if ($entityType !== '') {
            $sql .= " AND entity_type = ?";
            $types .= "s";
            $params[] = $entityType;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
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
        $sql = "SELECT * FROM {$this->table}
                WHERE deleted_at IS NULL
                ORDER BY created_at DESC";
        $res = $this->db->query($sql);
        if (!$res)
            throw new mysqli_sql_exception("Query error: " . $this->db->error);
        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}
                                    WHERE contact_email_id = ? AND deleted_at IS NULL");
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
        $entityType = $this->normalizeEntity($entityType);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}
                                    WHERE entity_type = ? AND entity_id = ? AND deleted_at IS NULL
                                    ORDER BY is_primary DESC, created_at DESC");
        if (!$stmt)
            throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        $stmt->bind_param("ss", $entityType, $entityId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    /* ===================== Mutations ===================== */
public function create(array $data): bool
{
    $this->db->begin_transaction();
    try {
        // Contexto de auditoría y hora actual
        [$env, $userId] = $this->applyAuditContext();
        $now  = $env->getCurrentDatetime();
        $uuid = $this->generateUUIDv4();

        // Normalizaciones
        $entityType = $this->normalizeEntity((string)($data['entity_type'] ?? ''));
        $entityId   = trim((string)($data['entity_id'] ?? ''));
        $email      = strtolower(trim((string)($data['email'] ?? '')));
        $isPrimary  = isset($data['is_primary']) ? (int) !!$data['is_primary'] : 0;
        $isActive   = isset($data['is_active'])  ? (int) !!$data['is_active']  : 1;

        // Validaciones mínimas
        if ($entityType === '' || $entityId === '' || $email === '') {
            throw new mysqli_sql_exception("entity_type, entity_id and email are required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new mysqli_sql_exception("Invalid email format.");
        }

        // Chequeo de duplicados (incluye reactivación si estaba soft-deleted)
        $existing = $this->findByEntityAndEmail($entityType, $entityId, $email);
        if ($existing && $existing['deleted_at'] !== null) {
            $sql = "UPDATE {$this->table}
                    SET is_active = ?, deleted_at = NULL, deleted_by = NULL,
                        updated_at = ?, updated_by = ?
                    WHERE contact_email_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("isss", $isActive, $now, $userId, $existing['contact_email_id']);
            $stmt->execute();
            $stmt->close();

            // Si se reactiva como primario, baja a los demás
            if ($isPrimary === 1) {
                $this->forceSinglePrimary($entityType, $entityId, $existing['contact_email_id']);
                $stmtP = $this->db->prepare("UPDATE {$this->table} SET is_primary = 1, updated_at = ?, updated_by = ? WHERE contact_email_id = ?");
                $stmtP->bind_param("sss", $now, $userId, $existing['contact_email_id']);
                $stmtP->execute();
                $stmtP->close();
            }

            $this->db->commit();
            return true;
        }
        if ($existing && $existing['deleted_at'] === null) {
            // Respetar UK y mensaje consistente
            throw new mysqli_sql_exception("Duplicate email for this entity (uk_contact_email).");
        }

        // Si va a ser primario, baja a los demás antes de insertar
        if ($isPrimary === 1) {
            $this->forceSinglePrimary($entityType, $entityId, null);
        }

        // Insert
        $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (contact_email_id, entity_type, entity_id, email, is_primary, is_active, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiis", $uuid, $entityType, $entityId, $email, $isPrimary, $isActive, $now, $userId);
        $stmt->execute();
        $stmt->close();

        $this->db->commit();
        return true;
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        // Traducir violaciones de índice único a un mensaje consistente
        if ($this->db->errno === 1062 || str_contains($e->getMessage(), 'uk_contact_email')) {
            throw new mysqli_sql_exception("Duplicate entry for (entity_type, entity_id, email).", 1062);
        }
        throw $e;
    }
}
    /** Buscar duplicados por entity + email */
    private function findByEntityAndEmail(string $entityType, string $entityId, string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE entity_type = ? AND entity_id = ? AND email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparing search: " . $this->db->error);
        }
        $stmt->bind_param("sss", $entityType, $entityId, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc() ?: null;
        $stmt->close();
        return $row;
    }
    // Dentro de la clase ContactEmailModel (y ContactPhoneModel si aplica)
private function applyAuditContext(): array
{
    // Toma el usuario logueado (puede ser null)
    $userId = $_SESSION['user_id'] ?? null;

    // Carga y aplica contexto de auditoría + timezone (igual que hacías antes)
    $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
    $env->applyAuditContext($this->db, $userId);
    (new TimezoneManager($this->db))->applyTimezone();

    // Devuelve lo que esperan tus métodos create/update
    return [$env, $userId];
}
    private function mustExist(string $id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE contact_email_id = ? AND deleted_at IS NULL");
        if (!$stmt) {
            throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            throw new mysqli_sql_exception("Contact email not found.");
        }
        return $row;
    }
public function update(string $id, array $data): bool
{
    $this->db->begin_transaction();
    try {
        // Asegurar existencia
        $this->mustExist($id);

        // Contexto de auditoría y hora actual
        [$env, $userId] = $this->applyAuditContext();
        $now = $env->getCurrentDatetime();

        // Cargar valores actuales para validaciones/comparaciones
        $stmtCur = $this->db->prepare("SELECT entity_type, entity_id, email FROM {$this->table} WHERE contact_email_id = ? LIMIT 1");
        $stmtCur->bind_param("s", $id);
        $stmtCur->execute();
        $current = $stmtCur->get_result()->fetch_assoc();
        $stmtCur->close();

        if (!$current) {
            throw new mysqli_sql_exception("Contact email not found.");
        }

        // Proyectar nuevos valores (sin tocar lo que no venga en $data)
        $entityType = array_key_exists('entity_type', $data) ? $this->normalizeEntity((string)$data['entity_type']) : $current['entity_type'];
        $entityId   = array_key_exists('entity_id',   $data) ? trim((string)$data['entity_id'])   : $current['entity_id'];
        $email      = array_key_exists('email',       $data) ? strtolower(trim((string)$data['email'])) : $current['email'];
        $isPrimary  = $data['is_primary'] ?? null; // null => no cambiar
        $isActive   = $data['is_active']  ?? null;

        // Validaciones si cambian campos clave
        if ($entityType === '' || $entityId === '' || $email === '') {
            throw new mysqli_sql_exception("entity_type, entity_id and email are required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new mysqli_sql_exception("Invalid email format.");
        }

        // Verificación de duplicados si se modifica alguno de los componentes de la UK
        if ($entityType !== $current['entity_type'] || $entityId !== $current['entity_id'] || $email !== $current['email']) {
            $dup = $this->findByEntityAndEmail($entityType, $entityId, $email);
            if ($dup && $dup['contact_email_id'] !== $id && $dup['deleted_at'] === null) {
                throw new mysqli_sql_exception("Duplicate email for this entity (uk_contact_email).");
            }
            if ($dup && $dup['contact_email_id'] !== $id && $dup['deleted_at'] !== null) {
                // Si existe soft-deleted el mismo email para esa entidad, “fusionar” reactivando ese y (opcional) marcando este como borrado
                $sql = "UPDATE {$this->table}
                        SET is_active = COALESCE(?, 1), deleted_at = NULL, deleted_by = NULL, updated_at = ?, updated_by = ?
                        WHERE contact_email_id = ?";
                $stmt = $this->db->prepare($sql);
                $act  = ($isActive === null) ? 1 : (int)!!$isActive;
                $stmt->bind_param("isss", $act, $now, $userId, $dup['contact_email_id']);
                $stmt->execute();
                $stmt->close();

                // Opcional: podrías soft-delete el registro actual si ya no se usará
                $stmtDel = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE contact_email_id = ?");
                $stmtDel->bind_param("sss", $now, $userId, $id);
                $stmtDel->execute();
                $stmtDel->close();

                // Asegurar unicidad de primario si se marcó como tal
                if ($isPrimary !== null && (int)!!$isPrimary === 1) {
                    $this->forceSinglePrimary($entityType, $entityId, $dup['contact_email_id']);
                    $stmtP = $this->db->prepare("UPDATE {$this->table} SET is_primary = 1, updated_at = ?, updated_by = ? WHERE contact_email_id = ?");
                    $stmtP->bind_param("sss", $now, $userId, $dup['contact_email_id']);
                    $stmtP->execute();
                    $stmtP->close();
                }

                $this->db->commit();
                return true;
            }
        }

        // Si se pedirá primario, primero bajamos a los demás
        if ($isPrimary !== null && (int)!!$isPrimary === 1) {
            $this->forceSinglePrimary($entityType, $entityId, $id);
        }

        // Armado dinámico del UPDATE
        $fields = [];
        $params = [];
        $types  = '';

        if (array_key_exists('entity_type', $data)) { $fields[] = "entity_type = ?"; $params[] = $entityType; $types .= 's'; }
        if (array_key_exists('entity_id',   $data)) { $fields[] = "entity_id = ?";   $params[] = $entityId;   $types .= 's'; }
        if (array_key_exists('email',       $data)) { $fields[] = "email = ?";       $params[] = $email;      $types .= 's'; }
        if ($isPrimary !== null)                   { $fields[] = "is_primary = ?";  $params[] = (int)!!$isPrimary; $types .= 'i'; }
        if ($isActive  !== null)                   { $fields[] = "is_active = ?";   $params[] = (int)!!$isActive;  $types .= 'i'; }

        $fields[] = "updated_at = ?"; $params[] = $now;    $types .= 's';
        $fields[] = "updated_by = ?"; $params[] = $userId; $types .= 's';

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE contact_email_id = ?";
        $stmt = $this->db->prepare($sql);
        $types .= 's';
        $params[] = $id;
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();

        $this->db->commit();
        return true;
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        if ($this->db->errno === 1062 || str_contains($e->getMessage(), 'uk_contact_email')) {
            throw new mysqli_sql_exception("Duplicate entry for (entity_type, entity_id, email).", 1062);
        }
        throw $e;
    }
}
// Añade dentro de la clase (privado)
// Convierte valores "boolean-like" a 0/1 (acepta '1', 1, true, 'true', etc.)
private function toIntBool($v): int
{
    if (is_bool($v)) return $v ? 1 : 0;
    $s = strtolower(trim((string)$v));
    return in_array($s, ['1','true','yes','y','on'], true) ? 1 : 0;
}

// Crea un "set" seguro de IDs: ['id1' => true, 'id2' => true, ...]
private function safeFlipIds(array $maybeIds): array
{
    $set = [];
    foreach ($maybeIds as $item) {
        $id = null;
        if (is_array($item)) {
            // intenta con claves comunes por si te pasan filas completas
            $id = $item['contact_email_id'] ?? $item['contact_phone_id'] ?? $item['id'] ?? null;
        } else {
            $id = $item;
        }
        if ($id !== null) {
            $id = trim((string)$id);
            if ($id !== '') {
                $set[$id] = true;
            }
        }
    }
    return $set;
}


    public function delete(string $id): bool
    {
        $this->db->begin_transaction();
        try {
            $exists = $this->getById($id);
            if (!$exists) {
                throw new mysqli_sql_exception("Registro no encontrado.");
            }

            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();

            $deletedAt = $env->getCurrentDatetime();
            $deletedBy = $userId;

            $stmt = $this->db->prepare("UPDATE {$this->table}
                                        SET deleted_at = ?, deleted_by = ?
                                        WHERE contact_email_id = ? AND deleted_at IS NULL");
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
        // Marca este como primario y baja los demás del mismo entity
        $this->db->begin_transaction();
        try {
            $row = $this->getById($id);
            if (!$row)
                throw new mysqli_sql_exception("Registro no encontrado.");

            $entityType = $row['entity_type'];
            $entityId = $row['entity_id'];

            $this->forceSinglePrimary($entityType, $entityId, $id);

            $stmt = $this->db->prepare("UPDATE {$this->table}
                                        SET is_primary = 1
                                        WHERE contact_email_id = ? AND deleted_at IS NULL");
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

    public function listIdsByEntity(string $entityType, string $entityId): array
    {
        $stmt = $this->db->prepare("
        SELECT contact_email_id
        FROM {$this->table}
        WHERE entity_type = ? AND entity_id = ? AND deleted_at IS NULL
    ");
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("ss", $entityType, $entityId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return array_map(fn($r) => $r['contact_email_id'], $rows);
    }

public function persistEmails(string $entityId, $emails, string $entityType = 'administrator'): void
{
    if (!is_array($emails)) $emails = [];

    // IDs actuales en BD
    $existingIds = $this->listIdsByEntity($entityType, $entityId); // array de strings
    $incomingIds = [];   // solo IDs que ya existían y vienen en el payload (para no borrarlos)
    $sawPrimary  = false;

    foreach ($emails as $item) {
        if (!is_array($item)) continue;

        $contactEmailId = trim((string)($item['contact_email_id'] ?? ''));
        $email          = strtolower(trim((string)($item['email'] ?? '')));
        $isPrimary      = $this->toIntBool($item['is_primary'] ?? 0);
        $isActive       = $this->toIntBool($item['is_active']  ?? 1);

        // Registrar ID de entrada solo si viene (para protegerlo de borrado)
        if ($contactEmailId !== '') {
            $incomingIds[] = $contactEmailId;
        }

        // Sin email => ignorar entrada
        if ($email === '') continue;

        // Validación formato
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \mysqli_sql_exception("Formato de email inválido: {$email}");
        }

        $payload = [
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'email'       => $email,
            'is_primary'  => $isPrimary,
            'is_active'   => $isActive,
        ];

        if ($contactEmailId !== '') {
            // update
            $this->update($contactEmailId, $payload);
        } else {
            // create (no necesitamos el ID para calcular deletions)
            $this->create($payload);
        }

        if ($isPrimary === 1) $sawPrimary = true;
    }

    // Eliminar los que ya no vienen (existían en BD pero no llegaron en el payload)
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


}
