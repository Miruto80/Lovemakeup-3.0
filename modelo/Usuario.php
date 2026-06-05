<?php
namespace LoveMakeup\Proyecto\Modelo;
use LoveMakeup\Proyecto\Config\Conexion;
use Dotenv\Dotenv;
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv::createImmutable(dirname(__DIR__), 'passconfig.env');
$dotenv->load();

class Usuario extends Conexion
{
    private $llaveprivada;
    private $metodocifrado;
    private $objtipousuario; 
    
    function __construct() {
        parent::__construct();
        $this->llaveprivada = $_ENV['SMTP_KEY'];
        $this->metodocifrado = $_ENV['SMTP_METODO'];
        $this->objtipousuario = new TipoUsuario();
    }
//-------------
    private function encryptClave($clave) { //------------------------------------------------------- [CIFRAR CLAVE]
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->metodocifrado)); 
        $encrypted = openssl_encrypt($clave, $this->metodocifrado, $this->llaveprivada, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
//---------------
    private function decryptClave($claveEncriptada) {//---------------------------------------------[DECIFRAR CLAVE]
        $data = base64_decode($claveEncriptada);
        $ivLength = openssl_cipher_iv_length($this->metodocifrado);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $this->metodocifrado, $this->llaveprivada, 0, $iv);
    }
//----------------   
    public function procesarUsuario($jsonDatos) { //-------------------------------- [ DATOS DE LA OPERACION ]
        $datos = json_decode($jsonDatos, true);
        $operacion = $datos['operacion'];
        $datosProcesar = $datos['datos'];
        
        try {
            switch ($operacion) {
                case 'registrar':
                      // VALIDAR ANTES DE REGISTRAR
                    if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                        return ['respuesta' => 0, 'accion' => 'incluir', 'text' => 'La cédula ya está registrada'];
                    }
                    if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'correo', 'valor' => $datosProcesar['correo']])) {
                        return ['respuesta' => 0, 'accion' => 'incluir', 'text' => 'El correo electrónico ya está registrado'];
                    }
                    if (!$this->verificarExistencia(['tabla' =>'rol','campo' => 'id_rol', 'valor' => $datosProcesar['id_rol']])) {
                        return ['respuesta' => 0, 'accion' => 'incluir', 'text' => 'el rol no existe'];
                    }
                    // IR AL METODO DE REGISTRAR
                    $datosProcesar['clave'] = $this->encryptClave($datosProcesar['clave']);
                    return $this->ejecutarRegistro($datosProcesar);
                    
               case 'actualizar':
                    //VALIDAR ANTES DE MODIFICAR
                    if ($datosProcesar['cedula'] !== $datosProcesar['cedula_actual']) {
                        if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                            return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'La cédula ya está registrada'];
                        }
                    }
                    if ($datosProcesar['correo'] !== $datosProcesar['correo_actual']) {
                        if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'correo', 'valor' => $datosProcesar['correo']])) {
                            return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'El correo electrónico ya está registrado'];
                        }
                    }
                    if (!$this->verificarExistencia(['tabla' =>'persona','campo' => 'cedula', 'valor' => $datosProcesar['cedula_actual']])) {
                        return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'el usuario no existe'];
                    }

                    if (!$this->verificarExistencia(['tabla' =>'rol','campo' => 'id_rol', 'valor' => $datosProcesar['id_rol']])) {
                        return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'el rol no existe'];
                    } 
                    // IR AL METODO DE ACTUALIZAR
                    return $this->ejecutarActualizacion($datosProcesar);
                    
                case 'eliminar':
                    //VALIDAR ANTES DE ELIMINAR
                    if (!$this->verificarExistencia(['tabla' =>'persona', 'campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                        return ['respuesta' => 0, 'accion' => 'eliminar', 'text' => 'el usuario no existe'];
                    }
                    // IR AL METODO ELIMINAR
                    return $this->ejecutarEliminacion($datosProcesar);

                case 'verificar':
                  if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                        return ['respuesta' => 1,'accion' => 'verificar','text' => 'La cédula ya está registrada' ];
                    } else {
                        return [ 'respuesta' => 0,'accion' => 'verificar','text' => 'La cédula no se encuentra registrada'];
                    }

                 case 'verificarCorreo':
                    if ($this->verificarExistencia(['tabla' =>'persona','campo' => 'correo', 'valor' => $datosProcesar['correo']])) {
                            return ['respuesta' => 1, 'accion' => 'verificarcorreo', 'text' => 'La correo ya está registrada' ];
                        } else {
                            return [ 'respuesta' => 0, 'accion' => 'verificarcorreo', 'text' => 'La correo no se encuentra registrada'  ];
                        } 

                case 'verificarrol':
                 if ($this->verificarExistencia(['tabla' =>'rol','campo' => 'id_rol', 'valor' => $datosProcesar['id_rol']])) {
                        return ['respuesta' => 1, 'accion' => 'verifirol' ];
                    } else {
                        return [ 'respuesta' => 0, 'accion' => 'verifirol', 'text' => 'no se encuentra un rol registrado'  ];
                    }         

                default:
                    return ['respuesta' => 0, 'accion' => 'verifirol', 'text' => 'Operación no válida'];
            }
        } catch (\Exception $e) {
            return ['respuesta' => 0, 'accion' => 'verifirol', 'text' => $e->getMessage()];
        }
    }
