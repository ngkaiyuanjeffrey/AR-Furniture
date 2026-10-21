# AR Furniture

A PHP 8.3+, MySQL, PDO, vanilla JavaScript furniture catalog with 3D GLB viewing and mobile AR handoff through `<model-viewer>`.

## XAMPP installation

1. Copy the project to `C:\xampp\htdocs\AR Furniture` (or rename the folder to `ar-furniture`).
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import `sql/database.sql`.
4. Check the credentials in `config/config.php`.
5. Visit `http://localhost/AR%20Furniture/` or your renamed folder URL.
6. Add real `.glb` models through the admin panel. The seeded model paths are intentionally placeholders.

Admin demo login: `admin` / `password`. Change this password in the database before deployment.

## cPanel deployment

Create a MySQL database and user, assign the user to the database, import `sql/database.sql`, upload the project into `public_html`, and update `config/config.php`. Set writable permissions only on `models/` and `uploads/` as required by the host. Enable HTTPS, verify GLB/ USDZ files load, then test a product page and AR on a supported mobile device.

For production, set `app.base_url` to the public project URL. Leaving it blank enables dynamic URL generation for local XAMPP and normal Apache hosting.

## AR notes

The product detail page uses `webxr scene-viewer quick-look`. Android browsers may use WebXR or Google Scene Viewer; compatible Apple devices use Quick Look when a USDZ file is supplied. Physical dimensions are entered in centimetres, while optional measured GLB axes are stored in metres for verification. AR support depends on browser, device, model format, and HTTPS.

## Security and maintenance

Use unique generated upload names, MIME/extension/size validation, prepared statements, CSRF tokens, escaped output, protected configuration, and session ID regeneration. Replace the seeded admin hash and use HTTPS in production. QR images are generated from the current product/catalog URL rather than a hard-coded host.
