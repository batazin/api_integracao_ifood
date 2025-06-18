<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Catalog\OptionGroup\merchants-merchantId-optionGroups-optionGroupId-status-patch.php

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

// Recebe merchantId e optionGroupId via query string (?merchantId=...&optionGroupId=...)
$merchantId = $_GET['merchantId'] ?? null;
$optionGroupId = $_GET['optionGroupId'] ?? null;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use PATCH']);
    exit;
}

if (!$merchantId || !$optionGroupId) {
    http_response_code(400);
    echo json_encode(['error' => 'merchantId e optionGroupId são obrigatórios']);
    exit;
}

// Recebe o corpo JSON da requisição PATCH
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Corpo da requisição inválido. Envie {"status": "AVAILABLE" ou "UNAVAILABLE"}']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$url = "https://merchant-api.ifood.com.br/catalog/v2.0/merchants/$merchantId/optionGroups/$optionGroupId/status";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'PATCH',
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
        'message' => 'Status do OptionGroup atualizado com sucesso!',
        'merchantId' => $merchantId,
        'optionGroupId' => $optionGroupId
    ]);
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode([
        'error' => 'Não foi possível atualizar o status do OptionGroup',
        'merchantId' => $merchantId,
        'optionGroupId' => $optionGroupId,
        'detalhe' => $response
    ]);
}