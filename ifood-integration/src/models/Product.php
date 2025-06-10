<?php

namespace App\Models;

class Product
{
    private $productId;
    private $name;
    private $price;

    public function __construct($productId, $name, $price)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }
}