<?php
namespace Seguridad;

class FileRateLimiter {
    private $storagePath;
    private $limit;
    private $window;
    private $blacklistFile;
    private $blacklistDuration;

    public function __construct($limit = 5, $window = 1) { // 5 solicitudes por segundo
        $this->limit = $limit;
        $this->window = $window; // Ventana de tiempo en segundos
        $this->storagePath = __DIR__ . '/data/';
        $this->blacklistFile = $this->storagePath . 'blacklist.json';
        $this->blacklistDuration = 12 * 60 * 60; // 12 horas

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
            return $this->getBlacklistTimeRemaining($ip); // Devuelve el tiempo restante
        }

        $file = $this->storagePath . hash('sha256', $ip) . '.json';
        $now = microtime(true); // Tiempo en segundos con microsegundos
        $data = ['timestamps' => []];

        if (file_exists($file)) {
            $content = file_get_contents($file);
            $decoded = json_decode($content, true);

            if ($decoded) {
                $data = $decoded;
            }

            // Filtrar las solicitudes fuera de la ventana de tiempo
            $data['timestamps'] = array_filter($data['timestamps'], function ($timestamp) use ($now) {
                return ($now - $timestamp) <= $this->window;
            });
        }

        // Agregar la nueva solicitud
        $data['timestamps'][] = $now;

        // Guardar los datos actualizados
        file_put_contents($file, json_encode($data));

        // Si excede el límite, manejar bloqueos consecutivos
        if (count($data['timestamps']) > $this->limit) {
            $this->handleConsecutiveBlocks($ip);
            return false;
        }

        return true;
    }

    private function isBlacklisted($ip) {
        $blacklist = json_decode(file_get_contents($this->blacklistFile), true);

        // Si la IP no está en la blacklist, permitir acceso
        if (!isset($blacklist[$ip])) {
            return false;
        }

        $now = time();
        $blacklistedAt = $blacklist[$ip];
        $timeElapsed = $now - $blacklistedAt;

        // Si han pasado más de 12 horas, eliminar de la blacklist
        if ($timeElapsed > $this->blacklistDuration) {
            unset($blacklist[$ip]);
            file_put_contents($this->blacklistFile, json_encode($blacklist));
            return false;
        }

        // Si aún está dentro del período de bloqueo, devolver el tiempo restante
        return $this->blacklistDuration - $timeElapsed;
    }

    private function addToBlacklist($ip) {
        $blacklist = json_decode(file_get_contents($this->blacklistFile), true);
        $blacklist[$ip] = time(); // Guardar el timestamp del bloqueo
        file_put_contents($this->blacklistFile, json_encode($blacklist));
    }

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

    private function getBlacklistTimeRemaining($ip) {
        $blacklist = json_decode(file_get_contents($this->blacklistFile), true);

        if (!isset($blacklist[$ip])) {
            return 0; // La IP no está en la blacklist
        }

        $now = time();
        $blacklistedAt = $blacklist[$ip];
        $timeElapsed = $now - $blacklistedAt;

        // Si el tiempo de bloqueo ha expirado, devolver 0
        if ($timeElapsed > $this->blacklistDuration) {
            return 0;
        }

        // Devolver el tiempo restante
        return $this->blacklistDuration - $timeElapsed;
    }
}