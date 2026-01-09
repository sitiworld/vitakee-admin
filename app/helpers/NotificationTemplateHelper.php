<?php
/**
 * NotificationTemplateHelper
 * Manejador de plantillas de notificación para Vitakee.
 *
 * Uso:
 * $tpl = NotificationTemplateHelper::render('biomarker_out_of_range', [
 * 'biomarker_name' => 'Glucosa',
 * 'value' => 110,
 * 'unit' => 'mg/dL',
 * 'status' => 'Alto',
 * 'ref_min' => 70,
 * 'ref_max' => 100
 * ]);
 * // $tpl['title'], $tpl['desc']
 *
 * // Para armar registro para INSERT en `notifications`:
 * $row = NotificationTemplateHelper::buildForInsert([
 * 'template_key'    => 'biomarker_out_of_range',
 * 'template_params' => $params, // array con las variables
 * 'route'           => '/records/energy/uuid-del-registro',
 * 'module'          => 'energy_metabolism',
 * 'user_id'         => 'uuid-del-usuario'
 * ]);
 */

// Asegúrate de que UuidHelper esté disponible si se usa buildForInsert
// require_once __DIR__ . '/UuidHelper.php';

class NotificationTemplateHelper
{
    /** @var array<string, array{module:string, rol:string, title:string, desc:string}> */
    private static $templates = [

        /* =========================
         * ALERTAS DE BIOMARCADORES (Genérica)
         * ========================= */
        'biomarker_out_of_range' => [
            'module' => 'biomarkers',
            'rol'    => 'user', // Notificación para el usuario
            'title'  => 'Alerta de biomarcador: {{biomarker_name}}',
            'desc'   => 'Tu resultado de {{biomarker_name}} es {{value}} {{unit}}, que está {{status}} del rango ({{ref_min}} - {{ref_max}} {{unit}}).'
        ],

        // Caso especial para Orina (RenalFunctionModel)
        'renal_urine_result_abnormal' => [
            'module' => 'renal_function',
            'rol'    => 'user',
            'title'  => 'Resultado de orina anormal',
            'desc'   => 'Se detectó un resultado anormal (A) en la prueba de orina (Albúmina: {{albumin}}, Creatinina: {{creatinine}}). Te recomendamos contactar a un especialista.'
        ],

/* =========================
         * SEGUNDAS OPINIONES (Specialists)
         * ========================= */
        'second_opinion_request_received' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist', // Notificación para el especialista
            'title'  => 'Nueva solicitud de segunda opinión',
            'desc'   => 'Has recibido una nueva solicitud de {{user_name}} para {{request_type}}. Revisa tu panel para aceptarla o rechazarla.'
        ],
        'second_opinion_status_changed' => [
            'module' => 'second_opinion',
            'rol'    => 'user', // Notificación para el usuario
            'title'  => 'Tu solicitud ha sido {{new_status}}',
            'desc'   => 'Tu solicitud de segunda opinión con {{specialist_name}} ha sido actualizada a: {{new_status}}.'
        ],

        // --- NUEVOS TEMPLATES PARA EL ESPECIALISTA ---

