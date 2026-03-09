<?php
/**
 * NotificationTemplateHelper
 * Manejador de plantillas de notificación para Vitakee.
 */
class NotificationTemplateHelper
{
    /** @var array<string, array{module:string, rol:string, title:string, desc:string}> */
    private static $templates = [

        /* =========================
         * ALERTAS DE BIOMARCADORES (Genérica)
         * ========================= */
        'biomarker_out_of_range' => [
            'module' => 'biomarkers',
            'rol'    => 'user', 
            'title'  => 'Alerta de biomarcador: {{biomarker_name}}',
            'desc'   => 'Tu resultado de {{biomarker_name}} es {{value}} {{unit}}, que está {{status}} del rango ({{ref_min}} - {{ref_max}} {{unit}}).',
            'title_en' => 'Biomarker alert: {{biomarker_name}}',
            'desc_en'  => 'Your result for {{biomarker_name}} is {{value}} {{unit}}, which is {{status}} the range ({{ref_min}} - {{ref_max}} {{unit}}).'
        ],

        // Caso especial para Orina
        'renal_urine_result_abnormal' => [
            'module' => 'renal_function',
            'rol'    => 'user',
            'title'  => 'Resultado de orina anormal',
            'desc'   => 'Se detectó un resultado anormal (A) en la prueba de orina (Albúmina: {{albumin}}, Creatinina: {{creatinine}}). Te recomendamos contactar a un especialista.',
            'title_en' => 'Abnormal urine result',
            'desc_en'  => 'An abnormal result (A) was detected in the urine test (Albumin: {{albumin}}, Creatinine: {{creatinine}}). We recommend contacting a specialist.'
        ],

        /* =========================
         * SEGUNDAS OPINIONES
         * ========================= */
        'second_opinion_request_received' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist',
            'title'  => 'Nueva solicitud de segunda opinión',
            'desc'   => 'Has recibido una nueva solicitud de {{user_name}} para {{request_type}}. Revisa tu panel para aceptarla o rechazarla.',
            'title_en' => 'New second opinion request',
            'desc_en'  => 'You have received a new request from {{user_name}} for {{request_type}}. Check your dashboard to accept or reject it.'
        ],
        'second_opinion_status_changed' => [
            'module' => 'second_opinion',
            'rol'    => 'user', 
            'title'  => 'Tu solicitud ha sido {{new_status}}',
            'desc'   => 'Tu solicitud de segunda opinión con {{specialist_name}} ha sido actualizada a: {{new_status}}.',
            'title_en' => 'Your request has been {{new_status}}',
            'desc_en'  => 'Your second opinion request with {{specialist_name}} has been updated to: {{new_status}}.'
        ],
        'second_opinion_cancelled_by_user' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist', 
            'title'  => 'Solicitud cancelada',
            'desc'   => 'El paciente {{user_name}} ha cancelado la solicitud de {{request_type}} que tenías pendiente.',
            'title_en' => 'Request cancelled',
            'desc_en'  => 'Patient {{user_name}} has cancelled their pending {{request_type}} request.'
        ],
        'second_opinion_pending_reminder' => [
            'module' => 'second_opinion',
            'rol'    => 'specialist', 
            'title'  => 'Recordatorio: Solicitud pendiente',
            'desc'   => 'Tienes una solicitud de segunda opinión de {{user_name}} ({{request_type}}) que aún espera tu revisión.',
            'title_en' => 'Reminder: Pending request',
            'desc_en'  => 'You have a second opinion request from {{user_name}} ({{request_type}}) that is still pending your review.'
        ],

         /* =========================
         * VIDEOLLAMADAS
         * ========================= */
        'video_call_scheduled' => [
            'module' => 'video_call',
            'rol'    => 'user', 
            'title'  => 'Videollamada programada',
            'desc'   => 'Tu videollamada con {{participant_name}} está programada para el {{schedule_date}} a las {{schedule_time}} ({{timezone}}).',
            'title_en' => 'Video call scheduled',
            'desc_en'  => 'Your video call with {{participant_name}} is scheduled for {{schedule_date}} at {{schedule_time}} ({{timezone}}).'
        ],
        'video_call_cancelled' => [
            'module' => 'video_call',
            'rol'    => 'user', 
            'title'  => 'Videollamada cancelada',
            'desc'   => 'La videollamada programada con {{participant_name}} para el {{schedule_date}} ha sido cancelada.',
            'title_en' => 'Video call cancelled',
            'desc_en'  => 'The video call scheduled with {{participant_name}} for {{schedule_date}} has been cancelled.'
        ],
        'second_opinion_meet_link_updated' => [
            'module' => 'second_opinion',
            'rol'    => 'user',
            'title'  => '¡Link de reunión listo!',
            'desc'   => 'El especialista {{specialist_name}} ha proporcionado o actualizado el link de ingreso para tu cita.',
            'title_en' => 'Meeting Link Ready!',
            'desc_en'  => '{{specialist_name}} has provided or updated the entry link for your appointment.'
        ],

        /* =========================
         * COMENTARIOS
         * ========================= */
        'new_comment_on_record' => [
            'module' => 'comments',
            'rol'    => 'user',
            'title'  => 'Nuevo comentario de especialista',
            'desc'   => 'El especialista {{specialist_name}} ha dejado un comentario en tu registro de {{biomarker_name}} del {{record_date}}.',
            'title_en' => 'New comment from specialist',
            'desc_en'  => 'Specialist {{specialist_name}} left a comment on your {{biomarker_name}} record from {{record_date}}.'
        ],

        /* =========================
         * REVIEWS
         * ========================= */
        'new_specialist_review' => [
            'module' => 'reviews',
            'rol'    => 'specialist', 
            'title' => 'Has recibido una nueva valoración',
            'desc'  => 'El paciente {{user_name}} te ha dejado una valoración de {{rating}} estrellas.',
            'title_en' => 'You received a new review',
            'desc_en'  => 'Patient {{user_name}} left you a {{rating}}-star review.'
        ],

        /* =========================
         * GENERAL / SISTEMA
         * ========================= */
        'welcome_user' => [
            'module' => 'system',
            'rol'    => 'user',
            'title'  => '¡Bienvenido a Vitakee, {{user_name}}!',
            'desc'   => 'Tu cuenta ha sido creada exitosamente. Explora la plataforma y comienza a registrar tus biomarcadores.',
            'title_en' => 'Welcome to Vitakee, {{user_name}}!',
            'desc_en'  => 'Your account has been created successfully. Explore the platform and start logging your biomarkers.'
        ],
        'password_reset_success' => [
            'module' => 'system',
            'rol'    => 'user',
            'title'  => 'Contraseña actualizada',
            'desc'   => 'Tu contraseña ha sido actualizada exitosamente el {{date}}.',
            'title_en' => 'Password updated',
            'desc_en'  => 'Your password was successfully updated on {{date}}.'
        ],

        // === TEMPLATES PARA ADMINISTRADORES ===
        'new_specialist_verification_request' => [
            'module' => 'verification_requests',
            'rol'    => 'administrator',
            'title'  => 'Nueva solicitud de verificación',
            'desc'   => 'El especialista {{specialist_name}} ha solicitado la verificación de su perfil.',
            'title_en' => 'New verification request',
            'desc_en'  => 'The specialist {{specialist_name}} has requested profile verification.'
        ],
        'new_user_registered' => [
            'module' => 'users',
            'rol'    => 'administrator',
            'title'  => 'Nuevo usuario registrado',
            'desc'   => 'Se ha registrado un nuevo usuario: {{user_name}}.',
            'title_en' => 'New user registered',
            'desc_en'  => 'A new user has registered: {{user_name}}.'
        ],

        /* =========================
         * VERIFICATION REQUESTS
         * ========================= */
        'verification_request_created' => [
            'module' => 'verification',
            'rol'    => 'administrator',
            'title'  => 'Nueva Solicitud de Verificación',
            'desc'   => 'Un nuevo especialista ha solicitado verificación.',
            'title_en' => 'New Verification Request',
            'desc_en'  => 'A new specialist has requested verification.'
        ],
        'verification_approved' => [
            'module' => 'verification',
            'rol'    => 'specialist',
            'title'  => 'Verificación Aprobada',
            'desc'   => 'Tu solicitud de verificación ha sido aprobada.',
            'title_en' => 'Verification Approved',
            'desc_en'  => 'Your verification request has been approved.'
        ],
        'verification_rejected' => [
            'module' => 'verification',
            'rol'    => 'specialist',
            'title'  => 'Verificación Rechazada',
            'desc'   => 'Tu solicitud de verificación ha sido rechazada. Por favor revisa los requisitos.',
            'title_en' => 'Verification Rejected',
            'desc_en'  => 'Your verification request has been rejected. Please review prerequisites.'
        ],
    ];

    public static function render($key, array $params = [], $overrideLang = null)
    {
        if (!isset(self::$templates[$key])) {
            return ['title' => $key, 'desc' => 'Plantilla no definida.'];
        }
        $tpl = self::$templates[$key];

        $idioma = $overrideLang ? strtoupper($overrideLang) : strtoupper($_SESSION['idioma'] ?? 'ES');
        $title_key = 'title_' . strtolower($idioma);
        $desc_key = 'desc_' . strtolower($idioma);

        $title = $tpl[$title_key] ?? $tpl['title'];
        $desc  = $tpl[$desc_key]  ?? $tpl['desc'];

        return [
            'title' => self::replacePlaceholders($title, $params),
            'desc'  => self::replacePlaceholders($desc,  $params),
        ];
    }

    public static function getMeta($key)
    {
        return self::$templates[$key] ?? null;
    }

    public static function allKeys()
    {
        return array_keys(self::$templates);
    }

    public static function buildForInsert(array $in)
    {
        $now = date('Y-m-d H:i:s');
        $meta = self::getMeta($in['template_key'] ?? '');
        
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
            'created_by'       => $actorId, 
            'updated_at'       => null,
            'updated_by'       => null,
            'deleted_at'       => null,
            'deleted_by'       => null,
        ];
    }

    private static function replacePlaceholders($text, array $params)
    {
        if ($text === '' || empty($params)) {
            return $text;
        }
        foreach ($params as $k => $v) {
            $value = is_scalar($v) ? (string)$v : (is_null($v) ? '' : '(array)');
            $text = str_replace('{{'.$k.'}}', $value, $text);
        }
        return $text;
    }
}
