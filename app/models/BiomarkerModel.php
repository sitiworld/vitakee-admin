<?php

require_once __DIR__ . '/../config/Database.php';

require_once __DIR__ . '/../models/UserModel.php';





class BiomarkerModel
{
    private $db;
    private $table = "biomarkers";
    private $userModel;


    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->userModel = new UserModel();
    }

    public function getAll(): array
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            $query = "SELECT * FROM {$this->table} ORDER BY name";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener biomarcadores: " . $this->db->error);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $row['display_name'] = ($idioma === 'ES') ? $row['name_es'] : $row['name'];
                $items[] = $row;
            }

            return $items;
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }


    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE biomarker_id  = ? LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            return $data ? $data : null;
        } catch (mysqli_sql_exception $e) {
            return null;
        }
    }
    public function countTodayBiomarkerRecords($minDate, $maxDate)
    {
        try {
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new \Exception("Error en mapeo: " . $mapping['error']);
            }

            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            $total = 0;

            foreach ($table_map as $panel_id => $table) {
                $date_field = $date_fields[$panel_id] ?? null;
                if (!$date_field)
                    continue;

                $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$date_field} BETWEEN ? AND ?";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new \mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
                }

                $stmt->bind_param("ss", $minDate, $maxDate);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $total += (int) $row['total'];
                }

                $stmt->close();
            }

            return $total;

        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getBiomarkerNamesIndexed($lang = 'EN')
    {
        try {
            $campo = $lang === 'ES' ? 'name_es' : 'name';
            $stmt = $this->db->prepare("SELECT biomarker_id , {$campo} AS nombre FROM {$this->table} WHERE deleted_at IS NULL");
            $stmt->execute();
            $result = $stmt->get_result();
            $nombres = [];

            while ($row = $result->fetch_assoc()) {
                $nombres[$row['biomarker_id']] = $row['nombre'] ?? "Biomarker #" . $row['biomarker_id'];
            }

            return $nombres;
        } catch (mysqli_sql_exception $e) {
            return [];
        }
    }
    private function buildPanelBiomarkerMappings(): array
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // Obtener todos los paneles válidos
        $queryPanels = "SELECT panel_id, panel_name FROM test_panels WHERE deleted_at IS NULL ORDER BY display_name";
        $resultPanels = $this->db->query($queryPanels);
        if (!$resultPanels) {
            throw new mysqli_sql_exception("Error al obtener paneles: " . $this->db->error);
        }

        $table_map  = [];
        $date_fields = [];
        $time_fields = [];
        $id_fields   = [];

        while ($panel = $resultPanels->fetch_assoc()) {
            $panel_id   = $panel['panel_id'];
            $panel_name = strtolower(trim($panel['panel_name']));
            $table_map[$panel_id] = $panel_name;

            // Detectar el campo de fecha, hora e id dentro de la tabla correspondiente
            $resultDesc = $this->db->query("DESCRIBE `{$panel_name}`");
            if (!$resultDesc) {
                throw new mysqli_sql_exception("Error al obtener estructura de tabla {$panel_name}: " . $this->db->error);
            }
            while ($col = $resultDesc->fetch_assoc()) {
                if (preg_match('/_date$/', $col['Field'])) {
                    $date_fields[$panel_id] = $col['Field'];
                }
                if (preg_match('/_time$/', $col['Field'])) {
                    $time_fields[$panel_id] = $col['Field'];
                }
                if (preg_match('/_id$/', $col['Field']) && !isset($id_fields[$panel_id])) {
                    $id_fields[$panel_id] = $col['Field']; // guarda solo el primero que coincide
                }
            }
        }

        // Traer todos los biomarcadores con name, name_es y name_db
        $queryBiomarkers = "SELECT biomarker_id, panel_id, name, name_es, name_db FROM biomarkers ORDER BY name";
        $resultBiomarkers = $this->db->query($queryBiomarkers);
        if (!$resultBiomarkers) {
            throw new mysqli_sql_exception("Error al obtener biomarcadores: " . $this->db->error);
        }

        $field_map = [];

        while ($biomarker = $resultBiomarkers->fetch_assoc()) {
            $panel_id = $biomarker['panel_id'];

            // claves en minúsculas
            $key_en = strtolower(trim($biomarker['name'] ?? ''));
            $key_es = strtolower(trim($biomarker['name_es'] ?? ''));
            $key_db = strtolower(trim($biomarker['name_db'] ?? ''));

            // fallback si falta name_db: derivar de name en snake_case simple
            if ($key_db === '' && $key_en !== '') {
                $key_db = preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en);
            }

            if ($key_en !== '') {
                $field_map[$panel_id][$key_en] = [$key_db];
            }
            if ($key_es !== '') {
                $field_map[$panel_id][$key_es] = [$key_db];
            }

            // Variantes normalizadas (opcionales, útiles para lookups tolerantes)
            $norm_en = $key_en !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_en) : '';
            $norm_es = $key_es !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $key_es) : '';
            if ($norm_en !== '' && $norm_en !== $key_en) {
                $field_map[$panel_id][$norm_en] = [$key_db];
            }
            if ($norm_es !== '' && $norm_es !== $key_es) {
                $field_map[$panel_id][$norm_es] = [$key_db];
            }
        }

        return [
            'table_map'   => $table_map,
            'id_fields'   => $id_fields,
            'date_fields' => $date_fields,
            'time_fields' => $time_fields,
            'field_map'   => $field_map
        ];

    } catch (mysqli_sql_exception $e) {
        return [
            'table_map'   => [],
            'id_fields'   => [],
            'date_fields' => [],
            'time_fields' => [],
            'field_map'   => [],
            'error'       => $e->getMessage()
        ];
    }
}





    public function countAllUsersBiomarkersOutOfRangeStreak($min, $max)
    {
        try {
            // Obtener usuarios (con edad calculada)
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday FROM users");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = [];

            while ($row = $result->fetch_assoc()) {
                $birthdate = new \DateTime($row['birthday']);
                $today = new \DateTime();
                $age = $today->diff($birthdate)->y;

                $users[] = [
                    'user_id' => $row['user_id'],
                    'sex_biological' => strtolower(trim($row['sex_biological'])),
                    'age' => $age
                ];
            }

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = [];

            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            // Obtener los mapeos
            $mapping = $this->buildPanelBiomarkerMappings();

            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $date_fields = $mapping['date_fields'];
            $table_map = $mapping['table_map'];

            $biomarker_out_of_range_count = 0;

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $sex_biological = $user['sex_biological'];
                $user_age = $user['age'];

                if (!in_array($sex_biological, ['m', 'f']))
                    continue;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $ref_min = floatval($b['reference_min']);
                    $ref_max = floatval($b['reference_max']);

                    // Filtrar por sex_biologicalo si aplica
                    if (
                        (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                        (strpos($name, 'female') !== false && $sex_biological !== 'f')
                    ) {
                        continue;
                    }

                    $matched_fields = $field_map[$panel_id][$name] ?? null;
                    if (!$matched_fields)
                        continue;

                    $date_field = $date_fields[$panel_id] ?? null;
                    $table = $table_map[$panel_id] ?? null;
                    $field = $matched_fields[0] ?? null;

                    if (!$table || !$date_field || !$field)
                        continue;

                    $sql = "SELECT $field, $date_field AS record_date 
                        FROM $table 
                        WHERE user_id = ? 
                        AND $field > 0 
                        AND $date_field BETWEEN ? AND ?
                        ORDER BY $date_field ASC";

                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("iss", $user_id, $min, $max);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $value = floatval($row[$field]);
                        $out_of_range = false;

                        if ($name === 'body age') {
                            $out_of_range = $value > $user_age;
                        } else {
                            $out_of_range = ($value < $ref_min || $value > $ref_max);
                        }

                        if ($out_of_range) {
                            $biomarker_out_of_range_count++;
                            break;
                        }
                    }
                }
            }

            return $biomarker_out_of_range_count;

        } catch (\Exception $e) {
            return 0;
        }
    }



    public function getUserBiomarkers($user_id)
    {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // Obtener el sex_biologicalo y cumpleaños del usuario
        $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $sex_biological = strtolower(trim($row['sex_biological']));
            $birthday = $row['birthday'];

            // Calcular la edad
            $birthdate = new DateTime($birthday);
            $today = new DateTime();
            $age = $today->diff($birthdate)->y;
        } else {
            throw new Exception('User not found');
        }

        // Preparar filtro por sex_biologicalo en el nombre
        $like_clause = ($sex_biological === 'm') ? '%- Male' : '%- Female';

        // Obtener todos los biomarcadores válidos para el usuario
        $stmt = $this->db->prepare("
        SELECT *
        FROM biomarkers
        WHERE (name NOT LIKE '%- Male' AND name NOT LIKE '%- Female')
           OR name LIKE ?
        ORDER BY name
    ");
        $stmt->bind_param("s", $like_clause);
        $stmt->execute();
        $result = $stmt->get_result();

        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            // Ajustar el máximo de referencia si es Body Age
            if (strtolower(trim($row['name'])) === 'body age') {
                $row['reference_max'] = $age;
            }

            // Agregar campo de nombre traducido
            $row['display_name'] = ($idioma === 'ES') ? $row['name_es'] : $row['name'];

            $biomarkers[] = $row;
        }

        return $biomarkers;
    }

    public function getAllUsersInRangePercentage($min, $max)
    {
        try {
            // Obtener usuarios (con edad calculada)
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday FROM users");
            $stmt->execute();
            $result = $stmt->get_result();
            $users = [];

            while ($row = $result->fetch_assoc()) {
                $birthdate = new \DateTime($row['birthday']);
                $today = new \DateTime();
                $age = $today->diff($birthdate)->y;

                $users[] = [
                    'user_id' => $row['user_id'],
                    'sex_biological' => strtolower(trim($row['sex_biological'])),
                    'age' => $age
                ];
            }

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = [];

            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            // Llamamos la función robusta de mapeo
            $mapping = $this->buildPanelBiomarkerMappings();

            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $date_fields = $mapping['date_fields'];
            $table_map = $mapping['table_map'];

            $in_range = 0;
            $out_range = 0;

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $sex_biological = $user['sex_biological'];
                $user_age = $user['age'];

                if (!in_array($sex_biological, ['m', 'f']))
                    continue;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $ref_min = floatval($b['reference_min']);
                    $ref_max = floatval($b['reference_max']);

                    if (
                        (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                        (strpos($name, 'female') !== false && $sex_biological !== 'f')
                    ) {
                        continue;
                    }

                    $matched_fields = $field_map[$panel_id][$name] ?? null;
                    if (!$matched_fields)
                        continue;

                    $date_field = $date_fields[$panel_id] ?? null;
                    $table = $table_map[$panel_id] ?? null;
                    $field = $matched_fields[0] ?? null;

                    if (!$table || !$date_field || !$field)
                        continue;

                    $sql = "SELECT $field FROM $table 
                        WHERE user_id = ? 
                        AND $field > 0 
                        AND $date_field BETWEEN ? AND ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("sss", $user_id, $min, $max);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $value = floatval($row[$field]);

                        if ($name === 'body age') {
                            if ($value <= $user_age) {
                                $in_range++;
                            } else {
                                $out_range++;
                            }
                        } else {
                            if ($value >= $ref_min && $value <= $ref_max) {
                                $in_range++;
                            } else {
                                $out_range++;
                            }
                        }
                    }
                }
            }

            $total = $in_range + $out_range;
            $percentage = $total > 0 ? round(($in_range / $total) * 100, 2) : 0;

            return $percentage;

        } catch (\Exception $e) {
            return 0;
        }
    }


    public function countUserValidBiomarkers($id_user, $minDate = '', $maxDate = '')
    {
        try {
            // Obtener sex_biologicalo del usuario
            $stmt = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$row = $result->fetch_assoc()) {
                return ['valid' => 0, 'total' => 0];
            }

            $sex_biological = strtolower(trim($row['sex_biological']));
            if (!in_array($sex_biological, ['m', 'f'])) {
                return ['valid' => 0, 'total' => 0];
            }

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            // Mapeo centralizado
            $mapping = $this->buildPanelBiomarkerMappings();

            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $date_fields = $mapping['date_fields'];
            $table_map = $mapping['table_map'];

            $biomarker_names_seen = [];
            $biomarker_names_total = [];

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);

                // Filtrado por sex_biologicalo
                if (
                    (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                    (strpos($name, 'female') !== false && $sex_biological !== 'f')
                ) {
                    continue;
                }

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields)
                    continue;

                $biomarker_names_total[$name] = true;

                $date_field = $date_fields[$panel_id] ?? null;
                $table = $table_map[$panel_id] ?? null;
                $field = $matched_fields[0] ?? null;

                if (!$table || !$date_field || !$field)
                    continue;

                // Armamos la consulta dinámica con filtros de fechas
                $sql = "SELECT 1 FROM $table WHERE user_id = ? AND $field > 0";
                $params = [$id_user];
                $types = "s";

                if ($minDate) {
                    $sql .= " AND $date_field >= ?";
                    $params[] = $minDate;
                    $types .= "s";
                }
                if ($maxDate) {
                    $sql .= " AND $date_field <= ?";
                    $params[] = $maxDate;
                    $types .= "s";
                }
                $sql .= " LIMIT 1";

                $stmt = $this->db->prepare($sql);
                if (!$stmt)
                    throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->fetch_assoc()) {
                    $biomarker_names_seen[$name] = true;
                }
            }

            return [
                'valid' => count($biomarker_names_seen),
                'total' => count($biomarker_names_total)
            ];
        } catch (\Exception $e) {
            return ['valid' => 0, 'total' => 0];
        }
    }


    public function getUserBiomarkerValues($id_biomarker, $id_user, $min = null, $max = null)
    {
        try {
            // Obtener los datos del biomarcador
            $stmt = $this->db->prepare("SELECT panel_id, name, reference_min, reference_max, unit FROM {$this->table} WHERE biomarker_id = ?");
            $stmt->bind_param("s", $id_biomarker);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                throw new Exception("Biomarcador no encontrado.");
            }

            $biomarker = $result->fetch_assoc();
            $panel_id = $biomarker['panel_id'];
            $name = strtolower($biomarker['name']);
            $reference_min = $biomarker['reference_min'];
            $reference_max = $biomarker['reference_max'];
            $unit = $biomarker['unit'];

            // Si es body age, calcular la edad actual
            if ($name === 'body age') {
                $stmt_age = $this->db->prepare("SELECT birthday FROM users WHERE user_id = ?");
                $stmt_age->bind_param("s", $id_user);
                $stmt_age->execute();
                $res_age = $stmt_age->get_result();
                if ($row = $res_age->fetch_assoc()) {
                    $birthday = new DateTime($row['birthday']);
                    $today = new DateTime();
                    $age = $today->diff($birthday)->y;
                    $reference_max = $age;
                }
                $stmt_age->close();
            }

            // Construir los mapeos dinámicos desde función centralizada
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];
            $id_fields = $mapping['id_fields'];

            // Obtener los campos mapeados
            $matched_fields = $field_map[$panel_id][$name] ?? null;
            if (!$matched_fields) {
                throw new Exception("No se encontró campo correspondiente para '$name'.");
            }

            $table = $table_map[$panel_id] ?? null;
            $date_field = $date_fields[$panel_id] ?? null;
            $id_field = $id_fields[$panel_id] ?? 'id'; // fallback por si acaso

            if (!$table || !$date_field || !$id_field) {
                throw new Exception("Error en los mapeos de tabla, ID o fecha para el panel.");
            }

            $fields_sql = implode(",", $matched_fields) . ", $id_field AS record_id, user_id, $date_field";
            $sql = "SELECT $fields_sql FROM $table WHERE user_id = ?";

            $params = [$id_user];
            $types = "s";

            if ($min && $max) {
                $sql .= " AND $date_field BETWEEN ? AND ?";
                $params[] = $min;
                $params[] = $max;
                $types .= "ss";
            }

            $stmt = $this->db->prepare($sql);
            if (!$stmt)
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            $values = [];
            while ($row = $result->fetch_assoc()) {
                $values[] = [
                    'record_id' => $row['record_id'],
                    'patient_id' => $row['user_id'],
                    'biomarker_id' => $id_biomarker,
                    'value' => $row[$matched_fields[0]],
                    'date' => $row[$date_field],
                    'reference_min' => $reference_min,
                    'reference_max' => $reference_max,
                    'unit' => $unit
                ];
            }

            return $values;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    public function getBiomarkerValuesByPanelTest($panel, $test)
    {
        try {
            // Obtener todos los mappings dinámicos
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];
            $id_fields = $mapping['id_fields'];

            if (!isset($table_map[$panel])) {
                throw new Exception("Panel no reconocido.");
            }

            $table = $table_map[$panel];
            $date_field = $date_fields[$panel];
            $id_field = $id_fields[$panel] ?? 'id'; // fallback en caso de que no se haya detectado

            // Validar si el panel tiene campos definidos
            $panel_field_map = $field_map[$panel] ?? null;
            if (!$panel_field_map) {
                throw new Exception("El panel no tiene biomarcadores mapeados.");
            }

            // Construir los campos de consulta
            $all_fields = [];
            foreach ($panel_field_map as $biomarker_name => $fields) {
                $all_fields = array_merge($all_fields, $fields);
            }

            $fields_sql = implode(",", array_unique($all_fields)) . ", $id_field AS record_id, user_id, $date_field";
            $sql = "SELECT $fields_sql FROM $table WHERE $id_field = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $test);
            $stmt->execute();
            $result = $stmt->get_result();

            $values = [];

            while ($row = $result->fetch_assoc()) {
                $user_id = $row['user_id'];
                $sex_biological = $this->getUserSex($user_id);

                foreach ($panel_field_map as $biomarker_name => $fields) {
                    // Filtrar biomarcadores por sexo si corresponde
                    if (
                        (strpos($biomarker_name, 'male') !== false && $sex_biological !== 'm') ||
                        (strpos($biomarker_name, 'female') !== false && $sex_biological !== 'f')
                    ) {
                        continue;
                    }

                    // Obtener el ID del biomarcador desde la tabla biomarkers
                    $stmt_bm = $this->db->prepare("SELECT biomarker_id FROM biomarkers WHERE panel_id = ? AND LOWER(name) = ?");
                    $normalized_name = strtolower($biomarker_name);
                    $stmt_bm->bind_param("ss", $panel, $normalized_name);
                    $stmt_bm->execute();
                    $bm_result = $stmt_bm->get_result();
                    $biomarker_row = $bm_result->fetch_assoc();
                    $stmt_bm->close();

                    if (!$biomarker_row)
                        continue;

                    foreach ($fields as $field) {
                        if (!isset($row[$field]))
                            continue;

                        $values[] = [
                            'biomarker_name' => $biomarker_name,
                            'biomarker_value' => $row[$field],
                            'id_biomarker' => $biomarker_row['biomarker_id'],
                            'date' => $row[$date_field]
                        ];
                    }
                }
            }

            return $values;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }





    public function getFilteredBiomarkerRecords(
        string $id_user,
        ?string $id_biomarker = null,
        string $minDate = '',
        string $maxDate = '',
        string $tipo = 'all'
    ) {
        try {
            $id_biomarker_most = null;

            // Obtener sex_biologicalo y cumpleaños
            $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $res = $stmt->get_result();
            if (!$row = $res->fetch_assoc())
                throw new Exception("User not found.");

            $sex_biological = strtolower(trim($row['sex_biological']));
            $birthday = $row['birthday'];
            if (!in_array($sex_biological, ['m', 'f']))
                throw new Exception("Invalid sex biological value.");

            // Obtener todos los biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, reference_min, reference_max, unit FROM {$this->table}");
            $stmt->execute();
            $res = $stmt->get_result();
            $biomarkers = $res->fetch_all(MYSQLI_ASSOC);

            // Cargar mapeos
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];
            $time_fields = $mapping['time_fields'];
            $id_fields = $mapping['id_fields'];

            // Buscar biomarcador con más registros
            $counts = [];
            foreach ($biomarkers as $b) {
                $pid = $b['panel_id'];
                $name = strtolower($b['name']);

                if ((strpos($name, 'male') !== false && $sex_biological !== 'm') || (strpos($name, 'female') !== false && $sex_biological !== 'f'))
                    continue;

                $fields = $field_map[$pid][$name] ?? null;
                if (!$fields)
                    continue;

                $field = $fields[0];
                $table = $table_map[$pid];
                $date_field = $date_fields[$pid];

                $sql = "SELECT COUNT(*) AS cnt FROM $table WHERE user_id=? AND $field>0";
                $types = "s";
                $params = [$id_user];
                if ($minDate !== '') {
                    $sql .= " AND $date_field >= ?";
                    $types .= "s";
                    $params[] = $minDate;
                }
                if ($maxDate !== '') {
                    $sql .= " AND $date_field <= ?";
                    $types .= "s";
                    $params[] = $maxDate;
                }

                $stm = $this->db->prepare($sql);
                $stm->bind_param($types, ...$params);
                $stm->execute();
                $res = $stm->get_result()->fetch_assoc();
                $counts[$b['biomarker_id']] = $res['cnt'];
            }

            if (empty($counts))
                throw new Exception("No biomarker records found.");

            $max = max($counts);
            $tops = array_keys(array_filter($counts, fn($v) => $v === $max));
            $id_biomarker_most = $tops[array_rand($tops)];

            if (is_null($id_biomarker)) {
                $id_biomarker = $id_biomarker_most;
            }

            // Obtener biomarcador actual
            $stmt = $this->db->prepare("SELECT panel_id, name, reference_min, reference_max, unit FROM {$this->table} WHERE biomarker_id=?");
            $stmt->bind_param("s", $id_biomarker);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0)
                throw new Exception("Biomarker not found.");
            $b = $res->fetch_assoc();

            $pid = $b['panel_id'];
            $name = strtolower($b['name']);
            $rmin = $b['reference_min'];
            $rmax = $b['reference_max'];
            $unit = $b['unit'];

            if ($name === 'body age') {
                $birth = new DateTime($birthday);
                $today = new DateTime();
                $rmax = $today->diff($birth)->y;
            }

            $fields = $field_map[$pid][$name] ?? null;
            if (!$fields)
                throw new Exception("No field mapping for '{$b['name']}'.");

            $field = $fields[0];
            $table = $table_map[$pid];
            $date_field = $date_fields[$pid];
            $time_field = $time_fields[$pid];
            $id_field = $id_fields[$pid] ?? 'id';

            // Construir consulta según fechas
            $params = [$id_user];
            $types = "s";

            if ($minDate !== '' && $maxDate !== '') {
                $date1 = new DateTime($minDate);
                $date2 = new DateTime($maxDate);
                $diffDays = $date1->diff($date2)->days;

                if ($diffDays <= 1) {
                    $sql = "SELECT $id_field AS record_id, user_id, $field AS value, $date_field, $time_field FROM $table WHERE user_id=? AND $date_field BETWEEN ? AND ?";
                    $types .= "ss";
                    $params[] = $minDate;
                    $params[] = $maxDate;
                } else {
                    $sql = "
                    SELECT r.$id_field AS record_id, r.user_id, r.$field AS value, r.$date_field, r.$time_field
                    FROM $table r
                    INNER JOIN (
                        SELECT user_id, $date_field, MAX($time_field) AS max_time
                        FROM $table
                        WHERE user_id = ? AND $date_field BETWEEN ? AND ?
                        GROUP BY $date_field
                    ) latest
                    ON r.user_id = latest.user_id
                    AND r.$date_field = latest.$date_field
                    AND r.$time_field = latest.max_time
                ";
                    $types .= "ss";
                    $params[] = $minDate;
                    $params[] = $maxDate;
                }
            } else {
                $sql = "SELECT $id_field AS record_id, user_id, $field AS value, $date_field, $time_field FROM $table WHERE user_id=?";
            }

            $stm = $this->db->prepare($sql);
            $stm->bind_param($types, ...$params);
            $stm->execute();
            $res = $stm->get_result();

            $data = [];
            while ($row = $res->fetch_assoc()) {
                $status = ($row['value'] >= $rmin && $row['value'] <= $rmax) ? 'in_range' : 'out_range';
                $data[] = [
                    'record_id' => $row['record_id'],
                    'patient_id' => $row['user_id'],
                    'biomarker_id' => $id_biomarker,
                    'value' => $row['value'],
                    'date' => $row[$date_field],
                    'time' => $row[$time_field],
                    'reference_min' => $rmin,
                    'reference_max' => $rmax,
                    'unit' => $unit,
                    'status' => $status,
                ];
            }

            // Filtrar
            switch (strtolower($tipo)) {
                case 'in':
                    $data = array_filter($data, fn($e) => $e['status'] === 'in_range');
                    break;
                case 'out':
                    $data = array_filter($data, fn($e) => $e['status'] === 'out_range');
                    break;
            }

            $idioma = $_SESSION['idioma'] ?? 'EN';
            $langPath = PROJECT_ROOT . '/lang/' . $idioma . '.php';
            $lang = file_exists($langPath) ? include $langPath : include PROJECT_ROOT . '/lang/EN.php';
            $labels = array_intersect_key($lang, array_flip([
                'yAxisRightText_chart',
                'inRange_chart',
                'outRange_chart',
                'leftYAxisText_chart',
                'dateFormat_chart',
                'annotationsMin_chart',
                'annotationsMax_chart',
                'inRange',
                'outOfRange',
                'noEntries',
                'donutTitle',
                'tooltipFormat',
            ]));

            return [
                'id_biomarker' => $id_biomarker,
                'id_biomarker_most' => $id_biomarker_most,
                'records' => array_values($data),
                'labels' => $labels
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }








    public function getMostFrequentBiomarker($id_user, $minDate = '', $maxDate = '')
    {
        try {
            // Obtener sex_biologicalo del usuario
            $stmt = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $res = $stmt->get_result();

            if (!$row = $res->fetch_assoc()) {
                throw new Exception("User not found.");
            }

            $sex_biological = strtolower(trim($row['sex_biological']));
            if (!in_array($sex_biological, ['m', 'f'])) {
                throw new Exception("Invalid sex biological value.");
            }

            // Obtener todos los biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name FROM {$this->table}");
            $stmt->execute();
            $res = $stmt->get_result();
            $biomarkers = $res->fetch_all(MYSQLI_ASSOC);

            // Obtener los mappings centralizados
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }
            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            $biomarker_counts = [];

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);
                $id_biomarker = $b['biomarker_id'];

                // Filtrar por sex_biologicalo si corresponde
                if (
                    (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                    (strpos($name, 'female') !== false && $sex_biological !== 'f')
                ) {
                    continue;
                }

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields)
                    continue;

                $field = $matched_fields[0];
                $table = $table_map[$panel_id];
                $date_field = $date_fields[$panel_id];

                // Construcción de query dinámica según fechas
                $sql = "SELECT COUNT(*) as count FROM $table WHERE user_id = ? AND $field > 0";
                $params = [$id_user];
                $types = "s";

                if (!empty($minDate)) {
                    $sql .= " AND $date_field >= ?";
                    $params[] = $minDate;
                    $types .= "s";
                }

                if (!empty($maxDate)) {
                    $sql .= " AND $date_field <= ?";
                    $params[] = $maxDate;
                    $types .= "s";
                }

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $res = $stmt->get_result();
                $count = $res->fetch_assoc()['count'];

                $biomarker_counts[$id_biomarker] = $count;
            }

            if (empty($biomarker_counts)) {
                throw new Exception("No biomarker records found.");
            }

            // Encontrar el biomarcador con más registros
            $max_count = max($biomarker_counts);
            $top_biomarkers = array_keys(array_filter($biomarker_counts, fn($v) => $v === $max_count));
            $selected = $top_biomarkers[array_rand($top_biomarkers)];

            return ['message' => '', "id_biomarker" => $selected];
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "id_biomarker" => null];
        }
    }

    public function getMostFrequentBiomarkerAllUsers($minDate = '', $maxDate = '')
    {
        try {
            // Obtener todos los biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name FROM {$this->table}");
            $stmt->execute();
            $res = $stmt->get_result();
            $biomarkers = $res->fetch_all(MYSQLI_ASSOC);

            // Obtener los mappings centralizados
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            $biomarker_counts = [];

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);
                $id_biomarker = $b['biomarker_id'];

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields)
                    continue;

                $field = $matched_fields[0];
                $table = $table_map[$panel_id];
                $date_field = $date_fields[$panel_id];

                // Obtener user_id y sex_biological de los usuarios con registros en esa tabla
                $query = "SELECT DISTINCT user_id FROM $table";
                $userResult = $this->db->query($query);
                if (!$userResult)
                    continue;

                $userIds = array_column($userResult->fetch_all(MYSQLI_ASSOC), 'user_id');

                foreach ($userIds as $user_id) {
                    // Obtener sex_biologicalo del usuario
                    $stmtSex = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
                    $stmtSex->bind_param("s", $user_id);
                    $stmtSex->execute();
                    $resSex = $stmtSex->get_result();

                    if (!$rowSex = $resSex->fetch_assoc())
                        continue;

                    $sex_biological = strtolower(trim($rowSex['sex_biological']));
                    if (!in_array($sex_biological, ['m', 'f']))
                        continue;

                    // Filtrar por sexo si el nombre del biomarcador incluye "male" o "female"
                    if (
                        (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                        (strpos($name, 'female') !== false && $sex_biological !== 'f')
                    ) {
                        continue;
                    }

                    // Construcción de query con fechas
                    $sql = "SELECT COUNT(*) as count FROM $table WHERE user_id = ? AND $field > 0";
                    $params = [$user_id];
                    $types = "s";

                    if (!empty($minDate)) {
                        $sql .= " AND $date_field >= ?";
                        $params[] = $minDate;
                        $types .= "s";
                    }

                    if (!empty($maxDate)) {
                        $sql .= " AND $date_field <= ?";
                        $params[] = $maxDate;
                        $types .= "s";
                    }

                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $count = $res->fetch_assoc()['count'];

                    if (!isset($biomarker_counts[$id_biomarker])) {
                        $biomarker_counts[$id_biomarker] = 0;
                    }

                    $biomarker_counts[$id_biomarker] += (int) $count;
                }
            }

            if (empty($biomarker_counts)) {
                throw new Exception("No biomarker records found.");
            }

            // Encontrar el biomarcador con más registros
            $max_count = max($biomarker_counts);
            $top_biomarkers = array_keys(array_filter($biomarker_counts, fn($v) => $v === $max_count));
            $selected = $top_biomarkers[array_rand($top_biomarkers)];

            return ['message' => '', "id_biomarker" => $selected];
        } catch (Exception $e) {
            return ["message" => $e->getMessage(), "id_biomarker" => null];
        }
    }



    public function countUserBiomarkersInRangeOutRange($id_user, $minDate, $maxDate)
    {
        try {
            // Obtener sex_biologicalo y cumpleaños del usuario
            $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();

            if (!$user = $result->fetch_assoc()) {
                return ['in_range' => 0, 'out_range' => 0];
            }

            $sex_biological = strtolower(trim($user['sex_biological']));
            if (!in_array($sex_biological, ['m', 'f'])) {
                return ['in_range' => 0, 'out_range' => 0];
            }

            $birthdate = new DateTime($user['birthday']);
            $today = new DateTime();
            $user_age = $today->diff($birthdate)->y;

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();

            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            // Obtener los mapeos centralizados
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            $in_range = 0;
            $out_range = 0;

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);
                $ref_min = floatval($b['reference_min']);
                $ref_max = floatval($b['reference_max']);

                // Filtrar por sex_biologicalo si corresponde
                if (
                    (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                    (strpos($name, 'female') !== false && $sex_biological !== 'f')
                ) {
                    continue;
                }

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields)
                    continue;

                $date_field = $date_fields[$panel_id];
                $table = $table_map[$panel_id];
                $field = $matched_fields[0];

                $sql = "SELECT $field FROM $table 
                    WHERE user_id = ? 
                    AND $field > 0 
                    AND $date_field BETWEEN ? AND ?";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("iss", $id_user, $minDate, $maxDate);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $value = floatval($row[$field]);

                    if ($name === 'body age') {
                        if ($value <= $user_age) {
                            $in_range++;
                        } else {
                            $out_range++;
                        }
                    } else {
                        if ($value >= $ref_min && $value <= $ref_max) {
                            $in_range++;
                        } else {
                            $out_range++;
                        }
                    }
                }
            }

            return ['in_range' => $in_range, 'out_range' => $out_range];

        } catch (\Exception $e) {
            return ['in_range' => 0, 'out_range' => 0];
        }
    }



    public function getUsersBiomarkerAvgAndOutRange($id_biomarker, $id_user = null, $status = 'all', $min = null, $max = null)
    {
        try {
            // Obtener biomarcador completo
            $biomarker = $this->getById($id_biomarker);
            if (!$biomarker) {
                return $this->jsonResponse(false, 'Biomarker not found');
            }

            $panel_id = $biomarker['panel_id'];
            $name = strtolower($biomarker['name']);
            $ref_min = floatval($biomarker['reference_min']);
            $ref_max = floatval($biomarker['reference_max']);

            // Obtener los mapeos centralizados
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error building mappings: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            // Validar que el biomarcador tenga mapeo
            $matched_fields = $field_map[$panel_id][$name] ?? null;
            if (!$matched_fields) {
                return $this->jsonResponse(false, 'Campo no mapeado para el biomarcador');
            }

            $date_field = $date_fields[$panel_id];
            $table = $table_map[$panel_id];
            $field = $matched_fields[0];

            // Obtener usuarios (todos o uno específico)
            $users = $id_user ? [$this->userModel->getById($id_user)] : $this->userModel->getAll();

            $results = [];

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $sex_biological = strtolower(trim($user['sex_biological']));
                $birthday = $user['birthday'];

                if (!in_array($sex_biological, ['m', 'f']) || !$birthday) {
                    continue;
                }

                // Filtrar biomarcadores por sex_biologicalo si corresponde
                if (
                    (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                    (strpos($name, 'female') !== false && $sex_biological !== 'f')
                ) {
                    continue;
                }

                // Si es Body Age calcular referencia real
                $birthDate = new DateTime($birthday);
                $today = new DateTime();
                $age = $birthDate->diff($today)->y;
                $adjusted_ref_max = ($name === 'body age') ? $age : $ref_max;

                // Armar consulta de valores del usuario
                $sql = "SELECT $field FROM $table WHERE user_id = ? AND $field > 0";
                $types = "s";
                $params = [$user_id];
                if ($min) {
                    $sql .= " AND $date_field >= ?";
                    $types .= "s";
                    $params[] = $min;
                }
                if ($max) {
                    $sql .= " AND $date_field <= ?";
                    $types .= "s";
                    $params[] = $max;
                }

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $res = $stmt->get_result();

                $values = [];
                $in_range = 0;
                $out_range = 0;

                while ($row = $res->fetch_assoc()) {
                    $value = floatval($row[$field]);
                    $values[] = $value;

                    if ($name === 'body age') {
                        if ($value > $adjusted_ref_max) {
                            $out_range++;
                        } else {
                            $in_range++;
                        }
                    } else {
                        if ($value < $ref_min || $value > $adjusted_ref_max) {
                            $out_range++;
                        } else {
                            $in_range++;
                        }
                    }
                }

                if (!empty($values)) {
                    $avg = round(array_sum($values) / count($values), 2);

                    // Filtrado según tipo de status
                    if ($status === 'in' && $in_range > 0) {
                        $results[] = [
                            'id_user' => $user_id,
                            'username' => $user['first_name'] . ' ' . $user['last_name'],
                            'avg' => $avg,
                            'status' => 'in_range',
                            'in_range_count' => $in_range
                        ];
                    } elseif ($status === 'out' && $out_range > 0) {
                        $results[] = [
                            'id_user' => $user_id,
                            'username' => $user['first_name'] . ' ' . $user['last_name'],
                            'avg' => $avg,
                            'status' => 'out_range',
                            'out_range_count' => $out_range
                        ];
                    } elseif ($status === 'all') {
                        $results[] = [
                            'id_user' => $user_id,
                            'username' => $user['first_name'] . ' ' . $user['last_name'],
                            'avg' => $avg,
                            'status' => $out_range > 0 ? 'out_range' : 'in_range',
                            'out_range_count' => $out_range,
                            'in_range_count' => $in_range
                        ];
                    }
                }
            }

            return $this->jsonResponse(true, '', [
                'biomarker' => [
                    'id' => $biomarker['biomarker_id'],
                    'name' => $biomarker['name'],
                    'panel_id' => $biomarker['panel_id'],
                    'reference_min' => $ref_min,
                    'reference_max' => $ref_max,
                    'unit' => $biomarker['unit'],
                ],
                'results' => $results
            ]);

        } catch (Exception $e) {
            return $this->jsonResponse(false, $e->getMessage());
        }
    }


