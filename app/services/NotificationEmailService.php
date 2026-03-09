<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers/mailHelper.php';
require_once __DIR__ . '/../models/NotificationPreferenceModel.php';
require_once __DIR__ . '/../helpers/NotificationTemplateHelper.php';
require_once __DIR__ . '/../models/NotificationModel.php';

/**
 * NotificationEmailService
 *
 * Servicio en segundo plano para notificaciones por correo de Administradores.
 */
class NotificationEmailService
{
    public static function dispatchIfEnabled(
        string $userId,
        string $userType,
        string $userEmail,
        ?array $notifications = null,
        string $userName = '',
        string $idioma = 'EN'
    ): bool {
        try {
            $prefModel = new NotificationPreferenceModel();
            
            $isEnabled = $prefModel->isEmailEnabled($userId, $userType);
            
            if (!$isEnabled) {
                return false; 
            }

            if ($notifications === null) {
                $notifications = self::fetchLastFive($userId, $idioma);
            }

            if (empty($notifications)) {
                return false;
            }

            $subject = self::buildSubject($idioma);
            $body    = self::buildEmailBody($notifications, $userEmail, $userName, $idioma);

            $mailer = new MailHelper();
            $result = $mailer->sendMail($userEmail, $subject, $body);
            
            return $result;

        } catch (\Throwable $e) {
            error_log("[NotificationEmailService] Error: " . $e->getMessage());
            return false;
        }
    }

    private static function fetchLastFive(string $userId, string $idioma): array
    {
        try {
            $previousIdioma = $_SESSION['idioma'] ?? null;
            $_SESSION['idioma'] = $idioma;

            $model = new NotificationModel();
            $data = $model->getByUserId($userId, 5, 0);

            if ($previousIdioma !== null) {
                $_SESSION['idioma'] = $previousIdioma;
            } else {
                unset($_SESSION['idioma']);
            }

            if (isset($data['value']) && $data['value'] === false) {
                return [];
            }

            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            error_log("[NotificationEmailService] fetchLastFive error: " . $e->getMessage());
            return [];
        }
    }

    private static function buildSubject(string $idioma): string
    {
        return strtoupper($idioma) === 'ES'
            ? '🔔 Notificaciones del Sistema — Vitakee Admin'
            : '🔔 System Notifications — Vitakee Admin';
    }

    private static function buildEmailBody(array $notifications, string $userEmail, string $userName = '', string $idioma = 'EN'): string
    {
        $idioma  = strtoupper($idioma);
        $isES    = ($idioma === 'ES');
        $baseUrl = defined('BASE_URL') ? BASE_URL : (($_ENV['APP_URL'] ?? 'https://app.vitakee.com') . '/');

        $greeting       = $isES ? "Hola" : "Hello";
        $subTitle       = $isES
            ? 'Tienes notificaciones pendientes en el panel de administrador:'
            : 'You have pending notifications on the admin dashboard:';
        $ctaText        = $isES ? 'Ir al Panel' : 'Go to Dashboard';
        $ctaUrl         = $baseUrl . 'administrator';
        $footerText     = $isES
            ? 'Recibiste este correo porque tienes habilitadas las notificaciones por email en Vitakee Admin.'
            : 'You received this email because you have email notifications enabled in Vitakee Admin.';
        $poweredBy      = 'Vitakee &copy; ' . date('Y');

        $itemsHtml = '';
        foreach ($notifications as $n) {
            $params     = $n['template_params'] ?? [];
            if(is_string($params)) $params = json_decode($params, true) ?? [];
            
            $tplKey     = $n['template_key']    ?? '';
            $rendered   = NotificationTemplateHelper::render($tplKey, $params, $idioma);
            $title      = htmlspecialchars($rendered['title'] ?? $tplKey);
            $desc       = htmlspecialchars($rendered['desc']  ?? '');
            $dateRaw    = $n['created_at'] ?? '';
            $date       = '';
            if ($dateRaw) {
                try {
                    $dt   = new \DateTime($dateRaw);
                    $date = $isES ? $dt->format('d/m/Y') : $dt->format('m/d/Y');
                } catch (\Exception $e) {
                    $date = $dateRaw;
                }
            }

            $iconColor = '#dbeafe';
            $icon      = '🔔';
            $url       = !empty($n['route']) ? $baseUrl . ltrim($n['route'], '/') : $ctaUrl;

            $itemsHtml .= <<<HTML
            <tr>
              <td style="padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="44" valign="top">
                      <div style="width:36px;height:36px;border-radius:50%;background:{$iconColor};display:flex;align-items:center;justify-content:center;text-align:center;line-height:36px;font-size:18px;">
                        {$icon}
                      </div>
                    </td>
                    <td style="padding-left:12px;">
                      <a href="{$url}" style="font-size:14px;font-weight:600;color:#1a1a2e;text-decoration:none;">{$title}</a>
                      <p style="margin:4px 0 0;font-size:13px;color:#6b7280;">{$desc}</p>
                      <p style="margin:4px 0 0;font-size:11px;color:#9ca3af;">{$date}</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            HTML;
        }

        $greetingFull = $userName ? ", {$userName}!" : '!';

        return <<<HTML
        <!DOCTYPE html>
        <html lang="{$idioma}">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Vitakee Notifications</title>
        </head>
        <body style="margin:0;padding:0;background:#f5f7fa;font-family:'Segoe UI',Arial,sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fa;padding:30px 0;">
            <tr>
              <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
                  <tr>
                    <td style="background:linear-gradient(135deg,#2852af 0%,#1a3a7c 100%);padding:28px 32px;text-align:center;">
                      <img src="{$baseUrl}public/assets/images/logo-index.png" alt="Vitakee" height="50" style="max-width:160px;">
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:28px 32px 8px;text-align:center;">
                      <div style="width:60px;height:60px;border-radius:50%;background:#ebf0ff;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:28px;">🔔</div>
                      <h2 style="margin:0;font-size:22px;color:#1a1a2e;">
                        {$greeting}{$greetingFull}
                      </h2>
                      <p style="margin:8px 0 0;font-size:15px;color:#6b7280;">{$subTitle}</p>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:8px 32px 16px;">
                      <table width="100%" cellpadding="0" cellspacing="0">
                        {$itemsHtml}
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:16px 32px 32px;text-align:center;">
                      <a href="{$ctaUrl}"
                         style="display:inline-block;background:linear-gradient(135deg,#2852af,#1a3a7c);color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:8px;font-size:15px;font-weight:600;letter-spacing:0.3px;">
                        {$ctaText} →
                      </a>
                    </td>
                  </tr>
                  <tr>
                    <td style="background:#f9fafb;border-top:1px solid #e5e7eb;padding:20px 32px;text-align:center;">
                      <p style="margin:0;font-size:12px;color:#9ca3af;line-height:1.6;">{$footerText}</p>
                      <p style="margin:8px 0 0;font-size:11px;color:#d1d5db;">{$poweredBy}</p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </body>
        </html>
        HTML;
    }
}
