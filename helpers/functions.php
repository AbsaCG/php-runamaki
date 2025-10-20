<?php
/**
 * Funciones de Ayuda Global
 * Runa Maki
 */

/**
 * Redireccionar a una URL
 */
function redirect($url) {
    header("Location: " . APP_URL . $url);
    exit();
}

/**
 * Sanitizar entrada de datos
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Verificar si el usuario está autenticado
 */
function isAuthenticated() {
    return isset($_SESSION['usuario_id']);
}

/**
 * Verificar si el usuario es administrador
 */
function isAdmin() {
    return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

/**
 * Obtener el ID del usuario actual
 */
function getCurrentUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtener el nombre del usuario actual
 */
function getCurrentUserName() {
    return $_SESSION['nombre'] ?? 'Invitado';
}

/**
 * Requerir autenticación
 */
function requireAuth() {
    if (!isAuthenticated()) {
        redirect('/index.php?page=login');
    }
}

/**
 * Requerir permisos de administrador
 */
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        redirect('/index.php?page=dashboard');
    }
}

/**
 * Generar token CSRF
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Formatear fecha
 */
function formatDate($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('d M Y', $timestamp);
}

/**
 * Calcular tiempo transcurrido
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Hace un momento';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return "Hace {$mins} " . ($mins == 1 ? 'minuto' : 'minutos');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "Hace {$hours} " . ($hours == 1 ? 'hora' : 'horas');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "Hace {$days} " . ($days == 1 ? 'día' : 'días');
    } else {
        return formatDate($datetime);
    }
}

/**
 * Mostrar mensaje flash
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Obtener y limpiar mensaje flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Incluir vista
 */
function view($viewName, $data = []) {
    extract($data);
    require_once ROOT_PATH . "/views/{$viewName}.php";
}

/**
 * Escapar HTML
 */
function e($string) {
    // Asegurar que no se pase null directamente a htmlspecialchars (deprecated en PHP 8.2+)
    if ($string === null) $string = '';
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Debug - imprimir y morir
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}
