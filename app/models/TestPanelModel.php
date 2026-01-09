<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/ClientEnvironmentInfo.php';
require_once __DIR__ . '/../config/TimezoneManager.php';

class TestPanelModel
{
    private $db;
    private $table = "test_panels";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll(): array
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            $query = "SELECT panel_id, panel_name, display_name, display_name_es 
                      FROM {$this->table} 
                      WHERE deleted_at IS NULL 
                      ORDER BY display_name";

            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener paneles de prueba: " . $this->db->error);
            }

            $panels = [];
            while ($row = $result->fetch_assoc()) {
                $row['translated_name'] = ($idioma === 'ES') ? $row['display_name_es'] : $row['display_name'];
                $panels[] = $row;
            }

            return $panels;
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT panel_id, panel_name, display_name 
                                        FROM {$this->table} 
                                        WHERE panel_id = ? AND deleted_at IS NULL 
                                        LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc() ?: null;
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $created_at = $env->getCurrentDatetime(); // ← Fecha con zona horaria aplicada

            // Generar UUID para panel_id
            $panelId = $this->generateUUIDv4();

            $query = "INSERT INTO {$this->table} 
                  (panel_id, panel_name, display_name, created_at, created_by) 
                  VALUES (?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param("sssss", $panelId, $data['panel_name'], $data['display_name'], $created_at, $userId);
            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return false;
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


    public function countUserRecordsByPanelName($user_id): array
    {
        $result = [];
        $idioma = $_SESSION['idioma'] ?? 'EN';
        $panels = $this->getAll(); // Debe retornar también 'id' de cada panel

        foreach ($panels as $panel) {
            $panelId = $panel['panel_id'] ?? null;
            $table = $panel['panel_name'];
            $translatedName = $panel['translated_name'];

            $sql = "SELECT COUNT(*) as total FROM `$table` WHERE user_id = ? AND deleted_at IS NULL";

            try {
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparando consulta para la tabla $table: " . $this->db->error);
                }

                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $res = $stmt->get_result();
                $row = $res->fetch_assoc();

                $result[] = [
                    'id' => $panelId,
                    'panel_name' => $table,
                    'translated_name' => $translatedName,
                    'record_count' => intval($row['total']),
                ];
            } catch (mysqli_sql_exception $e) {
                $result[] = [
                    'id' => $panelId,
                    'panel_name' => $table,
                    'translated_name' => $translatedName,
                    'record_count' => 0,
                ];
            }
        }

        return $result;
    }

    private function buildPanelBiomarkerMappings(?string $sex_biological = null): array
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            $queryPanels = "SELECT panel_id, panel_name FROM test_panels WHERE deleted_at IS NULL ORDER BY display_name";
            $resultPanels = $this->db->query($queryPanels);
            if (!$resultPanels) {
                throw new mysqli_sql_exception("Error al obtener paneles: " . $this->db->error);
            }

            $table_map = [];
            $date_fields = [];
            $time_fields = [];
            $id_fields = [];

            while ($panel = $resultPanels->fetch_assoc()) {
                $panel_id = $panel['panel_id'];
                $panel_name = strtolower(trim($panel['panel_name']));
                $table_map[$panel_id] = $panel_name;

                $resultDesc = $this->db->query("DESCRIBE {$panel_name}");
                if (!$resultDesc) {
                    throw new mysqli_sql_exception("Error al obtener estructura de tabla {$panel_name}: " . $this->db->error);
                }

                while ($col = $resultDesc->fetch_assoc()) {
                    $field = $col['Field'];
                    if (preg_match('/_date$/', $field)) {
                        $date_fields[$panel_id] = $field;
                    }
                    if (preg_match('/_time$/', $field)) {
                        $time_fields[$panel_id] = $field;
                    }
                    if (
                        ($field === 'id') ||
                        (str_ends_with($field, '_id') && $field !== 'user_id' && $field !== 'biomarker_id' && $field !== 'panel_id')
                    ) {
                        // Solo usarlo si aún no se ha definido para evitar que lo sobreescriba otro _id
                        if (!isset($id_fields[$panel_id])) {
                            $id_fields[$panel_id] = $field;
                        }
                    }

                }
            }

            // Traer todos los biomarcadores
            $queryBiomarkers = "SELECT panel_id, name, name_es FROM biomarkers ORDER BY name";
            $resultBiomarkers = $this->db->query($queryBiomarkers);
            if (!$resultBiomarkers) {
                throw new mysqli_sql_exception("Error al obtener biomarcadores: " . $this->db->error);
            }

            $field_map = [
                'male' => [],
                'female' => [],
                'neutral' => [],
            ];

            $biomarkerFieldMapping = [
                'glucose' => 'glucose',
                'ketones' => 'ketone',
                'body mass index (bmi)' => 'bmi',
                'body fat percentage - male' => 'body_fat_pct',
                'body fat percentage - female' => 'body_fat_pct',
                'total body water percentage' => 'water_pct',
                'muscle mass percentage - male' => 'muscle_pct',
                'muscle mass percentage - female' => 'muscle_pct',
                'resting metabolic rate (rmr)' => 'resting_metabolism',
                'visceral fat level' => 'visceral_fat',
                'body age' => 'body_age',
                'weight (lb)' => 'weight_lb',
                'ldl cholesterol' => 'ldl',
                'hdl cholesterol - male' => 'hdl',
                'hdl cholesterol - female' => 'hdl',
                'total cholesterol' => 'total_cholesterol',
                'triglycerides' => 'triglycerides',
                'non-hdl cholesterol' => 'non_hdl',
                'albumin' => 'albumin',
                'creatinine' => 'creatinine',
                'albumin-to-creatinine ratio' => 'acr'
            ];

            while ($biomarker = $resultBiomarkers->fetch_assoc()) {
                $panel_id = $biomarker['panel_id'];
                $bm_name_original = trim($biomarker['name']);
                $bm_name = strtolower($bm_name_original);

                $field_key = $biomarkerFieldMapping[$bm_name] ?? strtolower(str_replace([' ', '-', '(', ')'], ['_', '_', '', ''], $bm_name));

                if (str_contains($bm_name_original, 'Male')) {
                    $field_map['male'][$panel_id][$bm_name] = [$field_key];
                } elseif (str_contains($bm_name_original, 'Female')) {
                    $field_map['female'][$panel_id][$bm_name] = [$field_key];
                } else {
                    $field_map['neutral'][$panel_id][$bm_name] = [$field_key];
                }
            }

            return [
                'table_map' => $table_map,
                'date_fields' => $date_fields,
                'time_fields' => $time_fields,
                'id_fields' => $id_fields,
                'field_map' => $field_map
            ];

        } catch (mysqli_sql_exception $e) {
            return [
                'table_map' => [],
                'date_fields' => [],
                'time_fields' => [],
                'id_fields' => [],
                'field_map' => [],
                'error' => $e->getMessage()
            ];
        }
    }
