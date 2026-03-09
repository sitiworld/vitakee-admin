<?php
require_once __DIR__ . '/app/config/config.php';
require_once __DIR__ . '/app/models/Database.php';
require_once __DIR__ . '/app/models/NotificationModel.php';

session_start();

// Verifica que el usuario haya iniciado sesión como Administrador
if (!isset($_SESSION['roles_user']) || strtolower($_SESSION['roles_user']) !== 'administrator') {
    die("Error: Debes iniciar sesión como administrador en el panel para probar las notificaciones.");
}

// Obtenemos el ID del administrador desde la sesión
// En vitakee-admin, $_SESSION['administrator_id'] o $_SESSION['user_id'] contiene el UUID.
$adminId = $_SESSION['administrator_id'] ?? $_SESSION['user_id'] ?? null;

if (!$adminId) {
    die("Error: No se pudo encontrar tu ID de administrador en la sesión.");
}

echo "<h3>Enviando Notificación de Prueba...</h3>";

$notificationModel = new NotificationModel();

$data = [
    'template_key' => 'new_specialist_verification_request',
    'template_params' => [
        'specialist_name' => 'Dr. Test de Prueba'
    ],
    'route' => '/administrator', // Al hacer click en la app, nos llevará aquí
    'rol' => 'administrator',
    'module' => 'verification_requests',
    'user_id' => $adminId
];

try {
    $result = $notificationModel->create($data);
    
    if ($result['status'] === 'success') {
        echo "<p style='color:green;'>¡Éxito! Notificación creada.</p>";
        echo "<p>Deberías recibir lo siguiente (si tus preferencias están habilitadas):</p>";
        echo "<ul>";
        echo "<li><b>Web Push:</b> Una notificación emergente en tu computadora/navegador.</li>";
        echo "<li><b>Email:</b> Un correo en tu bandeja de entrada o en Mailtrap informando sobre el especialista de prueba.</li>";
        echo "<li><b>In-App:</b> El contador de la campanita roja en el menú superior (si no recibiste el Push, el sistema hará polling en unos segundos).</li>";
        echo "</ul>";
        echo "<p><a href='/administrator'>Volver al Dashboard</a></p>";
    } else {
        echo "<p style='color:red;'>Error al crear: " . htmlspecialchars($result['message']) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>Excepción capturada: " . $e->getMessage() . "</p>";
}