/**
 * Obtiene información de biomarcadores por nombre.
 * Si el user_id está en la sesión, calcula la edad del usuario
 * y la usa como 'reference_max' para 'body age'.
 *
 * @param array $names Nombres de los biomarcadores a buscar.
 * @return array Respuesta JSON estructurada.
 * @throws mysqli_sql_exception
 * @throws InvalidArgumentException
 * @throws Exception
 */
public function getBiomarkersByNames($names)
{
    try {
        if (empty($names)) {
            throw new InvalidArgumentException("No biomarker names provided.");
        }

        // --- INICIO: Lógica para obtener la edad del usuario (adaptada) ---
        
        // 1. Obtener user_id de la sesión
        $user_id = $_SESSION['user_id'] ?? null; 
        
        $userAge = null;
        if ($user_id !== null) {
            $userSql = "SELECT birthday FROM users WHERE user_id = ?";
            $userStmt = $this->db->prepare($userSql);
            if (!$userStmt) {
                // Usamos el mensaje de error de tu plantilla original
                throw new mysqli_sql_exception("Prepare failed: " . $this->db->error); 
            }

            // Asumiendo 'i' para integer. Cambia a 's' si es string.
            $userStmt->bind_param('i', $user_id); 
            $userStmt->execute();
            $userResult = $userStmt->get_result();

            if ($userRow = $userResult->fetch_assoc()) {
                if (!empty($userRow['birthday'])) {
                    try {
                        // Calcular la edad
                        $birthday = new DateTime($userRow['birthday']);
                        $today = new DateTime('today');
                        $userAge = $birthday->diff($today)->y; // 'y' obtiene la diferencia en años
                    } catch (Exception $e) {
                        // La fecha de nacimiento tenía un formato incorrecto, $userAge seguirá siendo null
                        // Opcional: registrar este error
                    }
                }
            }
            $userStmt->close();
        }
        // --- FIN: Lógica para obtener la edad del usuario ---


        $placeholders = implode(',', array_fill(0, count($names), '?'));
        $sql = "SELECT * FROM biomarkers WHERE name IN ($placeholders) OR name_es IN ($placeholders)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new mysqli_sql_exception("Prepare failed: " . $this->db->error);
        }

        $mergedParams = array_merge($names, $names);
        $types = str_repeat('s', count($mergedParams));
        $stmt->bind_param($types, ...$mergedParams);

        $stmt->execute();
        $result = $stmt->get_result();

        $idioma = strtoupper($_SESSION['idioma'] ?? 'EN');
        $langField = ($idioma === 'ES') ? 'name_es' : 'name';

        // Map: clave = name (en inglés siempre), valor = db_column real
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
            'body age' => 'body_age', // <--- El biomarcador relevante
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

        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $nameInEnglish = strtolower(trim($row['name']));
            $nameInLang = strtolower(trim($row[$langField]));

            if (isset($biomarkerFieldMapping[$nameInEnglish])) {
                $row['db_column'] = $biomarkerFieldMapping[$nameInEnglish];
                $row['original_column'] = $biomarkerFieldMapping[$nameInEnglish]; 
            } else {
                // fallback: generar alias automático
                $generated = preg_replace('/[^a-z0-9_]/', '_', $nameInLang);
                $generated = preg_replace('/_+/', '_', $generated);
                $row['db_column'] = $generated;
                $row['original_column'] = null; 
            }

            // --- INICIO: Lógica de 'body age' ---
            // Si calculamos la edad del usuario Y este biomarcador es 'body age'
            if ($userAge !== null && $nameInEnglish === 'body age') {
                // Sobrescribimos el reference_max con la edad real del usuario
                $row['reference_max'] = $userAge;
            }
            // --- FIN: Lógica de 'body age' ---

            $biomarkers[] = $row;
        }

        $stmt->close();
        return $this->jsonResponse(true, '', $biomarkers);

    } catch (mysqli_sql_exception $e) {
        throw new mysqli_sql_exception('Database error: ' . $e->getMessage());
    } catch (InvalidArgumentException $e) {
        throw new InvalidArgumentException('Invalid input: ' . $e->getMessage());
    } catch (Exception $e) {
        throw new Exception('Unexpected error: ' . $e->getMessage());
    }
}



    public function countUserValidBiomarkerValuesInRange($id_user, $minDate, $maxDate)
    {
        try {
            // Obtener sex_biologicalo del usuario
            $stmt = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$row = $result->fetch_assoc()) {
                return 0;
            }

            $sex_biological = strtolower(trim($row['sex_biological']));
            if (!in_array($sex_biological, ['m', 'f'])) {
                return 0;
            }

            // Obtener el mapeo centralizado
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error en el mapeo de paneles: " . $mapping['error']);
            }
            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            // Obtener los biomarcadores desde base de datos
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            $total_count = 0;

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);

                if (
                    (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                    (strpos($name, 'female') !== false && $sex_biological !== 'f')
                ) {
                    continue;
                }

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields) {
                    continue;
                }

                $field = $matched_fields[0];
                $table = $table_map[$panel_id];
                $date_field = $date_fields[$panel_id];

                $sql = "SELECT COUNT(*) as total FROM $table 
                    WHERE user_id = ? AND $field > 0 AND $date_field BETWEEN ? AND ?";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("sss", $id_user, $minDate, $maxDate);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $total_count += (int) $row['total'];
                }
            }

            return $total_count;

        } catch (\Exception $e) {
            return 0;
        }
    }


    public function getAllUsersInOutRangePercentage($min, $max, $id_user = null, $id_biomarker = null, $status = 'all')
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';
            $langPath = PROJECT_ROOT . '/lang/' . $idioma . '.php';
            $lang = file_exists($langPath)
                ? include $langPath
                : include PROJECT_ROOT . '/lang/EN.php';

            $labels = array_intersect_key($lang, array_flip([
                'inRange_admin',
                'outOfRange_admin',
                'noEntries_admin',
                'donutTitle_admin',
                'tooltipFormat_admin',
            ]));

            // Obtener usuarios
            $users = $id_user ? [$this->userModel->getById($id_user)] : $this->userModel->getAll();

            $in_range = 0;
            $out_range = 0;

            // Obtener biomarcadores
            $biomarkers = $id_biomarker ? [$this->getById($id_biomarker)] : $this->getAll();

            if (!isset($biomarkers)) {
                throw new Exception('Biomarker is needed');
            }
            if (!isset($users)) {
                throw new Exception('User not found');
            }

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $sex_biological = strtolower(trim($user['sex_biological']));
                $birthday = $user['birthday'];

                if (!in_array($sex_biological, ['m', 'f']) || !$birthday) {
                    continue;
                }

                $birthDate = new DateTime($birthday);
                $today = new DateTime();
                $age = $birthDate->diff($today)->y;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $ref_min = floatval($b['reference_min']);
                    $ref_max = floatval($b['reference_max']);

                    $sexLabels = ['m' => 'male', 'f' => 'female'];
                    $sexKeyword = $sexLabels[$sex_biological];

                    if (strpos($name, 'male') !== false || strpos($name, 'female') !== false) {
                        if (strpos($name, $sexKeyword) === false) {
                            continue;
                        }
                    }

                    $biomarker_values = $this->getUserBiomarkerValues($b['biomarker_id'], $user_id, $min, $max);

                    foreach ($biomarker_values as $value) {
                        $value = floatval($value['value']);
                        if ($name === 'body age') {
                            if ($value > $age) {
                                $out_range++;
                            } else {
                                $in_range++;
                            }
                        } else {
                            if ($value < $ref_min || $value > $ref_max) {
                                $out_range++;
                            } else {
                                $in_range++;
                            }
                        }
                    }
                }
            }

            if ($status === 'in') {
                $out_range = 0;
            } elseif ($status === 'out') {
                $in_range = 0;
            }

            $total = $in_range + $out_range;
            $percentage_in = $total > 0 ? round(($in_range / $total) * 100, 2) : 0;
            $percentage_out = $total > 0 ? round(($out_range / $total) * 100, 2) : 0;

            return $this->jsonResponse(true, '', [
                'in_range' => $percentage_in,
                'out_range' => $percentage_out,
                'in_count' => $in_range,
                'out_count' => $out_range,
                'labels' => $labels
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), [
                'in_range' => 0,
                'out_range' => 0,
                'labels' => []
            ]);
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

            $createdBy = $userId;
            if (!$createdBy) {
                throw new Exception("User not authenticated to create biomarker.");
            }

            $createdAt = $env->getCurrentDatetime();

            // Generar UUID para biomarker_id
            $uuid = $this->generateUUIDv4();

            $query = "INSERT INTO {$this->table}
            (biomarker_id, panel_id, name, unit, reference_min, reference_max, deficiency_label, excess_label, description, max_exam, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL)";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssssddssssss",
                $uuid,
                $data['panel_id'],
                $data['name'],
                $data['unit'],
                $data['reference_min'],
                $data['reference_max'],
                $data['deficiency_label'],
                $data['excess_label'],
                $data['description'],
                $data['max_exam'],
                $createdAt,
                $createdBy
            );

            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Biomarker created successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        } catch (Exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
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




    public function update($data)
    {
        // Verificar si el registro existe
        $checkStmt = $this->db->prepare("SELECT biomarker_id  FROM {$this->table} WHERE biomarker_id  = ? LIMIT 1");
        $checkStmt->bind_param("s", $data['id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            return ['status' => 'error', 'message' => 'Record not found for update.'];
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['status' => 'error', 'message' => 'User not authenticated for update.'];
        }

        $this->db->begin_transaction();
        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime(); // Obtener datetime con zona horaria

            $query = "UPDATE {$this->table}
              SET panel_id = ?, name = ?, unit = ?, reference_min = ?, reference_max = ?, 
                  deficiency_label = ?, excess_label = ?, description = ?, max_exam = ?, 
                  updated_at = ?, updated_by = ?
              WHERE biomarker_id  = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssddsssssss",
                $data['panel_id'],
                $data['name'],
                $data['unit'],
                $data['reference_min'],
                $data['reference_max'],
                $data['deficiency_label'],
                $data['excess_label'],
                $data['description'],
                $data['max_exam'],
                $updatedAt,
                $userId,
                $data['id']
            );

            $stmt->execute();
            $this->db->commit();

            return ['status' => 'success', 'message' => 'Biomarker updated successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function update_es($data)
    {
        // Verificar si el registro existe
        $checkStmt = $this->db->prepare("SELECT biomarker_id  FROM {$this->table} WHERE biomarker_id  = ? LIMIT 1");
        $checkStmt->bind_param("s", $data['id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows == 0) {
            return ['status' => 'error', 'message' => 'Record not found for update.'];
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return ['status' => 'error', 'message' => 'User not authenticated for update.'];
        }

        $this->db->begin_transaction();
        try {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);

            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();

            $query = "UPDATE {$this->table}
              SET panel_id = ?, unit = ?, reference_min = ?, reference_max = ?, 
                  name_es = ?, deficiency_es = ?, excess_es = ?, description_es = ?, 
                  max_exam = ?, updated_at = ?, updated_by = ?
              WHERE biomarker_id  = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssddssssssss",
                $data['panel_id'],
                $data['unit'],
                $data['reference_min'],
                $data['reference_max'],
                $data['name_es'],
                $data['deficiency_es'],
                $data['excess_es'],
                $data['description_es'],
                $data['max_exam'],
                $updatedAt,
                $userId,
                $data['id']
            );

            $stmt->execute();
            $this->db->commit();

            return ['status' => 'success', 'message' => 'Biomarker (ES) updated successfully.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



public function delete($id)
{
    $this->db->begin_transaction();
    try {
        // 1) Cargar idioma
        $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
        $archivo_idioma = PROJECT_ROOT . "/lang/{$lang}.php";
        $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

        $userId = $_SESSION['user_id'] ?? null;

        // 2) Verificar dependencias en comment_biomarker
        $stmtCheck1 = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM comment_biomarker
            WHERE id_biomarker = ? AND (deleted_at IS NULL OR deleted_at = deleted_at) -- si no existe deleted_at, no afecta
        ");
        if (!$stmtCheck1) {
            throw new mysqli_sql_exception("Error preparando la consulta de dependencias (comment_biomarker): " . $this->db->error);
        }
        $stmtCheck1->bind_param("s", $id);
        $stmtCheck1->execute();
        $res1 = $stmtCheck1->get_result();
        $row1 = $res1->fetch_assoc();
        $stmtCheck1->close();

        // 3) Verificar dependencias en notifications
        $stmtCheck2 = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM notifications
            WHERE id_biomarker = ? AND (deleted_at IS NULL OR deleted_at = deleted_at)
        ");
        if (!$stmtCheck2) {
            throw new mysqli_sql_exception("Error preparando la consulta de dependencias (notifications): " . $this->db->error);
        }
        $stmtCheck2->bind_param("s", $id);
        $stmtCheck2->execute();
        $res2 = $stmtCheck2->get_result();
        $row2 = $res2->fetch_assoc();
        $stmtCheck2->close();

        $totalDeps = (int)($row1['total'] ?? 0) + (int)($row2['total'] ?? 0);
        if ($totalDeps > 0) {
            $msg = $traducciones['biomarker_delete_dependency']
                ?? "Cannot delete biomarker: related records exist in comment_biomarker and/or notifications.";
            throw new mysqli_sql_exception($msg);
        }

        // 4) Auditoría (fecha/husos) y timestamp de borrado
        if (class_exists('ClientEnvironmentInfo') && class_exists('TimezoneManager')) {
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();
        } else {
            $deletedAt = date('Y-m-d H:i:s'); // Fallback
        }

        // 5) Eliminación lógica
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE biomarker_id = ?");
        if (!$stmt) {
            throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
        }
        $stmt->bind_param("sss", $deletedAt, $userId, $id);
        if (!$stmt->execute()) {
            throw new mysqli_sql_exception("Error eliminando el biomarcador: " . $stmt->error);
        }
        $stmt->close();

        $this->db->commit();
        return true;
    } catch (mysqli_sql_exception $e) {
        $this->db->rollback();
        throw $e;
    }
}



    public function getMaxExamById($biomarker_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT max_exam FROM {$this->table} WHERE biomarker_id = ? AND deleted_at IS NULL LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing statement: " . $this->db->error);
            }

            $stmt->bind_param("s", $biomarker_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return ['status' => 'error', 'message' => 'Biomarker not found or deleted.'];
            }

            $row = $result->fetch_assoc();
            return ['status' => 'success', 'max_exam' => (int) $row['max_exam']];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }



    public function getUniqueUserBiomarkerValues($id_user, $minDate = '', $maxDate = '')
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener sexo y cumpleaños del usuario
            $stmt = $this->db->prepare("SELECT sex_biological, birthday FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $id_user);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$row = $result->fetch_assoc()) {
                return ["error" => "User not found."];
            }

            $sex_biological = strtolower(trim($row['sex_biological']));
            $birthday = $row['birthday'];
            if (!in_array($sex_biological, ['m', 'f'])) {
                return ["error" => "Invalid sex biological value."];
            }

            // Obtener el mapeo centralizado
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error en el mapeo de paneles: " . $mapping['error']);
            }
            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];
            $id_fields = $mapping['id_fields'];


            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, unit, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            $values = [];
            $added_biomarkers = [];

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);
                $display_name = ($idioma === 'ES') ? $b['name_es'] : $b['name'];
                $unit = $b['unit'];
                $ref_min = $b['reference_min'];
                $ref_max = $b['reference_max'];

                // Filtrado por sex_biologicalo
                if ((strpos($name, 'male') !== false && $sex_biological !== 'm') || (strpos($name, 'female') !== false && $sex_biological !== 'f')) {
                    continue;
                }

                // Evitar duplicados de biomarcadores
                if (in_array($name, $added_biomarkers)) {
                    continue;
                }

                $matched_fields = $field_map[$panel_id][$name] ?? null;
                if (!$matched_fields)
                    continue;

                $field = $matched_fields[0];
                $table = $table_map[$panel_id];
                $date_field = $date_fields[$panel_id];

                $id_field = $id_fields[$panel_id];
                $sql = "SELECT $id_field AS record_id, $field, $date_field FROM $table WHERE user_id = ? AND $field > 0";

                $params = [$id_user];
                $types = "s";

                if ($minDate) {
                    $sql .= " AND $date_field >= ?";
                    $params[] = $minDate;
                    $types .= "s";
                }
                if ($maxDate) {
                    $sql .= " AND $date_field <= ?";
                    $params[] = $maxDate;
                    $types .= "s";
                }

                $sql .= " ORDER BY $date_field ASC LIMIT 1";
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $value = (float) $row[$field];
                    $date = DateTime::createFromFormat('Y-m-d', $row[$date_field]);
                    $formatted_date = $date ? $date->format('m/d/Y') : $row[$date_field];

                    // Calcular el estado
                    $status = 'Ok';
                    if ($name === 'body age') {
                        $age = date_diff(date_create($birthday), date_create('today'))->y;
                        $status = ($value > $age) ? 'High' : 'Ok';
                    } elseif (is_numeric($ref_min) && is_numeric($ref_max)) {
                        if ($value < $ref_min) {
                            $status = 'Low';
                        } elseif ($value > $ref_max) {
                            $status = 'High';
                        }
                    }

                    // Traducción del estado si ES
                    if ($idioma === 'ES') {
                        $status = match ($status) {
                            'Ok' => 'Normal',
                            'Low' => 'Bajo',
                            'High' => 'Alto',
                            default => $status
                        };
                    }

                    $values[] = [
                        'record_id' => $row['record_id'],
                        'date' => $formatted_date,
                        'biomarker' => $display_name,
                        'biomarker_key' => $field,
                        'value' => $value . ' ' . $unit,
                        'panel' => $panel_id,
                        'status' => $status
                    ];

                    $added_biomarkers[] = $name;
                }
            }

            return $values;
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }


