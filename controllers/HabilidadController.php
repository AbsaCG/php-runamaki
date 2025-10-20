<?php
/**
 * Controlador: Habilidades
 * CRUD de habilidades del usuario
 */

class HabilidadController {
    private $habilidadModel;
    private $categoriaModel;
    
    public function __construct() {
        $this->habilidadModel = new Habilidad();
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Listar mis habilidades
     */
    public function index() {
        requireAuth();
        
        $usuarioId = getCurrentUserId();
        $habilidades = $this->habilidadModel->listarPorUsuario($usuarioId);
        $categorias = $this->categoriaModel->listarTodas();
        
        $data = [
            'habilidades' => $habilidades,
            'categorias' => $categorias
        ];
        
        view('habilidades/index', $data);
    }
    
    /**
     * Crear nueva habilidad
     */
    public function crear() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=habilidades');
        }
        
        $usuarioId = getCurrentUserId();
        
        $datos = [
            'usuario_id' => $usuarioId,
            'categoria_id' => sanitize($_POST['categoria_id'] ?? ''),
            'titulo' => sanitize($_POST['titulo'] ?? ''),
            'descripcion' => sanitize($_POST['descripcion'] ?? ''),
            'horas_ofrecidas' => (int)($_POST['horas_ofrecidas'] ?? 1),
            'puntos_sugeridos' => (int)($_POST['puntos_sugeridos'] ?? 20),
            'imagen' => null
        ];
        
        // Validaciones
        if (empty($datos['categoria_id']) || empty($datos['titulo']) || empty($datos['descripcion'])) {
            setFlashMessage('Por favor completa todos los campos', 'error');
            redirect('/index.php?page=habilidades');
        }
        
        $habilidadId = $this->habilidadModel->crear($datos);
        
        if ($habilidadId) {
            setFlashMessage('Habilidad creada exitosamente', 'success');
        } else {
            setFlashMessage('Error al crear la habilidad', 'error');
        }
        
        redirect('/index.php?page=habilidades');
    }
    
    /**
     * Actualizar habilidad
     */
    public function actualizar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=habilidades');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        // Verificar que la habilidad pertenezca al usuario
        $habilidad = $this->habilidadModel->obtenerPorId($id);
        
        if (!$habilidad || $habilidad['usuario_id'] != $usuarioId) {
            setFlashMessage('No tienes permiso para editar esta habilidad', 'error');
            redirect('/index.php?page=habilidades');
        }
        
        $datos = [
            'categoria_id' => sanitize($_POST['categoria_id'] ?? ''),
            'titulo' => sanitize($_POST['titulo'] ?? ''),
            'descripcion' => sanitize($_POST['descripcion'] ?? ''),
            'horas_ofrecidas' => (int)($_POST['horas_ofrecidas'] ?? 1),
            'puntos_sugeridos' => (int)($_POST['puntos_sugeridos'] ?? 20)
        ];
        
        if ($this->habilidadModel->actualizar($id, $datos)) {
            setFlashMessage('Habilidad actualizada exitosamente', 'success');
        } else {
            setFlashMessage('Error al actualizar la habilidad', 'error');
        }
        
        redirect('/index.php?page=habilidades');
    }
    
    /**
     * Eliminar habilidad
     */
    public function eliminar() {
        requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?page=habilidades');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $usuarioId = getCurrentUserId();
        
        if ($this->habilidadModel->eliminar($id, $usuarioId)) {
            setFlashMessage('Habilidad eliminada exitosamente', 'success');
        } else {
            setFlashMessage('Error al eliminar la habilidad', 'error');
        }
        
        redirect('/index.php?page=habilidades');
    }
    
    /**
     * Buscar habilidades
     */
    public function buscar() {
        requireAuth();
        
        $categoriaId = $_GET['categoria'] ?? null;
        $busqueda = $_GET['q'] ?? null;
        
        $filtros = [];
        if ($categoriaId) {
            $filtros['categoria_id'] = $categoriaId;
        }
        if ($busqueda) {
            $filtros['busqueda'] = $busqueda;
        }
        
        $habilidades = $this->habilidadModel->listarTodas($filtros);
        $categorias = $this->categoriaModel->listarTodas();
        
        $data = [
            'habilidades' => $habilidades,
            'categorias' => $categorias,
            'filtro_categoria' => $categoriaId,
            'filtro_busqueda' => $busqueda
        ];
        
        view('habilidades/buscar', $data);
    }
    
    /**
     * Ver detalle de habilidad
     */
    public function detalle() {
        requireAuth();
        
        $id = (int)($_GET['id'] ?? 0);
        $habilidad = $this->habilidadModel->obtenerPorId($id);
        
        if (!$habilidad) {
            setFlashMessage('Habilidad no encontrada', 'error');
            redirect('/index.php?page=dashboard');
        }
        
        // Incrementar visitas
        $this->habilidadModel->incrementarVisitas($id);
        // Obtener datos relacionados para la vista
        $usuarioModel = new Usuario();
        $categoriaModel = new Categoria();

        $usuario = $usuarioModel->obtenerPorId($habilidad['usuario_id']);
        $categoria = $categoriaModel->obtenerPorId($habilidad['categoria_id']);

        if ($usuario === false || $usuario === null) {
            $usuario = ['nombre' => 'Usuario'];
        }
        if ($categoria === false || $categoria === null) {
            $categoria = ['nombre' => 'General'];
        }

        // Obtener las habilidades del usuario actual (para ofrecer en el trueque)
        $mis_habilidades = [];
        $currentUserId = getCurrentUserId();
        if (isAuthenticated() && $currentUserId !== 'guest') {
            $mis_habilidades = $this->habilidadModel->listarPorUsuario($currentUserId);
        }

        $data = [
            'habilidad' => $habilidad,
            'usuario' => $usuario,
            'categoria' => $categoria,
            'mis_habilidades' => $mis_habilidades
        ];
        view('habilidades/detalle', $data);
    }
}
