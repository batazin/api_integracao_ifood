<?php

namespace App\Services; // Certifique-se que este é o seu namespace correto

class AuthService
{
    private $clientId;
    private $clientSecret;
    private $tokenUrl;
    // Adicione outras URLs base ou de endpoints se necessário
    // private $userCodeUrl; 

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        // Defina a URL do token aqui, idealmente vinda de uma configuração
        $this->tokenUrl = 'https://merchant-api.ifood.com.br/authentication/v1.0/oauth/token';
        // $this->userCodeUrl = 'URL_PARA_OBTER_USER_CODE'; // Se você tiver um fluxo de user_code
    }

    /**
     * Obtém um token de acesso usando o fluxo client_credentials.
     *
     * @return array Os dados do token (incluindo accessToken, expiresIn, etc.)
     * @throws \Exception Se ocorrer um erro durante a obtenção do token.
     */
    public function getAccessToken(): array
    {
        error_log("AuthService::getAccessToken() - Solicitando token via client_credentials.");

        $ch = curl_init();
        if ($ch === false) {
            error_log("AuthService::getAccessToken() - Falha ao inicializar cURL (curl_init).");
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
            CURLOPT_ENCODING => "", // Para decodificar Gzip automaticamente
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        error_log("AuthService::getAccessToken() - Executando cURL para: " . $this->tokenUrl);
        $response = curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch); // Fechar o handle cURL o mais rápido possível

        if ($curlErrno) {
            error_log("AuthService::getAccessToken() - Erro cURL (#" . $curlErrno . "): " . $curlError . ". Resposta: " . $response);
            throw new \Exception('Erro na comunicação cURL: ' . $curlError);
        }

        error_log("AuthService::getAccessToken() - Resposta bruta da API: " . $response);
        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("AuthService::getAccessToken() - Erro ao decodificar JSON: " . json_last_error_msg() . ". Resposta: " . $response);
            throw new \Exception('Falha ao decodificar a resposta JSON. Resposta bruta: ' . $response);
        }

        if (isset($data['error'])) {
            $apiErrorMessage = $data['error_description'] ?? $data['message'] ?? (is_array($data['error']) ? json_encode($data['error']) : (string)$data['error']);
            error_log("AuthService::getAccessToken() - Erro da API iFood: " . $apiErrorMessage . ". Resposta completa: " . $response);
            throw new \Exception('Erro da API iFood: ' . $apiErrorMessage . ". Resposta bruta: " . $response);
        }

        if (isset($data['accessToken'])) {
            error_log("AuthService::getAccessToken() - Token de acesso obtido com sucesso.");
            return $data;
        }

        error_log("AuthService::getAccessToken() - 'accessToken' não encontrado na resposta. Resposta: " . $response);
        throw new \Exception('Não foi possível obter o token de acesso. Resposta bruta: ' . $response);
    }

    /**
     * Exemplo de estrutura para um método que lida com o fluxo de user_code.
     * A implementação real dependerá dos detalhes específicos da API do iFood para este fluxo.
     */
    public function getUserCode(): array
    {
        error_log("AuthService::getUserCode() - Iniciando fluxo para obter user_code.");
        // Esta é uma implementação de espaço reservado.
        // Você precisaria:
        // 1. Definir a URL correta para solicitar o user_code.
        // 2. Montar os dados corretos para a requisição POST (ex: clientId, scopes).
        // 3. Fazer a chamada cURL.
        // 4. Tratar a resposta e os erros de forma similar ao getAccessToken.
        // 5. Retornar os dados como userCode, verificationUrl, etc.

        // Exemplo:
        // $userCodeUrl = $this->userCodeUrl; // Definido no construtor ou como constante
        // $postData = ['clientId' => $this->clientId, /* outros parâmetros como 'scope' */ ];
        // ... lógica cURL similar a getAccessToken ...
        // $data = json_decode($response, true);
        // if (isset($data['userCode'])) {
        //     return $data;
        // }

        // Por enquanto, lança uma exceção indicando que não está implementado.
        throw new \Exception("AuthService::getUserCode() - Método não completamente implementado.");

        // Se fosse retornar a estrutura que você mencionou anteriormente:
        /*
        return [
            "userCode" => "HJLX-LPSQ",
            "authorizationCodeVerifier" => "g58p...",
            "verificationUrl" => "https://portal.ifood.com.br/apps/code",
            "verificationUrlComplete" => "https://portal.ifood.com.br/apps/code?c=HJLX-LPSQ",
            "expiresIn" => 600
        ];
        */
    }

    // Você pode adicionar outros métodos aqui para outros fluxos de autenticação
    // ou para trocar o authorization_code por um token, por exemplo.
}