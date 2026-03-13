<?php
require_once __DIR__ . '/../config/Database.php';

class BackupModel
{
    private $db;
    private $table = "backups";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function obtenerTodos()
    {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY date DESC";
            $result = $this->db->query($query);
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener respaldos: " . $this->db->error);
            }

            $backups = [];
            while ($fila = $result->fetch_assoc()) {
                $backups[] = $fila;
            }
            return $backups;
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function obtenerPorId($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE backup_id  = ? LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result) {
                throw new mysqli_sql_exception("Error al obtener el resultado: " . $stmt->error);
            }
            return $result->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function eliminar($id)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $now = $env->getCurrentDatetime(); // ← uso correcto de la clase de entorno

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE backup_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }

            $stmt->bind_param("sss", $now, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function crearBackup(): array
    {
        $database = 'bd_vitakee';
        $date = date('Y-m-d_H-i-s');
        $backupDir = $this->obtenerRutaBackupDir();
        $backupFile = $backupDir . DIRECTORY_SEPARATOR . "bd_vitakee-{$date}.sql";

        $this->db->query("SET NAMES 'utf8'");
        $sqlDump = "-- Backup completo generado por PHP\n";
        $sqlDump .= "-- Base de datos: {$database}\n";
        $sqlDump .= "-- Fecha: {$date}\n\n";
        $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // 1. Tablas
        $tables = [];
        $result = $this->db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }

        foreach ($tables as $table) {
            $result = $this->db->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_assoc();
            $sqlDump .= "-- Estructura de tabla `$table`\n";
            $sqlDump .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlDump .= $row['Create Table'] . ";\n\n";

            $result = $this->db->query("SELECT * FROM `$table`");
            if ($result->num_rows > 0) {
                $sqlDump .= "-- Datos de tabla `$table`\n";
                while ($row = $result->fetch_assoc()) {
                    $values = array_map(function ($v) {
                        if (is_null($v))
                            return "NULL";
                        return "'" . addslashes($v) . "'";
                    }, array_values($row));
                    $sqlDump .= "INSERT INTO `$table` VALUES (" . implode(",", $values) . ");\n";
                }
                $sqlDump .= "\n";
            }
        }

        // 2. Vistas
        $viewsResult = $this->db->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
        while ($viewRow = $viewsResult->fetch_array()) {
            $view = $viewRow[0];
            $res = $this->db->query("SHOW CREATE VIEW `$view`");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                $sqlDump .= "-- Vista `$view`\n";
                $sqlDump .= "DROP VIEW IF EXISTS `$view`;\n";
                if (isset($row['Create View'])) {
                    $sqlDump .= $row['Create View'] . ";\n\n";
                }
            }
        }

        // 3. Procedimientos
        $procResult = $this->db->query("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '$database' AND ROUTINE_TYPE = 'PROCEDURE'");
        while ($row = $procResult->fetch_assoc()) {
            $procName = $row['ROUTINE_NAME'];
            $res = $this->db->query("SHOW CREATE PROCEDURE `$procName`");
            if ($res && $res->num_rows > 0) {
                $procRow = $res->fetch_assoc();
                if (isset($procRow['Create Procedure'])) {
                    $sqlDump .= "-- Procedimiento `$procName`\n";
                    $sqlDump .= "DROP PROCEDURE IF EXISTS `$procName`;\n";
                    $sqlDump .= $procRow['Create Procedure'] . ";\n\n"; // ✅ Sin DELIMITER
                }
            }
        }

        // 4. Funciones
        $funcResult = $this->db->query("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '$database' AND ROUTINE_TYPE = 'FUNCTION'");
        while ($row = $funcResult->fetch_assoc()) {
            $funcName = $row['ROUTINE_NAME'];
            $res = $this->db->query("SHOW CREATE FUNCTION `$funcName`");
            if ($res && $res->num_rows > 0) {
                $funcRow = $res->fetch_assoc();
                if (isset($funcRow['Create Function'])) {
                    $sqlDump .= "-- Función `$funcName`\n";
                    $sqlDump .= "DROP FUNCTION IF EXISTS `$funcName`;\n";
                    $sqlDump .= $funcRow['Create Function'] . ";\n\n"; // ✅ Sin DELIMITER
                }
            }
        }

        // 5. Triggers
        $triggerRes = $this->db->query("SHOW TRIGGERS FROM `$database`");
        while ($triggerRow = $triggerRes->fetch_assoc()) {
            $triggerName = $triggerRow['Trigger'];
            $triggerStmt = $triggerRow['Statement'];
            $timing = $triggerRow['Timing'];
            $event = $triggerRow['Event'];
            $table = $triggerRow['Table'];
            $sqlDump .= "-- Trigger `$triggerName`\n";
            $sqlDump .= "DROP TRIGGER IF EXISTS `$triggerName`;\n";
            $sqlDump .= "CREATE TRIGGER `$triggerName` $timing $event ON `$table` FOR EACH ROW $triggerStmt;\n\n"; // ✅ Sin DELIMITER
        }

        $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Guardar el archivo
        file_put_contents($backupFile, $sqlDump);

        // Registrar en la base de datos
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $createdAt = $env->getCurrentDatetime();
            $name = basename($backupFile);
            $dateFormatted = date('Y-m-d');
            $uuid = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} (backup_id, name, date, created_at, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $uuid, $name, $dateFormatted, $createdAt, $userId);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();
            return ['status' => 'success', 'message' => 'Respaldo completo creado exitosamente.'];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => 'error', 'message' => 'Error al guardar el respaldo: ' . $e->getMessage()];
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



    public function restaurarBackup($id): array
    {
        try {
            $stmt = $this->db->prepare("SELECT name FROM {$this->table} WHERE backup_id = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error al preparar la consulta: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if (!$result || $result->num_rows === 0) {
                return ['status' => 'error', 'message' => 'Respaldo no encontrado'];
            }

            $row = $result->fetch_assoc();
            $backupDir = $this->obtenerRutaBackupDir();
            $file = $backupDir . DIRECTORY_SEPARATOR . $row['name'];

            if (!file_exists($file)) {
                return ['status' => 'error', 'message' => 'Archivo de respaldo no encontrado'];
            }

            $sql = file_get_contents($file);
            if (!$sql) {
                return ['status' => 'error', 'message' => 'No se pudo leer el archivo de respaldo'];
            }

            // 🔁 Eliminar cualquier DEFINER problemático de forma segura (previene error por pcre limit en archivos grandes)
            $sqlClean = preg_replace('/CREATE DEFINER=`[^`]+`@`[^`]+`\s+/', 'CREATE ', $sql);
            if ($sqlClean !== null) {
                $sql = $sqlClean;
            }

            // Aumentar temporalmente el max_allowed_packet para permitir enviar dumps grandes
            @$this->db->query("SET GLOBAL max_allowed_packet=1073741824");

            // Crear una nueva conexión dedicada para restaurar, 
            // así se adopta el nuevo max_allowed_packet global
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';
            $dbname = 'bd_vitakee';
            
            $restoreDb = new mysqli($host, $user, $pass, $dbname);
            if ($restoreDb->connect_error) {
                return ['status' => 'error', 'message' => 'Error de conexión para restaurar: ' . $restoreDb->connect_error];
            }
            $restoreDb->set_charset("utf8mb4");

            $restoreDb->query("SET FOREIGN_KEY_CHECKS = 0");

            if (!$restoreDb->multi_query($sql)) {
                $err = $restoreDb->error;
                $restoreDb->close();
                return ['status' => 'error', 'message' => 'Error ejecutando el respaldo: ' . $err];
            }

            // Ejecutar todos los bloques hasta el final
            do {
                if ($result = $restoreDb->store_result()) {
                    $result->free();
                }
            } while ($restoreDb->more_results() && $restoreDb->next_result());

            $restoreDb->query("SET FOREIGN_KEY_CHECKS = 1");
            $restoreDb->close();

            return ['status' => 'success', 'message' => 'Respaldo ejecutado completamente.'];
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => 'Error: ' . $e->getMessage()];
        }
    }




    private function obtenerRutaBackupDir(): string
    {
        $backupDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'sql';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        return $backupDir;
    }
}
