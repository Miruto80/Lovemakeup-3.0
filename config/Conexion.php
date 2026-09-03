<?php

namespace LoveMakeup\Proyecto\Config;

require_once (__DIR__.'/config.php');

class Conexion {
    private $conex1;
    private $conex2;

    public function __construct() {
        $this->conex1 = null;
        $this->conex2 = null;
    }

    public function getConex1() {
        if ($this->conex1 === null) {
            $this->conex1 = $this->crearConexion(DB_NAME_1, DB_USER, DB_PASS);
        }
        return $this->conex1;
    }

    public function getConex2() {
        if ($this->conex2 === null) {
            $this->conex2 = $this->crearConexion(DB_NAME_2, DB_USER_2, DB_PASS);
        }
        return $this->conex2;
    }

    private function crearConexion($baseDatos, $usuario, $clave) {
        try {
            $conexion = new \PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . $baseDatos . ";charset=utf8mb4",
                $usuario,
                $clave,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $conexion;
        } catch (\PDOException $e) {
            throw new \PDOException(
                'Conexión Fallida a la base de datos: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
    }
}
?>