//------------------
    private function ejecutarRegistro($datos) { //------------------------------------------- [REGISTRO NUEVO USUARIO]
    $conex = $this->getConex2();
        try {
            $conex->beginTransaction();

            // SENTECIA REGISTRO PERSONA
            $sqlPersona = "INSERT INTO persona (cedula, nombre, apellido, correo, telefono, tipo_documento)
                        VALUES (:cedula, :nombre, :apellido, :correo, :telefono, :tipo_documento)";
            $paramPersona = [
                'cedula' => $datos['cedula'],
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'correo' => $datos['correo'],
                'telefono' => $datos['telefono'],
                'tipo_documento' => $datos['tipo_documento']
            ];
            $stmtPersona = $conex->prepare($sqlPersona);
            $stmtPersona->execute($paramPersona);

            // SENTECIA REGISTRO USUARIO
            $sqlUsuario = "INSERT INTO usuario (cedula, clave, estatus, id_rol)
                        VALUES (:cedula, :clave, 1, :id_rol)";
            $paramUsuario = [
                'cedula' => $datos['cedula'],
                'clave' => $datos['clave'],
                'id_rol' => $datos['id_rol']
            ];
            $stmtUsuario = $conex->prepare($sqlUsuario);
            $stmtUsuario->execute($paramUsuario);


            $conex->commit();
            $conex = null;
            return ['respuesta' => 1, 'accion' => 'incluir'];

        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
                return ['respuesta' => 0, 'accion' => 'incluir', 'text' => $e->getMessage()];
            }
            throw $e;
        }
    }
//-----------------------
    private function ejecutarActualizacion($datos) {  //------------------------------------ [ ACTUALIZAR DATOS USUARIOS ]
    $conex = $this->getConex2();
        try {
            $conex->beginTransaction();

            // 0 - Bloqueo
            $sqlbloqueo = "SELECT cedula FROM persona WHERE cedula = :cedula_actual FOR UPDATE";
            $stmtbloqueo = $conex->prepare($sqlbloqueo);
            $stmtbloqueo->execute(['cedula_actual' => $datos['cedula_actual']]);

            // 1 - Actualizar datos en la tabla persona
            $sqlPersona = "UPDATE persona 
                        SET cedula = :cedula_nueva, 
                            correo = :correo, 
                            tipo_documento = :tipo_documento 
                        WHERE cedula = :cedula_actual";

            $paramPersona = [
                'cedula_nueva' => $datos['cedula'],
                'correo' => $datos['correo'],
                'tipo_documento' => $datos['tipo_documento'],
                'cedula_actual' => $datos['cedula_actual']
            ];
            $stmtPersona = $conex->prepare($sqlPersona);
            $stmtPersona->execute($paramPersona);

            if ($datos['cedula'] !== $datos['cedula_actual']) {
                    // 2.1 - Actualizar la bitacora 
                    $sqlbitacoraUpdate = "UPDATE bitacora 
                                        SET cedula = :cedula_nueva 
                                        WHERE cedula = :cedula_actual";

                    $datosbitacora = [
                        'cedula_nueva' => $datos['cedula'],
                        'cedula_actual' => $datos['cedula_actual']
                    ];
                    
                    $stmtbitacoraUpdate = $conex->prepare($sqlbitacoraUpdate);
                    $stmtbitacoraUpdate->execute($datosbitacora);

                    // 2.2 - Actualizar la bitacora en la tabla permiso
                    $sqlUsuario = "UPDATE usuario 
                        SET cedula = :cedula_nueva
                        WHERE cedula = :cedula_actual";

                    $paramUsuario = [
                        'cedula_nueva' => $datos['cedula'],
                        'cedula_actual' => $datos['cedula_actual']
                    ];

                    $stmtUsuario = $conex->prepare($sqlUsuario);
                    $stmtUsuario->execute($paramUsuario);
            }

            // 3 - Actualizar datos en la tabla usuario
            $sqlUsuario2 = "UPDATE usuario 
                        SET estatus = :estatus, 
                                id_rol = :id_rol 
                        WHERE cedula = :cedula_nueva";

            $paramUsuario2 = [
                'cedula_nueva' => $datos['cedula'],
                'estatus' => $datos['estatus'],
                'id_rol' => $datos['id_rol'],
            ];

            $stmtUsuario2 = $conex->prepare($sqlUsuario2);
            $stmtUsuario2->execute($paramUsuario2);

            $conex->commit();
            $conex = null;
            return ['respuesta' => 1, 'accion' => 'actualizar'];

        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
                return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => $e->getMessage()];
            }
            throw $e;
        }
    }
