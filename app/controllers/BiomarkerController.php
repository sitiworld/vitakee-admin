<?php



require_once __DIR__ . '/../models/BiomarkerModel.php';


class BiomarkerController
{
    private $biomarkerModel;

    public function __construct()
    {
        $this->biomarkerModel = new BiomarkerModel();
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


    public function getBiomarkerResumen($parametros)
    {
        $id = $parametros['id'] ?? null;
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$id) {
            $this->jsonResponse(false, "ID de usuario requerido.");
            return;
        }

        try {
            $result = $this->biomarkerModel->getUniqueUserBiomarkerValues($id, $minDate, $maxDate);
            echo json_encode([
                'value' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            $this->jsonResponse(false, "Error al obtener biomarcadores: " . $e->getMessage());
        }
    }


public function getBiomarkersInfo($nameParam)
{
    // Solo permitir método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->jsonResponse(false, "Método no permitido.");
        return;
    }

    // --- INICIO DE MODIFICACIÓN ---

    // Definir valores por defecto
    $names = [];
    $user_id = null; // Valor por defecto
    
    // 1. Obtener el tipo de usuario de la sesión
    $user_type = $_SESSION['user_type'] ?? null;

    // Obtener datos desde POST (form-data) o JSON (raw)
    if ($_POST) {
        $names = $_POST['names'] ?? [];
        
        // 2. Si es 'specialist', tomar el user_id del POST
        if ($user_type === 'specialist') {
            $user_id = $_POST['user_id'] ?? null;
        }
    } else {
        $data = $this->getJsonInput();
        $names = $data['names'] ?? [];
        
        // 2. Si es 'specialist', tomar el user_id del JSON
        if ($user_type === 'specialist') {
            $user_id = $data['user_id'] ?? null;
        }
    }

    // 3. Si es 'user', tomar SIEMPRE de la sesión (sobrescribe lo anterior)
    if ($user_type === 'user') {
        $user_id = $_SESSION['user_id'] ?? null;
    }
    
    // --- FIN DE MODIFICACIÓN ---

    // Validar que $names se haya recibido
    if (!is_array($names) || empty($names)) {
        $this->jsonResponse(false, "No se proporcionaron nombres de biomarcadores.");
        return;
    }

    try {
        // Pasar el $user_id determinado (sea de sesión, post, o null) al modelo
        $result = $this->biomarkerModel->getBiomarkersByNames($names, $user_id);

        // Ya viene estructurado con 'status', 'message', 'data'
        echo json_encode($result);
    } catch (\Exception $e) {
        $this->jsonResponse(false, "Error al obtener biomarcadores: " . $e->getMessage());
        return;
    }
}

    public function countUserValidBiomarkers($params)
    {
        $id = $params['id'] ?? null;
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$id) {
            return $this->jsonResponse(false, 'User ID is required.');
        }

        try {
            $result = $this->biomarkerModel->countUserValidBiomarkers($id, $minDate, $maxDate);
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error counting valid biomarkers: ' . $e->getMessage());
        }
    }
    public function countUserValidBiomarkerValuesInRange($params)
    {
        $id = $params['id'] ?? null;
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$id || !$minDate || !$maxDate) {
            return $this->jsonResponse(false, 'User ID, minDate and maxDate are required.');
        }

        try {
            $total = $this->biomarkerModel->countUserValidBiomarkerValuesInRange($id, $minDate, $maxDate);
            return $this->jsonResponse(true, '', ['count' => $total]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error counting biomarker values: ' . $e->getMessage());
        }
    }
    public function countUserInOutRange($params)
    {
        $id = $params['id'] ?? null;
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$id) {
            return $this->jsonResponse(false, 'User ID is required.');
        }

