<?php

namespace LoveMakeup\Proyecto\Modelo;

use LoveMakeup\Proyecto\Config\Conexion;
use LoveMakeup\Proyecto\Modelo\Bitacora;

class Categoria extends Conexion {
    function __construct() {
        parent::__construct();
    }

    private function prepararAuditoria($conex): void {
        if (session_status() === PHP_SESSION_NONE || empty($_SESSION['id'])) {
            throw new \RuntimeException('No hay un usuario autenticado para auditar la operación.');
        }

        $conex->exec('SET @app_cedula = ' . (int) $_SESSION['id']);
    }

    // 2) Router JSON → CRUD
    public function procesarCategoria(string $jsonDatos): array {
        $payload   = json_decode($jsonDatos, true);
        $operacion = $payload['operacion'] ?? ''; 
        $d         = $payload['datos']    ?? [];

        try {
            switch ($operacion) {
                case 'incluir':    return $this->insertar($d);
                case 'actualizar': return $this->actualizar($d);
                case 'eliminar':   return $this->eliminarLogico($d);
                default:
                    return [
                      'respuesta'=>0,
                      'accion'   =>$operacion,
                      'mensaje'  =>'Operación no válida'
                    ];
            }
        } catch (\PDOException $e) {
            return [
              'respuesta'=>0,
              'accion'   =>$operacion,
              'mensaje'  =>$e->getMessage()
            ];
        } catch (\Exception $e) {
            // Manejar excepciones personalizadas
            return [
              'respuesta'=>0,
              'accion'   =>$operacion,
              'mensaje'  =>$e->getMessage()
            ];
        }
    }