//-------------------------------
    private function ejecutarEliminacion($datos) { //---------------------------------- [ ELIMINACION LOGICA ]
    $conex = $this->getConex2();
        try {
            $conex->beginTransaction();

            // BLOQUEO
            $sqlbloqueo = "SELECT cedula FROM usuario WHERE cedula = :cedula FOR UPDATE"; // BLOQUEO
            $stmtbloqueo = $conex->prepare($sqlbloqueo);
            $stmtbloqueo->execute($datos);
            
            if (!$stmtbloqueo->fetch()) {
                $conex->rollBack();
                return ['respuesta' => 0, 'accion' => 'eliminar', 'text' => 'Registro no encontrado'];
            }

            // SENTENCIA DE ELIMINACION LOGICA
            $sql = "UPDATE usuario SET estatus = 0 WHERE cedula = :cedula"; 
            $stmt = $conex->prepare($sql);
            $resultado = $stmt->execute($datos);

            $conex->commit();
            $conex = null;
            return ['respuesta' => 1, 'accion' => 'eliminar'];

        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
                return ['respuesta' => 0, 'accion' => 'eliminar', 'text'=>$e->getMessage()];
            }
            throw $e;
        }
    }
//----------------------------  
    private function verificarExistencia($datos) { //------------------- [VERIFICAR EXISTENCIA EN LA BD]
        $conex = $this->getConex2();
        try {
            $conex->beginTransaction();
            $sql = "SELECT COUNT(*) 
                    FROM {$datos['tabla']} WHERE {$datos['campo']} = :valor FOR UPDATE";

            $stmt = $conex->prepare($sql);
            $stmt->execute(['valor' => $datos['valor']]);
            $existe = $stmt->fetchColumn() > 0;

            $conex->commit();
            $conex = null;
            return $existe;
        } catch (\PDOException $e) {
            if ($conex) 
            $conex = null;
            throw $e;
        }
    }
//------------------- 
    public function consultar($limite = 100) { //-------------------------- [CONSULTA GENERAL PARA LA VISTA] 
        $conex = $this->getConex2();
        try {
            $sql = "SELECT  
                        per.*, 
                        ru.id_rol, 
                        ru.nombre AS nombre_tipo, 
                        ru.nivel,
                        u.id_usuario,
                        u.estatus
                    FROM usuario u
                    INNER JOIN persona per ON u.cedula = per.cedula
                    INNER JOIN rol ru ON u.id_rol = ru.id_rol
                    WHERE ru.nivel IN (2, 3) 
                    AND u.estatus >= 1 AND u.id_usuario >=2
                    ORDER BY u.id_usuario DESC LIMIT :limite";
                    
            $stmt = $conex->prepare($sql);
            $stmt->bindParam(':limite', $limite, \PDO::PARAM_INT);
            $stmt->execute();

            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $conex = null;
            return $resultado;
        } catch (\PDOException $e) {
            if ($conex) {
                $conex = null;
            }
            throw $e;
        }
    }
//--------------
    public function obtenerRol() { //---------------------------- [ CONSULTAR ROL ]
        return $this->objtipousuario->consultar();
    }
//--------------
    public function contarTotal(){ // ------------------------- [CONTAR LOS USUARIO PARA EL LIMITE]
        $conex = $this->getConex2();
        try {
            $sql = "SELECT COUNT(*) AS total FROM usuario WHERE estatus >= 1 AND id_rol = 1 OR id_rol >= 3";
            $consulta = $conex->prepare($sql);
            $consulta->execute();

            $fila = $consulta->fetch(\PDO::FETCH_ASSOC);
            return $fila['total'];

        } catch (\PDOException $e) {
            if ($conex) {
                $conex = null;
            }
            throw $e;
        }
    }
//-----------    
}
