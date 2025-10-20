<?php
/**
 * Controlador: Autenticación
 * Gestión de login, registro y sesiones
 */

class AuthController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin() {
        if (isAuthenticated()) {
            redirect('/index.php?page=dashboard');
        }
        
        view('auth/login');
    }
    
    /**
     * Procesar login
     */
    public function procesarLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=login');
        }
        
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            setFlashMessage('Por favor completa todos los campos', 'error');
            redirect('/index.php?page=login');
        }
        
        $usuario = $this->usuarioModel->autenticar($email, $password);
        
        if ($usuario) {
            // Crear sesión
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['puntos_runa'] = $usuario['puntos_runa'];
            $_SESSION['reputacion'] = $usuario['reputacion'];
            
            setFlashMessage('¡Bienvenido de vuelta, ' . $usuario['nombre'] . '!', 'success');
            redirect('/index.php?page=dashboard');
        } else {
            setFlashMessage('Credenciales incorrectas', 'error');
            redirect('/index.php?page=login');
        }
    }
    
    /**
     * Mostrar formulario de registro
     */
    public function mostrarRegistro() {
        if (isAuthenticated()) {
            redirect('/index.php?page=dashboard');
        }
        
        view('auth/register');
    }
    
    /**
     * Procesar registro
     */
    public function procesarRegistro() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=register');
        }
        
        $nombre = sanitize($_POST['nombre'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $nivel = sanitize($_POST['nivel'] ?? 'Principiante');
        
        // Validaciones
        if (empty($nombre) || empty($email) || empty($password)) {
            setFlashMessage('Por favor completa todos los campos', 'error');
            redirect('/index.php?page=register');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('Email inválido', 'error');
            redirect('/index.php?page=register');
        }
        
        if (strlen($password) < 6) {
            setFlashMessage('La contraseña debe tener al menos 6 caracteres', 'error');
            redirect('/index.php?page=register');
        }
        
        // Verificar si el email ya existe
        if ($this->usuarioModel->obtenerPorEmail($email)) {
            setFlashMessage('Este email ya está registrado', 'error');
            redirect('/index.php?page=register');
        }
        
        // Crear usuario
        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password,
            'nivel' => $nivel
        ];
        
        $usuarioId = $this->usuarioModel->crear($datos);
        
        if ($usuarioId) {
            // Autenticar automáticamente
            $usuario = $this->usuarioModel->obtenerPorId($usuarioId);
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['puntos_runa'] = $usuario['puntos_runa'];
            $_SESSION['reputacion'] = $usuario['reputacion'];
            
            setFlashMessage('¡Cuenta creada exitosamente! Bienvenido a Runa Maki', 'success');
            redirect('/index.php?page=dashboard');
        } else {
            setFlashMessage('Error al crear la cuenta. Intenta nuevamente', 'error');
            redirect('/index.php?page=register');
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_destroy();
        setFlashMessage('Sesión cerrada correctamente', 'success');
        redirect('/index.php');
    }
    
    /**
     * Modo invitado
     */
    public function modoInvitado() {
        $_SESSION['usuario_id'] = 'guest';
        $_SESSION['nombre'] = 'Invitado';
        $_SESSION['email'] = 'invitado@runamaki.com';
        $_SESSION['rol'] = 'usuario';
        $_SESSION['puntos_runa'] = 0;
        $_SESSION['reputacion'] = 0;
        
        redirect('/index.php?page=dashboard');
    }
}
