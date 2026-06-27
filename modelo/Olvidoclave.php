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
//--------------------      
     private function encryptClave($datos) { //------------------------------------------------------- [CIFRAR CLAVE]
            $config = [
                'llaveprivada' => $_ENV['SMTP_KEY'],
                'metodo' => $_ENV['SMTP_METODO']
            ];
            
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($config['metodo']));
            $encrypted = openssl_encrypt($datos['clave'], $config['metodo'], $config['llaveprivada'], 0, $iv);
            return base64_encode($iv . $encrypted);
    }
//---------------     
    private function decryptClave($datos) { //---------------------------------------------[DECIFRAR CLAVE]
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
//-----------------
   public function procesarOlvido($jsonDatos) { //------------------------------------ [ OPERACIONES ]
        $datos = json_decode($jsonDatos, true);
        $operacion = $datos['operacion'];
        $datosProcesar = $datos['datos'];
        
        try {
            switch ($operacion) {
                case 'verificar':
                    if (!$this->verificarExistencia(['campo' => 'correo', 'valor' => $datosProcesar['correo']])) {
                        return ['respuesta' => 0, 'accion' => 'verificar', 'text' => 'El correo no registrado'];
                    }

                    return $this->datospersona($datosProcesar);

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
//-------------------
    private function ejecutarActualizacionUsuario($datos) { //-------------------------------- [ ACTULIZAR CLAVE ] 
        $conex = $this->getConex2();
        try {
            $conex->beginTransaction();

            $sqlbloqueo = "SELECT cedula FROM usuario WHERE cedula = :cedula FOR UPDATE";
            $stmtbloqueo = $conex->prepare($sqlbloqueo);
            $stmtbloqueo->execute(['cedula' => $datos['cedula']]);
            
            $sql = "UPDATE usuario 
                        SET  clave = :clave
                        WHERE cedula = :cedula";
            
               $parametros = [
                'clave' => $this->encryptClave(['clave' => $datos['clave']]),
                'cedula' => $datos['cedula']
                ];

            $stmt = $conex->prepare($sql);
            $resultado = $stmt->execute($parametros);
            
            $conex->commit();
            $conex = null;
            return ['respuesta' => 1, 'accion' => 'actualizar'];
            
        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
                return ['respuesta' => 0, 'accion' => 'actualizar', 'text'=>$e->getMessage()];
            }
            throw $e;
        }
    }
//---------------------
    private function verificarExistencia($datos) { //------------------------[ VERIFICAR EXISTENCIA ]
        $conex = $this->getConex2();
        try {
            $conex->beginTransaction();
            $sql = "SELECT COUNT(*) FROM persona 
                    WHERE ({$datos['campo']} = :valor) FOR UPDATE";

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
 //----------------  
    private function datospersona($datos) { //------------------------[ VERIFICAR EXISTENCIA POR CORREO ]
        $conex = $this->getConex2();
        try {
            
            $sql = "SELECT cedula FROM persona 
                    WHERE correo = :correo FOR UPDATE";

            $stmt = $conex->prepare($sql);
            $correoFinal = isset($datos['valor']) ? $datos['valor'] : ($datos['correo'] ?? null);

            $stmt->execute(['correo' => $correoFinal]);
            
            $cedula = $stmt->fetchColumn();

            $conex = null;
            return $cedula ? ['cedula' => $cedula] : false;            
        
            } catch (\PDOException $e) {
            if ($conex) {
                $conex = null;
            }
            throw $e;
        }
    }
//-----------
  
}
