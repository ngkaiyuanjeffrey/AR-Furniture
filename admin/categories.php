<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();
$categories = get_categories();
admin_header('Categories');
?><div class="admin-heading"><p class="section-note">Categories shape the storefront filter. Keep names clear and ordering intentional.</p><a class="button button-dark" href="<?= e(app_url('admin/category_edit.php')) ?>">Add category +</a></div><table class="admin-table"><thead><tr><th>Name</th><th>Slug</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead><tbody><?php foreach ($categories as $category): ?><tr><td><strong><?= e($category['name']) ?></strong></td><td><?= e($category['slug']) ?></td><td><?= e($category['sort_order']) ?></td><td><?= $category['is_active'] ? 'Active' : 'Hidden' ?></td><td class="admin-actions"><a class="text-link" href="<?= e(app_url('admin/category_edit.php?id=' . $category['id'])) ?>">Edit</a><form method="post" action="<?= e(app_url('admin/category_delete.php')) ?>" onsubmit="return confirm('Delete this category? It must be empty first.');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($category['id']) ?>"><button class="text-link danger" style="background:none;border:0;font:inherit;padding:0">Delete</button></form></td></tr><?php endforeach; ?></tbody></table><?php admin_footer(); ?>
