<?php

require_once __DIR__ . '/../config/Database.php';


class TestDocumentsModel
{
    private $db;
    private $table = "test_documents";

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getImageById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT test_documents_id, id_test_panel, id_test, name_image, description 
                                    FROM {$this->table} 
                                    WHERE test_documents_id  = ? AND deleted_at IS NULL 
                                    LIMIT 1");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();

            if ($data) {
                return $data;
            } else {
                return ['status' => 'error', 'message' => 'Image not found.'];
            }
        } catch (mysqli_sql_exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    public function getByPanelAndTest($testPanelId, $testId)
    {
        try {
            $query = "SELECT test_documents_id , id_test_panel, id_test, name_image, description 
                  FROM {$this->table} 
                  WHERE id_test_panel = ? AND id_test = ? AND deleted_at IS NULL 
                  ORDER BY test_documents_id  DESC";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }
            $stmt->bind_param("ss", $testPanelId, $testId);
            $stmt->execute();
            $result = $stmt->get_result();

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            if (count($items) > 0) {
                return ['value' => true, 'message' => '', "data" => $items];
            } else {
                return ['value' => true, 'message' => 'No images found.', "data" => []];
            }
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }


    public function create($data, $file)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                throw new Exception('Image file is required');
            }

            $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt)) {
                throw new Exception('Only JPG, PNG or PDF allowed');
            }

            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('Failed to create directories');
                }
            }

            $filename = uniqid('img_') . '.' . $ext;
            $dest = $uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $dest)) {
                throw new Exception('Failed to move uploaded file');
            }

            // Aplicar zona horaria y contexto
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            (new TimezoneManager($this->db))->applyTimezone();
            $created_at = $env->getCurrentDatetime();

            // Generar UUID para test_documents_id
            $testDocId = $this->generateUUIDv4();

            $query = "INSERT INTO {$this->table} 
                  (test_documents_id, id_test_panel, id_test, name_image, description, created_at, created_by)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing query: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssssss",
                $testDocId,
                $data['id_test_panel'],
                $data['id_test'],
                $filename,
                $data['description'],
                $created_at,
                $userId
            );
            $stmt->execute();
            $this->db->commit();

            return ['value' => true, 'message' => 'Image created successfully.', 'id' => $testDocId];
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            if (isset($filename) && file_exists($dest)) {
                unlink($dest);
            }
            return ['value' => false, 'message' => $e->getMessage()];
        } catch (Exception $e) {
            $this->db->rollback();
            if (isset($filename) && file_exists($dest)) {
                unlink($dest);
            }
            return ['value' => false, 'message' => $e->getMessage()];
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


    public function update($data, $file = null)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $uploadDir = 'uploads/';
            $filename = null;

            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception('Failed to create directories');
                }
            }

            if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload error');
                }

                $allowedExt = ['jpg', 'jpeg', 'png', 'pdf'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExt)) {
                    throw new Exception('Only JPG, PNG or PDF allowed');
                }

                $filename = uniqid('img_') . '.' . $ext;
                $dest = $uploadDir . $filename;
                if (!move_uploaded_file($file['tmp_name'], $dest)) {
                    throw new Exception('Failed to move uploaded file');
                }

                // Delete old image
                $stmt = $this->db->prepare("SELECT name_image FROM {$this->table} WHERE test_documents_id  = ?");
                $stmt->bind_param('s', $data['id']);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    @unlink($uploadDir . $row['name_image']);
                }
                $stmt->close();
            }

            // Aplicar zona horaria y contexto
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $updated_at = $env->getCurrentDatetime(); // ← zona horaria aplicada

            if ($filename) {
                $query = "UPDATE {$this->table} 
                      SET name_image = ?, description = ?, updated_at = ?, updated_by = ?
                      WHERE test_documents_id  = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("sssss", $filename, $data['description'], $updated_at, $userId, $data['id']);
            } else {
                $query = "UPDATE {$this->table} 
                      SET description = ?, updated_at = ?, updated_by = ?
                      WHERE test_documents_id  = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bind_param("ssss", $data['description'], $updated_at, $userId, $data['id']);
            }

            $stmt->execute();
            return ['value' => true, 'message' => 'Image updated successfully.'];
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }




    public function delete($id)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('User not authenticated');
            }

            $checkStmt = $this->db->prepare("SELECT test_documents_id , name_image FROM {$this->table} WHERE test_documents_id  = ? LIMIT 1");
            if (!$checkStmt) {
                throw new mysqli_sql_exception("Error preparing check query: " . $this->db->error);
            }
            $checkStmt->bind_param("s", $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            if ($checkResult->num_rows == 0) {
                throw new mysqli_sql_exception("Image not found for deletion.");
            }

            $row = $checkResult->fetch_assoc();
            $filename = $row['name_image'];

            $uploadDir = __DIR__ . '/uploads/';
            if (file_exists($uploadDir . $filename)) {
                unlink($uploadDir . $filename);
            }

            // Aplicar contexto y zona horaria
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deleted_at = $env->getCurrentDatetime(); // ← Fecha con zona horaria

            // Eliminación lógica
            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE test_documents_id  = ?");
            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparing delete query: " . $this->db->error);
            }

            $stmt->bind_param("sss", $deleted_at, $userId, $id);
            $stmt->execute();

            return ['value' => true, 'message' => 'Image deleted successfully (logical).'];
        } catch (mysqli_sql_exception $e) {
            return ['value' => false, 'message' => $e->getMessage()];
        }
    }


}
