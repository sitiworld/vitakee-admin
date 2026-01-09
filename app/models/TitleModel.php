<?php
require_once __DIR__ . '/../config/Database.php';

class TitleModel
{
    private $db;
    private $table = 'specialists_titles';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY title_id  ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE title_id  = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
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

            $createdAt = $env->getCurrentDatetime();

            // Generar UUID para title_id
            $uuid = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (title_id, name_en, name_es, created_at, created_by) 
            VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing insert: " . $this->db->error);
            }

            $stmt->bind_param("sssss", $uuid, $data['name_en'], $data['name_es'], $createdAt, $userId);

            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }


    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();

            $updatedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} 
                SET name_en = ?, name_es = ?, updated_at = ?, updated_by = ? 
                WHERE title_id  = ?");
            $stmt->bind_param("sssss", $data['name_en'], $data['name_es'], $updatedAt, $userId, $id);

            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            // Cargar traducciones según idioma de sesión
            $lang = strtoupper($_SESSION['idioma'] ?? 'EN');
            $archivo_idioma = PROJECT_ROOT . "/lang/{$lang}.php";
            $traducciones = file_exists($archivo_idioma) ? include $archivo_idioma : [];

            $userId = $_SESSION['user_id'] ?? null;

            // Verificar dependencias en specialists
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) as total FROM specialists WHERE title_id = ? AND deleted_at IS NULL");
            if (!$stmtCheck) {
                throw new mysqli_sql_exception("Error preparando la consulta de dependencias: " . $this->db->error);
            }
            $stmtCheck->bind_param("s", $id);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
            $stmtCheck->close();

            if ($row['total'] > 0) {
                $msg = $traducciones['title_delete_dependency'] ?? "Cannot delete title: related specialists exist.";
                throw new mysqli_sql_exception($msg);
            }

            // Auditoría
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            // Eliminación lógica
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE title_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
            }
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error eliminando el título: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function replaceAllIdsWithUUID(): array
    {
        $this->db->begin_transaction();

        try {
            // Definir aquí las tablas y sus campos ID
            $tablesWithIds = [
                'backups' => 'backup_id',
                // agrega más si deseas
            ];

            $resultSummary = [];

            foreach ($tablesWithIds as $table => $idField) {
                $sql = "SELECT `$idField` FROM `$table` WHERE deleted_at IS NULL";
                $result = $this->db->query($sql);

                if (!$result || $result->num_rows === 0) {
                    $resultSummary[$table] = 'No rows to update.';
                    continue;
                }

                $updates = [];
                while ($row = $result->fetch_assoc()) {
                    $oldId = $row[$idField];
                    $newId = $this->generateUUIDv4();
                    $updates[] = ['old' => $oldId, 'new' => $newId];
                }

                $stmt = $this->db->prepare("UPDATE `$table` SET `$idField` = ? WHERE `$idField` = ?");
                if (!$stmt) {
                    throw new mysqli_sql_exception("Error preparing statement for $table: " . $this->db->error);
                }

                foreach ($updates as $pair) {
                    $stmt->bind_param('ss', $pair['new'], $pair['old']);
                    $stmt->execute();
                }

                $stmt->close();
                $resultSummary[$table] = count($updates) . ' IDs updated.';
            }

            $this->db->commit();
            return ['status' => true, 'message' => 'IDs updated successfully.', 'details' => $resultSummary];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            return ['status' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // Debe incluirse en la misma clase:
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














    public function exportAllTitlesToCSV()
    {
        try {
            $query = "SELECT title_id, name_en, name_es 
                  FROM {$this->table} 
                  WHERE deleted_at IS NULL 
                  ORDER BY title_id  ASC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->db->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "titles_export.csv";
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');

                // CSV header
                fputcsv($output, ['ID', 'Name (EN)', 'Name (ES)']);

                // Data rows
                while ($row = $result->fetch_assoc()) {
                    fputcsv($output, $row);
                }

                fclose($output);
                $stmt->close();
                exit;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No titles found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
