<?php
/**
 * Controlador: Perfil
 * Gestión del perfil del usuario
 */

class PerfilController {
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Actualizar información del perfil
     */
    public function actualizar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=perfil');
        }
        
        $usuarioId = getCurrentUserId();
        
        $datos = [
            'nombre' => sanitize($_POST['nombre'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'ubicacion' => sanitize($_POST['ubicacion'] ?? 'Cusco, Perú')
        ];
        
        // Validaciones
        if (empty($datos['nombre']) || empty($datos['email'])) {
            setFlashMessage('Por favor completa todos los campos requeridos', 'error');
            redirect('/index.php?page=perfil');
        }
        
        if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            setFlashMessage('Email inválido', 'error');
            redirect('/index.php?page=perfil');
        }
        
        // Verificar si el email ya existe (excepto el del usuario actual)
        $usuarioExistente = $this->usuarioModel->obtenerPorEmail($datos['email']);
        if ($usuarioExistente && $usuarioExistente['id'] != $usuarioId) {
            setFlashMessage('Este email ya está registrado por otro usuario', 'error');
            redirect('/index.php?page=perfil');
        }
        
        if ($this->usuarioModel->actualizar($usuarioId, $datos)) {
            // Actualizar sesión
            $_SESSION['nombre'] = $datos['nombre'];
            $_SESSION['email'] = $datos['email'];
            
            setFlashMessage('Perfil actualizado correctamente', 'success');
        } else {
            setFlashMessage('Error al actualizar el perfil', 'error');
        }
        
        redirect('/index.php?page=perfil');
    }
    
    /**
     * Cambiar contraseña
     */
    public function cambiarPassword() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=perfil');
        }
        
        $usuarioId = getCurrentUserId();
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordNueva = $_POST['password_nueva'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';
        
        // Validaciones
        if (empty($passwordActual) || empty($passwordNueva) || empty($passwordConfirmar)) {
            setFlashMessage('Por favor completa todos los campos', 'error');
            redirect('/index.php?page=perfil');
        }
        
        if (strlen($passwordNueva) < 6) {
            setFlashMessage('La nueva contraseña debe tener al menos 6 caracteres', 'error');
            redirect('/index.php?page=perfil');
        }
        
        if ($passwordNueva !== $passwordConfirmar) {
            setFlashMessage('Las contraseñas no coinciden', 'error');
            redirect('/index.php?page=perfil');
        }
        
        // Verificar contraseña actual
        $usuario = $this->usuarioModel->obtenerPorId($usuarioId);
        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            setFlashMessage('La contraseña actual es incorrecta', 'error');
            redirect('/index.php?page=perfil');
        }
        
        // Actualizar contraseña
        $sql = "UPDATE usuarios SET password_hash = :password_hash WHERE id = :id";
        $db = Database::getInstance();
        $conn = $db->getConnection();
        $stmt = $conn->prepare($sql);
        
        $passwordHash = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':id', $usuarioId);
        
        if ($stmt->execute()) {
            setFlashMessage('Contraseña cambiada correctamente', 'success');
        } else {
            setFlashMessage('Error al cambiar la contraseña', 'error');
        }
        
        redirect('/index.php?page=perfil');
    }
}
