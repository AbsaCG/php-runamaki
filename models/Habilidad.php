<?php
/**
 * Modelo: Habilidad
 * Gestión de habilidades y servicios ofrecidos
 */

class Habilidad {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Crear nueva habilidad
     */
    public function crear($datos) {
        $sql = "INSERT INTO habilidades 
                (usuario_id, categoria_id, titulo, descripcion, horas_ofrecidas, puntos_sugeridos, imagen) 
                VALUES (:usuario_id, :categoria_id, :titulo, :descripcion, :horas_ofrecidas, :puntos_sugeridos, :imagen)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':usuario_id', $datos['usuario_id']);
        $stmt->bindParam(':categoria_id', $datos['categoria_id']);
        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':horas_ofrecidas', $datos['horas_ofrecidas']);
        $stmt->bindParam(':puntos_sugeridos', $datos['puntos_sugeridos']);
        $stmt->bindParam(':imagen', $datos['imagen']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener habilidad por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT h.*, c.nombre as categoria_nombre, c.icono as categoria_icono,
                u.nombre as usuario_nombre, u.reputacion as usuario_reputacion
                FROM habilidades h
                INNER JOIN categorias c ON h.categoria_id = c.id
                INNER JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Listar habilidades
     */
    public function listarTodas($filtros = [], $limit = 20, $offset = 0) {
        $sql = "SELECT h.*, c.nombre as categoria_nombre, c.icono as categoria_icono,
                u.nombre as usuario_nombre, u.reputacion as usuario_reputacion, u.avatar
                FROM habilidades h
                INNER JOIN categorias c ON h.categoria_id = c.id
                INNER JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.estado = 'aprobado'";
        
        $params = [];
        
        // Filtros
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND h.categoria_id = :categoria_id";
            $params[':categoria_id'] = $filtros['categoria_id'];
        }
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND h.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (h.titulo LIKE :busqueda OR h.descripcion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }
        
        $sql .= " ORDER BY h.fecha_creacion DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Listar habilidades del usuario
     */
    public function listarPorUsuario($usuarioId) {
        $sql = "SELECT h.*, c.nombre as categoria_nombre, c.icono as categoria_icono
                FROM habilidades h
                INNER JOIN categorias c ON h.categoria_id = c.id
                WHERE h.usuario_id = :usuario_id
                ORDER BY h.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Actualizar habilidad
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE habilidades SET 
                categoria_id = :categoria_id,
                titulo = :titulo,
                descripcion = :descripcion,
                horas_ofrecidas = :horas_ofrecidas,
                puntos_sugeridos = :puntos_sugeridos
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':categoria_id', $datos['categoria_id']);
        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':horas_ofrecidas', $datos['horas_ofrecidas']);
        $stmt->bindParam(':puntos_sugeridos', $datos['puntos_sugeridos']);
        
        return $stmt->execute();
    }
    
    /**
     * Eliminar habilidad
     */
    public function eliminar($id, $usuarioId) {
        // Verificar que la habilidad pertenezca al usuario
        $sql = "DELETE FROM habilidades WHERE id = :id AND usuario_id = :usuario_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuarioId);
        
        return $stmt->execute();
    }
    
    /**
     * Cambiar estado de habilidad
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE habilidades SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Incrementar visitas
     */
    public function incrementarVisitas($id) {
        $sql = "UPDATE habilidades SET visitas = visitas + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener habilidades destacadas
     */
    public function obtenerDestacadas($limit = 8) {
        $sql = "SELECT h.*, c.nombre as categoria_nombre, c.icono as categoria_icono,
                u.nombre as usuario_nombre, u.reputacion as usuario_reputacion, u.avatar
                FROM habilidades h
                INNER JOIN categorias c ON h.categoria_id = c.id
                INNER JOIN usuarios u ON h.usuario_id = u.id
                WHERE h.estado = 'aprobado'
                ORDER BY h.visitas DESC, h.fecha_creacion DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Contar habilidades
     */
    public function contar($filtros = []) {
        $sql = "SELECT COUNT(*) as total FROM habilidades WHERE estado = 'aprobado'";
        
        $params = [];
        
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND categoria_id = :categoria_id";
            $params[':categoria_id'] = $filtros['categoria_id'];
        }
        
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $resultado = $stmt->fetch();
        
        return $resultado['total'] ?? 0;
    }
}
