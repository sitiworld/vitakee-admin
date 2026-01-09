<?php
require_once __DIR__ . '/../config/Database.php';


class SecondOpinionDataModel
{
    private \mysqli $db;
    private string $table = 'second_opinion_data';

    public function __construct(?\mysqli $db = null)
    {
        $this->db = $db ?: \Database::getInstance();
    }

    /* ===================== Helpers ===================== */

    private function uuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
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

    private function nowWithAudit(?string $userId): array
    {
        $env = new \ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
        $env->applyAuditContext($this->db, $userId);
        (new \TimezoneManager($this->db))->applyTimezone();

        return [
            'dt'  => $env->getCurrentDatetime(),
            'uid' => $userId
        ];
    }

    private function ensureExistsById(string $id): void
    {
        $stmt = $this->db->prepare("SELECT 1 FROM {$this->table} WHERE second_opinion_data_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$stmt) {
            throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            throw new mysqli_sql_exception("Record not found");
        }
    }

    /* ===================== Helpers nuevos ===================== */
private static function decodeJsonList(?string $json): array
{
    if (!$json) return [];
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}

private static function encodeJsonList(array $arr): ?string
{
    $arr = array_values(array_unique(array_filter(array_map('strval', $arr), fn($v) => $v !== '')));
    return empty($arr) ? null : json_encode($arr, JSON_UNESCAPED_UNICODE);
}

private static function mergeJsonLists(?string $existingJson, ?string $incomingJson): ?string
{
    $a = self::decodeJsonList($existingJson);
    $b = self::decodeJsonList($incomingJson);
    return self::encodeJsonList(array_merge($a, $b));
}

    /**
     * Normaliza varias formas de listas de IDs a un array plano de strings.
     * Acepta: array plano, array de objetos/arrays con {id:...}, CSV, JSON string, string simple.
     */
    private function parseIdList($raw): array
    {
        if ($raw === null || $raw === '') return [];

        // Si viene string JSON (array o array de objetos)
        if (is_string($raw) && strlen($raw)) {
            $try = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $try;
            }
        }

        // Array de objetos/arrays con {id:...}
        if (is_array($raw)) {
            // Caso: array plano de strings/números
            if (isset($raw[0]) && (is_string($raw[0]) || is_numeric($raw[0]))) {
                return array_values(array_filter(array_map(fn($v) => trim((string)$v), $raw), fn($v) => $v !== ''));
            }
            // Caso: array de objetos/arrays con key 'id'
            $out = [];
            foreach ($raw as $row) {
                if (is_array($row) && array_key_exists('id', $row)) {
                    $val = trim((string)$row['id']);
                    if ($val !== '') $out[] = $val;
                }
            }
            if (!empty($out)) return array_values(array_unique($out));
            // Fallback: si no hubo 'id', intenta aplanar valores escalares
            $out2 = [];
            array_walk_recursive($raw, function($v) use (&$out2) {
                if (is_string($v) || is_numeric($v)) {
                    $vv = trim((string)$v);
                    if ($vv !== '') $out2[] = $vv;
                }
            });
            return array_values(array_unique($out2));
        }

