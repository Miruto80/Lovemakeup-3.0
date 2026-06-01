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

    public function check($ip) {
        $file = $this->storagePath . md5($ip) . '.json';
        $now = time();
        $data = ['count' => 1, 'start' => $now];

        if (file_exists($file)) {
            $handle = fopen($file, 'c+');
            if ($handle && flock($handle, LOCK_EX)) {
                $content = stream_get_contents($handle);
                if (!empty($content)) {
                    $data = json_decode($content, true) ?: $data;
                }

                if (($now - $data['start']) > $this->window) {
                    $data = ['count' => 1, 'start' => $now];
                } else {
                    $data['count']++;
                }

                ftruncate($handle, 0);
                rewind($handle);
                fwrite($handle, json_encode($data));
                fflush($handle);
                flock($handle, LOCK_UN);
            }
            fclose($handle);
        } else {
            file_put_contents($file, json_encode($data));
        }

        return $data['count'] <= $this->limit;
    }
}