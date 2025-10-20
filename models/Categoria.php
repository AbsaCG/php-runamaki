<?php
/**
 * Modelo: Categoria
 * Gestión de categorías de habilidades
 */

class Categoria {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Listar todas las categorías activas
     */
    public function listarTodas() {
        $sql = "SELECT c.*, COUNT(h.id) as total_habilidades
                FROM categorias c
                LEFT JOIN habilidades h ON c.id = h.categoria_id AND h.estado = 'aprobado'
                WHERE c.activo = TRUE
                GROUP BY c.id
                ORDER BY c.nombre ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtener categoría por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Crear categoría
     */
    public function crear($datos) {
        $sql = "INSERT INTO categorias (nombre, descripcion, icono, color) 
                VALUES (:nombre, :descripcion, :icono, :color)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':icono', $datos['icono']);
        $stmt->bindParam(':color', $datos['color']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
}
