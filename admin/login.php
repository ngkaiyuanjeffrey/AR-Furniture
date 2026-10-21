<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
start_secure_session();
if (is_admin()) { redirect('admin/index.php'); }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (attempt_login(trim((string) ($_POST['username'] ?? '')), (string) ($_POST['password'] ?? ''))) { redirect('admin/index.php'); }
    $error = 'The username or password is not correct.';
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin login | AR Furniture</title><link rel="stylesheet" href="<?= e(app_url('css/styles.css')) ?>"></head><body class="admin-body"><main class="login-page"><form class="login-card" method="post"><a class="brand" href="<?= e(app_url('index.php')) ?>">AR<span>Furniture</span></a><p class="eyebrow" style="margin-top:45px">Private workspace</p><h1>Welcome back.</h1><?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="field"><label for="username">Username</label><input id="username" name="username" required autocomplete="username"></div><div class="field" style="margin-top:16px"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div><button class="button button-dark" style="border:0;margin-top:26px;width:100%;justify-content:center">Sign in ↗</button><p style="color:var(--muted);font-size:.8rem;margin-top:24px">Initial demo account: admin / password</p></form></main></body></html>
