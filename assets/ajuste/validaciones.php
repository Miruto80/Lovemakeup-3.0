<?php  
// FUNCION PARA MENSAJES ECHO JSON
function MensajeJSON($respuesta, $accion, $texto) {
    echo json_encode([
        'respuesta' => $respuesta,
        'accion'    => $accion,
        'text'      => $texto
    ]);
    exit;
}

// FUNCIONES PARA EXPRESIONES REGULARES
function validarExpresiones($tipo, $valor, $mensaje, $accion) {
    $valido = false;

    switch ($tipo) {
        case 'id_usuario':       $valido = preg_match('/^[0-9]{1,8}$/', $valor); break; //l
        case 'cedula':   $valido = preg_match('/^[0-9]{7,9}$/', $valor); break;//l
        case 'documento':      $valido = preg_match('/^[A-Za-z]{1}$/', $valor); break;//l
        case 'apellido': $valido = preg_match('/^[A-Za-z]{3,20}$/', $valor); break;//l
        case 'nombre':   $valido = preg_match('/^[A-Za-z]{3,20}$/', $valor); break;//l
        case 'nombre_numero_e':   $valido = preg_match('/^[A-Za-z0-9 ]{3,20}$/', $valor); break;//l
        case 'correo':   $valido = filter_var($valor, FILTER_VALIDATE_EMAIL) && strlen($valor) >= 5 && strlen($valor) <= 200; break;//l
        case 'telefono': $valido = preg_match('/^[0-9]{4}-[0-9]{7}$/', $valor); break;//l
        case 'rol':      $valido = preg_match('/^[0-9]{1,3}$/', $valor); break;//1
        case 'estatus':  $valido = preg_match('/^[0-9]{1}$/', $valor); break;//l
        case 'clave':  $valido = preg_match('/^[A-Za-z0-9\.\$\#\*\/]{8,16}$/', $valor); break;//l
        case 'codigo_ingresado':  $valido = preg_match('/^[0-9]{6}$/', $valor); break;//l
        case 'nivel_acceso':  $valido = preg_match('/^[2-3]{1}$/', $valor); break;//l
        case 'dolar':  $valido = preg_match('/^\d{1,5}([.,]\d{1,3})?$/', $valor) || strlen(str_replace([',','.'],'',$valor)) < 4 || strlen(str_replace([',','.'],'',$valor)) > 8; break;
        case 'id_fk':  $valido = preg_match('/^[0-9]+$/', $valor); break;//l
    }

    if (!$valido) {
        echo json_encode([
            'respuesta' => 0, 
            'accion'    => $accion, 
            'text'      => "$mensaje - ERROR 510"
        ]);
        exit;
    }
    return true; // Si es válido, continúa el flujo
}

function validarExpresionesAPP($tipo, $valor, $mensaje) {
    $valido = false;

    switch ($tipo) {
        case 'id_usuario':       $valido = preg_match('/^[0-9]{1,8}$/', $valor); break; //l
        case 'cedula':   $valido = preg_match('/^[0-9]{7,9}$/', $valor); break;//l
        case 'documento':      $valido = preg_match('/^[VEJ]{1}$/i', $valor); break;//l
        case 'apellido': $valido = preg_match('/^[A-Za-z]{3,20}$/', $valor); break;//l
        case 'nombre':   $valido = preg_match('/^[A-Za-z]{3,20}$/', $valor); break;//l
        case 'nombre_numero_e':   $valido = preg_match('/^[A-Za-z0-9 ]{3,20}$/', $valor); break;//l
        case 'correo':   $valido = filter_var($valor, FILTER_VALIDATE_EMAIL) && strlen($valor) >= 5 && strlen($valor) <= 200; break;//l
        case 'telefono': $valido = preg_match('/^[0-9]{4}-[0-9]{7}$/', $valor); break;//l
        case 'rol':      $valido = preg_match('/^[0-9]{1,3}$/', $valor); break;//1
        case 'estatus':  $valido = preg_match('/^[0-9]{1}$/', $valor); break;//l
        case 'clave':  $valido = preg_match('/^[A-Za-z0-9\.\$\#\*\/]{8,16}$/', $valor); break;//l
        case 'codigo_ingresado':  $valido = preg_match('/^[0-9]{6}$/', $valor); break;//l
        case 'nivel_acceso':  $valido = preg_match('/^[2-3]{1}$/', $valor); break;//l
        case 'dolar':  $valido = preg_match('/^\d{1,5}([.,]\d{1,3})?$/', $valor) || strlen(str_replace([',','.'],'',$valor)) < 4 || strlen(str_replace([',','.'],'',$valor)) > 8; break;
        case 'id_fk':  $valido = preg_match('/^[0-9]+$/', $valor); break;//l
    }

    if (!$valido) { 
        http_response_code(401);
        echo json_encode([
            'respuesta' => 0, 
              'mensaje' => "$mensaje - revisar"
        ]);
        exit;
    }
    return true; // Si es válido, continúa el flujo
}

function validarEntradaSQL($input) {
        // Si es array → validar cada elemento
        if (is_array($input)) {
            foreach ($input as $valor) {
                if (!validarEntradaSQL($valor)) { 
                    return false;
                }
            }
            return true;
        }
        // Convertir a string por seguridad
        $input = (string)$input;

        $blacklist = [
            'SELECT', 'INSERT', 'UPDATE', 'DELETE', 'DROP', 'TRUNCATE', 'ALTER',
            'CREATE', 'RENAME', 'REPLACE', 'UNION', 'JOIN', 'WHERE', 'HAVING',
            'FROM', 'TABLE', 'DATABASE', 'SCHEMA', 'GRANT', 'REVOKE',
            '--', ';', '#', '/*', '*/', '@@', '@', 'CHAR', 'CAST', 'CONVERT',
            'EXEC', 'EXECUTE', 'xp_', 'sp_', 'OR', 'AND'
        ];
      
        foreach ($blacklist as $prohibida) {
            $pattern = '/\b' . preg_quote($prohibida, '/') . '\b/i'; 
            if (preg_match($pattern, $input)) {
                return false;
            }
        }
        return true;
}

function validarTipoDocumento($tipo_documento) {
        $tipos_validos = ['V', 'E','J'];
        return in_array($tipo_documento, $tipos_validos, true);
}

 
function obtenerIP(): string {
    $ip = '127.0.0.1';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Convertir localhost IPv6 a IPv4 para pruebas locales
    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}