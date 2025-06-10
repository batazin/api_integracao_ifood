<?php

namespace App\Controllers;

use App\Services\IfoodService;
use App\Models\Order;

class OrderController
{
    protected $ifoodService;

    public function __construct(IfoodService $ifoodService)
    {
        $this->ifoodService = $ifoodService;
    }

    public function createOrder(array $orderData)
    {
        // Logic to create an order using the iFood API
        $response = $this->ifoodService->placeOrder($orderData);
        return $response;
    }

    public function getOrder($orderId)
    {
        // Logic to retrieve an order using the iFood API
        $order = $this->ifoodService->getOrder($orderId);
        return $order;
    }

    public function listOrders()
    {
        // Logic to list all orders
        $orders = $this->ifoodService->listOrders();
        return $orders;
    }
}