        // CSV o string simple
        $txt = trim((string)$raw);
        if ($txt === '') return [];
        if (strpos($txt, ',') !== false) {
            $parts = array_map('trim', explode(',', $txt));
            return array_values(array_filter($parts, fn($v) => $v !== ''));
        }
        // string simple (un id)
        return [$txt];
    }

    /**
     * Convierte entrada diversa (array, objetos, CSV, JSON, string) a JSON array string o NULL.
     * Por convención toma el campo "id" cuando vienen objetos.
     */
    private static function jsonIds($input, string $childKey = 'id'): ?string
    {
        if ($input === null || $input === '') return null;

        // Si es string y parece JSON, decodifica primero
        if (is_string($input) && strlen($input)) {
            $tmp = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $input = $tmp;
            }
        }

        // CSV
        if (is_string($input) && strpos($input, ',') !== false) {
            $arr = array_values(array_filter(array_map('trim', explode(',', $input))));
            return empty($arr) ? null : json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // Array plano
        if (is_array($input) && isset($input[0]) && (is_string($input[0]) || is_numeric($input[0]))) {
            $arr = array_values(array_filter(array_map('strval', $input)));
            return empty($arr) ? null : json_encode($arr, JSON_UNESCAPED_UNICODE);
        }

        // Array de objetos/arrays con {id:...}
        if (is_array($input)) {
            $out = [];
            foreach ($input as $row) {
                if (is_array($row) && array_key_exists($childKey, $row)) {
                    $val = (string)$row[$childKey];
                    if (trim($val) !== '') $out[] = $val;
                }
            }
            if (!empty($out)) {
                return json_encode(array_values(array_unique($out)), JSON_UNESCAPED_UNICODE);
            }
            // Fallback: aplana escalares
            $out2 = [];
            array_walk_recursive($input, function($v) use (&$out2) {
                if (is_string($v) || is_numeric($v)) {
                    $vv = trim((string)$v);
                    if ($vv !== '') $out2[] = $vv;
                }
            });
            return empty($out2) ? null : json_encode(array_values(array_unique($out2)), JSON_UNESCAPED_UNICODE);
        }

        // string simple (un id)
        if (is_string($input)) {
            $id = trim($input);
            return $id === '' ? null : json_encode([$id], JSON_UNESCAPED_UNICODE);
        }

        return null;
    }

    /** Resuelve pk por convención: nombre_tabla + '_id'. */
    private function guessPk(string $table): string
    {
        $special = [
            // 'tabla' => 'primary_key'
        ];
        if (isset($special[$table])) return $special[$table];
        return "{$table}_id";
    }

    private function getSecondOpinionRequest(string $secondOpinionId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM second_opinion_requests WHERE second_opinion_id = ? AND deleted_at IS NULL");
        if (!$stmt) throw new mysqli_sql_exception("Prepare failed (requests): " . $this->db->error);
        $stmt->bind_param("s", $secondOpinionId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    private function getPanelNameById(?string $panelId): ?string
    {
        if (empty($panelId)) return null;
        $stmt = $this->db->prepare("SELECT panel_name FROM test_panels WHERE panel_id = ? AND deleted_at IS NULL");
        if (!$stmt) throw new mysqli_sql_exception("Prepare failed (test_panels): " . $this->db->error);
        $stmt->bind_param("s", $panelId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row['panel_name'] ?? null;
    }

    /** Devuelve lista de name_db para una lista de biomarker_id */
    private function getBiomarkerNameDbs(array $biomarkerIds): array
    {
        if (empty($biomarkerIds)) return [];

        $placeholders = implode(',', array_fill(0, count($biomarkerIds), '?'));
        $types = str_repeat('s', count($biomarkerIds));
        $sql = "SELECT name_db FROM biomarkers WHERE biomarker_id IN ($placeholders) AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new mysqli_sql_exception("Prepare failed (biomarkers): " . $this->db->error);
        $stmt->bind_param($types, ...$biomarkerIds);
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            if (!empty($r['name_db'])) $out[] = $r['name_db'];
        }
        $stmt->close();
        return array_values(array_unique(array_map('trim', $out)));
    }

    /**
     * Core: resuelve registros de la tabla del panel según reglas y devuelve:
     * ['table' => ..., 'pk' => ..., 'columns'=>[...], 'rows'=>[...]]
     */
    private function resolvePanelData(
        string $secondOpinionId,
        ?string $panelId,
        ?string $biomarkersRaw,
        ?string $recordsRaw
    ): array {
        $request = $this->getSecondOpinionRequest($secondOpinionId);
        if (!$request) {
            return ['table'=>null, 'pk'=>null, 'columns'=>[], 'rows'=>[], 'request'=>null];
        }
        $userId = $request['user_id'] ?? null;

        $panelTable = $this->getPanelNameById($panelId);
        if (!$panelTable) {
            return ['table'=>null, 'pk'=>null, 'columns'=>[], 'rows'=>[], 'request'=>$request];
        }
        $pk = $this->guessPk($panelTable);

        $biomarkerIds = $this->parseIdList($biomarkersRaw);
        $recordIds    = $this->parseIdList($recordsRaw);

        $baseCols = [$pk, 'user_id', 'created_at', 'updated_at'];
        $biomarkerCols = $this->getBiomarkerNameDbs($biomarkerIds);

        $selectCols = '*';
        if (!empty($biomarkerCols)) {
            $all = array_values(array_unique(array_merge($baseCols, $biomarkerCols)));
            $safeCols = array_map(fn($c) => "`" . str_replace("`", "``", $c) . "`", $all);
            $selectCols = implode(',', $safeCols);
        }

        $where  = [];
        $params = [];
        $types  = '';

        if (!empty($userId)) {
            $where[]  = "user_id = ?";
            $params[] = $userId;
            $types   .= 's';
        }

        if (!empty($recordIds)) {
            $ph = implode(',', array_fill(0, count($recordIds), '?'));
            $where[]  = "$pk IN ($ph)";
            $params   = array_merge($params, $recordIds);
            $types   .= str_repeat('s', count($recordIds));
        }

        $where[] = "deleted_at IS NULL";

        $sql = "SELECT $selectCols FROM `{$panelTable}` WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) throw new mysqli_sql_exception("Prepare failed ({$panelTable}): " . $this->db->error);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return [
            'table'   => $panelTable,
            'pk'      => $pk,
            'columns' => !empty($biomarkerCols) ? array_values(array_unique(array_merge($baseCols, $biomarkerCols))) : [],
            'rows'    => $rows,
            'request' => $request
        ];
    }

/* ===================== Queries ===================== */

public function getAll(): array
{
    $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY created_at DESC";

    try {
        $res = $this->db->query($sql);
        if (!$res) {
            error_log("[SecondOpinionDataModel::getAll] Query error: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Query error: " . $this->db->error);
        }

        $rows = $res->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$r) {
            try {
                $resolved = $this->resolvePanelData(
                    $r['second_opinion_id'] ?? '',
                    $r['panel_id'] ?? null,
                    $r['biomarkers_id'] ?? null,
                    $r['records_id'] ?? null
                );
                $r['second_opinion_request'] = $resolved['request'];
                $r['resolved_table']         = $resolved['table'];
                $r['resolved_pk']            = $resolved['pk'];
                $r['resolved_columns']       = $resolved['columns'];
                $r['resolved_rows']          = $resolved['rows'];
            } catch (\Throwable $ex) {
                error_log("[SecondOpinionDataModel::getAll] Error en resolvePanelData: " . $ex->getMessage() .
                          " | second_opinion_id=" . ($r['second_opinion_id'] ?? 'NULL'));
                $r['__resolve_error'] = true;
            }
        }
        unset($r);

        return $rows;

    } catch (\Throwable $ex) {
        error_log("[SecondOpinionDataModel::getAll] Excepción: " . $ex->getMessage() . "\n" . $ex->getTraceAsString());
        throw $ex;
    }
}

