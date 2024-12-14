<?php
class Products {
    private static $instance = null;
    private $products = [];
    private $jsonFile;

    private function __construct() {
        $this->jsonFile = __DIR__ . '/../json/products.json';
        $this->loadProducts();
    }

    private function loadProducts() {
        if (file_exists($this->jsonFile)) {
            $jsonData = file_get_contents($this->jsonFile);
            $data = json_decode($jsonData, true);
            $this->products = $data['products'] ?? [];
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getAllProducts($page = 1, $itemsPerPage = 12) {
        $offset = ($page - 1) * $itemsPerPage;
        return array_slice($this->products, $offset, $itemsPerPage);
    }

    public function getProductById($id) {
        foreach ($this->products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }
        return null;
    }

    public function getProductsByIds($ids) {
        return array_filter($this->products, function($product) use ($ids) {
            return in_array($product['id'], $ids);
        });
    }

    public function getFeaturedProducts() {
        return array_filter($this->products, function($product) {
            return $product['featured'] === true;
        });
    }

    public function getProductsByCategory($category) {
        return array_filter($this->products, function($product) use ($category) {
            return $product['category'] === $category;
        });
    }

    public function searchProducts($query) {
        return array_filter($this->products, function($product) use ($query) {
            return stripos($product['name'], $query) !== false || 
                   stripos($product['description'], $query) !== false;
        });
    }
} 