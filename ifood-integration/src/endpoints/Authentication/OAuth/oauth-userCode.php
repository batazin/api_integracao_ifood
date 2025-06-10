<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../services/AuthService.php';

// Ajuste o namespace se necessário
use App\Services\AuthService;

header('Content-Type: application/json');

try {
    // Instancie o AuthService com as variáveis do config.php
    $authService = new AuthService($clientId, $clientSecret, $tokenUrl);

    // Obtenha o user code
    $userCode = $authService->getUserCode();

    echo json_encode([
        'success' => true,
        'userCode' => $userCode
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}