<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Merchant\Merchant\merchants-merchantId.php

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

if (!$merchantId) {
    http_response_code(400);
    echo json_encode(['error' => 'merchantId não informado']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://merchant-api.ifood.com.br/merchant/v1.0/merchants/$merchantId",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Accept: application/json"
    ]
]);
$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;