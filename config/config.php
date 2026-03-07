<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'FormaFlow By Desiste');
define('APP_URL', 'http://localhost/FormaFlow-By-Desiste');
define('APP_TIMEZONE', 'Europe/Rome');
define('UPLOAD_PATH', __DIR__ . '/../uploads');

date_default_timezone_set(APP_TIMEZONE);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
