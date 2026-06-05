<?php
namespace Seguridad;

class FileRateLimiter {
    private $storagePath;
    private $limit;
    private $window;

    public function __construct($limit = 10, $window = 60) {
        $this->limit = $limit;
        $this->window = $window;
        $this->storagePath = __DIR__ . '/data/';
        
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }
    }

    public function check($ip = null) {
        // 1. Detección de IP real (Quitamos el truco de test_ip)
        if (!$ip) {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            }
        }
    
     
    
        $file = $this->storagePath . hash('sha256', $ip) . '.json';
        $now = time();
        $data = ['count' => 1, 'start' => $now];
    
        // 2. Operación con el archivo (Sin mensajes en pantalla)
        $handle = fopen($file, 'c+');
        
        if ($handle) {
            if (flock($handle, LOCK_EX)) {
                $content = stream_get_contents($handle);
                
                if (!empty($content)) {
                    $decoded = json_decode($content, true);
                    if ($decoded) {
                        $data = $decoded;
                    }
    
                    if (($now - $data['start']) > $this->window) {
                        $data = ['count' => 1, 'start' => $now];
                    } else {
                        $data['count']++;
                    }
                }
    
                ftruncate($handle, 0);
                rewind($handle);
                fwrite($handle, json_encode($data));
                fflush($handle);
                flock($handle, LOCK_UN);
            }
            fclose($handle);
        }
    
        return $data['count'] <= $this->limit;
    }
}