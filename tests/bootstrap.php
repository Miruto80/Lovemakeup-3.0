<?php
/**
 * Bootstrap para la suite PHPUnit de LoveMakeup.
 * Carga el autoload del proyecto y permite redirigir temporalmente el host
 * de la base de datos SIN editar passconfig.env:
 *
 *   $env:AUDIT_DB_HOST = "srvXXX.herosite.pro"
 *   php vendor/phpunit/phpunit/phpunit --configuration tests/phpunit.xml
 *
 * Si la variable no existe, se usan los valores normales del proyecto
 * (passconfig.env). Armour nada se sobreescribe permanente.
 */
require_once __DIR__ . '/../vendor/autoload.php';

$hostAudit = getenv('AUDIT_DB_HOST');
if ($hostAudit && trim($hostAudit) !== '') {
    $_ENV['DB_HOST'] = trim($hostAudit);
}
