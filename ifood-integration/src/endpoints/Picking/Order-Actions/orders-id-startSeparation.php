<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Picking\Order\orders-id-items-post.php

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

// Recebe orderId via query string (?id=...)
$orderId = $_GET['id'] ?? null;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use POST']);
    exit;
}

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'id do pedido não informado']);
    exit;
}

// Recebe o corpo JSON da requisição POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['quantity']) || !isset($input['ean'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Corpo da requisição inválido. Envie {"quantity": ..., "ean": "..."}']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$url = "https://merchant-api.ifood.com.br/picking/v1.0/orders/$orderId/items";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
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

if ($httpCode >= 200 && $httpCode < 300) {
    echo $response ?: json_encode([
        'success' => true,
        'message' => 'Item adicionado ao pedido com sucesso!',
        'orderId' => $orderId
    ]);
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode([
        'error' => 'Não foi possível adicionar o item ao pedido',
        'orderId' => $orderId,
        'detalhe' => $response
    ]);
}