public function getBiomarkersByPanelId(string $panel_id): array
{
    try {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $useSpanish = $idioma === 'ES';

        $sql = "SELECT biomarker_id, name, name_es, description, description_es
                FROM biomarkers
                WHERE panel_id = ? AND deleted_at IS NULL
                ORDER BY name ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        }

        $stmt->bind_param("s", $panel_id);
        $stmt->execute();
        $res = $stmt->get_result();

        // Normaliza sexo
        $rawSex = $_SESSION['sex_biological'] ?? 'M';
        $userSex = is_string($rawSex) ? trim($rawSex) : 'M';

        $biomarkers = [];

        while ($row = $res->fetch_assoc()) {
            $name = strtolower(trim((string)($row['name'] ?? '')));

            // Detecta etiquetas de género
            $isFemaleTagged = (bool)preg_match('/\bfemale\b/i', $name);
            $isMaleTagged   = (bool)preg_match('/\bmale\b/i', $name);

            // Reglas de inclusión
            if ($userSex === 'm' && $isFemaleTagged) {
                continue; // excluye solo los femeninos
            }
            if ($userSex === 'f' && $isMaleTagged) {
                continue; // excluye solo los masculinos
            }
            // Si es 'M', incluye todo (no continua)
            // Si es 'u' o cualquier otro, incluye neutros y todos los que no estén etiquetados

            $biomarkers[] = [
                'biomarker_id' => $row['biomarker_id'],
                'name' => $useSpanish && !empty($row['name_es']) ? $row['name_es'] : $row['name'],
                'description' => $useSpanish && !empty($row['description_es']) ? $row['description_es'] : $row['description'],
            ];
        }

        return $biomarkers;
    } catch (\Throwable $e) {
        // opcional: error_log("[getBiomarkersByPanelId] " . $e->getMessage());
        return [];
    }
}


    public function getBiomarkersByPanelId2(string $panel_id): array
    {
        try {
            $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
            $useSpanish = ($idioma === 'ES');

            // Mapeo conocido: clave = nombre EN normalizado, valor = columna real en DB (ej. body_composition / labs, etc.)
            $biomarkerFieldMapping = [
                'glucose' => 'glucose',
                'ketones' => 'ketone',
                'body mass index (bmi)' => 'bmi',
                'body fat percentage - male' => 'body_fat_pct',
                'body fat percentage - female' => 'body_fat_pct',
                'total body water percentage' => 'water_pct',
                'muscle mass percentage - male' => 'muscle_pct',
                'muscle mass percentage - female' => 'muscle_pct',
                'resting metabolic rate (rmr)' => 'resting_metabolism',
                'visceral fat level' => 'visceral_fat',
                'body age' => 'body_age',
                'weight (lb)' => 'weight_lb',
                'ldl cholesterol' => 'ldl',
                'hdl cholesterol - male' => 'hdl',
                'hdl cholesterol - female' => 'hdl',
                'total cholesterol' => 'total_cholesterol',
                'triglycerides' => 'triglycerides',
                'non-hdl cholesterol' => 'non_hdl',
                'albumin' => 'albumin',
                'creatinine' => 'creatinine',
                'albumin-to-creatinine ratio' => 'acr',
                'uric acid blood' => 'uric_acid_blood',
                'serum creatinine' => 'serum_creatinine',
                'bun blood' => 'bun_blood',
                'egfr' => 'egfr',
                'hba1c' => 'HbA1c',
            ];

            // OJO: incluimos name_db y campos útiles por si quieres mostrarlos
            $sql = "SELECT
                    biomarker_id, panel_id,
                    name, name_es,
                    description, description_es,
                    unit, reference_min, reference_max,
                    deficiency_label, excess_label,
                    deficiency_es, excess_es,
                    name_db
                FROM biomarkers
                WHERE panel_id = ? AND deleted_at IS NULL
                ORDER BY name ASC";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new \mysqli_sql_exception('Error preparando consulta: ' . $this->db->error);
            }

            $stmt->bind_param('s', $panel_id);
            $stmt->execute();
            $res = $stmt->get_result();

            $biomarkers = [];
            while ($row = $res->fetch_assoc()) {
                // Texto según idioma
                $nameOut = $useSpanish && !empty($row['name_es']) ? $row['name_es'] : $row['name'];
                $descriptionOut = $useSpanish && !empty($row['description_es']) ? $row['description_es'] : $row['description'];
                $defLabelOut = $useSpanish && !empty($row['deficiency_es']) ? $row['deficiency_es'] : $row['deficiency_label'];
                $excLabelOut = $useSpanish && !empty($row['excess_es']) ? $row['excess_es'] : $row['excess_label'];

                // Determinar db_column y original_column
                // 1) Si hay mapeo por nombre EN conocido, úsalo (coincide con tu función "parecida a esta").
                // 2) Si no hay mapeo pero existe name_db en la tabla, úsalo.
                // 3) Si no, generar alias limpio a partir del nombre mostrado en el idioma actual.
                $nameEnglishNormalized = strtolower(trim($row['name']));
                $dbColumn = null;
                $originalColumn = null;

                if (isset($biomarkerFieldMapping[$nameEnglishNormalized])) {
                    $dbColumn = $biomarkerFieldMapping[$nameEnglishNormalized];
                    $originalColumn = $dbColumn; // igual que en tu ejemplo
                } elseif (!empty($row['name_db'])) {
                    $dbColumn = $row['name_db'];
                    $originalColumn = null; // no proviene del mapping explícito
                } else {
                    // Fallback: alias automático legible y seguro
                    $base = strtolower(trim($useSpanish ? $row['name_es'] : $row['name']));
                    $generated = preg_replace('/[^a-z0-9_]/', '_', $base);
                    $generated = preg_replace('/_+/', '_', $generated);
                    $generated = trim($generated, '_');
                    $dbColumn = $generated ?: 'biomarker_' . substr($row['biomarker_id'], 0, 8);
                    $originalColumn = null;
                }

                $biomarkers[] = [
                    'biomarker_id' => $row['biomarker_id'],
                    'name' => $nameOut,
                    'description' => $descriptionOut,
                    'unit' => $row['unit'],
                    'reference_min' => $row['reference_min'],
                    'reference_max' => $row['reference_max'],
                    'deficiency_label' => $defLabelOut,
                    'excess_label' => $excLabelOut,
                    'db_column' => $dbColumn,
                    'original_column' => $originalColumn,
                    // opcionales por si los necesitas aguas abajo:
                    'name_db' => $row['name_db'],
                ];
            }

            $stmt->close();
            return $biomarkers;

        } catch (\Throwable $e) {
            // Puedes registrar el error si quieres
            // error_log("[getBiomarkersByPanelId] " . $e->getMessage());
            return [];
        }
    }

    public function getAllRecordsByPanelId(string $panel_id): array
    {
        try {
            // 1) Resolver la tabla a partir de test_panels
            $sqlPanel = "SELECT panel_name 
                     FROM test_panels 
                     WHERE panel_id = ? AND deleted_at IS NULL";
            $stmt = $this->db->prepare($sqlPanel);
            if (!$stmt) {
                throw new \mysqli_sql_exception("[getAllRecordsByPanelId] Error preparando lookup panel: " . $this->db->error);
            }
            $stmt->bind_param("s", $panel_id);
            $stmt->execute();
            $resPanel = $stmt->get_result();
            $rowPanel = $resPanel->fetch_assoc();
            $stmt->close();

            if (!$rowPanel || empty($rowPanel['panel_name'])) {
                throw new \mysqli_sql_exception("[getAllRecordsByPanelId] Panel no encontrado o sin panel_name. panel_id={$panel_id}");
            }

            $table = trim($rowPanel['panel_name']);
            error_log("[getAllRecordsByPanelId][info] panel_id={$panel_id} -> table={$table}");

            // 2) Armar consulta base
            $sql = "SELECT * FROM `{$table}`";
            if ($this->tableHasColumn($table, 'deleted_at')) {
                $sql .= " WHERE deleted_at IS NULL";
            }

            // 3) Ejecutar consulta
            $res = $this->db->query($sql);
            if (!$res) {
                throw new \mysqli_sql_exception("[getAllRecordsByPanelId] Error ejecutando consulta: " . $this->db->error);
            }

            // 4) Retornar todos los registros tal cual
            $rows = [];
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            return $rows;

        } catch (\Throwable $e) {
            error_log("[getAllRecordsByPanelId][error] panel_id={$panel_id} msg=" . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si una tabla tiene una columna dada.
     */
    private function tableHasColumn(string $table, string $column): bool
    {
        $tableEsc = $this->db->real_escape_string($table);
        $colEsc = $this->db->real_escape_string($column);
        $sql = "SHOW COLUMNS FROM `{$tableEsc}` LIKE '{$colEsc}'";
        $res = $this->db->query($sql);
        return $res && $res->num_rows > 0;
    }




    public function getUserRecordsByPanelId($user_id, $panel_id): array
{
    try {
        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $useSpanish = $idioma === 'ES';

        // 1) Obtener sexo del usuario (respetar 'M' como "ambos")
        $stmtSex = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
        if (!$stmtSex) {
            throw new \Exception("Error preparando consulta de sexo: " . $this->db->error);
        }
        $stmtSex->bind_param("s", $user_id);
        $stmtSex->execute();
        $userData = $stmtSex->get_result()->fetch_assoc();
        if (!$userData) {
            throw new \Exception("Usuario no encontrado.");
        }

        $rawSex = is_string($userData['sex_biological'] ?? '') ? trim($userData['sex_biological']) : '';
        // Normalización: 'm', 'f', 'M' (M = ambos). Otro/ vacío => 'u'
        if ($rawSex === 'M') {
            $sex_biological = 'M';
        } else {
            $lc = strtolower($rawSex);
            $sex_biological = in_array($lc, ['m','f'], true) ? $lc : 'u';
        }

        // 2) Obtener mapeos por sexo
        $mappings = $this->buildPanelBiomarkerMappings($sex_biological);
        if (!isset($mappings['table_map'][$panel_id])) {
            throw new \Exception("Panel no mapeado.");
        }

        $table    = $mappings['table_map'][$panel_id];
        $dateField= $mappings['date_fields'][$panel_id] ?? null;
        $timeField= $mappings['time_fields'][$panel_id] ?? null;
        $idField  = $mappings['id_fields'][$panel_id]   ?? null; // <- Asegúrate que esto no sea 'user_id'

        // 3) Combinar campos por sexo
        //    m => neutral + male
        //    f => neutral + female
        //    M => neutral + male + female
        //    u => neutral
        $fieldMap = [];
        $neutral  = $mappings['field_map']['neutral'][$panel_id] ?? [];
        $male     = $mappings['field_map']['male'][$panel_id]    ?? [];
        $female   = $mappings['field_map']['female'][$panel_id]  ?? [];

        if ($sex_biological === 'm') {
            $fieldMap = array_merge($neutral, $male);
        } elseif ($sex_biological === 'f') {
            $fieldMap = array_merge($neutral, $female);
        } elseif ($sex_biological === 'M') {
            $fieldMap = array_merge($neutral, $male, $female);
        } else {
            // 'u' u otro valor => solo neutros
            $fieldMap = $neutral;
        }

        // 4) Nombres traducidos (uso consulta preparada)
        $biomarkers = [];
        $stmtBm = $this->db->prepare("SELECT name, name_es FROM biomarkers WHERE panel_id = ? AND deleted_at IS NULL");
        if (!$stmtBm) {
            throw new \Exception("Error preparando consulta de biomarcadores: " . $this->db->error);
        }
        $stmtBm->bind_param("s", $panel_id);
        $stmtBm->execute();
        $resBm = $stmtBm->get_result();
        while ($bm = $resBm->fetch_assoc()) {
            $bm_name = strtolower(trim((string)$bm['name']));
            $original   = trim((string)$bm['name']);
            $translated = trim((string)($bm['name_es'] ?? ''));
            $biomarkers[$bm_name] = ($useSpanish && $translated !== '') ? $translated : $original;
        }

        // 5) Obtener registros del usuario
        $sql = "SELECT * FROM `$table` WHERE user_id = ? AND deleted_at IS NULL ORDER BY `$idField` DESC";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
        }
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $records = [];
        while ($row = $res->fetch_assoc()) {
            $filtered = [
                'record_id' => $row[$idField] ?? null,
                'user_id'   => $row['user_id'] ?? null,
            ];

            if ($dateField && isset($row[$dateField])) {
                $filtered[$dateField] = $row[$dateField];
            }
            if ($timeField && isset($row[$timeField])) {
                $filtered[$timeField] = $row[$timeField];
            }

            // Agregar valores por cada biomarcador según el fieldMap seleccionado por sexo
            // $fieldMap: ['bm_key' => ['columna_db', ...], ...]
            foreach ($fieldMap as $bm_name => $columnsInfo) {
                // admite forma ['col', 'unit'] o ['col'] — tomamos el primer elemento como columna
                $column = is_array($columnsInfo) ? ($columnsInfo[0] ?? null) : $columnsInfo;
                if (!$column) {
                    continue;
                }
                $bm_key = strtolower(trim((string)$bm_name));
                if (!isset($biomarkers[$bm_key])) {
                    // si el biomarcador no existe en la lista (por ejemplo, filtrado por panel), saltar
                    continue;
                }
                $displayName = $biomarkers[$bm_key];

                if (!array_key_exists($displayName, $filtered) && array_key_exists($column, $row)) {
                    $filtered[$displayName] = $row[$column];
                }
            }

            $records[] = $filtered;
        }

        return $records;
    } catch (\Throwable $e) {
        // opcional: error_log('[getUserRecordsByPanelId] '.$e->getMessage());
        return [];
    }
}








    public function update($id, $data)
    {
        $checkStmt = $this->db->prepare("SELECT panel_id FROM {$this->table} WHERE panel_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt) {
            throw new mysqli_sql_exception("Error preparando consulta de verificacion: " . $this->db->error);
        }
        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            return false;
        }

        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updated_at = $env->getCurrentDatetime(); // ← zona horaria ajustada

            $query = "UPDATE {$this->table} 
                  SET panel_name = ?, display_name = ?, updated_at = ?, updated_by = ? 
                  WHERE panel_id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param("sssss", $data['panel_name'], $data['display_name'], $updated_at, $userId, $id);
            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return false;
        }
    }


    public function exportAllPanelsToCSV()
    {
        try {
            $query = "SELECT panel_id, panel_name, display_name 
                      FROM {$this->table} 
                      WHERE deleted_at IS NULL 
                      ORDER BY display_name";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->db->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "panels_export.csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                // CSV header
                fputcsv($output, ['ID', 'Panel Name', 'Display Name']);

                // Data rows
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                $stmt->close();
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No panels found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete(string $id): bool
    {
        // 1) Verificar que el panel exista y NO esté borrado lógicamente
        $checkStmt = $this->db->prepare("SELECT panel_id FROM {$this->table} WHERE panel_id = ? AND deleted_at IS NULL LIMIT 1");
        if (!$checkStmt) {
            throw new \mysqli_sql_exception("Error preparando consulta de verificación: " . $this->db->error);
        }
        $checkStmt->bind_param("s", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows === 0) {
            return false; // No existe o ya está eliminado
        }

        // 2) Verificar dependencias por panel_id antes de eliminar (borrado lógico)
        //    Aplica AND deleted_at IS NULL SOLO donde la columna existe (biomarkers, comment_biomarker, test_documents).
        $depsSql = "
        SELECT
            (SELECT COUNT(*) FROM biomarkers         WHERE panel_id = ?      AND deleted_at IS NULL) +
            (SELECT COUNT(*) FROM comment_biomarker WHERE id_test_panel = ? AND deleted_at IS NULL) +
            (SELECT COUNT(*) FROM notifications     WHERE id_panel = ?) +
            (SELECT COUNT(*) FROM test_documents    WHERE id_test_panel = ? AND deleted_at IS NULL)
        AS total_dependencias
    ";
        $depsStmt = $this->db->prepare($depsSql);
        if (!$depsStmt) {
            throw new \mysqli_sql_exception("Error preparando verificación de dependencias: " . $this->db->error);
        }
        $depsStmt->bind_param("ssss", $id, $id, $id, $id);
        $depsStmt->execute();
        $deps = $depsStmt->get_result()->fetch_assoc();
        $totalDeps = (int) ($deps['total_dependencias'] ?? 0);

        if ($totalDeps > 0) {
            throw new \mysqli_sql_exception("No se puede eliminar el panel: existen $totalDeps registros dependientes.");
        }

        // 3) Ejecutar borrado lógico con contexto de auditoría/zonahoraria
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $deleted_at = $env->getCurrentDatetime(); // con TZ aplicada

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE panel_id = ?");
            if (!$stmt) {
                throw new \mysqli_sql_exception("Error preparando actualización de borrado lógico: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_at, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            return false;
        }
    }



}
