<?php
require_once __DIR__ . '/../config/Database.php';

class SpecialistPricingModel
{
    private $db;
    private $table = 'specialist_pricing';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY pricing_id ASC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE pricing_id = ? AND deleted_at IS NULL");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function getByIdSpecialist(string $id): array
    {
        $sql = "
        SELECT
            pricing_id,
            specialist_id,
            service_type,
            duration_services,
            duration_services AS duration_minutes,   -- alias de compatibilidad
            description,
            description      AS description_en,      -- alias de compatibilidad
            description      AS description_es,      -- alias de compatibilidad
            price_usd,
            'USD'            AS currency,            -- alias si tu UI espera 'currency'
            is_active,
            created_at,
            created_by,
            updated_at,
            updated_by,
            deleted_at,
            deleted_by
        FROM {$this->table}
        WHERE specialist_id = ? AND deleted_at IS NULL
        ORDER BY created_at DESC
    ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \mysqli_sql_exception("Prepare error: " . $this->db->error);
        }
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows ?: [];
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

            // Generar UUID para pricing_id
            $pricingId = $this->generateUUIDv4();

            $stmt = $this->db->prepare("INSERT INTO {$this->table}
            (pricing_id, specialist_id, service_type, duration_services, description, price_usd, is_active, created_at, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssssdsss", // ojo: ajustado en la siguiente línea
                $pricingId,
                $data['specialist_id'],
                $data['service_type'],
                $data['duration_services'], // ✅ nuevo campo
                $data['description'],
                $data['price_usd'],
                $data['is_active'],
                $createdAt,
                $userId
            );

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
            SET service_type = ?, duration_services = ?, description = ?, price_usd = ?, is_active = ?, updated_at = ?, updated_by = ?
            WHERE pricing_id = ? AND deleted_at IS NULL");

            if (!$stmt) {
                throw new mysqli_sql_exception("Error preparando consulta: " . $this->db->error);
            }

            $stmt->bind_param(
                "ssdsssss",
                $data['service_type'],
                $data['duration_services'], // ✅ nuevo campo
                $data['description'],
                $data['price_usd'],
                $data['is_active'],
                $updatedAt,
                $userId,
                $id
            );

            $stmt->execute();
            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
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

    /**
     * Obtiene los datos de disponibilidad y los eventos ocupados para renderizar en FullCalendar.
     *
     * @param string $specialistId ID del especialista.
     * @param string $startDateString Fecha de inicio de la búsqueda (ej. '2025-10-01').
     * @param string $endDateString Fecha de fin de la búsqueda (ej. '2025-11-30').
     * @return array Un array con dos claves: 'recurringAvailability' y 'busySlots'.
     */
    /**
     * Lógica Definitiva: Genera todos los datos para FullCalendar, incluyendo los slots clicables.
     * Calcula todo en la zona horaria del especialista y luego traduce el resultado a la del usuario.
     */
    public function getCalendarData(string $specialistId, string $pricingId, string $startDateString, string $endDateString, string $userTimezoneString): array
    {
        // --- 1. Cargar Dependencias y Datos Iniciales ---
        require_once __DIR__ . '/SpecialistAvailabilityModel.php';
        require_once __DIR__ . '/SecondOpinionRequestsModel.php';
        require_once __DIR__ . '/SpecialistModel.php';

        $service = $this->getById($pricingId);
        if (!$service || empty($service['duration_services'])) {
            throw new Exception("Servicio no encontrado o sin duración definida.");
        }
        $serviceDuration = (int) $service['duration_services'];

        $specialistModel = new SpecialistModel();
        $specialist = $specialistModel->getById($specialistId);
        if (!$specialist || empty($specialist['timezone'])) {
            throw new Exception("Especialista no encontrado o sin zona horaria definida.");
        }

        $specialistTimezone = new DateTimeZone($specialist['timezone']);
        $userTimezone = new DateTimeZone($userTimezoneString); // La TZ del usuario en sesión

        $availabilityModel = new SpecialistAvailabilityModel();
        $weeklyAvailability = $availabilityModel->getByIdSpecialist($specialistId);
        $requestsModel = new SecondOpinionRequestsModel();
        $busySlotsRaw = $requestsModel->getBusySlotsForSpecialist($specialistId, $startDateString, $endDateString);

        // --- 2. Generar Slots en la zona horaria del ESPECIALISTA ---

        // ▼▼▼ PASO 1: Obtener la hora actual en la zona horaria del especialista ▼▼▼
        $nowInSpecialistTz = new DateTime('now', $specialistTimezone);

        $slotsInSpecialistTz = [];
        $period = new DatePeriod(
            new DateTime($startDateString, $specialistTimezone),
            new DateInterval('P1D'),
            (new DateTime($endDateString, $specialistTimezone))->modify('+1 day')
        );

        foreach ($period as $date) {
            $dayOfWeekName = $date->format('l');
            foreach ($weeklyAvailability as $availBlock) {
                if ($availBlock['weekday'] === $dayOfWeekName) {
                    $bufferMinutes = (int) ($availBlock['buffer_time_minutes'] ?? 0);
                    $totalSlotTime = $serviceDuration + $bufferMinutes;

                    $blockStart = new DateTime($date->format('Y-m-d') . ' ' . $availBlock['start_time'], $specialistTimezone);
                    $blockEnd = new DateTime($date->format('Y-m-d') . ' ' . $availBlock['end_time'], $specialistTimezone);

                    $currentSlotStart = clone $blockStart;
                    while (true) {
                        $currentSlotEnd = (clone $currentSlotStart)->modify("+" . $serviceDuration . " minutes");
                        if ($currentSlotEnd > $blockEnd) {
                            break;
                        }

                        $isAvailable = true;
                        foreach ($busySlotsRaw as $busy) {
                            if ($currentSlotStart < $busy['end'] && $currentSlotEnd > $busy['start']) {
                                $isAvailable = false;
                                break;
                            }
                        }

                        // ▼▼▼ PASO 2: Añadir la comprobación de que el slot no esté en el pasado ▼▼▼
                        if ($isAvailable && $currentSlotStart >= $nowInSpecialistTz) {
                            // Guardamos los objetos DateTime SIN formatear todavía
                            $slotsInSpecialistTz[] = ['start' => clone $currentSlotStart, 'end' => clone $currentSlotEnd];
                        }

                        $currentSlotStart->modify("+" . $totalSlotTime . " minutes");
                    }
                }
            }
        }

        // --- 3. TRADUCIR TODOS LOS SLOTS (CLICABLES Y OCUPADOS) a la zona horaria del USUARIO ---
        $finalClickableSlots = [];
        foreach ($slotsInSpecialistTz as $slot) {
            $finalClickableSlots[] = [
                'start' => $slot['start']->setTimezone($userTimezone)->format(DateTime::ATOM),
                'end' => $slot['end']->setTimezone($userTimezone)->format(DateTime::ATOM),
            ];
        }

        $finalBusySlots = [];
        foreach ($busySlotsRaw as $slot) {
            $finalBusySlots[] = [
                'start' => $slot['start']->format(DateTime::ATOM),
                'end' => $slot['end']->format(DateTime::ATOM),
            ];
        }

        return [
            'clickableSlots' => $finalClickableSlots,
            'busySlots' => $finalBusySlots,
        ];
    }

    public function delete($id)
    {
        $this->db->begin_transaction();
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $env = new ClientEnvironmentInfo(PROJECT_ROOT . '/app/config/geolite.mmdb');
            $env->applyAuditContext($this->db, $userId);
            $tzManager = new TimezoneManager($this->db);
            $tzManager->applyTimezone();
            $deletedAt = $env->getCurrentDatetime();

            $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ?, deleted_by = ? WHERE pricing_id = ?");
            $stmt->bind_param("sss", $deletedAt, $userId, $id);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (mysqli_sql_exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
