<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';

function get_categories(bool $activeOnly = false): array
{
    $sql = 'SELECT * FROM categories' . ($activeOnly ? ' WHERE is_active = 1' : '') . ' ORDER BY sort_order ASC, name ASC';
    return db()->query($sql)->fetchAll();
}

function get_products(?string $categorySlug = null, bool $activeOnly = true): array
{
    $conditions = [];
    $params = [];
    if ($activeOnly) {
        $conditions[] = 'p.is_active = 1 AND c.is_active = 1';
    }
    if ($categorySlug) {
        $conditions[] = 'c.slug = ?';
        $params[] = $categorySlug;
    }
    $where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
    $statement = db()->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id{$where} ORDER BY p.sort_order ASC, p.name ASC");
    $statement->execute($params);
    return $statement->fetchAll();
}

function get_product_by_slug(string $slug, bool $activeOnly = true): ?array
{
    $condition = $activeOnly ? ' AND p.is_active = 1 AND c.is_active = 1' : '';
    $statement = db()->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE p.slug = ?{$condition} LIMIT 1");
    $statement->execute([$slug]);
    $product = $statement->fetch();
    return $product ?: null;
}

function get_product(int $id): ?array
{
    $statement = db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $statement->execute([$id]);
    $product = $statement->fetch();
    return $product ?: null;
}
