<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Shipping\Cancellation\orders-orderId-cancel.php

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

// Recebe o orderId via query string (?orderId=...)
$orderId = $_GET['orderId'] ?? null;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use POST']);
    exit;
}

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'orderId não informado']);
    exit;
}

// Recebe o corpo JSON da requisição POST (motivo do cancelamento)
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['reasonId'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Corpo da requisição inválido. Envie {"reasonId": "MOTIVO_ID"}']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$url = "https://merchant-api.ifood.com.br/shipping/v1.0/orders/$orderId/cancel";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        "reasonId" => $input['reasonId'],
        "comments" => $input['comments'] ?? ""
    ]),
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "Content-Type: application/json"
    ]
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents('debug_cancel_ifood.txt', print_r([
    'url' => $url,
    'httpCode' => $httpCode,
    'response' => $response,
    'curlError' => $curlError,
    'input' => $input,
    'accessToken' => $accessToken
], true), FILE_APPEND);

if ($httpCode >= 200 && $httpCode < 300) {
    echo $response ?: json_encode([
        'success' => true,
        'message' => 'Pedido cancelado com sucesso!',
        'orderId' => $orderId
    ]);
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode([
        'error' => 'Não foi possível cancelar o pedido',
        'orderId' => $orderId,
        'detalhe' => $response
    ]);
}