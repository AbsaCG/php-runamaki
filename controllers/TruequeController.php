<?php
/**
 * Controlador: Trueques
 * Gestión de intercambios de servicios
 */

class TruequeController {
    private $truequeModel;
    
    public function __construct() {
        $this->truequeModel = new Trueque();
    }
    
    /**
     * Listar mis trueques
     */
    public function index() {
        requireAuth();
        
        $usuarioId = getCurrentUserId();
        $estado = $_GET['estado'] ?? null;
        
        $trueques = $this->truequeModel->listarPorUsuario($usuarioId, $estado);
        
        $data = [
            'trueques' => $trueques,
            'filtro_estado' => $estado
        ];
        
        view('trueques/index', $data);
    }
    
    /**
     * Ver detalle del trueque
     */
    public function detalle() {
        requireAuth();
        
        $id = (int)($_GET['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        $trueque = $this->truequeModel->obtenerPorId($id);
        
        if (!$trueque || 
            ($trueque['usuario_ofrece_id'] != $usuarioId && $trueque['usuario_recibe_id'] != $usuarioId)) {
            setFlashMessage('Trueque no encontrado', 'error');
            redirect('/index.php?page=trueques');
        }
        
        // Obtener mensajes
        $mensajes = $this->truequeModel->obtenerMensajes($id);
        // Verificar si el usuario ya valoró este trueque
        $yaValoro = false;
        if ($trueque['estado'] === 'completado') {
            $existing = $this->truequeModel->obtenerValoracionPorTruequeYEvaluador($id, $usuarioId);
            $yaValoro = (bool)$existing;
        }

        $data = [
            'trueque' => $trueque,
            'mensajes' => $mensajes,
            'es_mi_turno' => $trueque['usuario_recibe_id'] == $usuarioId,
            'ya_valoro' => $yaValoro
        ];
        
        view('trueques/detalle', $data);
    }
    
    /**
     * Crear nuevo trueque
     */
    public function crear() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=dashboard');
        }
        
        $usuarioId = getCurrentUserId();
        
        $datos = [
            'usuario_ofrece_id' => (int)($_POST['usuario_ofrece_id'] ?? 0),
            'usuario_recibe_id' => (int)($_POST['usuario_recibe_id'] ?? 0),
            'habilidad_ofrece_id' => (int)($_POST['habilidad_ofrece_id'] ?? 0),
            'habilidad_recibe_id' => (int)($_POST['habilidad_recibe_id'] ?? 0),
            'puntos_intercambio' => (int)($_POST['puntos_intercambio'] ?? 0)
        ];
        $mensajeInicial = sanitize($_POST['mensaje_inicial'] ?? '');
        
        // Validar que el usuario sea parte del trueque
        if ($datos['usuario_ofrece_id'] != $usuarioId && $datos['usuario_recibe_id'] != $usuarioId) {
            setFlashMessage('Error al crear el trueque', 'error');
            redirect('/index.php?page=dashboard');
        }

        // Validar que la habilidad que ofrece pertenezca al usuario
        $habilidadOfreceId = (int)$datos['habilidad_ofrece_id'];
        if ($habilidadOfreceId <= 0) {
            setFlashMessage('Debes seleccionar la habilidad que ofreces', 'error');
            redirect('/index.php?page=habilidad-detalle&id=' . ($datos['habilidad_recibe_id'] ?? 0));
        }

        $habilidadModel = new Habilidad();
        $miHabilidad = $habilidadModel->obtenerPorId($habilidadOfreceId);
        if (!$miHabilidad || $miHabilidad['usuario_id'] != $usuarioId) {
            setFlashMessage('La habilidad seleccionada no es válida', 'error');
            redirect('/index.php?page=habilidad-detalle&id=' . ($datos['habilidad_recibe_id'] ?? 0));
        }
        
        $truequeId = $this->truequeModel->crear($datos);
        
        if ($truequeId) {
            // Si se envió un mensaje inicial, guárdalo asociado al trueque
            if (!empty($mensajeInicial)) {
                $this->truequeModel->enviarMensaje($truequeId, $usuarioId, $mensajeInicial);
            }

            setFlashMessage('Propuesta de trueque enviada', 'success');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        } else {
            setFlashMessage('Error al crear el trueque', 'error');
            redirect('/index.php?page=dashboard');
        }
    }
    
