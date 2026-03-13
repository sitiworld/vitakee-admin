<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/PushSubscriptionModel.php';
require_once __DIR__ . '/../models/NotificationPreferenceModel.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * NotificationPushService
 */
class NotificationPushService
{
    public static function dispatchIfEnabled(
        string $userId,
        string $userType,
        string $title,
        string $body,
        string $url = ''
    ): bool {
        try {
            $prefModel = new NotificationPreferenceModel();
            if (!$prefModel->isPushEnabled($userId, $userType)) {
                return false;
            }

            $subModel = new PushSubscriptionModel();
            $subscriptions = $subModel->getByUserId($userId);

            if (empty($subscriptions)) {
                return false;
            }

            $baseUrl = defined('BASE_URL') ? BASE_URL : ($_ENV['APP_URL'] ?? '');
            
            // --- FIX CROSS-REPO URLS (LOCAL Y PRODUCCIÓN) ---
            if ($userType === 'administrator') {
                if (!empty($_ENV['CROSS_REPO_ADMIN_URL'])) {
                    $baseUrl = rtrim($_ENV['CROSS_REPO_ADMIN_URL'], '/');
                } elseif (strpos($baseUrl, 'vitakee-users') !== false) {
                    $baseUrl = str_replace('vitakee-users', 'vitakee-admin', $baseUrl);
                }
            } elseif (in_array($userType, ['specialist', 'user'])) {
                if (!empty($_ENV['CROSS_REPO_USERS_URL'])) {
                    $baseUrl = rtrim($_ENV['CROSS_REPO_USERS_URL'], '/');
                } elseif (strpos($baseUrl, 'vitakee-admin') !== false) {
                    $baseUrl = str_replace('vitakee-admin', 'vitakee-users', $baseUrl);
                }
            }

            $baseUrl = rtrim($baseUrl, '/');

            // Asegurar que la ruta sea absoluta para el Service Worker
            if (!empty($url) && !preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $url = $baseUrl . '/' . ltrim($url, '/');
            }

            $payload = json_encode([
                'title' => $title,
                'body'  => $body,
                'url'   => $url,
                'icon'  => $baseUrl . '/public/assets/images/logo-sm2.svg',
                'badge' => $baseUrl . '/public/assets/images/logo-sm2.svg',
            ]);

            return self::sendToSubscriptions($subscriptions, $payload, $subModel);

        } catch (\Throwable $e) {
            error_log("[NotificationPushService] Error: " . $e->getMessage());
            return false;
        }
    }

    private static function sendToSubscriptions(
        array $subscriptions,
        string $payload,
        PushSubscriptionModel $subModel
    ): bool {
        $vapidPublicKey  = $_ENV['VAPID_PUBLIC_KEY']  ?? '';
        $vapidPrivateKey = $_ENV['VAPID_PRIVATE_KEY'] ?? '';
        $vapidSubject    = $_ENV['APP_URL']            ?? 'https://app.vitakee.com';

        if (empty($vapidPublicKey) || empty($vapidPrivateKey)) {
            error_log("[NotificationPushService] VAPID keys missing in .env");
            return false;
        }

        $auth = [
            'VAPID' => [
                'subject'    => $vapidSubject,
                'publicKey'  => $vapidPublicKey,
                'privateKey' => $vapidPrivateKey,
            ],
        ];

        $webPush = new WebPush($auth);

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub['endpoint'],
                'keys'     => [
                    'p256dh' => $sub['p256dh'],
                    'auth'   => $sub['auth'],
                ],
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        $sent = 0;
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();

            if ($report->isSuccess()) {
                $sent++;
            } else {
                $statusCode = $report->getResponse()?->getStatusCode() ?? 0;
                
                if (in_array($statusCode, [404, 410])) {
                    $subModel->deleteByEndpoint($endpoint);
                }
            }
        }

        return $sent > 0;
    }
}
