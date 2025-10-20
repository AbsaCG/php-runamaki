<?php
/**
 * RUNA MAKI - Plataforma de Trueque de Habilidades
 * Archivo Principal de Enrutamiento
 */

require_once 'config/config.php';

// Obtener página solicitada
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// Enrutador simple
try {
    switch ($page) {
        // Páginas públicas
        case 'home':
        case 'landing':
            view('landing');
            break;
        
        // Autenticación
        case 'login':
            $controller = new AuthController();
            $controller->mostrarLogin();
            break;
        
        case 'login-submit':
            $controller = new AuthController();
            $controller->procesarLogin();
            break;
        
        case 'register':
            $controller = new AuthController();
            $controller->mostrarRegistro();
            break;
        
        case 'register-submit':
            $controller = new AuthController();
            $controller->procesarRegistro();
            break;
        
        case 'logout':
            $controller = new AuthController();
            $controller->logout();
            break;
        
        case 'guest':
            $controller = new AuthController();
            $controller->modoInvitado();
            break;
        
        // Dashboard
        case 'dashboard':
            $controller = new DashboardController();
            $controller->index();
            break;
        
        // Habilidades
        case 'habilidades':
            $controller = new HabilidadController();
            $controller->index();
            break;
        
        case 'habilidad-crear':
            $controller = new HabilidadController();
            $controller->crear();
            break;
        
        case 'habilidad-actualizar':
            $controller = new HabilidadController();
            $controller->actualizar();
            break;
        
        case 'habilidad-eliminar':
            $controller = new HabilidadController();
            $controller->eliminar();
            break;
        
        case 'habilidad-detalle':
            $controller = new HabilidadController();
            $controller->detalle();
            break;
        
        case 'buscar':
            $controller = new HabilidadController();
            $controller->buscar();
            break;
        
        // Trueques
        case 'trueques':
            $controller = new TruequeController();
            $controller->index();
            break;
        
        case 'trueque-detalle':
            $controller = new TruequeController();
            $controller->detalle();
            break;
        
        case 'trueque-crear':
            $controller = new TruequeController();
            $controller->crear();
            break;
        
        case 'trueque-aceptar':
            $controller = new TruequeController();
            $controller->aceptar();
            break;
        
        case 'trueque-completar':
            $controller = new TruequeController();
            $controller->completar();
            break;
        
        case 'trueque-rechazar':
            $controller = new TruequeController();
            $controller->rechazar();
            break;
        
        case 'mensaje-enviar':
            $controller = new TruequeController();
            $controller->enviarMensaje();
            break;
        
        // Perfil
        case 'perfil':
            requireAuth();
            view('perfil/index');
            break;
        
        case 'perfil-actualizar':
            $controller = new PerfilController();
            $controller->actualizar();
            break;
        
        case 'perfil-cambiar-password':
            $controller = new PerfilController();
            $controller->cambiarPassword();
            break;
        
        // Admin
        case 'admin':
            requireAdmin();
            view('admin/index');
            break;
        
        // 404
        default:
            http_response_code(404);
            view('errors/404');
            break;
    }
    // Manejo de excepciones (errores del sistema)
} catch (Exception $e) {
    if (DEBUG_MODE) {
        die("Error: " . $e->getMessage());
    } else {
        // Muestra página genérica de error 500
        view('errors/500');
    }
}
