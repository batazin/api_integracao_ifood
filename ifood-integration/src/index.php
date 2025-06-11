<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// This is the entry point of the application.
// Initialize the application and handle incoming requests.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/services/IfoodService.php';
require_once __DIR__ . '/services/AuthService.php';
require_once __DIR__ . '/controllers/OrderController.php';
require_once __DIR__ . '/controllers/MenuController.php';

use App\Services\AuthService;
use App\Services\IfoodService;
use App\Controllers\OrderController;
use App\Controllers\MenuController;

// Initialize services and controllers
$authService = new AuthService($clientId, $clientSecret, $tokenUrl);
$ifoodService = new IfoodService($authService);
$orderController = new OrderController($ifoodService);
$menuController = new MenuController($ifoodService);

// Handle incoming requests (this is a simple example)
$requestUri = $_SERVER['REQUEST_URI'];

switch ($requestUri) {
    case '/orders':
        $orderController->handleRequest();
        break;
    case '/menu':
        $menuController->handleRequest();
        break;
    default:
        http_response_code(404);
        echo 'Not Found';
        break;
}
echo "</br>Welcome to the iFood Integration API!<br>";

try {
    $tokenData = $authService->getAccessToken(); // Chame o método que usa client_credentials
    echo "<pre>Access Token Data:<br>" . htmlspecialchars(print_r($tokenData, true)) . "</pre>";
} catch (Exception $e) {
    echo "<br><pre>Erro ao obter access token: <br>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
?>