public function evaluateBiomarkerValueStatus($id_biomarker, $value)
{
    try {
        // Obtener datos del biomarcador
        $biomarker = $this->getById($id_biomarker);

        if (
            !$biomarker ||
            !isset($biomarker['reference_min'], $biomarker['reference_max'])
        ) {
            return [
                'status'  => 'error',
                'message' => 'Invalid biomarker or missing reference values.',
                'result'  => null
            ];
        }

        $ref_min = (float) $biomarker['reference_min'];
        $ref_max = (float) $biomarker['reference_max'];
        $value   = (float) $value;

        // Identificar body_age por name / name_db (ES/EN)
        $name    = strtolower(trim($biomarker['name']    ?? ''));
        $name_db = strtolower(trim($biomarker['name_db'] ?? ''));
        $isBodyAge = ($name_db === 'body_age') || ($name === 'body age') || ($name === 'edad corporal');

        if ($isBodyAge && is_numeric($value)) {
            // Calcular edad real desde la sesión
            $userAge = null;
            if (!empty($_SESSION['birthday'])) {
                try {
                    $birth = new DateTime((string) $_SESSION['birthday']);
                    $today = new DateTime();
                    $userAge = (int) $today->diff($birth)->y;
                } catch (\Throwable $e) {
                    $userAge = null;
                }
            }

            if ($userAge !== null) {
                // Regla solicitada: si value > edad_real => High; si no => Normal
                $result = ($value > $userAge) ? 'High' : 'Normal';

                return [
                    'status'  => 'success',
                    'message' => 'Value evaluated (body_age vs real age).',
                    'result'  => $result
                ];
            }
            // Si no se pudo calcular la edad, continuar con la lógica estándar más abajo.
        }

        // ===== Lógica estándar para otros biomarcadores (o body_age sin edad disponible) =====
        if ($value >= $ref_max) {
            $result = 'High';
        } elseif ($value <= $ref_min) {
            $result = 'Low';
        } else {
            $result = 'Normal';
        }

        return [
            'status'  => 'success',
            'message' => 'Value evaluated.',
            'result'  => $result
        ];

    } catch (Exception $e) {
        return [
            'status'  => 'error',
            'message' => 'Exception: ' . $e->getMessage(),
            'result'  => null
        ];
    }
}





    public function countCompletedFieldsByUser($user_id, $minDate = null, $maxDate = null)
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener sex_biologicalsex_biologicalo
            $stmtSex = $this->db->prepare("SELECT sex_biological FROM users WHERE user_id = ?");
            $stmtSex->bind_param("s", $user_id);
            $stmtSex->execute();
            $resSex = $stmtSex->get_result();
            $sexRow = $resSex->fetch_assoc();
            $sex_biological = strtolower(trim($sexRow['sex_biological'] ?? ''));
            $stmtSex->close();

            if (!in_array($sex_biological, ['m', 'f'])) {
                throw new Exception("Invalid sex biological for user.");
            }

            // Obtener los nombres de los paneles (traducidos)
            $stmtPanels = $this->db->prepare("SELECT panel_id, display_name, display_name_es, panel_name FROM test_panels");
            $stmtPanels->execute();
            $resPanels = $stmtPanels->get_result();
            $panelNames = [];
            $panelNameMap = [];
            while ($row = $resPanels->fetch_assoc()) {
                $panelNames[$row['panel_id']] = ($idioma === 'ES') ? $row['display_name_es'] : $row['display_name'];
                $panelNameMap[$row['panel_id']] = $row['panel_name'];
            }

            // Obtener mapeo centralizado
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error en el mapeo de paneles: " . $mapping['error']);
            }
            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];
            $time_fields = $mapping['time_fields'];

            $resultados = [];

            foreach ($table_map as $panel_id => $table) {
                $date_field = $date_fields[$panel_id];
                $time_field = $time_fields[$panel_id] ?? null;

                $sql = "SELECT * FROM $table WHERE user_id = ?";
                $types = "s";
                $params = [$user_id];

                if (!empty($minDate) && !empty($maxDate)) {
                    $sql .= " AND $date_field BETWEEN ? AND ?";
                    $types .= "ss";
                    $params[] = $minDate;
                    $params[] = $maxDate;
                }

                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $res = $stmt->get_result();

                while ($row = $res->fetch_assoc()) {
                    $completed = 0;
                    $expected = 0;

                    if (!isset($field_map[$panel_id])) {
                        continue;
                    }

                    foreach ($field_map[$panel_id] as $name => $fields) {
                        if (
                            (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                            (strpos($name, 'female') !== false && $sex_biological !== 'f')
                        ) {
                            continue;
                        }
                        $expected++;

                        $col = $fields[0];
                        $val = isset($row[$col]) ? (float) $row[$col] : 0.0;

                        if ($val > 0) {
                            $completed++;
                        }
                    }

                    $estado = ($completed === $expected) ? 'Complete' : 'Partial';
                    if ($idioma === 'ES') {
                        $estado = ($estado === 'Complete') ? 'Completado' : 'Parcial';
                    }

                    $resultados[] = [
                        'nombre' => $panelNames[$panel_id] ?? 'Unknown',
                        'completados' => $completed,
                        'no_completados' => max(0, $expected - $completed),
                        'fecha' => $row[$date_field],
                        'hora' => $time_field ? $row[$time_field] : null,
                        'status' => $estado
                    ];
                }

                $stmt->close();
            }

            return $resultados;

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }





    // Inside your BiomarkerModel class

    public function exportBiomarkersCSV()
    {
        try {

            // Configurar cabeceras CSV
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="biomarkers_export_' . date('Ymd_His') . '.csv"');

            $output = fopen('php://output', 'w');

            // Encabezados de columna
            fputcsv($output, [
                'ID',
                'Panel ID',
                'Name',
                'Unit',
                'Reference Min',
                'Reference Max',
                'Deficiency Label',
                'Excess Label',
                'Description'
            ]);

            // Consulta de biomarcadores
            $stmt = $this->db->prepare("
            SELECT biomarker_id, panel_id, name, unit, reference_min, reference_max,
                   deficiency_label, excess_label, description
            FROM biomarkers
            ORDER BY name
        ");
            $stmt->execute();
            $result = $stmt->get_result();

            // Escribir filas
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, [
                    $row['biomarker_id'],
                    $row['panel_id'],
                    $row['name'],
                    $row['unit'],
                    $row['reference_min'],
                    $row['reference_max'],
                    $row['deficiency_label'],
                    $row['excess_label'],
                    $row['description']
                ]);
            }

            fclose($output);
            $stmt->close();

        } catch (Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }


    public function countAllUsersOutOfRangeByUser($min, $max)
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener usuarios con edad calculada
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday, first_name, last_name FROM users");
            $stmt->execute();
            $result = $stmt->get_result();

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $birthdate = new DateTime($row['birthday']);
                $today = new DateTime();
                $age = $today->diff($birthdate)->y;

                $users[] = [
                    'id' => $row['user_id'],
                    'sex_biological' => strtolower(trim($row['sex_biological'])),
                    'age' => $age,
                    'name' => trim($row['first_name'] . ' ' . $row['last_name'])
                ];
            }

            // Obtener todos los biomarcadores con panel_id actualizado
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();

            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            // Cargar mapeo centralizado
            $mapping = $this->buildPanelBiomarkerMappings();
            if (!empty($mapping['error'])) {
                throw new Exception("Error en el mapeo de paneles: " . $mapping['error']);
            }

            $field_map = $mapping['field_map'];
            $table_map = $mapping['table_map'];
            $date_fields = $mapping['date_fields'];

            $results = [];

            foreach ($users as $user) {
                $user_id = $user['id'];
                $sex_biological = $user['sex_biological'];
                $age = $user['age'];
                $user_name = $user['name'];

                if (!in_array($sex_biological, ['m', 'f']))
                    continue;

                $alerts = 0;
                $alert_details = [];
                $latest_out_marker = null;
                $latest_out_date = null;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $display_name = ($idioma === 'ES') ? $b['name_es'] : $b['name'];
                    $ref_min = floatval($b['reference_min']);
                    $ref_max = floatval($b['reference_max']);

                    if (
                        (strpos($name, 'male') !== false && $sex_biological !== 'm') ||
                        (strpos($name, 'female') !== false && $sex_biological !== 'f')
                    ) {
                        continue;
                    }

                    $matched_fields = $field_map[$panel_id][$name] ?? null;
                    if (!$matched_fields)
                        continue;

                    $date_field = $date_fields[$panel_id];
                    $table = $table_map[$panel_id];
                    $field = $matched_fields[0];

                    $sql = "SELECT $field, $date_field AS record_date 
                        FROM $table 
                        WHERE user_id = ? 
                        AND $field > 0 
                        AND $date_field BETWEEN ? AND ?
                        ORDER BY $date_field DESC";

                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("sss", $user_id, $min, $max);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $value = floatval($row[$field]);
                        $date = $row['record_date'];

                        $out_of_range = false;
                        if ($name === 'body age') {
                            $out_of_range = $value > $age;
                        } else {
                            $out_of_range = ($value < $ref_min || $value > $ref_max);
                        }

                        if ($out_of_range) {
                            $alerts++;
                            $alert_details[] = [
                                'biomarker' => $display_name,
                                'value' => $value,
                                'reference_min' => $ref_min,
                                'reference_max' => $ref_max,
                                'date' => $date
                            ];

                            if (!$latest_out_date || $date > $latest_out_date) {
                                $latest_out_date = $date;
                                $latest_out_marker = $display_name;
                            }

                            break; // solo cuenta una vez por biomarcador
                        }
                    }
                }

                if ($alerts > 0) {
                    $results[] = [
                        'user' => $user_name,
                        'alerts' => $alerts,
                        'latest_out_marker' => $latest_out_marker,
                        'alert_details' => $alert_details,
                        'id_user' => $user_id
                    ];
                }
            }

            usort($results, fn($a, $b) => $b['alerts'] <=> $a['alerts']);

            return $results;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }



    public function updateAlertsByUser($user_id)
    {
        try {
            // Obtener sexo y edad del usuario
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday, first_name, last_name FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if (!$user) {
                throw new Exception("Usuario no encontrado.");
            }

            $birthdate = new DateTime($user['birthday']);
            $today = new DateTime();
            $age = $today->diff($birthdate)->y;
            $sex_biological = strtolower(trim($user['sex_biological']));

            if (!in_array($sex_biological, ['m', 'f'])) {
                throw new Exception("Sexo inválido para el usuario.");
            }

            // Obtener los mapeos centralizados
            $mappings = $this->buildPanelBiomarkerMappings();
            if (isset($mappings['error'])) {
                throw new Exception("Error al obtener mappings: " . $mappings['error']);
            }

            $table_map = $mappings['table_map'];
            $date_fields = $mappings['date_fields'];
            $field_map = $mappings['field_map'];

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();

            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            $this->db->begin_transaction();

            foreach ($biomarkers as $b) {
                $panel_id = $b['panel_id'];
                $name = strtolower($b['name']);
                $ref_min = floatval($b['reference_min']);
                $ref_max = floatval($b['reference_max']);

                // Filtrar biomarcadores por sexo
                if ((strpos($name, 'male') !== false && $sex_biological !== 'm') || (strpos($name, 'female') !== false && $sex_biological !== 'f')) {
                    continue;
                }

                if (!isset($field_map[$panel_id][$name])) {
                    continue;
                }

                $field = $field_map[$panel_id][$name][0];
                $date_field = $date_fields[$panel_id] ?? null;
                $table = $table_map[$panel_id] ?? null;

                if (!$table || !$date_field || !$field) {
                    continue;
                }

                $sql = "SELECT {$table}_id AS id, $field, $date_field AS record_date 
                    FROM $table 
                    WHERE user_id = ? 
                    AND $field > 0 
                    AND (alert IS NULL OR alert = 0)
                    ORDER BY $date_field DESC";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param("s", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $value = floatval($row[$field]);
                    $record_id = $row['id'];
                    $is_alert = false;

                    if ($name === 'body age') {
                        $is_alert = $value > $age;
                    } else {
                        $is_alert = ($value < $ref_min || $value > $ref_max);
                    }

                    if ($is_alert) {
                        $update = $this->db->prepare("UPDATE $table SET alert = 1 WHERE {$table}_id = ?");
                        $update->bind_param("s", $record_id);
                        $update->execute();
                        $update->close();
                        break; // solo una alerta por biomarcador
                    }
                }
            }

            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollback();
            return ['error' => $e->getMessage()];
        }
    }