        try {
            // session_start(); // Necesario para acceder a $_SESSION['age']
            $result = $this->biomarkerModel->countUserBiomarkersInRangeOutRange($id, $minDate, $maxDate);
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error calculating in/out range: ' . $e->getMessage());
        }
    }

    public function getUserBiomarkerValues($params)
    {
        $biomarkerId = $params['id_biomarker'] ?? null;
        $userId = $params['id_user'] ?? null;

        if (!$biomarkerId || !$userId) {
            return $this->jsonResponse(false, "Se requieren los parámetros id_biomarker e id_user.");
        }

        try {
            $result = $this->biomarkerModel->getUserBiomarkerValues($biomarkerId, $userId);

            if (isset($result['error'])) {
                return $this->jsonResponse(false, $result['error']);
            }

            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error al obtener valores del biomarcador: ' . $e->getMessage());
        }
    }
    public function getBiomarkerValuesByPanelTest($parametros)
    {
        $panel = $parametros['panel'] ?? 0;
        $test = $parametros['test'] ?? 0;
        error_log("Panel: " . $panel); // Agregar log para depuración
        error_log("Test: " . $test); // Agregar log para depuración

        try {
            if ($panel > 0 && $test > 0) {
                $data = $this->biomarkerModel->getBiomarkerValuesByPanelTest($panel, $test);
                return $this->jsonResponse(true, '', $data);
            } else {
                return $this->jsonResponse(false, 'Invalid panel or test ID');
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Error fetching comments: " . $e->getMessage());
        }
    }

    public function getFilteredBiomarkerRecords($parametros)
    {
        $id_user = $_SESSION['user_id'];
        $id_biomarker = $parametros['id_biomarker'] ?? 0;
        $minDate = $parametros['minDate'] ?? null;
        $maxDate = $parametros['maxDate'] ?? null;
        $tipo = strtolower($parametros['tipo'] ?? 'all');


        try {
            if ($id_user != 0 && $id_biomarker != 0 && in_array($tipo, ['all', 'in', 'out'])) {
                $data = $this->biomarkerModel->getFilteredBiomarkerRecords($id_user, $id_biomarker, $minDate, $maxDate, $tipo);
                return $this->jsonResponse(true, '', $data);
            } else {
                return $this->jsonResponse(false, 'Invalid parameters: Ensure user ID, biomarker ID, and filter type are valid.');
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, "Database error: " . $e->getMessage());
        } catch (Exception $e) {
            return $this->jsonResponse(false, "Unexpected error: " . $e->getMessage());
        }
    }




    public function getMostFrequentBiomarker($params)
    {
        $data = $this->getJsonInput();
        $id_user = $data['id'] ?? null;
        $minDate = $data['minDate'] ?? '';
        $maxDate = $data['maxDate'] ?? '';



        if (!$id_user) {
            return $this->jsonResponse(false, 'User ID is required');
        }

        try {
            $result = $this->biomarkerModel->getMostFrequentBiomarker($id_user, $minDate, $maxDate);
            return $this->jsonResponse(true, $result['message'], $result['id_biomarker']);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving most frequent biomarker: ' . $e->getMessage());
        }
    }
    public function getMostFrequentBiomarkerGlobal($params)
    {
        $data = $this->getJsonInput();
        $minDate = $data['minDate'] ?? '';
        $maxDate = $data['maxDate'] ?? '';

        try {
            $result = $this->biomarkerModel->getMostFrequentBiomarkerAllUsers($minDate, $maxDate);
            return $this->jsonResponse(true, $result['message'], $result['id_biomarker']);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving most frequent biomarker: ' . $e->getMessage());
        }
    }


    public function getUsersBiomarkerAvgAndOutRange($params)
    {
        $data = $this->getJsonInput();

        $min = $data['min'] ?? '';  // Obtención de fecha mínima desde los parámetros de la solicitud
        $max = $data['max'] ?? '';  // Obtención de fecha máxima desde los parámetros de la solicitud
        $id_user = $data['id_user'] ?? null;
        $id_biomarker = $data['id_biomarker'] ?? null;
        $status = $data['status'] ?? 'all';  // Establece 'all' por defecto si no se pasa un estado


        if (!$id_biomarker) {
            return $this->jsonResponse(false, 'Biomarker ID is required');
        }

        try {
            // Llamada al modelo para obtener los datos
            $result = $this->biomarkerModel->getUsersBiomarkerAvgAndOutRange($id_biomarker, $id_user, $status, $min, $max);

            // Retorno de la respuesta exitosa con los resultados
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving biomarker data: ' . $e->getMessage());
        }
    }

    public function countUsersBiomarkersOutOfRange()
    {
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$minDate || !$maxDate) {
            return $this->jsonResponse(false, 'Both minDate and maxDate are required.');
        }

        try {
            $count = $this->biomarkerModel->countAllUsersBiomarkersOutOfRangeStreak($minDate, $maxDate);
            return $this->jsonResponse(true, '', ['total' => $count]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving biomarker streak count: ' . $e->getMessage());
        }
    }
    public function getUsersBiomarkersInRangePercentage()
    {
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$minDate || !$maxDate) {
            return $this->jsonResponse(false, 'Both minDate and maxDate are required.');
        }

        try {
            $percentage = $this->biomarkerModel->getAllUsersInRangePercentage($minDate, $maxDate);
            return $this->jsonResponse(true, '', ['percentage' => $percentage]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error calculating percentage: ' . $e->getMessage());
        }
    }

    public function getUsersInOutRangePercentage($params)
    {

        $data = $this->getJsonInput();

        $min = $data['min'] ?? '';  // Obtención de fecha mínima desde los parámetros de la solicitud
        $max = $data['max'] ?? '';  // Obtención de fecha máxima desde los parámetros de la solicitud
        $id_user = $data['id_user'] ?? null;
        $id_biomarker = $data['id_biomarker'] ?? null;
        $status = $data['status'] ?? 'all';  // Establece 'all' por defecto si no se pasa un estado


        try {
            // Llamar al modelo para obtener el porcentaje de usuarios en y fuera del rango
            $result = $this->biomarkerModel->getAllUsersInOutRangePercentage($min, $max, $id_user, $id_biomarker, $status);

            // Retornar la respuesta en formato JSON
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->jsonResponse(false, 'Error obteniendo porcentaje de usuarios en y fuera del rango: ' . $e->getMessage());
        }
    }
    public function getUserInOutRangePercentageByBiomarker($params)
    {


        $data = $this->getJsonInput();

        $min = $data['min'] ?? '';
        $max = $data['max'] ?? '';
        $user_id = $_SESSION['user_id'] ?? null;
        $id_biomarker = $data['id_biomarker'] ?? null;
        $status = $data['status'] ?? 'all';

        if (!$user_id) {
            return $this->jsonResponse(false, 'User not authenticated.');
        }

        if (!$id_biomarker) {
            return $this->jsonResponse(false, 'Biomarker ID is required.');
        }

        try {
            $result = $this->biomarkerModel->getAllUsersInOutRangePercentage($min, $max, $user_id, $id_biomarker, $status);
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error getting in/out range percentage: ' . $e->getMessage());
        }
    }
    public function evaluateValueAgainstBiomarkerRange($params)
    {
        $data = $this->getJsonInput();

        $id_biomarker = $data['id_biomarker'] ?? null;
        $value = $data['value'] ?? null;

        if (!isset($id_biomarker) || !is_numeric($id_biomarker)) {
            return $this->jsonResponse(false, 'Biomarker ID is required.');
        }

        if (!isset($value) || !is_numeric($value)) {
            return $this->jsonResponse(false, 'A numeric value is required.');
        }

        try {
            $result = $this->biomarkerModel->evaluateBiomarkerValueStatus($id_biomarker, $value);

            if ($result['status'] === 'success') {
                return $this->jsonResponse(true, $result['message'], ['status' => $result['result']]);
            } else {
                return $this->jsonResponse(false, $result['message']);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error evaluating value: ' . $e->getMessage());
        }
    }


    public function getUserBiomarkers($params)
    {
        $user_id = $params['id_user'];  // Obtener ID del usuario desde los parámetros de la ruta

        try {
            // Llamar al modelo para obtener los biomarcadores del usuario
            $biomarkers = $this->biomarkerModel->getUserBiomarkers($user_id);

            // Retornar la respuesta en formato JSON
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->jsonResponse(false, 'Error obteniendo los biomarcadores: ' . $e->getMessage());
        }
    }
    public function verifyIntegrityAlerts($params)
    {
        $user_id = $params['id'];  // Obtener ID del usuario desde los parámetros de la ruta

        try {
            // Llamar al modelo para obtener los biomarcadores del usuario
            $biomarkers = $this->biomarkerModel->verifyIntegrityAlerts($user_id);

            // Retornar la respuesta en formato JSON
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->jsonResponse(false, 'Error obteniendo los biomarcadores: ' . $e->getMessage());
        }
    }

    public function getAlertBiomarkerDetailsByUser($params)
    {
        $user_id = $params['id'];  // Obtener ID del usuario desde los parámetros de la ruta

        try {
            // Llamar al modelo para obtener los biomarcadores del usuario
            $biomarkers = $this->biomarkerModel->getAlertBiomarkerDetailsByUser($user_id);

            // Retornar la respuesta en formato JSON
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->jsonResponse(false, 'Error obteniendo los biomarcadores: ' . $e->getMessage());
        }
    }
    public function getAllUsersAlertBiomarkerDetails()
    {

        try {
            // Llamar al modelo para obtener los biomarcadores del usuario
            $biomarkers = $this->biomarkerModel->getAllUsersAlertBiomarkerDetails();

            // Retornar la respuesta en formato JSON
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (\Exception $e) {
            // Manejo de errores
            return $this->jsonResponse(false, 'Error obteniendo los biomarcadores: ' . $e->getMessage());
        }
    }


    public function getBiomarkerResumenWithStatus($parametros)
    {
        $id = $parametros['id'] ?? null;
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$id) {
            return $this->jsonResponse(false, "User ID is required.");
        }

        try {
            $userModel = new UserModel();
            $user = $userModel->getById($id);

            if (!$user) {
                return $this->jsonResponse(false, "User not found.");
            }


            $result = $this->biomarkerModel->countCompletedFieldsByUser($id, $minDate, $maxDate);

            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving biomarker summary: ' . $e->getMessage());
        }
    }

    public function countTodayRecords()
    {
        try {
            $minDate = $_GET['minDate'] ?? '';
            $maxDate = $_GET['maxDate'] ?? '';

            if (empty($minDate) || empty($maxDate)) {
                return $this->jsonResponse(false, 'Both minDate and maxDate are required.');
            }

            $total = $this->biomarkerModel->countTodayBiomarkerRecords($minDate, $maxDate);

            return $this->jsonResponse(true, '', ['total' => $total]);

        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error counting records: ' . $e->getMessage());
        }
    }




    public function countAllUsersOutOfRange()
    {
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        if (!$minDate || !$maxDate) {
            return $this->jsonResponse(false, 'Both minDate and maxDate are required.');
        }

        try {
            $result = $this->biomarkerModel->countAllUsersOutOfRangeByUser($minDate, $maxDate);
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving out-of-range alerts: ' . $e->getMessage());
        }
    }

    public function getAllUsersBiomarkersWithStatus()
    {
        $minDate = $_GET['minDate'] ?? '';
        $maxDate = $_GET['maxDate'] ?? '';

        try {
            $result = $this->biomarkerModel->getAllUsersUniqueBiomarkerValuesWithStatus($minDate, $maxDate);
            return $this->jsonResponse(true, '', $result);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, 'Error retrieving user biomarkers: ' . $e->getMessage());
        }
    }




    public function showAll()
    {
        try {
            $biomarkers = $this->biomarkerModel->getAll();
            return $this->jsonResponse(true, '', $biomarkers);
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while listing biomarkers: ' . $e->getMessage());
        }
    }

    public function showById($params)
    {
        $id = $params['id'] ?? null;
        try {
            $biomarker = $this->biomarkerModel->getById($id);
            if ($biomarker) {
                return $this->jsonResponse(true, '', $biomarker);
            } else {
                return $this->jsonResponse(false, 'Biomarker not found');
            }
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while retrieving biomarker: ' . $e->getMessage());
        }
    }
    // Example controller action for exporting biomarkers as CSV
    public function exportBiomarkers()
    {
        $biomarkerModel = new BiomarkerModel();

        $result = $biomarkerModel->exportBiomarkersCSV();

        if (isset($result['error'])) {
            // Handle error
            echo "Error: " . $result['error'];
        }
    }



    public function create()
    {
        $data = $this->getJsonInput();
        try {
            $result = $this->biomarkerModel->create($data);
            return $this->jsonResponse(
                $result['status'] === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while creating biomarker: ' . $e->getMessage());
        }
    }

    public function update($params)
    {
        $id = $params['id'] ?? null;
        $data = $this->getJsonInput();

        // Asegúrate de agregar el id al array de datos, si no lo está
        $data['id'] = $id;

        try {
            // Verificamos si el biomarcador existe
            $result = $this->biomarkerModel->update($data);
            return $this->jsonResponse(
                $result['status'] === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while updating biomarker: ' . $e->getMessage());
        }
    }
    public function update_es($params)
    {
        $id = $params['id'] ?? null;
        $data = $this->getJsonInput();

        // Asegurarse de que el ID esté presente en los datos
        $data['id'] = $id;

        try {
            // Llamar al método update_es del modelo
            $result = $this->biomarkerModel->update_es($data);

            return $this->jsonResponse(
                $result['status'] === 'success',
                $result['message'] ?? '',
                $result
            );
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while updating biomarker (ES): ' . $e->getMessage());
        }
    }




    public function delete($params)
    {
        $id = $params['id'] ?? null;
        try {
            $result = $this->biomarkerModel->delete($id);
            return $this->jsonResponse(
                $result,
                $result ? 'Biomarker successfully deleted' : 'Error deleting biomarker'
            );
        } catch (mysqli_sql_exception $e) {
            return $this->jsonResponse(false, 'Error while deleting biomarker: ' . $e->getMessage());
        }
    }
}
