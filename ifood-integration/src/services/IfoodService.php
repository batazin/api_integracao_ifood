<?php

class IfoodService {
    private $apiUrl;
    private $authService;

    public function __construct(AuthService $authService) {
        $this->apiUrl = 'https://api.ifood.com.br/v1/';
        $this->authService = $authService;
    }

    public function placeOrder($orderData) {
        $accessToken = $this->authService->getAccessToken();
        $response = $this->sendRequest('orders', 'POST', $orderData, $accessToken);
        return $response;
    }

    public function getMenuItems($restaurantId) {
        $accessToken = $this->authService->getAccessToken();
        $response = $this->sendRequest("restaurants/{$restaurantId}/menu", 'GET', [], $accessToken);
        return $response;
    }

    private function sendRequest($endpoint, $method, $data = [], $accessToken) {
        $url = $this->apiUrl . $endpoint;
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}