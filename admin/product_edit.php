<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/products.php';
require_once __DIR__ . '/../includes/admin_layout.php';
require_admin();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = $id ? get_product($id) : null;
$categories = get_categories();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
    $product = $id ? get_product($id) : null;
    $name = trim((string) ($_POST['name'] ?? ''));
    $slug = slugify((string) ($_POST['slug'] ?? $name));
    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: 0;
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    if ($name === '' || $slug === '' || !$categoryId || $price === false || $price < 0) {
        $error = 'Name, category, slug, and a valid non-negative price are required.';
    } else {
        try {
            $config = app_config();
            $glb = upload_file($_FILES['glb'] ?? [], 'models', ['glb', 'gltf'], ['model/gltf-binary', 'model/gltf+json', 'application/octet-stream', 'text/plain'], $config['app']['upload_max_bytes']);
            $usdz = upload_file($_FILES['usdz'] ?? [], 'models', ['usdz'], ['model/vnd.usdz+zip', 'application/zip', 'application/octet-stream'], $config['app']['upload_max_bytes']);
            $thumb = upload_file($_FILES['thumb'] ?? [], 'uploads/thumbs', ['jpg', 'jpeg', 'png', 'webp'], ['image/jpeg', 'image/png', 'image/webp'], 8 * 1024 * 1024);
            $values = [$categoryId, $slug, $name, trim((string) ($_POST['description'] ?? '')), $price, strtoupper(trim((string) ($_POST['currency'] ?? 'RM'))), $glb ?: ($product['glb_path'] ?? ''), $usdz ?: ($product['usdz_path'] ?? null), $thumb ?: ($product['thumb_path'] ?? null), filter_input(INPUT_POST, 'width_cm', FILTER_VALIDATE_FLOAT) ?: null, filter_input(INPUT_POST, 'height_cm', FILTER_VALIDATE_FLOAT) ?: null, filter_input(INPUT_POST, 'depth_cm', FILTER_VALIDATE_FLOAT) ?: null, filter_input(INPUT_POST, 'glb_x_m', FILTER_VALIDATE_FLOAT) ?: null, filter_input(INPUT_POST, 'glb_y_m', FILTER_VALIDATE_FLOAT) ?: null, filter_input(INPUT_POST, 'glb_z_m', FILTER_VALIDATE_FLOAT) ?: null, isset($_POST['is_active']) ? 1 : 0, filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT) ?: 0];
            if (!$values[6]) throw new InvalidArgumentException('A GLB or GLTF model is required.');
            if ($id) {
                $values[] = $id;
                db()->prepare('UPDATE products SET category_id=?, slug=?, name=?, description=?, price=?, currency=?, glb_path=?, usdz_path=?, thumb_path=?, width_cm=?, height_cm=?, depth_cm=?, glb_x_m=?, glb_y_m=?, glb_z_m=?, is_active=?, sort_order=? WHERE id=?')->execute($values);
            } else {
                db()->prepare('INSERT INTO products (category_id,slug,name,description,price,currency,glb_path,usdz_path,thumb_path,width_cm,height_cm,depth_cm,glb_x_m,glb_y_m,glb_z_m,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)')->execute($values);
            }
            redirect('admin/index.php');
        } catch (Throwable $exception) { $error = $exception instanceof InvalidArgumentException ? $exception->getMessage() : 'Could not save this product. Check the slug is unique and try again.'; }
    }
}
$product = $product ?: ['name'=>'','slug'=>'','category_id'=>'','description'=>'','price'=>'','currency'=>'RM','glb_path'=>'','usdz_path'=>'','thumb_path'=>'','width_cm'=>'','height_cm'=>'','depth_cm'=>'','glb_x_m'=>'','glb_y_m'=>'','glb_z_m'=>'','is_active'=>1,'sort_order'=>0];
admin_header($id ? 'Edit product' : 'Add product');
?><form class="admin-card" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= e($id) ?>"><?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?><div class="form-grid"><div class="field"><label for="name">Product name</label><input id="name" name="name" value="<?= e($product['name']) ?>" required></div><div class="field"><label for="slug">Slug</label><input id="slug" name="slug" value="<?= e($product['slug']) ?>" required></div><div class="field"><label for="category_id">Category</label><select id="category_id" name="category_id" required><option value="">Choose category</option><?php foreach ($categories as $category): ?><option value="<?= e($category['id']) ?>"<?= (string) $product['category_id'] === (string) $category['id'] ? ' selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></div><div class="field"><label for="price">Price</label><input id="price" name="price" type="number" step="0.01" min="0" value="<?= e($product['price']) ?>" required></div><div class="field"><label for="currency">Currency</label><input id="currency" name="currency" maxlength="8" value="<?= e($product['currency']) ?>"></div><div class="field"><label for="sort_order">Sort order</label><input id="sort_order" name="sort_order" type="number" value="<?= e($product['sort_order']) ?>"></div><div class="field full"><label for="description">Description</label><textarea id="description" name="description"><?= e($product['description']) ?></textarea></div><div class="field"><label for="glb">GLB / GLTF model <?= $product['glb_path'] ? '(leave blank to keep current)' : '' ?></label><input id="glb" name="glb" type="file" accept=".glb,.gltf" <?= $product['glb_path'] ? '' : 'required' ?>></div><div class="field"><label for="usdz">Optional USDZ model</label><input id="usdz" name="usdz" type="file" accept=".usdz"></div><div class="field"><label for="thumb">Thumbnail</label><input id="thumb" name="thumb" type="file" accept=".jpg,.jpeg,.png,.webp"></div><div class="field"><label for="width_cm">Width (cm)</label><input id="width_cm" name="width_cm" type="number" step="0.01" value="<?= e($product['width_cm']) ?>"></div><div class="field"><label for="height_cm">Height (cm)</label><input id="height_cm" name="height_cm" type="number" step="0.01" value="<?= e($product['height_cm']) ?>"></div><div class="field"><label for="depth_cm">Depth (cm)</label><input id="depth_cm" name="depth_cm" type="number" step="0.01" value="<?= e($product['depth_cm']) ?>"></div><div class="field"><label for="glb_x_m">Measured GLB X (m)</label><input id="glb_x_m" name="glb_x_m" type="number" step="0.0001" value="<?= e($product['glb_x_m']) ?>"></div><div class="field"><label for="glb_y_m">Measured GLB Y (m)</label><input id="glb_y_m" name="glb_y_m" type="number" step="0.0001" value="<?= e($product['glb_y_m']) ?>"></div><div class="field"><label for="glb_z_m">Measured GLB Z (m)</label><input id="glb_z_m" name="glb_z_m" type="number" step="0.0001" value="<?= e($product['glb_z_m']) ?>"></div><div class="field"><label><input type="checkbox" name="is_active" value="1"<?= $product['is_active'] ? ' checked' : '' ?>> Visible in catalog</label></div></div><div class="form-actions"><button class="button button-dark" type="submit">Save product</button><a class="button" href="<?= e(app_url('admin/index.php')) ?>">Cancel</a></div></form><?php admin_footer(); ?>