    /**
     * Aceptar trueque
     */
    public function aceptar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=trueques');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        if ($this->truequeModel->aceptar($id, $usuarioId)) {
            setFlashMessage('Trueque aceptado exitosamente', 'success');
        } else {
            setFlashMessage('Error al aceptar el trueque', 'error');
        }
        
        redirect('/index.php?page=trueque-detalle&id=' . $id);
    }
    
    /**
     * Completar trueque
     */
    public function completar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=trueques');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        if ($this->truequeModel->completar($id, $usuarioId)) {
            setFlashMessage('¡Trueque completado! Puntos Runa agregados', 'success');
            
            // Actualizar puntos en sesión
            $usuarioModel = new Usuario();
            $usuario = $usuarioModel->obtenerPorId($usuarioId);
            $_SESSION['puntos_runa'] = $usuario['puntos_runa'];
        } else {
            setFlashMessage('Error al completar el trueque', 'error');
        }
        
        redirect('/index.php?page=trueque-detalle&id=' . $id);
    }
    
    /**
     * Rechazar trueque
     */
    public function rechazar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=trueques');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        if ($this->truequeModel->rechazar($id, $usuarioId)) {
            setFlashMessage('Trueque rechazado', 'success');
        } else {
            setFlashMessage('Error al rechazar el trueque', 'error');
        }
        
        redirect('/index.php?page=trueques');
    }

    /**
     * Valorar un trueque (puntaje al evaluado)
     */
    public function valorar() {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=trueques');
        }

        $truequeId = (int)($_POST['trueque_id'] ?? 0);
        $puntuacion = (int)($_POST['puntuacion'] ?? 0);
        $comentario = sanitize($_POST['comentario'] ?? '');
        $usuarioId = getCurrentUserId();

        $trueque = $this->truequeModel->obtenerPorId($truequeId);
        if (!$trueque || $trueque['estado'] !== 'completado') {
            setFlashMessage('No se puede valorar este trueque', 'error');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        }

        // Determinar a quién se va a evaluar
        $evaluadoId = null;
        if ($trueque['usuario_ofrece_id'] == $usuarioId) {
            $evaluadoId = $trueque['usuario_recibe_id'];
        } elseif ($trueque['usuario_recibe_id'] == $usuarioId) {
            $evaluadoId = $trueque['usuario_ofrece_id'];
        } else {
            setFlashMessage('No eres parte de este trueque', 'error');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        }

        if ($puntuacion < 1 || $puntuacion > 5) {
            setFlashMessage('Puntuación inválida', 'error');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        }

        // Verificar que no exista ya una valoración del mismo evaluador
        $existing = $this->truequeModel->obtenerValoracionPorTruequeYEvaluador($truequeId, $usuarioId);
        if ($existing) {
            setFlashMessage('Ya has valorado este trueque', 'info');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        }

        if ($this->truequeModel->crearValoracion($truequeId, $usuarioId, $evaluadoId, $puntuacion, $comentario)) {
            // Actualizar reputación promedio en usuarios
            $usuarioModel = new Usuario();
            $usuarioModel->actualizarReputacion($evaluadoId);

            setFlashMessage('Gracias por valorar', 'success');
        } else {
            setFlashMessage('Error al guardar la valoración', 'error');
        }

        redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
    }
    
    /**
     * Enviar mensaje
     */
    public function enviarMensaje() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=trueques');
        }
        
        $truequeId = (int)($_POST['trueque_id'] ?? 0);
        $mensaje = sanitize($_POST['mensaje'] ?? '');
        $usuarioId = getCurrentUserId();
        
        if (empty($mensaje)) {
            setFlashMessage('El mensaje no puede estar vacío', 'error');
            redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
        }
        
        if ($this->truequeModel->enviarMensaje($truequeId, $usuarioId, $mensaje)) {
            setFlashMessage('Mensaje enviado', 'success');
        } else {
            setFlashMessage('Error al enviar el mensaje', 'error');
        }
        
        redirect('/index.php?page=trueque-detalle&id=' . $truequeId);
    }
}
