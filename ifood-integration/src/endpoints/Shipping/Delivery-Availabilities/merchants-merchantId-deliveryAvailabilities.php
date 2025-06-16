<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Shipping\Delivery-Availabilities\merchants-merchantId-deliveryAvailabilities.php

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use GET']);
    exit;
}

if (!$merchantId) {
    http_response_code(400);
    echo json_encode(['error' => 'merchantId não informado']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$channel = $_GET['channel'] ?? 'IFOOD';
$deliveryType = $_GET['deliveryType'] ?? 'DELIVERY';

$url = "https://merchant-api.ifood.com.br/shipping/v1.0/merchants/$merchantId/deliveryAvailabilities?channel=$channel&deliveryType=$deliveryType";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Accept: application/json"
    ]
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

file_put_contents('debug_ifood_shipping.txt', print_r([
    'url' => $url,
    'httpCode' => $httpCode,
    'response' => $response
], true), FILE_APPEND);

if ($httpCode === 200 && $response) {
    echo $response;
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode([
        'error' => 'Não foi possível obter as disponibilidades de entrega',
        'merchantId' => $merchantId,
        'detalhe' => $response
    ]);
}