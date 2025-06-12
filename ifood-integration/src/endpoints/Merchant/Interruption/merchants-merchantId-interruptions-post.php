<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Merchant\Interruption\merchants-merchantId-interruptions-post.php

// Carrega as credenciais do arquivo de configuração
$config = require __DIR__ . '/../../../config/config.php';
$clientId = $config['client_id'];
$clientSecret = $config['client_secret'];

// Função para obter o access token
function getAccessToken($clientId, $clientSecret) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'grantType' => 'client_credentials',
            'clientId' => $clientId,
            'clientSecret' => $clientSecret
        ]),
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/x-www-form-urlencoded"
        ]
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    return $data['accessToken'] ?? null;
}

// Recebe o merchantId via query string (?merchantId=...)
$merchantId = $_GET['merchantId'] ?? null;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use POST']);
    exit;
}

if (!$merchantId) {
    http_response_code(400);
    echo json_encode(['error' => 'merchantId não informado']);
    exit;
}

// Recebe o corpo JSON da requisição POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Corpo da requisição inválido ou vazio']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://merchant-api.ifood.com.br/merchant/v1.0/merchants/$merchantId/interruptions",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($input),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "Content-Type: application/json"
    ]
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300 && $response) {
    echo $response;
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode(['error' => 'Não foi possível criar a interrupção', 'merchantId' => $merchantId, 'detalhe' => $response]);
}