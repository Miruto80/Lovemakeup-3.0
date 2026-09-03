<?php
namespace LoveMakeup\Proyecto\Config;

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Dotenv\Dotenv;

class CloudinaryConfig
{
    private static ?Cloudinary $cloudinary = null;

    public static function getInstance(): Cloudinary
    {
        if (self::$cloudinary === null) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__), 'passconfig.env');
            $dotenv->safeLoad();

            if (!empty($_ENV['CLOUDINARY_URL'])) {
                Configuration::instance($_ENV['CLOUDINARY_URL']);
            } else {
                Configuration::instance([
                    'cloud' => [
                        'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? null,
                        'api_key' => $_ENV['CLOUDINARY_API_KEY'] ?? null,
                        'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? null,
                    ],
                    'url' => [
                        'secure' => true,
                    ],
                ]);
            }

            self::$cloudinary = new Cloudinary(Configuration::instance());
        }

        return self::$cloudinary;
    }

    public static function uploadImage(string $filePath): array
    {
        $response = self::getInstance()->uploadApi()->upload($filePath, [
            'folder' => 'lovemakeup/productos',
            'resource_type' => 'image'
        ]);

        return [
            'url_imagen' => $response['secure_url'],
            'public_id' => $response['public_id']
        ];
    }


    public static function uploadComprobante(string $origen): array
    {
        $response = self::getInstance()->uploadApi()->upload($origen, [
            'folder' => 'lovemakeup/comprobantes',
            'resource_type' => 'image'
        ]);

        return [
            'url_imagen' => $response['secure_url'],
            'public_id' => $response['public_id']
        ];
    }
}