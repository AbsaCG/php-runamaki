<?php
/**
 * Controlador: Dashboard
 * Panel principal del usuario
 */

class DashboardController {
    private $usuarioModel;
    private $habilidadModel;
    private $truequeModel;
    private $categoriaModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->habilidadModel = new Habilidad();
        $this->truequeModel = new Trueque();
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Mostrar dashboard principal
     */
    public function index() {
        // requireAuth en este proyecto fuerza login; permitir modo invitado 'guest'
        // si no hay usuario autenticado el helper requireAuth redirige, pero el modo
        // invitado establece session usuario_id = 'guest' en AuthController::modoInvitado().
        requireAuth();

        $usuarioId = getCurrentUserId();

        // Obtener datos del usuario. Algunos métodos pueden devolver false si no se
        // encuentra el registro (por ejemplo usuario 'guest'), así que normalizamos.
        $usuario = $this->usuarioModel->obtenerPorId($usuarioId);
        if ($usuario === false || $usuario === null) {
            // Datos por defecto para invitados
            $usuario = [
                'id' => $usuarioId,
                'nombre' => 'Invitado',
                'email' => 'invitado@runamaki.com',
                'puntos_runa' => 0,
                'reputacion' => 0.0
            ];
        }

        $estadisticas = $this->usuarioModel->obtenerEstadisticas($usuarioId);
        if ($estadisticas === false || $estadisticas === null) {
            $estadisticas = [
                'trueques_completados' => 0,
                'habilidades_activas' => 0,
                'logros_obtenidos' => 0,
                'puntos_ganados_total' => 0,
                'puntos_gastados_total' => 0
            ];
        }
        
        // Obtener ofertas destacadas
        $ofertasDestacadas = $this->habilidadModel->obtenerDestacadas(8);
        
        // Obtener trueques recientes
        $truequesRecientes = $this->truequeModel->listarPorUsuario($usuarioId);
        $truequesRecientes = array_slice($truequesRecientes, 0, 5);
        
        // Contar trueques por estado
        $truequesPendientes = $this->truequeModel->contarPorEstado($usuarioId, 'pendiente');
        $truequesActivos = $this->truequeModel->contarPorEstado($usuarioId, 'aceptado');
        $truequesCompletados = $this->truequeModel->contarPorEstado($usuarioId, 'completado');
        
        // Obtener categorías
        $categorias = $this->categoriaModel->listarTodas();
        
        $data = [
            'usuario' => $usuario,
            'estadisticas' => $estadisticas,
            'ofertas_destacadas' => $ofertasDestacadas,
            'trueques_recientes' => $truequesRecientes,
            'trueques_pendientes' => $truequesPendientes,
            'trueques_activos' => $truequesActivos,
            'trueques_completados' => $truequesCompletados,
            'categorias' => $categorias
        ];
        
        view('dashboard/index', $data);
    }
    
    /**
     * Actualizar sesión con datos frescos
     */
    private function actualizarSesion($usuario) {
        $_SESSION['puntos_runa'] = $usuario['puntos_runa'];
        $_SESSION['reputacion'] = $usuario['reputacion'];
    }
}
