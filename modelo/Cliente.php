<?php

namespace LoveMakeup\Proyecto\Modelo;

use LoveMakeup\Proyecto\Config\Conexion;

/*||||||||||||||||||||||||||||||| TOTAL DE METODOS =  |||||||||||||||||||||||||  04  |||||*/    

class Cliente extends Conexion{

    function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
    }

/*||||||||||||||||||||||||||||||| OPERACIONES  |||||||||||||||||||||||||  01  |||||*/        
    public function procesarCliente($jsonDatos) {
        $datos = json_decode($jsonDatos, true);
        $operacion = $datos['operacion'];
        $datosProcesar = $datos['datos'];
        
        try {
            switch ($operacion) {
                case 'actualizar':
                    
                    // Verifica si cambió la cédula antes de validar existencia
                    if ($datosProcesar['cedula'] !== $datosProcesar['cedula_actual']) {
                        if ($this->verificarExistencia(['campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                            return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'La cédula ya está registrada'];
                        }
                    }

                    // Verifica si cambió el correo antes de validar existencia
                    if ($datosProcesar['correo'] !== $datosProcesar['correo_actual']) {
                        if ($this->verificarExistencia(['campo' => 'correo', 'valor' => $datosProcesar['correo']])) {
                            return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'El correo electrónico ya está registrado'];
                        }
                    }

                    if (!$this->verificarExistencia(['campo' => 'cedula', 'valor' => $datosProcesar['cedula_actual']])) {
                        return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'el usuario no existe'];
                    }

                    return $this->ejecutarActualizacion($datosProcesar);
                
                case 'verificar':
                    if ($this->verificarExistencia(['campo' => 'cedula', 'valor' => $datosProcesar['cedula']])) {
                        return ['respuesta' => 1,'accion' => 'verificar','text' => 'La cédula ya está registrada' ];
                    } else {
                        return [ 'respuesta' => 0,'accion' => 'verificar','text' => 'La cédula no se encuentra registrada'];
                    }
                        
                default:
                    return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => 'Operación no válida'];
            }
        } catch (\Exception $e) {
            return ['respuesta' => 0, 'accion' => 'actualizar', 'text' => $e->getMessage()];
        }
    }
