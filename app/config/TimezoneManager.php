<?php
final class TimezoneManager
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * (Opcional) Mantengo tu método original, por si en otros flujos quieres seguir usando sesión.
     */
    public function applyTimezone(): void
    {
        try {
            $region = $_SESSION['timezone'] ?? 'America/Los_Angeles';
            $tz = new DateTimeZone($region);
            $now = new DateTime('now', $tz);
            $offset = $now->format('P');
            $this->mysqli->query("SET time_zone = '{$this->mysqli->real_escape_string($offset)}'");
        } catch (Throwable $e) {
            error_log("Error applying timezone to MariaDB: " . $e->getMessage());
        }
    }


}
