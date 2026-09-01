<?php
    use Dotenv\Dotenv;
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv::createImmutable(dirname(__DIR__), 'passconfig.env');
    $dotenv->load();

    define('DB_NAME_1L', $_ENV['DB_NAME_1L']);
    define('DB_NAME_2L', $_ENV['DB_NAME_2L']);
    define('DB_HOSTL', $_ENV['DB_HOST']);
    define('DB_USERL', $_ENV['DB_USERL']);
    define('DB_PASSL', $_ENV['DB_PASSL']);

    define('CLOUDINARY_CLOUD_NAME', $_ENV['CLOUDINARY_CLOUD_NAME']);
    define('CLOUDINARY_API_KEY', $_ENV['CLOUDINARY_API_KEY']);
    define('CLOUDINARY_API_SECRET', $_ENV['CLOUDINARY_API_SECRET']);

    date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Caracas');
?>