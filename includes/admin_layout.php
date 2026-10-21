<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';
function admin_header(string $title): void { ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($title) ?> | <?= e(app_config()['app']['name']) ?></title><link rel="stylesheet" href="<?= e(app_url('css/styles.css')) ?>"></head><body class="admin-body"><header class="admin-header"><a class="brand" href="<?= e(app_url('admin/index.php')) ?>">AR<span>Furniture</span></a><nav><a href="<?= e(app_url('admin/index.php')) ?>">Products</a><a href="<?= e(app_url('admin/categories.php')) ?>">Categories</a><a href="<?= e(app_url('index.php')) ?>">Storefront</a><a href="<?= e(app_url('admin/logout.php')) ?>">Logout</a></nav></header><main class="admin-main"><div class="admin-heading"><div><p class="eyebrow">Control room</p><h1><?= e($title) ?></h1></div></div>
<?php }
function admin_footer(): void { ?></main></body></html><?php }
