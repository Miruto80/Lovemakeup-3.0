<?php
namespace Seguridad;

class FileRateLimiter {
    private $storagePath;
    private $limit;
    private $window;
    private $blacklistFile;

    public function __construct($limit = 3, $window = 30) {
        $this->limit = $limit;
        $this->window = $window;
        $this->storagePath = __DIR__ . '/data/';
        $this->blacklistFile = $this->storagePath . 'blacklist.json';

        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0777, true);
        }

        // Crear el archivo de blacklist si no existe
        if (!file_exists($this->blacklistFile)) {
            file_put_contents($this->blacklistFile, json_encode([]));
        }
    }

    public function check($ip = null) {
        if (!$ip) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        }

        // Verificar si la IP está en la blacklist
        if ($this->isBlacklisted($ip)) {
            echo "<p>IP bloqueada permanentemente: $ip</p>";
            return false;
        }

        $file = $this->storagePath . hash('sha256', $ip) . '.json';
        $now = time();
        $data = ['count' => 1, 'start' => $now];

        if (file_exists($file)) {
            $content = file_get_contents($file);
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

        file_put_contents($file, json_encode($data));

        // Si la IP excede el límite, manejar bloqueos consecutivos
        if ($data['count'] > $this->limit) {
            $this->handleConsecutiveBlocks($ip);
            return false;
        }

        return true;
    }

    // Verificar si una IP está en la blacklist
    private function isBlacklisted($ip) {
        $blacklist = json_decode(file_get_contents($this->blacklistFile), true);
        return isset($blacklist[$ip]);
    }

    // Agregar una IP a la blacklist
    private function addToBlacklist($ip) {
        $blacklist = json_decode(file_get_contents($this->blacklistFile), true);
        $blacklist[$ip] = true;
        file_put_contents($this->blacklistFile, json_encode($blacklist));
    }

    // Manejar bloqueos consecutivos
    private function handleConsecutiveBlocks($ip) {
        $blockCountsFile = $this->storagePath . 'block_counts.json';

        // Crear el archivo de contadores si no existe
        if (!file_exists($blockCountsFile)) {
            file_put_contents($blockCountsFile, json_encode([]));
        }

        $blockCounts = json_decode(file_get_contents($blockCountsFile), true);

        if (!isset($blockCounts[$ip])) {
            $blockCounts[$ip] = 1;
        } else {
            $blockCounts[$ip]++;
        }

        // Si la IP ha sido bloqueada 3 veces consecutivas, agregarla a la blacklist
        if ($blockCounts[$ip] >= 3) {
            $this->addToBlacklist($ip);
        }

        file_put_contents($blockCountsFile, json_encode($blockCounts));
    }
}