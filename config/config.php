<?php
/**
 * Configuración General de la Aplicación
 * Runa Maki - Plataforma de Trueque de Habilidades
 */

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Zona horaria
date_default_timezone_set('America/Lima');

// Constantes de la aplicación
define('APP_NAME', 'Runa Maki');
define('APP_VERSION', '1.0.0');
// NOTE: Adjust APP_URL to match the folder where the app is served from in Apache/XAMPP.
// If you access the app at http://localhost/php-runamaki set APP_URL accordingly.
define('APP_URL', 'http://localhost/php-runamaki');

// Rutas
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Configuración de errores (cambiar en producción)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoload de clases
spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/models/',
        ROOT_PATH . '/controllers/',
        ROOT_PATH . '/config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Funciones de ayuda
require_once ROOT_PATH . '/helpers/functions.php';
