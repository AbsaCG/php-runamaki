<?php
/**
 * Modelo: Logro
 * Gestión de logros y asignación a usuarios
 */

class Logro {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Obtener todos los logros definidos en la plataforma
     */
    public function listarTodos() {
        $sql = "SELECT * FROM logros ORDER BY requisito_tipo, requisito_valor";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener un logro por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM logros WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Obtener logros obtenidos por un usuario
     */
    public function obtenerPorUsuario($usuarioId) {
        $sql = "SELECT ul.*, l.nombre, l.descripcion, l.icono, l.requisito_tipo, l.requisito_valor
                FROM usuarios_logros ul
                INNER JOIN logros l ON ul.logro_id = l.id
                WHERE ul.usuario_id = :usuario_id
                ORDER BY ul.fecha_obtencion DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Asignar un logro a un usuario (si no existe)
     */
    public function asignarAUsuario($usuarioId, $logroId) {
        // Evitar duplicados por la constraint UNIQUE
        $sql = "INSERT INTO usuarios_logros (usuario_id, logro_id) VALUES (:usuario_id, :logro_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->bindParam(':logro_id', $logroId);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Si ya existe (clave única), retornar true para indicar que "está asignado"
            return false;
        }
    }

    /**
     * Eliminar logro asignado (admin)
     */
    public function eliminarAsignacion($id) {
        $sql = "DELETE FROM usuarios_logros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
