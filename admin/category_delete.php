<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('admin/categories.php');
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$count = db()->prepare('SELECT COUNT(*) FROM products WHERE category_id=?'); $count->execute([$id]);
if ((int) $count->fetchColumn() === 0) db()->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
redirect('admin/categories.php');
