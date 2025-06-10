<?php

class Order {
    private $orderId;
    private $status;
    private $products;

    public function __construct($orderId, $status, $products = []) {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->products = $products;
    }

    public function getOrderId() {
        return $this->orderId;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getProducts() {
        return $this->products;
    }

    public function addProduct($product) {
        $this->products[] = $product;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}