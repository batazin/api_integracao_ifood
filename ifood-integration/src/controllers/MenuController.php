<?php

namespace App\Controllers;

use App\Services\IfoodService;

class MenuController
{
    protected $ifoodService;

    public function __construct(IfoodService $ifoodService)
    {
        $this->ifoodService = $ifoodService;
    }

    public function getMenuItems()
    {
        // Logic to retrieve menu items from iFood API
        $menuItems = $this->ifoodService->fetchMenuItems();
        return $menuItems;
    }

    public function getCategories()
    {
        // Logic to retrieve menu categories from iFood API
        $categories = $this->ifoodService->fetchCategories();
        return $categories;
    }
}