<?php
// filepath: c:\WF\api_integracao_ifood\ifood-integration\src\single_ifood_auth.php

// -- Configuração --
// Em um cenário real, carregue isso de um arquivo de configuração seguro ou variáveis de ambiente.
$clientId = 'd7a787be-7f33-4574-99ff-6417873f3fe8'; // SEU CLIENT ID REAL
$clientSecret = 'j1jtnsqzc0w5un8tn5sfhmgll4a0e3zp6e7nardeso1oaeg50ox4ty0mb746pdye14uq83jp9ql3s6sqlhgf7mxqdacxl2kckil'; // SEU CLIENT SECRET REAL
$tokenUrl = 'https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token';
$userCodeUrl = 'https://merchant-api.ifood.com.br/authentication/v1.0/oauth/userCode';

// -- Classe AuthService --
// Removi o namespace para simplicidade neste arquivo único, mas em um projeto, use namespaces.
class AuthService
{
    private $clientId;
    private $clientSecret;
    private $tokenUrl;
    private $userCodeUrl;

    public function __construct($clientId, $clientSecret, $tokenUrl, $userCodeUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenUrl = $tokenUrl;
        $this->userCodeUrl = $userCodeUrl;
    }

    public function getAccessToken(): array
    {
        error_log("AuthService::getAccessToken() - Solicitando token via client_credentials.");
        $ch = curl_init();
        if ($ch === false) {
            throw new \Exception("Falha ao inicializar a sessão cURL.");
        }

        $postData = [
            'grantType' => 'client_credentials',
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret
        ];
        $queryString = http_build_query($postData);

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->tokenUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $queryString,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded']
        ]);

        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlErrno) {
            throw new \Exception('Erro na comunicação cURL: ' . $curlError . '. Resposta: ' . $response);
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Falha ao decodificar a resposta JSON. Resposta bruta: ' . $response);
        }

        if (isset($data['error'])) {
            $apiErrorMessage = $data['error_description'] ?? $data['message'] ?? json_encode($data['error']);
            throw new \Exception('Erro da API iFood: ' . $apiErrorMessage . ". Resposta bruta: " . $response);
        }

        if (isset($data['accessToken'])) {
            return $data;
        }
        throw new \Exception('Não foi possível obter o token de acesso. Resposta bruta: ' . $response);
    }

    public function requestUserCode(array $additionalParams = []): array
    {
        error_log("AuthService::requestUserCode() - Solicitando userCode.");
        $ch = curl_init();
        if ($ch === false) {
            throw new \Exception("Falha ao inicializar a sessão cURL para userCode.");
        }

        $postData = array_merge(['clientId' => $this->clientId], $additionalParams);
        $queryString = http_build_query($postData);

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->userCodeUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $queryString,
            CURLOPT_ENCODING => "",
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded']
        ]);
        
        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlErrno) {
            throw new \Exception('Erro na comunicação cURL ao solicitar userCode: ' . $curlError . '. Resposta: ' . $response);
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Falha ao decodificar a resposta JSON do userCode. Resposta bruta: ' . $response);
        }

        if (isset($data['error'])) {
            $apiErrorMessage = $data['error_description'] ?? $data['message'] ?? json_encode($data['error']);
            throw new \Exception('Erro da API iFood ao solicitar userCode: ' . $apiErrorMessage . ". Resposta bruta: " . $response);
        }

        if (isset($data['userCode']) && isset($data['verificationUrl'])) {
            return $data;
        }
        throw new \Exception('Resposta da API para userCode não contém os campos esperados. Resposta bruta: ' . $response);
    }
}

// -- Lógica do Endpoint --
header('Content-Type: application/json');
// ini_set('log_errors', 1);
// ini_set('error_log', '/caminho/para/seu/php-error.log'); 

$action = $_GET['action'] ?? null; 

try {
    $authService = new AuthService($clientId, $clientSecret, $tokenUrl, $userCodeUrl);
    $responseData = null;
    $outputJson = null; // Para armazenar o JSON antes de dar echo

    if ($action === 'get_token') {
        $responseData = $authService->getAccessToken();
        // Log para depuração
        error_log("get_token - ResponseData antes do json_encode: " . print_r($responseData, true));
        $outputJson = json_encode(['success' => true, 'tokenData' => $responseData]);
    } elseif ($action === 'get_user_code') {
        $additionalParamsForUserCode = []; 
        $responseData = $authService->requestUserCode($additionalParamsForUserCode);
        // Log para depuração
        error_log("get_user_code - ResponseData antes do json_encode: " . print_r($responseData, true));
        // Para get_user_code, retornamos DIRETAMENTE a resposta da API do iFood
        // que já está em $responseData como um array.
        $outputJson = json_encode($responseData); 
    } else {
        http_response_code(400); 
        $outputJson = json_encode(['success' => false, 'error' => 'Ação inválida ou não especificada. Use ?action=get_token ou ?action=get_user_code']);
    }

    // Verificar se json_encode falhou
    if ($outputJson === false) {
        $jsonError = json_last_error_msg();
        error_log("Erro ao codificar JSON final (Ação: " . $action . "): " . $jsonError . " - Dados brutos: " . print_r($responseData, true));
        // Não sobrescreva o http_response_code se já for 400
        if (http_response_code() !== 400) {
            http_response_code(500); // Erro interno do servidor se json_encode falhou
        }
        // Envie um erro JSON claro se o json_encode principal falhou
        echo json_encode(['success' => false, 'error' => 'Falha ao gerar resposta JSON: ' . $jsonError]);
    } else {
        echo $outputJson;
    }

} catch (\Exception $e) {
    // Não sobrescreva o http_response_code se já for 400 (do 'else' da ação)
    if (http_response_code() !== 400) {
         http_response_code(500); 
    }
    error_log("Erro no script (Ação: " . $action . "): " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    // Tenta codificar a mensagem de erro, mas com cuidado
    $errorMessage = 'Ocorreu uma exceção: ' . $e->getMessage();
    $errorJson = json_encode(['success' => false, 'error' => $errorMessage]);
    if ($errorJson === false) {
        // Se até a mensagem de erro falhar ao codificar, envie texto plano
        error_log("Falha ao codificar JSON da mensagem de erro: " . json_last_error_msg());
        echo '{"success":false, "error":"Erro interno crítico ao processar a exceção e gerar JSON."}';
    } else {
        echo $errorJson;
    }
}

?>