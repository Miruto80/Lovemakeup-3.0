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

        // Generar URL optimizada con WebP automático
        $optimizedUrl = self::optimizeImageUrl($response['secure_url']);

        return [
            'url_imagen' => $optimizedUrl,
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
    
    /**
     * Genera una URL optimizada con WebP automático y calidad automática
     * 
     * @param string $secureUrl URL segura de Cloudinary
     * @param string|null $width Ancho opcional (en píxeles)
     * @param string|null $height Alto opcional (en píxeles)
     * @param string $quality Calidad: 'auto', 'high', 'medium', 'low' (por defecto 'auto')
     * @return string URL optimizada
     * 
     * Ejemplo: 
     * optimizeImageUrl('https://res.cloudinary.com/.../image.jpg', '300', '300')
     * Resultado: agrega /q_auto,f_auto/c_fill,w_300,h_300/ para WebP automático
     */
    public static function optimizeImageUrl(
        string $secureUrl,
        ?string $width = null,
        ?string $height = null,
        string $quality = 'auto'
    ): string {
        if (empty($secureUrl)) {
            return $secureUrl;
        }

        // Construir transformaciones
        $transformations = [];

        // Agregar optimizaciones automáticas
        // q_auto = calidad automática, f_auto = formato automático (WebP si soportado)
        $transformations[] = 'q_auto';
        $transformations[] = 'f_auto';

        // Agregar dimensiones si se especifican
        if (!empty($width) || !empty($height)) {
            $w = !empty($width) ? 'w_' . $width : '';
            $h = !empty($height) ? 'h_' . $height : '';
            $transformations[] = 'c_fill'; // crop and fill
            if (!empty($w)) {
                $transformations[] = $w;
            }
            if (!empty($h)) {
                $transformations[] = $h;
            }
        }

        // Insertar transformaciones en la URL
        // Formato: https://res.cloudinary.com/cloud_name/image/upload/TRANSFORMATIONS/public_id
        $transformationString = implode(',', $transformations);
        $optimizedUrl = str_replace(
            '/image/upload/',
            '/image/upload/' . $transformationString . '/',
            $secureUrl
        );

        return $optimizedUrl;
    }

    /**
     * Genera una URL forzando WebP específicamente
     * Útil para navegadores que definitivamente soportan WebP
     * 
     * @param string $secureUrl URL segura de Cloudinary
     * @param string|null $width Ancho opcional
     * @param string|null $height Alto opcional
     * @return string URL con formato WebP
     */
    public static function toWebP(
        string $secureUrl,
        ?string $width = null,
        ?string $height = null
    ): string {
        if (empty($secureUrl)) {
            return $secureUrl;
        }

        $transformations = [];
        $transformations[] = 'q_auto';
        $transformations[] = 'f_webp'; // Forzar WebP

        if (!empty($width) || !empty($height)) {
            $w = !empty($width) ? 'w_' . $width : '';
            $h = !empty($height) ? 'h_' . $height : '';
            $transformations[] = 'c_fill';
            if (!empty($w)) {
                $transformations[] = $w;
            }
            if (!empty($h)) {
                $transformations[] = $h;
            }
        }

        $transformationString = implode(',', $transformations);
        $webpUrl = str_replace(
            '/image/upload/',
            '/image/upload/' . $transformationString . '/',
            $secureUrl
        );

        return $webpUrl;
    }
}