<?php
    use Dotenv\Dotenv;
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv::createImmutable(dirname(__DIR__), 'passconfig.env');
    $dotenv->load();

    define('DB_NAME_1', $_ENV['DB_NAME_1']);
    define('DB_NAME_2', $_ENV['DB_NAME_2']);
    define('DB_HOST', $_ENV['DB_HOST']);
    define('DB_USER', $_ENV['DB_USER']);
    define('DB_USER_2', $_ENV['DB_USER_2']);
    define('DB_PASS', $_ENV['DB_PASS']);

    date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Caracas');
?>