<?php

require_once __DIR__ . '/../../components/Header.php';

// session_start();

// // Verificar si la variable de sesión 'user_id' existe
// if (!isset($_SESSION['user_id'])) {
//     // Redirigir a index.php si no existe
//     header("Location: index");
//     exit();  // Asegúrate de que el script se detenga después de la redirección
// }


$userName = $_SESSION['user_name'] ?? 'Guest'; // Valor por defecto si no está definido
$userSex = $_SESSION['sex'] ?? 'u'; // 'm', 'f', o 'u' (unknown)
$userId = $_SESSION['user_id'] ?? null;


// var_dump($_SESSION);
$headerComponent = new Header($userName, $userSex, $userId);
$headerComponent->render();


?>