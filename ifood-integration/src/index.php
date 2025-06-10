<?php
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
?>