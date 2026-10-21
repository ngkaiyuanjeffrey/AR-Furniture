<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/index.php'); }
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = $id ? get_product($id) : null;
if ($product) {
    db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    foreach ([$product['glb_path'], $product['usdz_path'], $product['thumb_path']] as $path) {
        if ($path && preg_match('#^(models|uploads/thumbs)/[a-f0-9]{24}\.(glb|gltf|usdz|jpg|jpeg|png|webp)$#', $path)) @unlink(dirname(__DIR__) . '/' . $path);
    }
}
redirect('admin/index.php');
