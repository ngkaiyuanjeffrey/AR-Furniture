<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';

function db(): PDO
{
    static $pdo = null;
    global $config;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $config['db']['host'], $config['db']['name']),
            $config['db']['user'],
            $config['db']['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        throw new RuntimeException('Database unavailable. Please check the database configuration.');
    }

    return $pdo;
}
