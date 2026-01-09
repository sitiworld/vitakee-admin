<?php

class AvailabilitySlotGenerator
{
    private $availabilityModel;
    private $secondOpinionModel;
    private $pricingModel; // Nueva propiedad

    // El constructor ahora acepta los tres modelos
    public function __construct(
        SpecialistAvailabilityModel $availabilityModel,
        SecondOpinionRequestsModel $secondOpinionModel,
        SpecialistPricingModel $pricingModel // Nuevo
    ) {
        $this->availabilityModel = $availabilityModel;
        $this->secondOpinionModel = $secondOpinionModel;
        $this->pricingModel = $pricingModel;
    }

    /**
     * Genera slots de disponibilidad basados en la duración de un servicio específico.
     *
     * @param string $specialistId
     * @param string $pricingId ID del servicio para determinar la duración del slot.
     * @param string $targetTimezone
     * @param string $rangeStartStr
     * @param string $rangeEndStr
     * @return array
     * @throws Exception
     */
    public function generateSlots(string $specialistId, string $pricingId, string $targetTimezone, string $rangeStartStr, string $rangeEndStr): array
    {
        // 1. Inicializar la estructura de la respuesta
        $result = [
            'clickableSlots' => [],
            'busySlots' => [],
        ];

        // ... (código para obtener $slotDurationMinutes, $weeklyAvailability, etc. sin cambios) ...
        $service = $this->pricingModel->getById($pricingId);
        if (!$service || empty($service['duration_services'])) {
            throw new Exception("Servicio no encontrado o sin duración definida.");
        }
        $slotDurationMinutes = (int) $service['duration_services'];

        $weeklyAvailability = $this->availabilityModel->getByIdSpecialist($specialistId);
        if (empty($weeklyAvailability)) {
            return $result;
        }
        $availabilityByDay = [];
        foreach ($weeklyAvailability as $avail) {
            $availabilityByDay[$avail['weekday']] = $avail;
        }

        // --- CORRECCIÓN 1: Simplificar `busySlots` ---
        // Obtenemos únicamente las citas/bloqueos existentes.
        $utcZone = new DateTimeZone('UTC');
        $rangeStartUTC = (new DateTime($rangeStartStr))->setTimezone($utcZone)->format('Y-m-d H:i:s');
        $rangeEndUTC = (new DateTime($rangeEndStr))->setTimezone($utcZone)->format('Y-m-d H:i:s');
        $existingAppointmentsUTC = $this->secondOpinionModel->getBusySlotsForSpecialist($specialistId, $rangeStartUTC, $rangeEndUTC);

        $targetTimezoneObj = new DateTimeZone($targetTimezone);

        // Procesar las citas existentes para añadirlas al array `busySlots`
        foreach ($existingAppointmentsUTC as $appointment) {
            $startUserTz = (clone $appointment['start'])->setTimezone($targetTimezoneObj);
            $endUserTz = (clone $appointment['end'])->setTimezone($targetTimezoneObj);

            $result['busySlots'][] = [
                'start' => $startUserTz->format(DateTime::ATOM),
                'end' => $endUserTz->format(DateTime::ATOM),
            ];
        }

        // --- CORRECCIÓN 2: No generar slots en el pasado ---
        // Obtenemos la fecha y hora actual EN LA ZONA HORARIA DEL USUARIO para la comparación.
        $nowInUserTz = new DateTime('now', $targetTimezoneObj);

        $period = new DatePeriod(
            new DateTime($rangeStartStr),
            new DateInterval('P1D'),
            new DateTime($rangeEndStr)
        );

        foreach ($period as $date) {
            $weekday = $date->format('l');
            if (isset($availabilityByDay[$weekday])) {
                $dayAvailability = $availabilityByDay[$weekday];
                $specialistTimezoneObj = new DateTimeZone($dayAvailability['timezone']);
                $workdayStart = new DateTime($date->format('Y-m-d') . ' ' . $dayAvailability['start_time'], $specialistTimezoneObj);
                $workdayEnd = new DateTime($date->format('Y-m-d') . ' ' . $dayAvailability['end_time'], $specialistTimezoneObj);
                $bufferMinutes = $dayAvailability['buffer_time_minutes'];

                $cursor = clone $workdayStart;
                while ($cursor < $workdayEnd) {
                    $slotStart = clone $cursor;
                    $slotEnd = clone $slotStart;
                    $slotEnd->modify("+{$slotDurationMinutes} minutes");

                    if ($slotEnd > $workdayEnd)
                        break;

                    // --- APLICACIÓN DE LA CORRECCIÓN 2 ---
                    // Convertimos el inicio del slot a la zona del usuario para comparar.
                    $slotStartInUserTz = (clone $slotStart)->setTimezone($targetTimezoneObj);

                    // Solo proceder si el slot es en el futuro Y no se solapa con una cita existente.
                    if ($slotStartInUserTz > $nowInUserTz && !$this->isOverlapping($slotStart, $slotEnd, $existingAppointmentsUTC)) {
                        // Si es válido, lo añadimos a los slots clickeables
                        // La conversión final a la zona del usuario se mantiene
                        $slotStart->setTimezone($targetTimezoneObj);
                        $slotEnd->setTimezone($targetTimezoneObj);

                        $result['clickableSlots'][] = [
                            'start' => $slotStart->format(DateTime::ATOM),
                            'end' => $slotEnd->format(DateTime::ATOM),
                        ];
                    }
                    $cursor->modify("+" . ($slotDurationMinutes + $bufferMinutes) . " minutes");
                }
            }
        }
        return $result;
    }


    private function isOverlapping(DateTime $slotStart, DateTime $slotEnd, array $busySlotsUTC): bool
    {
        // Convertimos el slot potencial a UTC para una comparación precisa.
        $utcZone = new DateTimeZone('UTC');
        $slotStartUTC = (clone $slotStart)->setTimezone($utcZone);
        $slotEndUTC = (clone $slotEnd)->setTimezone($utcZone);

        foreach ($busySlotsUTC as $busySlot) {
            $busyStart = $busySlot['start']; // Ya es un objeto DateTime en UTC
            $busyEnd = $busySlot['end'];     // Ya es un objeto DateTime en UTC

            // La condición de solapamiento es: (InicioA < FinB) y (FinA > InicioB)
            if ($slotStartUTC < $busyEnd && $slotEndUTC > $busyStart) {
                return true; // Hay solapamiento
            }
        }
        return false; // No hay solapamiento
    }
}