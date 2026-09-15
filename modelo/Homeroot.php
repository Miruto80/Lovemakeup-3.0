<?php 
namespace LoveMakeup\Proyecto\Modelo;

use LoveMakeup\Proyecto\Config\Conexion;
use PDO;
use Exception;

class Homeroot extends Conexion {
    
    public function __construct() {
        parent::__construct();
    }
    public function obtenerMetricasSistema(): array
    {
        $sistemaOperativo = PHP_OS_FAMILY;
        $ejecucionPermitida = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        $metricas = [
            'entorno'               => $ejecucionPermitida ? 'Sin Restricciones (Local/VPS)' : 'Restringido (Hosting Compartido)',
            'uso_cpu'               => 0,
            'ram_total'             => 0,
            'ram_libre'             => 0,
            'ram_usada'             => 0,
            'porcentaje_ram'        => 0,
            'disco_total'           => disk_total_space('.'),
            'disco_libre'           => disk_free_space('.'),
            'disco_usado'           => 0,
            'porcentaje_disco'      => 0,
            'mensaje_advertencia'   => null
        ];

        // Cálculo de Almacenamiento (Disco)
        if ($metricas['disco_total'] > 0) {
            $metricas['disco_usado'] = $metricas['disco_total'] - $metricas['disco_libre'];
            $metricas['porcentaje_disco'] = round(($metricas['disco_usado'] / $metricas['disco_total']) * 100, 2);
        }

        // Si shell_exec está bloqueado por el Hosting
        if (!$ejecucionPermitida) {
            $metricas['mensaje_advertencia'] = 'La función shell_exec está deshabilitada en el archivo php.ini por políticas de seguridad del hosting.';
            return $metricas;
        }

        // --- ENTORNO XAMPP / VPS ---
        if ($sistemaOperativo === 'Windows') {
            // Uso de CPU en Windows
            $salidaCpu = @shell_exec('wmic cpu get LoadPercentage 2>&1');
            if ($salidaCpu && preg_match('/\d+/', $salidaCpu, $coincidencias)) {
                $metricas['uso_cpu'] = (int)$coincidencias[0];
            }

            // Memoria RAM en Windows
            $salidaRam = @shell_exec('wmic os get FreePhysicalMemory,TotalVisibleMemorySize /Value 2>&1');
            if ($salidaRam && preg_match_all('/=(\d+)/', $salidaRam, $coincidencias)) {
                if (count($coincidencias[1]) >= 2) {
                    $metricas['ram_libre'] = (float)$coincidencias[1][0] * 1024; // Convertir a Bytes
                    $metricas['ram_total'] = (float)$coincidencias[1][1] * 1024;
                }
            }
        } elseif ($sistemaOperativo === 'Linux') {
            // Uso de CPU en Linux
            if (function_exists('sys_getloadavg')) {
                $carga = sys_getloadavg();
                $metricas['uso_cpu'] = $carga[0] ?? 0;
            }

            // Memoria RAM en Linux
            if (@file_exists('/proc/meminfo')) {
                $informacionMemoria = @file_get_contents('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)\s+kB/', $informacionMemoria, $total);
                preg_match('/MemAvailable:\s+(\d+)\s+kB/', $informacionMemoria, $disponible);
                if (isset($total[1]) && isset($disponible[1])) {
                    $metricas['ram_total'] = (float)$total[1] * 1024;
                    $metricas['ram_libre'] = (float)$disponible[1] * 1024;
                }
            }
        }

        // Procesar cálculo de RAM
        if ($metricas['ram_total'] > 0) {
            $metricas['ram_usada'] = $metricas['ram_total'] - $metricas['ram_libre'];
            $metricas['porcentaje_ram'] = round(($metricas['ram_usada'] / $metricas['ram_total']) * 100, 2);
        }

        return $metricas;
    }

    /**
     * Obtiene los procesos en ejecución activa.
     */
    public function obtenerProcesosActivos(): array
    {
        $sistemaOperativo = PHP_OS_FAMILY;
        $listaProcesos = [];
        $ejecucionPermitida = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

        if (!$ejecucionPermitida) {
            return [
                'estado' => 'error', 
                'mensaje' => 'No es posible obtener los procesos en un hosting compartido (shell_exec bloqueado).'
            ];
        }

        if ($sistemaOperativo === 'Windows') {
            $salida = @shell_exec('tasklist /FO CSV /NH 2>&1');
            if ($salida) {
                $lineas = explode("\n", trim($salida));
                foreach (array_slice($lineas, 0, 15) as $linea) {
                    $datos = str_getcsv($linea);
                    if (count($datos) >= 5) {
                        $listaProcesos[] = [
                            'nombre'  => $datos[0],
                            'pid'     => $datos[1],
                            'sesion'  => $datos[2],
                            'memoria' => $datos[4]
                        ];
                    }
                }
            }
        } elseif ($sistemaOperativo === 'Linux') {
            $salida = @shell_exec('ps aux --sort=-%cpu | head -n 16 2>&1');
            if ($salida) {
                $lineas = explode("\n", trim($salida));
                array_shift($lineas); // Remover encabezado
                foreach ($lineas as $linea) {
                    if (!empty(trim($linea))) {
                        $columnas = preg_split('/\s+/', trim($linea));
                        if (count($columnas) >= 11) {
                            $listaProcesos[] = [
                                'usuario' => $columnas[0],
                                'pid'     => $columnas[1],
                                'cpu'     => $columnas[2],
                                'memoria' => $columnas[3],
                                'comando' => $columnas[10]
                            ];
                        }
                    }
                }
            }
        }

        return ['estado' => 'exito', 'datos' => $listaProcesos];
    }


   public function obtenerInfoBDConexion($pdo, $nombreBD) {
        $inicio = microtime(true);
        $activa = false;
        $tamanoBytes = 0;

        if ($pdo instanceof PDO) {
            try {

                $test = $pdo->query("SELECT 1");
                if ($test !== false) {
                    $activa = true;

                    $sql = "SHOW TABLE STATUS FROM `$nombreBD`";
                    $stmt = $pdo->query($sql);
                    
                    if ($stmt) {
                        $tablas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tablas as $tabla) {
                            $tamanoBytes += ($tabla['Data_length'] + $tabla['Index_length']);
                        }
                    }
                }
            } catch (Exception $e) {
                $activa = false;
            }
        }

        $fin = microtime(true);
        $tiempoRespuestaMs = round(($fin - $inicio) * 1000);

        return [
            'nombre'       => $nombreBD,
            'activa'       => $activa,
            'tamano_bytes' => (float)$tamanoBytes,
            'latencia_ms'  => $tiempoRespuestaMs
        ];
    }

    public function obtenerInfoBDNegocio() {
        try {
            $conex = $this->getConex1();
            return $this->obtenerInfoBDConexion($conex, 'lovemakeupbd1');
        } catch (Exception $e) {
            return [
                'nombre'       => 'lovemakeupbd1',
                'activa'       => false,
                'tamano_bytes' => 0,
                'latencia_ms'  => 0
            ];
        }
    }

    public function obtenerInfoBDSeguridad() {
        try {
            $conex = $this->getConex2();
            return $this->obtenerInfoBDConexion($conex, 'lovemakeupbds2');
        } catch (Exception $e) {
            return [
                'nombre'       => 'lovemakeupbds2',
                'activa'       => false,
                'tamano_bytes' => 0,
                'latencia_ms'  => 0
            ];
        }
    }


}

?>