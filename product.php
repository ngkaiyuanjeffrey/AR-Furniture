<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/products.php';
require_once __DIR__ . '/includes/public_layout.php';
$slug = trim((string) ($_GET['slug'] ?? ''));
$databaseError = '';
try {
	$product = $slug ? get_product_by_slug($slug) : null;
} catch (RuntimeException $exception) {
	$product = null;
	$databaseError = $exception->getMessage();
}
if ($databaseError) { http_response_code(503); public_header('Database unavailable'); echo '<main class="message-page"><p class="eyebrow">Temporarily unavailable</p><h1>' . e($databaseError) . '</h1><p>Start MySQL in XAMPP, import <code>sql/database.sql</code>, and refresh this page.</p></main>'; public_footer(); exit; }
if (!$product) { http_response_code(404); public_header('Product not found'); echo '<main class="message-page"><p class="eyebrow">404</p><h1>Product not found.</h1><p>That piece may have moved. Return to the catalog to keep browsing.</p><a class="button button-dark" href="' . e(app_url('index.php')) . '">Back to catalog</a></main>'; public_footer(); exit; }
public_header($product['name']);
$qrUrl = app_url('product.php?slug=' . rawurlencode($product['slug']));
?>
<main class="detail-page"><div class="detail-breadcrumb"><a href="<?= e(app_url('index.php')) ?>">Catalog</a><span>/</span><span><?= e($product['category_name']) ?></span></div><section class="detail-layout"><div class="viewer-wrap"><model-viewer id="product-viewer" src="<?= e(public_file($product['glb_path'])) ?>"<?= $product['usdz_path'] ? ' ios-src="' . e(public_file($product['usdz_path'])) . '"' : '' ?> camera-controls ar ar-modes="webxr scene-viewer quick-look" shadow-intensity="1" exposure="0.9" alt="Interactive 3D model of <?= e($product['name']) ?>"><div class="viewer-loading" slot="progress-bar">Loading 3D model...</div><button class="ar-button" slot="ar-button">Place in your space ↗</button></model-viewer><div id="viewer-status" class="viewer-status" role="status" aria-live="polite">AR works on compatible mobile devices. Open this page on your phone and tap the AR button.</div></div><div class="detail-copy"><p class="eyebrow"><?= e($product['category_name']) ?></p><h1><?= e($product['name']) ?></h1><p class="detail-description"><?= nl2br(e($product['description'])) ?></p><div class="detail-price"><?= format_price($product['price'], $product['currency']) ?></div><div class="dimensions"><div><span>Width</span><strong><?= e((string) $product['width_cm']) ?> cm</strong></div><div><span>Height</span><strong><?= e((string) $product['height_cm']) ?> cm</strong></div><div><span>Depth</span><strong><?= e((string) $product['depth_cm']) ?> cm</strong></div></div><div class="detail-actions"><a class="button button-dark" href="#product-viewer">Inspect in 3D ↗</a><div class="share-card"><img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&amp;data=<?= rawurlencode($qrUrl) ?>" alt="QR code for <?= e($product['name']) ?>"><p><strong>Move to mobile</strong><br>Scan to open this piece on your phone.</p></div></div></div></section></main><?php public_footer(); ?>
