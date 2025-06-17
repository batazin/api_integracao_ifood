<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\endpoints\Catalog\Category\merchants-merchantId-categories-categoryId-delete.php

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

// Recebe merchantId e categoryId via query string (?merchantId=...&categoryId=...)
$merchantId = $_GET['merchantId'] ?? null;
$categoryId = $_GET['categoryId'] ?? null;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido, use DELETE']);
    exit;
}

if (!$merchantId || !$categoryId) {
    http_response_code(400);
    echo json_encode(['error' => 'merchantId e categoryId são obrigatórios']);
    exit;
}

$accessToken = getAccessToken($clientId, $clientSecret);

$url = "https://merchant-api.ifood.com.br/catalog/v2.0/merchants/$merchantId/categories/$categoryId";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Accept: application/json"
    ]
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode([
        'success' => true,
        'message' => 'Categoria deletada com sucesso!',
        'merchantId' => $merchantId,
        'categoryId' => $categoryId
    ]);
} else {
    http_response_code($httpCode !== 200 ? $httpCode : 500);
    echo json_encode([
        'error' => 'Não foi possível deletar a categoria',
        'merchantId' => $merchantId,
        'categoryId' => $categoryId,
        'detalhe' => $response
    ]);
}