public function getById(string $id): ?array
{
    $sql = "SELECT * FROM {$this->table} WHERE second_opinion_data_id = ? AND deleted_at IS NULL";
    try {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("[SecondOpinionDataModel::getById] Prepare failed: " . $this->db->error . " | SQL: $sql");
            throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }

        $stmt->bind_param("s", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            error_log("[SecondOpinionDataModel::getById] No se encontró registro con ID=$id");
            return null;
        }

        $resolved = $this->resolvePanelData(
            $row['second_opinion_id'] ?? '',
            $row['panel_id'] ?? null,
            $row['biomarkers_id'] ?? null,
            $row['records_id'] ?? null
        );

        $row['second_opinion_request'] = $resolved['request'];
        $row['resolved_table']         = $resolved['table'];
        $row['resolved_pk']            = $resolved['pk'];
        $row['resolved_columns']       = $resolved['columns'];
        $row['resolved_rows']          = $resolved['rows'];

        return $row;

    } catch (\Throwable $ex) {
        error_log("[SecondOpinionDataModel::getById] Excepción: " . $ex->getMessage() . "\n" . $ex->getTraceAsString());
        throw $ex;
    }
}


    public function listBySecondOpinionId(string $secondOpinionId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE second_opinion_id = ? AND deleted_at IS NULL ORDER BY created_at DESC");
        if (!$stmt) {
            throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("s", $secondOpinionId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    /* ===================== Mutations ===================== */

   public function create(array $data, bool $inTransaction = false): string
    {
        $second_opinion_id = trim($data['second_opinion_id'] ?? '');
        $share_type        = trim($data['share_type'] ?? '');
        $panel_id          = $data['panel_id'] ?? null;

        $biomarkers_json = self::jsonIds($data['biomarkers_id'] ?? null, 'id');
        $records_json    = self::jsonIds($data['records_id']    ?? null, 'id');

        if ($second_opinion_id === '') {
            throw new \mysqli_sql_exception("second_opinion_id is required");
        }
        if (($biomarkers_json !== null || $records_json !== null) && empty($panel_id)) {
            throw new \mysqli_sql_exception("panel_id is required when providing biomarkers_id or records_id");
        }

        $startedTxHere = false;
        if (!$inTransaction) {
            $this->db->begin_transaction();
            $startedTxHere = true;
        }

        try {
            $userId = $_SESSION['user_id'] ?? null;
            $audit  = $this->nowWithAudit($userId);

            // ¿Existe fila (second_opinion_id, panel_id)?
            $sqlSel = "
                SELECT second_opinion_data_id, biomarkers_id, records_id
                FROM {$this->table}
                WHERE second_opinion_id = ? AND " . (empty($panel_id) ? "panel_id IS NULL" : "panel_id = ?") . "
                  AND deleted_at IS NULL
                LIMIT 1
            ";
            $stmt = $this->db->prepare($sqlSel);
            if (!$stmt) throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);

            if (empty($panel_id)) {
                $stmt->bind_param("s", $second_opinion_id);
            } else {
                $stmt->bind_param("ss", $second_opinion_id, $panel_id);
            }
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($existing) {
                // MERGE
                $mergedBiomarkers = self::mergeJsonLists($existing['biomarkers_id'] ?? null, $biomarkers_json);
                $mergedRecords    = self::mergeJsonLists($existing['records_id']    ?? null, $records_json);

                $id  = $existing['second_opinion_data_id'];
                $upd = $this->db->prepare("
                    UPDATE {$this->table}
                    SET share_type = ?, biomarkers_id = ?, records_id = ?, updated_at = ?, updated_by = ?
                    WHERE second_opinion_data_id = ?
                ");
                if (!$upd) {
                    throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
                }

                // 1 share_type, 2 biomarkers_id, 3 records_id, 4 updated_at, 5 updated_by, 6 id
                $upd->bind_param("ssssss", $share_type, $mergedBiomarkers, $mergedRecords, $audit['dt'], $audit['uid'], $id);
                if (!$upd->execute()) {
                    throw new \mysqli_sql_exception("Execute failed: " . $upd->error);
                }
                $upd->close();

                if ($startedTxHere) $this->db->commit();
                return $id;
            }

            // INSERT
            $uuid = $this->uuid();
            $ins = $this->db->prepare("
                INSERT INTO {$this->table}
                    (second_opinion_data_id, second_opinion_id, share_type, panel_id, biomarkers_id, records_id, created_at, created_by)
                VALUES (?,?,?,?,?,?,?,?)
            ");
            if (!$ins) throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);

            $ins->bind_param(
                "ssssssss",
                $uuid,
                $second_opinion_id,
                $share_type,
                $panel_id,
                $biomarkers_json,
                $records_json,
                $audit['dt'],
                $audit['uid']
            );
            if (!$ins->execute()) throw new \mysqli_sql_exception("Execute failed: " . $ins->error);
            $ins->close();

            if ($startedTxHere) $this->db->commit();
            return $uuid;
        } catch (\Throwable $e) {
            if ($startedTxHere) $this->db->rollback();
            throw $e;
        }
    }

    public function update(string $id, array $data, bool $inTransaction = false): bool
    {
        $this->ensureExistsById($id);

        $stmt = $this->db->prepare("SELECT biomarkers_id, records_id FROM {$this->table} WHERE second_opinion_data_id = ? AND deleted_at IS NULL");
        if (!$stmt) throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $cur = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $share_type = $data['share_type'] ?? null;
        $panel_id   = $data['panel_id']   ?? null;

        $sets   = [];
        $params = [];
        $types  = "";

        if ($share_type !== null) { $sets[] = "share_type = ?"; $params[] = $share_type; $types .= "s"; }
        if ($panel_id   !== null) { $sets[] = "panel_id = ?";   $params[] = $panel_id;   $types .= "s"; }

        if (array_key_exists('biomarkers_id', $data)) {
            $incoming = self::jsonIds($data['biomarkers_id'], 'id');
            $merged   = self::mergeJsonLists($cur['biomarkers_id'] ?? null, $incoming);
            $sets[] = "biomarkers_id = ?"; $params[] = $merged; $types .= "s";
        }

        if (array_key_exists('records_id', $data)) {
            $incoming = self::jsonIds($data['records_id'], 'id');
            $merged   = self::mergeJsonLists($cur['records_id'] ?? null, $incoming);
            $sets[] = "records_id = ?"; $params[] = $merged; $types .= "s";
        }

        if (empty($sets)) return true;

        $startedTxHere = false;
        if (!$inTransaction) {
            $this->db->begin_transaction();
            $startedTxHere = true;
        }

        try {
            $userId = $_SESSION['user_id'] ?? null;
            $audit  = $this->nowWithAudit($userId);

            $sets[]   = "updated_at = ?";
            $sets[]   = "updated_by = ?";
            $params[] = $audit['dt'];
            $params[] = $audit['uid'];
            $types   .= "ss";

            $sql = "UPDATE {$this->table} SET " . implode(", ", $sets) . " WHERE second_opinion_data_id = ?";
            $params[] = $id; $types .= "s";

            $upd = $this->db->prepare($sql);
            if (!$upd) throw new \mysqli_sql_exception("Prepare failed: " . $this->db->error);

            // Importante: sin mezclar unpacking y argumentos posicionales
            $upd->bind_param($types, ...$params);

            if (!$upd->execute()) throw new \mysqli_sql_exception("Execute failed: " . $upd->error);
            $upd->close();

            if ($startedTxHere) $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($startedTxHere) $this->db->rollback();
            throw $e;
        }
    }



    public function delete(string $id): bool
    {
        $this->ensureExistsById($id);

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $audit  = $this->nowWithAudit($userId);

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE second_opinion_data_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
            }
            $stmt->bind_param("sss", $audit['dt'], $audit['uid'], $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Execute failed: " . $stmt->error);
            }
            $ok = $stmt->affected_rows > 0;
            $stmt->close();

            $this->db->commit();
            return $ok;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