public function getAlertBiomarkerDetailsByUser($user_id)
{
    try {
        $idioma = $_SESSION['idioma'] ?? 'EN';

        // 1) Usuario
        $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday, first_name, last_name FROM `users` WHERE `user_id` = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!$user_data) {
            return ['error' => 'User not found'];
        }

        $age = null;
        if (!empty($user_data['birthday'])) {
            $birthdate = new DateTime($user_data['birthday']);
            $today     = new DateTime();
            $age       = $today->diff($birthdate)->y;
        }

        $sex_biological = strtolower(trim($user_data['sex_biological'] ?? ''));
        if (!in_array($sex_biological, ['m', 'f'], true)) {
            return ['error' => 'Invalid sex biological'];
        }

        // 2) Mappings
        $mappings = $this->buildPanelBiomarkerMappings();
        if (isset($mappings['error'])) {
            throw new Exception("Error al obtener mappings: " . $mappings['error']);
        }

        $table_map   = $mappings['table_map']   ?? [];
        $date_fields = $mappings['date_fields'] ?? [];
        $field_map   = $mappings['field_map']   ?? [];
        $id_fields   = $mappings['id_fields']   ?? [];

        if (empty($table_map)) {
            return [];
        }

        // 3) Panel names (para panel_name según idioma)
        $stmt = $this->db->prepare("SELECT `panel_id`, `display_name`, `display_name_es` FROM `test_panels`");
        $stmt->execute();
        $resPanels = $stmt->get_result();
        $panelNames = [];
        while ($p = $resPanels->fetch_assoc()) {
            $panelNames[$p['panel_id']] = $p;
        }

        // 4) Biomarcadores (incluye name_db)
        $sqlBm = "SELECT `biomarker_id`, `panel_id`, `name`, `name_es`, `name_db`, `reference_min`, `reference_max` FROM `{$this->table}`";
        $stmt = $this->db->prepare($sqlBm);
        $stmt->execute();
        $result = $stmt->get_result();

        $biomarkers = [];
        while ($row = $result->fetch_assoc()) {
            $biomarkers[] = $row;
        }

        $alerts = [];
        $alert_count = 0;

        // 5) Iterar por biomarcador
        foreach ($biomarkers as $b) {
            $panel_id = $b['panel_id'];

            // Claves posibles en minúsculas
            $name_en   = strtolower(trim($b['name'] ?? ''));
            $name_es   = strtolower(trim($b['name_es'] ?? ''));
            $name_db   = strtolower(trim($b['name_db'] ?? ''));
            $norm_en   = $name_en !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $name_en) : '';
            $norm_es   = $name_es !== '' ? preg_replace(['/[\s\-]+/','/[()]/'], ['_',''], $name_es) : '';

            $ref_min = isset($b['reference_min']) ? (float)$b['reference_min'] : null;
            $ref_max = isset($b['reference_max']) ? (float)$b['reference_max'] : null;

            // Filtro por sexo en nombres EN (male/female)
            $en_contains_male   = $name_en !== '' && strpos($name_en, 'male')   !== false;
            $en_contains_female = $name_en !== '' && strpos($name_en, 'female') !== false;
            if (($en_contains_male && $sex_biological !== 'm') || ($en_contains_female && $sex_biological !== 'f')) {
                continue;
            }

            if (!isset($table_map[$panel_id])) {
                continue;
            }

            $table      = $table_map[$panel_id];
            $date_field = $date_fields[$panel_id] ?? null;

            // Resolver campo por orden: name → name_es → norm(name) → norm(name_es) → (opcional) name_db
            $panelFields = $field_map[$panel_id] ?? [];
            $field = null;
            if ($name_en !== '' && isset($panelFields[$name_en])) {
                $field = $panelFields[$name_en][0];
            } elseif ($name_es !== '' && isset($panelFields[$name_es])) {
                $field = $panelFields[$name_es][0];
            } elseif ($norm_en !== '' && isset($panelFields[$norm_en])) {
                $field = $panelFields[$norm_en][0];
            } elseif ($norm_es !== '' && isset($panelFields[$norm_es])) {
                $field = $panelFields[$norm_es][0];
            } elseif ($name_db !== '' && isset($panelFields[$name_db])) {
                $field = $panelFields[$name_db][0];
            }

            if (!$field) {
                continue;
            }

            // PK real desde mappings; fallback
            $pk = $id_fields[$panel_id] ?? "{$table}_id";

            // Lectura de valores (si no hay campo fecha, devuelve NULL)
            $date_sql = $date_field ? ", `{$date_field}` AS `record_date`" : ", NULL AS `record_date`";
            $sql = "
                SELECT
                    `{$pk}`   AS `record_id`,
                    `{$field}` AS `val`
                    {$date_sql}
                FROM `{$table}`
                WHERE `user_id` = ?
                  AND `{$field}` > 0
                ORDER BY " . ($date_field ? "`{$date_field}`" : "`{$pk}`") . " DESC
            ";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                continue;
            }
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $resVals = $stmt->get_result();

            while ($rowVal = $resVals->fetch_assoc()) {
                $value = isset($rowVal['val']) && is_numeric($rowVal['val']) ? (float)$rowVal['val'] : null;
                if ($value === null) {
                    continue;
                }

                $record_id = $rowVal['record_id'] ?? null;
                $date      = $rowVal['record_date'] ?? null;

                // Estado (caso especial body_age)
                $status = null;
                $is_body_age = ($name_db === 'body_age') || ($name_en === 'body age') || ($name_es === 'edad corporal');

                if ($is_body_age && $age !== null) {
                    if ($value > $age) {
                        $status = ($idioma === 'ES') ? 'Alto' : 'High';
                    }
                } else {
                    if ($ref_min !== null && $value < $ref_min) {
                        $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                    } elseif ($ref_max !== null && $value > $ref_max) {
                        $status = ($idioma === 'ES') ? 'Alto' : 'High';
                    }
                }

                if ($status) {
                    $alert_count++;
                    $alerts[] = [
                        'biomarker'       => ($idioma === 'ES') ? ($b['name_es'] ?? $b['name']) : ($b['name'] ?? ''),
                        'value'           => $value,
                        'status'          => $status,
                        'date'            => $date,
                        'panel'           => $panel_id,
                        'panel_name'      => ($idioma === 'ES')
                            ? ($panelNames[$panel_id]['display_name_es'] ?? '')
                            : ($panelNames[$panel_id]['display_name']    ?? ''),
                        'biomarker_key'   => $field,
                        'record_id'       => $record_id,
                        // Estos campos quedan en 0 si no existen en la tabla:
                        'no_alert_user'   => (int)($rowVal['no_alert_user']  ?? 0),
                        'no_alert_admin'  => (int)($rowVal['no_alert_admin'] ?? 0),
                        'total_alerts'    => 0, // se rellena después
                    ];
                    break; // solo una por biomarcador
                }
            }
        }

        // 6) Propagar total
        foreach ($alerts as &$a) {
            $a['total_alerts'] = $alert_count;
        }

        return $alerts;

    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}


    public function getAllUsersAlertBiomarkerDetails()
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener todos los usuarios
            $users = [];
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday, first_name, last_name FROM users");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            // Obtener mappings dinámicos
            $mappings = $this->buildPanelBiomarkerMappings();
            if (isset($mappings['error'])) {
                throw new Exception("Error al obtener mappings: " . $mappings['error']);
            }

            $table_map = $mappings['table_map'];
            $date_fields = $mappings['date_fields'];
            $field_map = $mappings['field_map'];

            // Obtener biomarcadores
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();

            $biomarkers = [];
            while ($row = $result->fetch_assoc()) {
                $biomarkers[] = $row;
            }

            $all_alerts = [];

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $birthdate = new DateTime($user['birthday']);
                $today = new DateTime();
                $age = $today->diff($birthdate)->y;
                $sex_biological = strtolower(trim($user['sex_biological']));

                if (!in_array($sex_biological, ['m', 'f']))
                    continue;

                $user_alerts = [];
                $alert_count = 0;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $ref_min = floatval($b['reference_min']);
                    $ref_max = floatval($b['reference_max']);

                    // Filtrar según sex_biologicalo
                    if ((strpos($name, 'male') !== false && $sex_biological !== 'm') || (strpos($name, 'female') !== false && $sex_biological !== 'f')) {
                        continue;
                    }

                    if (!isset($field_map[$panel_id][$name])) {
                        continue;
                    }

                    $field = $field_map[$panel_id][$name][0];
                    $date_field = $date_fields[$panel_id] ?? null;
                    $table = $table_map[$panel_id] ?? null;

                    if (!$table || !$date_field || !$field)
                        continue;

                    $primary_key = "{$table}_id";

                    $sql = "SELECT $primary_key AS record_id, $field, $date_field AS record_date, no_alert_user, no_alert_admin 
                        FROM $table 
                        WHERE user_id = ? AND alert = 1 AND $field > 0 
                        ORDER BY $date_field DESC";

                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while ($row = $result->fetch_assoc()) {
                        $value = floatval($row[$field]);
                        $record_id = $row['record_id'];
                        $date = $row['record_date'];

                        $status = null;
                        if ($name === 'body age') {
                            if ($value > $age) {
                                $status = ($idioma === 'ES') ? 'Alto' : 'High';
                            }
                        } elseif ($value < $ref_min) {
                            $status = ($idioma === 'ES') ? 'Bajo' : 'Low';
                        } elseif ($value > $ref_max) {
                            $status = ($idioma === 'ES') ? 'Alto' : 'High';
                        }

                        if ($status) {
                            $alert_count++;
                            $user_alerts[] = [
                                'biomarker' => ($idioma === 'ES') ? $b['name_es'] : $b['name'],
                                'value' => $value,
                                'status' => $status,
                                'date' => $date,
                                'panel' => $panel_id,
                                'biomarker_key' => $field,
                                'no_alert_user' => (int) $row['no_alert_user'],
                                'no_alert_admin' => (int) $row['no_alert_admin'],
                                'record_id' => $record_id,
                                'total_alerts' => 0
                            ];
                            break;
                        }
                    }
                }

                foreach ($user_alerts as &$a) {
                    $a['total_alerts'] = $alert_count;
                }

                if (!empty($user_alerts)) {
                    $all_alerts[] = [
                        'user_id' => $user_id,
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'alerts' => $user_alerts
                    ];
                }
            }

            return $all_alerts;

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }






    public function getAllUsersUniqueBiomarkerValuesWithStatus($minDate = '', $maxDate = '')
    {
        try {
            $idioma = $_SESSION['idioma'] ?? 'EN';

            // Obtener todos los usuarios
            $stmt = $this->db->prepare("SELECT user_id, sex_biological, birthday, first_name, last_name FROM users");
            $stmt->execute();
            $result = $stmt->get_result();

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            // Obtener mappings dinámicos
            $mappings = $this->buildPanelBiomarkerMappings();
            if (isset($mappings['error'])) {
                throw new Exception("Error al obtener mappings: " . $mappings['error']);
            }

            $table_map = $mappings['table_map'];
            $date_fields = $mappings['date_fields'];
            $field_map = $mappings['field_map'];

            // Obtener biomarcadores completos
            $stmt = $this->db->prepare("SELECT biomarker_id, panel_id, name, name_es, unit, reference_min, reference_max FROM {$this->table}");
            $stmt->execute();
            $result = $stmt->get_result();
            $biomarkers = $result->fetch_all(MYSQLI_ASSOC);

            $final_results = [];

            foreach ($users as $user) {
                $user_id = $user['user_id'];
                $sex_biological = strtolower(trim($user['sex_biological']));
                $birthday = $user['birthday'];
                $user_name = trim($user['first_name'] . ' ' . $user['last_name']);
                $added_biomarkers = [];

                if (!in_array($sex_biological, ['m', 'f']) || !$birthday)
                    continue;

                foreach ($biomarkers as $b) {
                    $panel_id = $b['panel_id'];
                    $name = strtolower($b['name']);
                    $unit = $b['unit'];
                    $reference_min = $b['reference_min'];
                    $reference_max = $b['reference_max'];
                    $display_name = ($idioma === 'ES') ? $b['name_es'] : $b['name'];

                    if ((strpos($name, 'male') !== false && $sex_biological !== 'm') || (strpos($name, 'female') !== false && $sex_biological !== 'f')) {
                        continue;
                    }

                    if (in_array($name, $added_biomarkers))
                        continue;
                    if (!isset($field_map[$panel_id][$name]))
                        continue;

                    $field = $field_map[$panel_id][$name][0];
                    $date_field = $date_fields[$panel_id];
                    $table = $table_map[$panel_id];
                    $primary_key = "{$table}_id";

                    $sql = "SELECT $primary_key AS record_id, $field, $date_field FROM $table WHERE user_id = ? AND $field > 0";
                    $params = [$user_id];
                    $types = "s";

                    if ($minDate) {
                        $sql .= " AND $date_field >= ?";
                        $params[] = $minDate;
                        $types .= "s";
                    }
                    if ($maxDate) {
                        $sql .= " AND $date_field <= ?";
                        $params[] = $maxDate;
                        $types .= "s";
                    }

                    $sql .= " ORDER BY $date_field ASC LIMIT 1";

                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($row = $result->fetch_assoc()) {
                        $value = (float) $row[$field];
                        $date = DateTime::createFromFormat('Y-m-d', $row[$date_field]);
                        $formatted_date = $date ? $date->format('m/d/Y') : $row[$date_field];

                        // Evaluar status
                        $status = 'Ok';
                        if ($name === 'body age') {
                            $age = date_diff(date_create($birthday), date_create('today'))->y;
                            $status = ($value > $age) ? 'High' : 'Ok';
                        } else {
                            if (is_numeric($reference_min) && is_numeric($reference_max)) {
                                if ($value < $reference_min) {
                                    $status = 'Low';
                                } elseif ($value > $reference_max) {
                                    $status = 'High';
                                }
                            }
                        }

                        // Traducción si ES
                        if ($idioma === 'ES') {
                            $status = match ($status) {
                                'Ok' => 'Normal',
                                'Low' => 'Bajo',
                                'High' => 'Alto',
                                default => $status
                            };
                        }

                        $final_results[] = [
                            'user' => $user_name,
                            'biomarker' => $display_name,
                            'value' => $value . ' ' . $unit,
                            'date' => $formatted_date,
                            'status' => $status,
                            'id_user' => $user_id
                        ];

                        $added_biomarkers[] = $name;
                    }
                }
            }

            return $final_results;

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    private function getJsonInput(): array
    {
        $input = file_get_contents("php://input");
        return json_decode($input, true) ?? [];
    }

    private function jsonResponse(bool $value, string $message = '', $data = null)
    {
        header('Content-Type: application/json');

        $response = [
            'value' => $value,
            'message' => $message,
            'data' => is_array($data) ? $data : ($data !== null ? [$data] : [])
        ];

        echo json_encode($response);
        exit;
    }


}

