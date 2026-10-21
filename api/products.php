<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/products.php';
header('Content-Type: application/json; charset=utf-8');
try {
    $products = array_map(static function (array $product): array {
        return ['id' => (int) $product['id'], 'slug' => $product['slug'], 'name' => $product['name'], 'description' => $product['description'], 'price' => (float) $product['price'], 'currency' => $product['currency'], 'category' => $product['category_name'], 'thumbnail' => public_file($product['thumb_path']), 'model' => public_file($product['glb_path']), 'dimensions_cm' => ['width' => $product['width_cm'] ? (float) $product['width_cm'] : null, 'height' => $product['height_cm'] ? (float) $product['height_cm'] : null, 'depth' => $product['depth_cm'] ? (float) $product['depth_cm'] : null]];
    }, get_products());
    echo json_encode(['success' => true, 'products' => $products], JSON_UNESCAPED_SLASHES);
} catch (Throwable $exception) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Database unavailable. Please check the database configuration.']);
}
