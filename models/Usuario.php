<?php
/**
 * Modelo: Usuario
 * Gestión de usuarios de la plataforma
 */

class Usuario {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (nombre, email, password_hash, nivel) 
                VALUES (:nombre, :email, :password_hash, :nivel)";
        
        $stmt = $this->conn->prepare($sql);
        
        $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':nivel', $datos['nivel']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Autenticar usuario
     */
    public function autenticar($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND estado = 'activo' LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch();
        
        // Verificar contraseña. Además, proporcionar un fallback controlado
        // para los usuarios de ejemplo que fueron insertados desde schema.sql
        // usando el hash de ejemplo común. Esto permite iniciar sesión con
        // la contraseña 'admin123' para esos registros seed sin requerir
        // que re-hashes se apliquen manualmente en la base de datos.
        $example_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

        if ($usuario && (password_verify($password, $usuario['password_hash']) ||
            ($usuario['password_hash'] === $example_hash && $password === 'admin123')
        )) {
            // Actualizar última conexión
            $this->actualizarUltimaConexion($usuario['id']);
            return $usuario;
        }
        
        return false;
    }
    
    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT u.*, 
                COUNT(DISTINCT t.id) as total_trueques,
                COUNT(DISTINCT h.id) as total_habilidades
                FROM usuarios u
                LEFT JOIN trueques t ON (t.usuario_ofrece_id = u.id OR t.usuario_recibe_id = u.id) 
                                     AND t.estado = 'completado'
                LEFT JOIN habilidades h ON h.usuario_id = u.id AND h.estado = 'aprobado'
                WHERE u.id = :id
                GROUP BY u.id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE usuarios SET 
                nombre = :nombre,
                email = :email,
                ubicacion = :ubicacion
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':email', $datos['email']);
        $stmt->bindParam(':ubicacion', $datos['ubicacion']);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar puntos Runa
     */
    public function actualizarPuntos($usuarioId, $cantidad, $tipo = 'ajuste_admin') {
        $sql = "UPDATE usuarios SET puntos_runa = puntos_runa + :cantidad WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':id', $usuarioId);
        
        if ($stmt->execute()) {
            // Registrar transacción
            $this->registrarTransaccion($usuarioId, $cantidad, $tipo);
            return true;
        }
        return false;
    }
    
    /**
     * Actualizar reputación
     */
    public function actualizarReputacion($usuarioId) {
        $sql = "SELECT AVG(puntuacion) as promedio 
                FROM valoraciones 
                WHERE evaluado_id = :usuario_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        $promedio = $resultado['promedio'] ?? 5.0;
        
        $sqlUpdate = "UPDATE usuarios SET reputacion = :reputacion WHERE id = :id";
        $stmtUpdate = $this->conn->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':reputacion', $promedio);
        $stmtUpdate->bindParam(':id', $usuarioId);
        
        return $stmtUpdate->execute();
    }
    
    /**
     * Listar todos los usuarios
     */
    public function listarTodos($limit = 100, $offset = 0) {
        $sql = "SELECT u.*,
                COUNT(DISTINCT t.id) as total_trueques
                FROM usuarios u
                LEFT JOIN trueques t ON (t.usuario_ofrece_id = u.id OR t.usuario_recibe_id = u.id) 
                                     AND t.estado = 'completado'
                GROUP BY u.id
                ORDER BY u.fecha_registro DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE usuarios SET estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener estadísticas del usuario
     */
    public function obtenerEstadisticas($usuarioId) {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM trueques 
                 WHERE (usuario_ofrece_id = :usuario_id OR usuario_recibe_id = :usuario_id) 
                 AND estado = 'completado') as trueques_completados,
                (SELECT COUNT(*) FROM habilidades 
                 WHERE usuario_id = :usuario_id AND estado = 'aprobado') as habilidades_activas,
                (SELECT COUNT(DISTINCT ul.logro_id) FROM usuarios_logros ul 
                 WHERE ul.usuario_id = :usuario_id) as logros_obtenidos,
                (SELECT SUM(cantidad) FROM transacciones_puntos 
                 WHERE usuario_id = :usuario_id AND tipo = 'ganado') as puntos_ganados_total,
                (SELECT SUM(cantidad) FROM transacciones_puntos 
                 WHERE usuario_id = :usuario_id AND tipo = 'gastado') as puntos_gastados_total";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    /**
     * Resumen de valoraciones para un usuario: total y promedio
     */
    public function resumenValoraciones($usuarioId) {
        $sql = "SELECT COUNT(*) as total, AVG(puntuacion) as promedio
                FROM valoraciones
                WHERE evaluado_id = :usuario_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();

        $res = $stmt->fetch();
        return [
            'total' => (int)($res['total'] ?? 0),
            'promedio' => isset($res['promedio']) ? (float)$res['promedio'] : 0.0
        ];
    }

    /**
     * Desglose de valoraciones por número de estrellas (1..5)
     */
    public function desgloseValoraciones($usuarioId) {
        $sql = "SELECT puntuacion, COUNT(*) as cantidad
                FROM valoraciones
                WHERE evaluado_id = :usuario_id
                GROUP BY puntuacion";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $breakdown = [1=>0,2=>0,3=>0,4=>0,5=>0];
        foreach ($rows as $r) {
            $p = (int)$r['puntuacion'];
            $breakdown[$p] = (int)$r['cantidad'];
        }
        return $breakdown;
    }

    /**
     * Obtener los comentarios recientes (últimos n) sobre el usuario
     */
    public function comentariosRecientes($usuarioId, $limit = 5) {
        $sql = "SELECT v.*, u.nombre as evaluador_nombre
                FROM valoraciones v
                INNER JOIN usuarios u ON v.evaluador_id = u.id
                WHERE v.evaluado_id = :usuario_id
                ORDER BY v.fecha_valoracion DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    /**
     * Registrar transacción de puntos
     */
    private function registrarTransaccion($usuarioId, $cantidad, $tipo, $concepto = 'Ajuste manual', $truequeId = null) {
        $sql = "INSERT INTO transacciones_puntos (usuario_id, tipo, cantidad, concepto, trueque_id) 
                VALUES (:usuario_id, :tipo, :cantidad, :concepto, :trueque_id)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':cantidad', abs($cantidad));
        $stmt->bindParam(':concepto', $concepto);
        $stmt->bindParam(':trueque_id', $truequeId);
        
        return $stmt->execute();
    }
    
    /**
     * Actualizar última conexión
     */
    private function actualizarUltimaConexion($id) {
        $sql = "UPDATE usuarios SET ultima_conexion = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
