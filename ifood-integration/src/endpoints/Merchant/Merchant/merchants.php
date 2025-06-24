<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Merchant\Merchant\merchants.php

// Carrega as credenciais do arquivo de configuração
$config = require __DIR__ . '/../../../config/config.php';

$cnpj = $_GET['cnpj'] ?? null;

if (!$cnpj || !isset($config['cnpjs'][$cnpj])) {
    http_response_code(400);
    echo json_encode(['error' => 'CNPJ não informado ou não cadastrado']);
    exit;
}

$clientId = $config['cnpjs'][$cnpj]['client_id'];
$clientSecret = $config['cnpjs'][$cnpj]['client_secret'];
$clientId = $config['cnpjs'][$cnpj]['client_id'];
$clientSecret = $config['cnpjs'][$cnpj]['client_secret'];

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

$accessToken = getAccessToken($clientId, $clientSecret);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://merchant-api.ifood.com.br/merchant/v1.0/merchants',
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