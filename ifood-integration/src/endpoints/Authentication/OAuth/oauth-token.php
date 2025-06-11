<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Authentication\OAuth\oauth-userCode.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para capturar erros fatais (mantenha por segurança)
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR, E_PARSE])) {
        error_log("oauth-userCode.php: FATAL ERROR DETECTED BY SHUTDOWN FUNCTION: " . print_r($error, true));
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        echo json_encode(['success' => false, 'fatal_error' => ['type' => $error['type'], 'message' => $error['message'], 'file' => $error['file'], 'line' => $error['line']]]);
    }
});

error_log("oauth-userCode.php: Script started.");
header('Content-Type: application/json');

require_once __DIR__ . '/../../../services/AuthService.php';
use App\Services\AuthService; // Certifique-se que o namespace está correto

try {
    error_log("oauth-userCode.php: Inside try block, before AuthService instantiation.");
    $authService = new AuthService(
        'd7a787be-7f33-4574-99ff-6417873f3fe8',
        'j1jtnsqzc0w5un8tn5sfhmgll4a0e3zp6e7nardeso1oaeg50ox4ty0mb746pdye14uq83jp9ql3s6sqlhgf7mxqdacxl2kckil',
        'https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token'
    );
    error_log("oauth-userCode.php: AuthService instantiated. Calling getAccessToken...");
    $tokenData = $authService->getAccessToken(); // Esta é a chamada importante
    error_log("oauth-userCode.php: getAccessToken returned. Data: " . print_r($tokenData, true)); // LOG O QUE FOI RETORNADO

    // Verifique se $tokenData não está vazio antes de tentar codificar
    if (empty($tokenData)) {
        error_log("oauth-userCode.php: WARNING - getAccessToken returned empty data. Sending empty success response.");
        // Decida o que fazer aqui - talvez um erro?
        // Por agora, vamos manter o fluxo para ver se o json_encode funciona
    }

    echo json_encode([
        'success' => true,
        'tokenData' => $tokenData // Aqui está o echo principal
    ]);
    error_log("oauth-userCode.php: Success JSON sent.");

} catch (Exception $e) {
    error_log("oauth-userCode.php: EXCEPTION CAUGHT: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    if (!headers_sent()) { // Boa prática, mas Content-Type já foi setado
        http_response_code(500);
    }
    echo json_encode(['success' => false, 'error' => 'An exception occurred: ' . $e->getMessage()]);
    error_log("oauth-userCode.php: Error JSON sent due to exception.");
}
error_log("oauth-userCode.php: Script finished.");
