<?php
declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function app_config(): array
{
    static $config;
    if (!$config) {
        $config = require __DIR__ . '/../config/config.php';
    }
    return $config;
}

function app_url(string $path = ''): string
{
    $base = rtrim((string) app_config()['app']['base_url'], '/');
    if ($base !== '') {
        return $base . '/' . ltrim($path, '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $folder = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    if (str_ends_with($folder, '/admin') || str_ends_with($folder, '/api')) {
        $folder = dirname($folder);
    }
    return $scheme . '://' . $host . ($folder === '/' ? '' : $folder) . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . app_url($path));
    exit;
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function format_price(float|int|string $price, string $currency = 'RM'): string
{
    return e($currency) . ' ' . number_format((float) $price, 2);
}

function public_file(string|null $path): string
{
    return $path ? app_url($path) : '';
}

function upload_file(array $file, string $directory, array $allowedExtensions, array $allowedMimeTypes, int $maxBytes): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > $maxBytes) {
        throw new InvalidArgumentException('The uploaded file is missing, invalid, or too large.');
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file((string) $file['tmp_name']);
    if (!in_array($extension, $allowedExtensions, true) || !in_array($mime, $allowedMimeTypes, true)) {
        throw new InvalidArgumentException('That file type is not allowed.');
    }

    $root = dirname(__DIR__);
    $targetDirectory = $root . '/' . trim($directory, '/');
    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true) && !is_dir($targetDirectory)) {
        throw new RuntimeException('Upload directory could not be created.');
    }
    $filename = bin2hex(random_bytes(12)) . '.' . $extension;
    if (!move_uploaded_file((string) $file['tmp_name'], $targetDirectory . '/' . $filename)) {
        throw new RuntimeException('The uploaded file could not be saved.');
    }
    return trim($directory, '/') . '/' . $filename;
}