        'second_opinion_cancelled_by_user' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist', // Notificación para el especialista
            'title'  => 'Solicitud cancelada',
            'desc'   => 'El paciente {{user_name}} ha cancelado la solicitud de {{request_type}} que tenías pendiente.'
        ],
        'second_opinion_pending_reminder' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist', // Notificación para el especialista
            'title'  => 'Recordatorio: Solicitud pendiente',
            'desc'   => 'Tienes una solicitud de segunda opinión de {{user_name}} ({{request_type}}) que aún espera tu revisión.'
        ],


         /* =========================
         * VIDEOLLAMADAS (Appointments)
         * ========================= */
        'video_call_scheduled' => [
            'module' => 'video_call',
            'rol'    => 'user', // O 'all' si es para ambos
            'title'  => 'Videollamada programada',
            'desc'   => 'Tu videollamada con {{participant_name}} está programada para el {{schedule_date}} a las {{schedule_time}} ({{timezone}}).'
        ],
        'video_call_cancelled' => [
            'module' => 'video_call',
            'rol'    => 'user', // O 'all'
            'title'  => 'Videollamada cancelada',
            'desc'   => 'La videollamada programada con {{participant_name}} para el {{schedule_date}} ha sido cancelada.'
        ],

        /* =========================
         * COMENTARIOS (comment_biomarker)
         * ========================= */
        'new_comment_on_record' => [
            'module' => 'comments',
            'rol'    => 'user',
            'title'  => 'Nuevo comentario de especialista',
            'desc'   => 'El especialista {{specialist_name}} ha dejado un comentario en tu registro de {{biomarker_name}} del {{record_date}}.'
        ],

        /* =========================
         * REVIEWS (specialist_reviews)
         * ========================= */
             'new_specialist_review' => [
             'module' => 'reviews',
             'rol'    => 'specialist', // Notificación para el especialista
            'title' => 'Has recibido una nueva valoración',
            'desc'  => 'El paciente {{user_name}} te ha dejado una valoración de {{rating}} estrellas.'
         ],

        /* =========================
         * GENERAL / SISTEMA
         * ========================= */
        'welcome_user' => [
            'module' => 'system',
            'rol'    => 'user',
            'title'  => '¡Bienvenido a Vitakee, {{user_name}}!',
            'desc'   => 'Tu cuenta ha sido creada exitosamente. Explora la plataforma y comienza a registrar tus biomarcadores.'
        ],
        'password_reset_success' => [
            'module' => 'system',
            'rol'    => 'user',
            'title'  => 'Contraseña actualizada',
            'desc'   => 'Tu contraseña ha sido actualizada exitosamente el {{date}}.'
        ],

    ];

    /**
     * Devuelve ['title' => ..., 'desc' => ...] con placeholders reemplazados.
     * @param string $key
     * @param array  $params
     * @return array{title:string,desc:string}
     */
    public static function render($key, array $params = [])
    {
        if (!isset(self::$templates[$key])) {
            return ['title' => $key, 'desc' => 'Plantilla no definida.'];
        }
        $tpl = self::$templates[$key];

        // Determinar idioma desde la sesión, default a 'ES'
        $idioma = $_SESSION['idioma'] ?? 'ES';
        $title_key = 'title_' . strtolower($idioma);
        $desc_key = 'desc_' . strtolower($idioma);

        // Fallback a inglés si la traducción no existe
        $title = $tpl[$title_key] ?? $tpl['title'];
        $desc  = $tpl[$desc_key]  ?? $tpl['desc'];

        return [
            'title' => self::replacePlaceholders($title, $params),
            'desc'  => self::replacePlaceholders($desc,  $params),
        ];
    }

    /**
     * Devuelve la metadata de una plantilla (module, title, desc crudos).
     * @param string $key
     * @return array|null
     */
    public static function getMeta($key)
    {
        return self::$templates[$key] ?? null;
    }

    /**
     * Lista todas las claves disponibles.
     * @return string[]
     */
    public static function allKeys()
    {
        return array_keys(self::$templates);
    }

    /**
     * Construye un array listo para INSERT en `notifications`.
     * Requiere UuidHelper::generateUUIDv4().
     *
     * @param array $in [
     * template_key(string), template_params(array), route(?string),
     * module(?string), rol(?string), user_id(?string)
     * ]
     * @return array
     */
    public static function buildForInsert(array $in)
    {
        $now = date('Y-m-d H:i:s');
        $meta = self::getMeta($in['template_key'] ?? '');
        
        // El 'created_by' debe ser el actor actual (admin, user, specialist)
        $actorId = $_SESSION['user_id'] ?? $_SESSION['specialist_id'] ?? $_SESSION['administrator_id'] ?? null;

        return [
            'notifications_id' => UuidHelper::generateUUIDv4(),
            'template_key'     => (string)($in['template_key'] ?? 'general'),
            'template_params'  => json_encode($in['template_params'] ?? [], JSON_UNESCAPED_UNICODE),
            'route'            => isset($in['route'])   ? (string)$in['route']   : null,
            'module'           => (string)($in['module'] ?? $meta['module'] ?? 'general'),
            'rol'              => isset($in['rol'])     ? (string)$in['rol']     : ($meta['rol'] ?? 'user'),
            'user_id'          => isset($in['user_id']) ? (string)$in['user_id'] : null,
            'new'              => 1,
            'read_unread'      => 0,
            'created_at'       => $now,
            'created_by'       => $actorId, // Usar el actor en sesión
            'updated_at'       => null,
            'updated_by'       => null,
            'deleted_at'       => null,
            'deleted_by'       => null,
        ];
    }

    /* =========================
     * Helpers internos
     * ========================= */
    private static function replacePlaceholders($text, array $params)
    {
        if ($text === '' || empty($params)) {
            return $text;
        }
        foreach ($params as $k => $v) {
            // Asegurarse de que el valor sea un string simple
            $value = is_scalar($v) ? (string)$v : (is_null($v) ? '' : '(array)');
            $text = str_replace('{{'.$k.'}}', $value, $text);
        }
        return $text;
    }
}
