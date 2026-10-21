<?php
declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function csrf_token(): string
{
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    start_secure_session();
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

function is_admin(): bool
{
    start_secure_session();
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $username, string $password): bool
{
    require_once __DIR__ . '/db.php';
    $statement = db()->prepare('SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1');
    $statement->execute([$username]);
    $admin = $statement->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    start_secure_session();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    return true;
}

function logout_admin(): void
{
    start_secure_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
}
