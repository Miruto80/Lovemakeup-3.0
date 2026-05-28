<?php

namespace LoveMakeup\Proyecto\Modelo;

use LoveMakeup\Proyecto\Config\Conexion;
use Dotenv\Dotenv;
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(dirname(__DIR__), 'passconfig.env');
$dotenv->load();
/*||||||||||||||||||||||||||||||| TOTAL METODOS =   06  |||||||||||||||||||||||||||||*/    

class Olvidoclave extends Conexion{
    
    function __construct() {
       parent::__construct(); // Llama al constructor de la clase padre
      
    }

/*||||||||||||||||||||||||||||||| ENCRIPTACION DE CLAVE  |||||||||||||||||||||||||  01  |||||*/        
     private function encryptClave($datos) {
            $config = [
                'llaveprivada' => $_ENV['SMTP_KEY'],
                'metodo' => $_ENV['SMTP_METODO']
            ];
            
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($config['metodo']));
            $encrypted = openssl_encrypt($datos['clave'], $config['metodo'], $config['llaveprivada'], 0, $iv);
            return base64_encode($iv . $encrypted);
    }

/*||||||||||||||||||||||||||||||| DESINCRIPTACION DE CLAVE   |||||||||||||||||||||||||  02  |||||*/        
    private function decryptClave($datos) {
        $config = [
            'llaveprivada' => $_ENV['SMTP_KEY'],
            'metodo' => $_ENV['SMTP_METODO']
        ];
        
        $data = base64_decode($datos['clave_encriptada']);
        $ivLength = openssl_cipher_iv_length($config['metodo']);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $config['metodo'], $config['llaveprivada'], 0, $iv);
    }


/*||||||||||||||||||||||||||||||| OPERACIONES  |||||||||||||||||||||||||  03  |||||*/         
   public function procesarOlvido($jsonDatos) {
        $datos = json_decode($jsonDatos, true);
        $operacion = $datos['operacion'];
        $datosProcesar = $datos['datos'];
        
        try {
            switch ($operacion) {
                 case 'actualizar':

                    if (!$this->verificarExistencia(['campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                        return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'El usuario no existe'];
                    }

                    return $this->ejecutarActualizacionUsuario($datosProcesar);

                default:
                    return ['respuesta' => 0, 'mensaje' => 'Operación no válida'];
            }
        } catch (\Exception $e) {
            return ['respuesta' => 0, 'mensaje' => $e->getMessage()];
        }
    }


/*||||||||||||||||||||||||||||||| ACTUALIZAR DE CLAVE USUARIO  |||||||||||||||||||||||||  06  |||||*/        
     protected function ejecutarActualizacionUsuario($datos) {
        $conex = $this->getConex2();
        try {
            $conex->beginTransaction();
            
            $sql = "UPDATE usuario 
                        SET  clave = :clave
                        WHERE cedula = :cedula";
            
               $parametros = [
                'clave' => $this->encryptClave(['clave' => $datos['clave']]),
                'cedula' => $datos['cedula']
                ];

            $stmt = $conex->prepare($sql);
            $resultado = $stmt->execute($parametros);
            
            if ($resultado) {
                $conex->commit();
                $conex = null;
                return ['respuesta' => 1, 'accion' => 'actualizar'];
            }
            
            $conex->rollBack();
            $conex = null;
            return ['respuesta' => 0, 'accion' => 'actualizar'];
            
        } catch (\PDOException $e) {
            if ($conex) {
                return ['respuesta' => 0, 'accion' => 'actualizar'];
                $conex->rollBack();
                $conex = null;
            }
            throw $e;
        }
    }

    private function verificarExistencia($datos) {
        $conex = $this->getConex2();
        try {
            $conex->beginTransaction();
            $sql = "SELECT COUNT(*) FROM persona 
                    WHERE ({$datos['campo']} = :valor)";

            $stmt = $conex->prepare($sql);
            $stmt->execute(['valor' => $datos['valor']]);
            $existe = $stmt->fetchColumn() > 0;

            $conex->commit();
            $conex = null;
            return $existe;
        } catch (\PDOException $e) {
            if ($conex) $conex = null;
            throw $e;
        }
    }
   
  
}