//-------------------
    private function ejecutarActualizacion($datos) {//----------------------- [ ACTULIZAR DATOS DEL CLIENTE ]
        $conex  = $this->getConex2(); // BD principal (persona, usuario)
        $conex2 = $this->getConex1(); // BD tienda (pedido, direccion)

        try {
            // INICIAR TRANSACCIONES
            $conex->beginTransaction();
            $conex2->beginTransaction();

            // BLOQUEAR LA FILA DE PERSONA 
            $sqlLock = "SELECT cedula 
                        FROM persona 
                        WHERE cedula = :cedula_actual
                        FOR UPDATE";
            $stmtLock = $conex->prepare($sqlLock);
            $stmtLock->execute(['cedula_actual' => $datos['cedula_actual']]);

            // BLOQUEAR PEDIDOS Y DIRECCIONES (si existen)
            $sqlLockPedidos = "SELECT id_pedido 
                            FROM pedido 
                            WHERE cedula = :cedula_actual
                            FOR UPDATE";
            $stmtLP = $conex2->prepare($sqlLockPedidos);
            $stmtLP->execute(['cedula_actual' => $datos['cedula_actual']]);

            $sqlLockDir = "SELECT id_direccion 
                        FROM direccion 
                        WHERE cedula = :cedula_actual
                        FOR UPDATE";
            $stmtLD = $conex2->prepare($sqlLockDir);
            $stmtLD->execute(['cedula_actual' => $datos['cedula_actual']]);


            // ACTUALIZAR PERSONA 
            $sqlPersona = "UPDATE persona 
                        SET cedula = :cedula_nueva,
                            correo = :correo,
                            tipo_documento = :tipo_documento
                        WHERE cedula = :cedula_actual";
            $stmtPersona = $conex->prepare($sqlPersona);
            $stmtPersona->execute([
                'cedula_nueva' => $datos['cedula'],
                'correo' => $datos['correo'],
                'tipo_documento' => $datos['tipo_documento'],
                'cedula_actual' => $datos['cedula_actual']
            ]);

            //  ACTUALIZAR USUARIO (cédula)
            $sqlUsuario = "UPDATE usuario 
                        SET cedula = :cedula_nueva
                        WHERE cedula = :cedula_actual";
            $stmtUsuario = $conex->prepare($sqlUsuario);
            $stmtUsuario->execute([
                'cedula_nueva' => $datos['cedula'],
                'cedula_actual' => $datos['cedula_actual']
            ]);

            // ACTUALIZAR USUARIO (estatus)
            $sqlUsuario2 = "UPDATE usuario 
                            SET estatus = :estatus
                            WHERE cedula = :cedula_nueva";
            $stmtUsuario2 = $conex->prepare($sqlUsuario2);
            $stmtUsuario2->execute([
                'cedula_nueva' => $datos['cedula'],
                'estatus' => $datos['estatus']
            ]);

            //  SI LA CÉDULA CAMBIA - ACTUALIZAR OTRO TABLAS
            if ($datos['cedula'] !== $datos['cedula_actual']) {
                // PEDIDOS
                $sqlPedido = "UPDATE pedido 
                            SET cedula = :cedula_nueva 
                            WHERE cedula = :cedula_actual";
                $stmtPedido = $conex2->prepare($sqlPedido);
                $stmtPedido->execute([
                    'cedula_nueva' => $datos['cedula'],
                    'cedula_actual' => $datos['cedula_actual']
                ]);

                // DIRECCIONES
                $sqlDireccion = "UPDATE direccion 
                                SET cedula = :cedula_nueva 
                                WHERE cedula = :cedula_actual";
                $stmtDireccion = $conex2->prepare($sqlDireccion);
                $stmtDireccion->execute([
                    'cedula_nueva' => $datos['cedula'],
                    'cedula_actual' => $datos['cedula_actual']
                ]);
            }

            

            // CONFIRMAR AMBAS TRANSACCIONES
            $conex->commit();
            $conex2->commit();
            $conex = null;
            $conex2 = null;

            return ['respuesta' => 1, 'accion' => 'actualizar'];

        } catch (\PDOException $e) {
            if ($conex){
                $conex->rollBack();
                $conex2 = null;
            }  
            if ($conex2) {
                $conex2->rollBack(); 
                $conex2 = null;
            }
            
            return [ 'respuesta' => 0, 'accion' => 'actualizar', 'text' => $e->getMessage()];
        }
    }
//-------------------
   private function verificarExistencia($datos) { //------------------------- [ VERIFICAR SI EXISTE EN LA BD ] 
    $conex = $this->getConex2();
    try {
        $sql = "SELECT COUNT(*) FROM persona 
                WHERE ({$datos['campo']} = :valor) FOR UPDATE";

        $stmt = $conex->prepare($sql);
        $stmt->execute(['valor' => $datos['valor']]);
        $existe = $stmt->fetchColumn() > 0;

        $conex = null;
        return $existe;
    } catch (\PDOException $e) {
        if ($conex) $conex = null;
        throw $e;
    }
}
//--------------
    public function consultar($limite = 100) { // ------------------------ [ CONSULTA GENERAL PARA LA VISTA ]
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
                    WHERE ru.nivel IN (1) 
                    AND u.estatus >= 1
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
//-------------
    public function contarTotal(){ //----------------------- [CONTAR PARA LIMITAR] 
    $conex = $this->getConex2();
        try {
            $sql = "SELECT COUNT(*) AS total FROM usuario WHERE estatus >= 1 AND id_rol = 2";
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
//--------------
}
