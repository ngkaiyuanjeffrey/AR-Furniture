<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/products.php';
require_once __DIR__ . '/includes/public_layout.php';
$category = isset($_GET['category']) ? trim((string) $_GET['category']) : null;
$databaseError = '';
try {
	$products = get_products($category ?: null);
	$categories = get_categories(true);
} catch (RuntimeException $exception) {
	$products = [];
	$categories = [];
	$databaseError = $exception->getMessage();
}
public_header('Catalog');
$currentUrl = app_url('index.php' . ($category ? '?category=' . rawurlencode($category) : ''));
?>
<main>
<?php if ($databaseError): ?><div class="database-alert" role="alert"><strong><?= e($databaseError) ?></strong><span>Start MySQL in XAMPP, import <code>sql/database.sql</code>, and refresh this page.</span></div><?php endif; ?>
<section class="hero"><div class="hero-copy"><p class="eyebrow">Furniture, with a sense of place</p><h1>See it before it becomes yours.</h1><p class="hero-lede">Explore considered pieces in 3D, then place them in your own room with augmented reality.</p><a class="button button-light" href="#catalog">Explore the collection <span>↘</span></a></div><div class="hero-aside"><div class="hero-orbit"><div class="orbit-ring"></div><div class="orbit-label">3D / AR<br><strong>ready</strong></div></div><div class="qr-card"><img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&amp;data=<?= rawurlencode($currentUrl) ?>" alt="QR code for this catalog page"><div><strong>Take the room with you</strong><p>Scan to browse on mobile and try AR.</p></div></div></div></section>
<section class="catalog-section" id="catalog"><div class="section-heading"><div><p class="eyebrow">The collection</p><h2>Pieces for real rooms</h2></div><p class="section-note">Tap into a product to inspect its form, dimensions, and place it in your space.</p></div>
<div class="category-bar"><a class="category-pill<?= !$category ? ' active' : '' ?>" href="<?= e(app_url('index.php')) ?>">All</a><?php foreach ($categories as $item): ?><a class="category-pill<?= $category === $item['slug'] ? ' active' : '' ?>" href="<?= e(app_url('index.php?category=' . rawurlencode($item['slug']))) ?>"><?= e($item['name']) ?></a><?php endforeach; ?></div>
<div class="product-grid"><?php foreach ($products as $product): ?><article class="product-card"><a class="product-image" href="<?= e(app_url('product.php?slug=' . rawurlencode($product['slug']))) ?>"><?php if ($product['thumb_path']): ?><img loading="lazy" src="<?= e(public_file($product['thumb_path'])) ?>" alt="<?= e($product['name']) ?>"><?php else: ?><div class="image-placeholder"><span><?= e(substr($product['name'], 0, 1)) ?></span><small>3D / AR</small></div><?php endif; ?><span class="view-badge">View in your space ↗</span></a><div class="product-info"><p class="product-category"><?= e($product['category_name']) ?></p><h3><?= e($product['name']) ?></h3><div class="product-meta"><strong><?= format_price($product['price'], $product['currency']) ?></strong><span><?= e((string) $product['width_cm']) ?> × <?= e((string) $product['height_cm']) ?> × <?= e((string) $product['depth_cm']) ?> cm</span></div></div></article><?php endforeach; ?></div>
<?php if (!$products): ?><div class="empty-state"><h3>No pieces in this category yet.</h3><p>Try another category or return to the full collection.</p></div><?php endif; ?></section></main><?php public_footer(); ?>
