<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Configurazione base applicazione.
 *
 * APP_URL può essere impostato via variabile ambiente per hosting/proxy,
 * altrimenti viene calcolato automaticamente dalla richiesta HTTP corrente.
 */
define('APP_NAME', 'FormaFlow By Desiste');
define('APP_TIMEZONE', 'Europe/Rome');
define('UPLOAD_PATH', __DIR__ . '/../uploads');

$envAppUrl = getenv('APP_URL');
if (!empty($envAppUrl)) {
    define('APP_URL', rtrim($envAppUrl, '/'));
} else {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    define('APP_URL', $scheme . '://' . $host . ($basePath === '' ? '' : $basePath));
}

date_default_timezone_set(APP_TIMEZONE);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
