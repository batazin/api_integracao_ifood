<?php

namespace App\Services;

class AuthService
{
    private $clientId;
    private $clientSecret;
    private $tokenUrl;

    public function __construct($clientId, $clientSecret, $tokenUrl)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenUrl = $tokenUrl;
    }
    public function getAccessToken()
    {
        error_log("AuthService::getAccessToken() - Method started.");

        // 1. Descomente a inicialização do cURL
        error_log("AuthService::getAccessToken() - Initializing cURL.");
        $ch = curl_init();
        if ($ch === false) {
            error_log("AuthService::getAccessToken() - curl_init() failed!");
            throw new \Exception("Failed to initialize cURL session.");
        }
        error_log("AuthService::getAccessToken() - cURL initialized.");
        // Mantenha o resto comentado e retorne o array fixo por enquanto
        return ['test' => 'curl_init_ok'];

        // 2. Descomente as opções cURL uma por uma ou em pequenos grupos
        error_log("AuthService::getAccessToken() - Setting cURL options.");
        curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json', // Verifique se este header é necessário/correto para client_credentials
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'client_credentials', // Certifique-se que isso está aqui para o fluxo client_credentials
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]));
        error_log("AuthService::getAccessToken() - cURL options set.");

        // 3. Descomente a execução do cURL
        error_log("AuthService::getAccessToken() - Executing cURL.");
        $response = curl_exec($ch);
        error_log("AuthService::getAccessToken() - cURL executed. Raw response: " . $response);

        // 4. Descomente o tratamento de erro cURL
        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            error_log("AuthService::getAccessToken() - cURL error: " . $curlError);
            curl_close($ch);
            throw new \Exception('cURL Error: ' . $curlError);
        }
        error_log("AuthService::getAccessToken() - No cURL errors.");

        // 5. Descomente o fechamento do cURL
        curl_close($ch);
        error_log("AuthService::getAccessToken() - cURL closed.");

        // 6. Descomente o json_decode e seu tratamento de erro
        error_log("AuthService::getAccessToken() - Decoding JSON response.");
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("AuthService::getAccessToken() - JSON decode error: " . json_last_error_msg() . ". Response was: " . $response);
            throw new \Exception('Failed to decode JSON response: ' . $response);
        }
        error_log("AuthService::getAccessToken() - JSON decoded successfully.");

        // 7. Descomente o tratamento de erro da API iFood
        if (isset($data['error'])) {
            $errorDetails = $data['error_description'] ?? $data['error'] ?? 'Unknown API error';
            $errorMessage = 'API Error: ' . (is_array($errorDetails) ? json_encode($errorDetails) : (string)$errorDetails);
            error_log("AuthService::getAccessToken() - API error detected: " . $errorMessage);
            throw new \Exception($errorMessage);
        }
        error_log("AuthService::getAccessToken() - No API errors in response.");

        // 8. Descomente a verificação do access_token e o retorno
        if (isset($data['access_token'])) {
            error_log("AuthService::getAccessToken() - Access token found. Returning data.");
            return $data;
        }

        error_log("AuthService::getAccessToken() - Access token NOT found in response. Response: " . $response);
        throw new \Exception('Unable to obtain access token: ' . $response);
    }

    public function getUserCode()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'clientId' => $this->clientId
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['userCode'])) {
            return $data['userCode'];
        }

        throw new \Exception('Unable to obtain user code: ' . $response);
    }
}