    // 3a) Incluir
    private function insertar(array $d): array {
        $conex = $this->getConex1();
        try {
            $this->prepararAuditoria($conex);

            // ========================================
            // VALIDACIÓN ESTRICTA DE DATOS
            // ========================================
            
            // Validar que el nombre no esté vacío
            if (empty($d['nombre'])) {
                throw new \Exception("El nombre de la categoría no puede estar vacío.");
            }
            
            // Validación estricta del nombre (letras y espacios, 3-50 caracteres)
            if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$/', $d['nombre'])) {
                throw new \Exception("Nombre inválido. Solo letras y espacios, 3-50 caracteres");
            }
            
            // Verificar si ya existe una categoría con el mismo nombre (ignorando mayúsculas/minúsculas)
            $sqlCheck = "SELECT COUNT(*) FROM categoria WHERE LOWER(nombre) = LOWER(:nombre) AND estatus = 1";
            $stmtCheck = $conex->prepare($sqlCheck);
            $stmtCheck->execute(['nombre' => $d['nombre']]);
            if ($stmtCheck->fetchColumn() > 0) {
                throw new \Exception("Ya existe una categoría con el nombre \"{$d['nombre']}\".");
            }
            
            $conex->beginTransaction();

            $sql  = "INSERT INTO categoria (nombre, estatus)
                     VALUES (:nombre, 1)";
            $stmt = $conex->prepare($sql);
            $ok   = $stmt->execute(['nombre'=>$d['nombre']]);

            if ($ok) {
                $conex->commit();
                // Registrar en bitácora
                $bitacora = new Bitacora();
                $bitacora->registrarOperacion(
                    'CREAR',
                    'Categoria',
                    "ID: " . $conex->lastInsertId() . " | Nombre: " . $d['nombre']
                );
                $conex = null;
                $respuesta = ['respuesta'=>1,'accion'=>'incluir','mensaje'=>'Categoría creada'];
            } else {
                $conex->rollBack();
                $respuesta = ['respuesta'=>0,'accion'=>'incluir','mensaje'=>'Error al crear'];
            }
            $conex = null;
            return $respuesta;
        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
            }
            throw $e;
        }
    }

    // 3b) Actualizar
    private function actualizar(array $d): array {
        $conex = $this->getConex1();
        try {
            $this->prepararAuditoria($conex);

            // ========================================
            // VALIDACIÓN ESTRICTA DE DATOS
            // ========================================
            
            // Validar que el nombre no esté vacío
            if (empty($d['nombre'])) {
                throw new \Exception("El nombre de la categoría no puede estar vacío.");
            }
            
            // Validación estricta del nombre (letras y espacios, 3-50 caracteres)
            if (!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{3,50}$/', $d['nombre'])) {
                throw new \Exception("Nombre inválido. Solo letras y espacios, 3-50 caracteres");
            }
            
            // Verificar si ya existe otra categoría con el mismo nombre (ignorando mayúsculas/minúsculas)
            $sqlCheckName = "SELECT COUNT(*) FROM categoria WHERE LOWER(nombre) = LOWER(:nombre) AND id_categoria != :id AND estatus = 1";
            $stmtCheckName = $conex->prepare($sqlCheckName);
            $stmtCheckName->execute([
                'nombre' => $d['nombre'],
                'id' => $d['id_categoria']
            ]);
            if ($stmtCheckName->fetchColumn() > 0) {
                throw new \Exception("Ya existe otra categoría con el nombre \"{$d['nombre']}\".");
            }

            $conex->beginTransaction();

            $sql  = "UPDATE categoria
                     SET nombre = :nombre
                     WHERE id_categoria = :id";
            $stmt= $conex->prepare($sql);
            $ok  = $stmt->execute([
                'id'     => $d['id_categoria'],
                'nombre' => $d['nombre']
            ]);

            if ($ok) {
                $conex->commit();
                // Registrar en bitácora
                $bitacora = new Bitacora();
                $bitacora->registrarOperacion(
                    'MODIFICAR',
                    'Categoria',
                    "ID: " . $d['id_categoria'] . " | Nombre: " . $d['nombre']
                );
                $conex = null;
                $respuesta = ['respuesta'=>1,'accion'=>'actualizar','mensaje'=>'Categoría modificada'];
            } else {
                $conex->rollBack();
                $respuesta = ['respuesta'=>0,'accion'=>'actualizar','mensaje'=>'Error al modificar'];
            }
            $conex = null;
            return $respuesta;
        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
            }
            throw $e;
        }
    }

    // 3c) Eliminar lógico
    private function eliminarLogico(array $d): array {
        $conex = $this->getConex1();
        try {
            $this->prepararAuditoria($conex);

            $conex->beginTransaction();

            $sql  = "UPDATE categoria
                     SET estatus = 0
                     WHERE id_categoria = :id";
            $stmt= $conex->prepare($sql);
            $ok  = $stmt->execute(['id'=>$d['id_categoria']]);

            if ($ok) {
                $conex->commit();
                // Registrar en bitácora
                $bitacora = new Bitacora();
                $bitacora->registrarOperacion(
                    'ELIMINAR',
                    'Categoria',
                    "ID: " . $d['id_categoria']
                );
                $conex = null;
                $respuesta = ['respuesta'=>1,'accion'=>'eliminar','mensaje'=>'Categoría eliminada'];
            } else {
                $conex->rollBack();
                $respuesta = ['respuesta'=>0,'accion'=>'eliminar','mensaje'=>'Error al eliminar'];
            }
            $conex = null;
            return $respuesta;
        } catch (\PDOException $e) {
            if ($conex) {
                $conex->rollBack();
                $conex = null;
            }
            throw $e;
        }
    }

    // 4) Consultar (listado)
    public function consultar(): array {
        $conex = $this->getConex1();
        try {
            $sql   = "SELECT id_categoria, nombre
                      FROM categoria
                      WHERE estatus = 1
                      ORDER BY id_categoria DESC";
            $stmt  = $conex->prepare($sql);
            $stmt->execute();
            $datos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $conex = null;
            return $datos;
        } catch (\PDOException $e) {
            if ($conex) $conex = null;
            throw $e;
        }
    }
}