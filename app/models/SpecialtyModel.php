<?php

require_once __DIR__ . '/../config/Database.php';

class SpecialtyModel
{
    private $db;
    private $table = 'specialty';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY specialty_id  ASC";
            $result = $this->db->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error fetching specialties: " . $e->getMessage());
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE specialty_id  = ? AND deleted_at IS NULL");
            $stmt->bind_param("s", $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error fetching specialty by ID: " . $e->getMessage());
        }
    }

    public function create($data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            if (class_exists('ClientEnvironmentInfo') && class_exists('TimezoneManager')) {
                $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
                $env->applyAuditContext($this->db, $userId);
                $tzManager = new TimezoneManager($this->db);
                $tzManager->applyTimezone();
                $createdAt = $env->getCurrentDatetime();
            } else {
                $createdAt = date('Y-m-d H:i:s'); // Fallback
            }

            // Generar UUID
            $uuid = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table} 
            (specialty_id, name_en, name_es, created_at, created_by) 
            VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing insert statement: " . $this->db->error);
            }

            $stmt->bind_param("sssss", $uuid, $data['name_en'], $data['name_es'], $createdAt, $userId);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error creating specialty: " . $e->getMessage());
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


    public function update($id, $data)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;

            if (class_exists('ClientEnvironmentInfo') && class_exists('TimezoneManager')) {
                $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
                $env->applyAuditContext($this->db, $userId);
                $tzManager = new TimezoneManager($this->db);
                $tzManager->applyTimezone();
                $updatedAt = $env->getCurrentDatetime();
            } else {
                $updatedAt = date('Y-m-d H:i:s'); // Fallback
            }

            $stmt = $this->db->prepare("UPDATE {$this->table} 
                SET name_en = ?, name_es = ?, updated_at = ?, updated_by = ? 
                WHERE specialty_id  = ?");
            $stmt->bind_param("sssss", $data['name_en'], $data['name_es'], $updatedAt, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error updating specialty: " . $e->getMessage());
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

            // Verificar dependencias en specialists
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) as total FROM specialists WHERE specialty_id = ? AND deleted_at IS NULL");
            if (!$stmtCheck) {
                throw new mysqli_sql_exception("Error preparando la consulta de dependencias: " . $this->db->error);
            }
            $stmtCheck->bind_param("s", $id);
            $stmtCheck->execute();
            $result = $stmtCheck->get_result();
            $row = $result->fetch_assoc();
            $stmtCheck->close();

            if ($row['total'] > 0) {
                $msg = $traducciones['specialty_delete_dependency'] ?? "Cannot delete specialty: related specialists exist.";
                throw new mysqli_sql_exception($msg);
            }

            // Auditoría
            if (class_exists('ClientEnvironmentInfo') && class_exists('TimezoneManager')) {
                $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
                $env->applyAuditContext($this->db, $userId);
                $tzManager = new TimezoneManager($this->db);
                $tzManager->applyTimezone();
                $deletedAt = $env->getCurrentDatetime();
            } else {
                $deletedAt = date('Y-m-d H:i:s'); // Fallback
            }

            // Eliminación lógica
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE specialty_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando la eliminación: " . $this->db->error);
            }
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            if (!$stmt->execute()) {
                throw new mysqli_sql_exception("Error eliminando la especialidad: " . $stmt->error);
            }
            $stmt->close();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    public function exportAllSpecialtiesToCSV()
    {
        try {
            $query = "SELECT specialty_id, name_en, name_es 
                  FROM {$this->table} 
                  WHERE deleted_at IS NULL 
                  ORDER BY name_es";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception('Prepare failed: ' . $this->db->error);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $filename = "specialties_export.csv";
                header('Content-Type: text/csv');
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
                echo json_encode(['status' => 'error', 'message' => 'No specialties found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

}
