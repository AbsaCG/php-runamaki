<?php
/**
 * Modelo: Trueque
 * Gestión de intercambios de servicios
 */

class Trueque {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Crear nuevo trueque
     */
    public function crear($datos) {
        $sql = "INSERT INTO trueques 
                (usuario_ofrece_id, usuario_recibe_id, habilidad_ofrece_id, habilidad_recibe_id, puntos_intercambio) 
                VALUES (:usuario_ofrece_id, :usuario_recibe_id, :habilidad_ofrece_id, :habilidad_recibe_id, :puntos_intercambio)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindParam(':usuario_ofrece_id', $datos['usuario_ofrece_id']);
        $stmt->bindParam(':usuario_recibe_id', $datos['usuario_recibe_id']);
        $stmt->bindParam(':habilidad_ofrece_id', $datos['habilidad_ofrece_id']);
        $stmt->bindParam(':habilidad_recibe_id', $datos['habilidad_recibe_id']);
        $stmt->bindParam(':puntos_intercambio', $datos['puntos_intercambio']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtener trueque por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT t.*,
                u1.nombre as usuario_ofrece_nombre, u1.avatar as usuario_ofrece_avatar, u1.reputacion as usuario_ofrece_reputacion,
                u2.nombre as usuario_recibe_nombre, u2.avatar as usuario_recibe_avatar, u2.reputacion as usuario_recibe_reputacion,
                h1.titulo as habilidad_ofrece_titulo,
                h2.titulo as habilidad_recibe_titulo
                FROM trueques t
                INNER JOIN usuarios u1 ON t.usuario_ofrece_id = u1.id
                INNER JOIN usuarios u2 ON t.usuario_recibe_id = u2.id
                INNER JOIN habilidades h1 ON t.habilidad_ofrece_id = h1.id
                INNER JOIN habilidades h2 ON t.habilidad_recibe_id = h2.id
                WHERE t.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Listar trueques del usuario
     */
    public function listarPorUsuario($usuarioId, $estado = null) {
        $sql = "SELECT t.*,
                CASE 
                    WHEN t.usuario_ofrece_id = :usuario_id THEN u2.nombre
                    ELSE u1.nombre
                END as partner_nombre,
                CASE 
                    WHEN t.usuario_ofrece_id = :usuario_id THEN u2.avatar
                    ELSE u1.avatar
                END as partner_avatar,
                CASE 
                    WHEN t.usuario_ofrece_id = :usuario_id THEN u2.reputacion
                    ELSE u1.reputacion
                END as partner_reputacion,
                CASE 
                    WHEN t.usuario_ofrece_id = :usuario_id THEN h1.titulo
                    ELSE h2.titulo
                END as mi_servicio,
                CASE 
                    WHEN t.usuario_ofrece_id = :usuario_id THEN h2.titulo
                    ELSE h1.titulo
                END as su_servicio
                FROM trueques t
                INNER JOIN usuarios u1 ON t.usuario_ofrece_id = u1.id
                INNER JOIN usuarios u2 ON t.usuario_recibe_id = u2.id
                INNER JOIN habilidades h1 ON t.habilidad_ofrece_id = h1.id
                INNER JOIN habilidades h2 ON t.habilidad_recibe_id = h2.id
                WHERE (t.usuario_ofrece_id = :usuario_id OR t.usuario_recibe_id = :usuario_id)";
        
        if ($estado) {
            $sql .= " AND t.estado = :estado";
        }
        
        $sql .= " ORDER BY t.fecha_creacion DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        
        if ($estado) {
            $stmt->bindParam(':estado', $estado);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Aceptar trueque
     */
    public function aceptar($id, $usuarioId) {
        // Verificar que el usuario sea parte del trueque
        $trueque = $this->obtenerPorId($id);
        
        if (!$trueque || 
            ($trueque['usuario_ofrece_id'] != $usuarioId && $trueque['usuario_recibe_id'] != $usuarioId)) {
            return false;
        }
        
        $sql = "UPDATE trueques SET estado = 'aceptado', fecha_aceptacion = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Completar trueque
     */
    public function completar($id, $usuarioId) {
        try {
            $this->conn->beginTransaction();
            
            // Obtener trueque
            $trueque = $this->obtenerPorId($id);
            
            if (!$trueque || $trueque['estado'] !== 'aceptado') {
                $this->conn->rollBack();
                return false;
            }
            
            // Actualizar estado del trueque
            $sql = "UPDATE trueques SET estado = 'completado', fecha_completado = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            // Transferir puntos
            $puntos = $trueque['puntos_intercambio'];
            
            // Usuario que ofrece gana puntos
            $sqlPuntos1 = "UPDATE usuarios SET puntos_runa = puntos_runa + :puntos WHERE id = :id";
            $stmt1 = $this->conn->prepare($sqlPuntos1);
            $stmt1->bindParam(':puntos', $puntos);
            $stmt1->bindParam(':id', $trueque['usuario_ofrece_id']);
            $stmt1->execute();
            
            // Registrar transacción
            $this->registrarTransaccion($trueque['usuario_ofrece_id'], $puntos, 'ganado', 'Trueque completado', $id);
            
            $this->conn->commit();
            // Después de completar, asignar logros automáticos al usuario que ofreció
            try {
                require_once ROOT_PATH . '/models/Logro.php';
                require_once ROOT_PATH . '/models/Usuario.php';

                $logroModel = new Logro();
                $usuarioModel = new Usuario();

                // Obtener estadisticas actualizadas
                $estad = $usuarioModel->obtenerEstadisticas($trueque['usuario_ofrece_id']);
                $totalCompleted = (int)($estad['trueques_completados'] ?? 0);

                // Mapeo de hitos a ids de logros ya definidos en schema.sql
                // Suponemos que los logros fueron insertados en el schema y sus ids están en el orden:
                // 1 = Primer Trueque, 2 = 10 Trueques, 3 = 50 Trueques, 4 = 100 Trueques
                $hitos = [1 => 1, 10 => 2, 50 => 3, 100 => 4];

                foreach ($hitos as $need => $logroId) {
                    if ($totalCompleted >= $need) {
                        // Intentar asignar (si ya existe la constraint UNIQUE, la función manejará el caso)
                        $logroModel->asignarAUsuario($trueque['usuario_ofrece_id'], $logroId);
                    }
                }
            } catch (Exception $e) {
                // No impedir la ejecución principal si falla la asignación de logros
            }

            return true;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
    
    /**
     * Rechazar trueque
     */
    public function rechazar($id, $usuarioId) {
        $sql = "UPDATE trueques SET estado = 'rechazado' 
                WHERE id = :id 
                AND (usuario_ofrece_id = :usuario_id OR usuario_recibe_id = :usuario_id)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuarioId);
        
        return $stmt->execute();
    }
    
    /**
     * Cancelar trueque
     */
    public function cancelar($id, $usuarioId) {
        $sql = "UPDATE trueques SET estado = 'cancelado' 
                WHERE id = :id 
                AND (usuario_ofrece_id = :usuario_id OR usuario_recibe_id = :usuario_id)
                AND estado IN ('pendiente', 'aceptado')";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':usuario_id', $usuarioId);
        
        return $stmt->execute();
    }
    
    /**
     * Obtener mensajes del trueque
     */
    public function obtenerMensajes($truequeId) {
        $sql = "SELECT m.*, u.nombre as remitente_nombre
                FROM mensajes m
                INNER JOIN usuarios u ON m.remitente_id = u.id
                WHERE m.trueque_id = :trueque_id
                ORDER BY m.fecha_envio ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trueque_id', $truequeId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Crear una valoración para un trueque
     */
    public function crearValoracion($truequeId, $evaluadorId, $evaluadoId, $puntuacion, $comentario = null) {
        $sql = "INSERT INTO valoraciones (trueque_id, evaluador_id, evaluado_id, puntuacion, comentario) \
                VALUES (:trueque_id, :evaluador_id, :evaluado_id, :puntuacion, :comentario)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trueque_id', $truequeId);
        $stmt->bindParam(':evaluador_id', $evaluadorId);
        $stmt->bindParam(':evaluado_id', $evaluadoId);
        $stmt->bindParam(':puntuacion', $puntuacion);
        $stmt->bindParam(':comentario', $comentario);

        return $stmt->execute();
    }

    /**
     * Obtener valoración hecha por un evaluador en un trueque (si existe)
     */
    public function obtenerValoracionPorTruequeYEvaluador($truequeId, $evaluadorId) {
        $sql = "SELECT * FROM valoraciones WHERE trueque_id = :trueque_id AND evaluador_id = :evaluador_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trueque_id', $truequeId);
        $stmt->bindParam(':evaluador_id', $evaluadorId);
        $stmt->execute();

        return $stmt->fetch();
    }
    
    /**
     * Enviar mensaje
     */
    public function enviarMensaje($truequeId, $remitenteId, $mensaje) {
        $sql = "INSERT INTO mensajes (trueque_id, remitente_id, mensaje) 
                VALUES (:trueque_id, :remitente_id, :mensaje)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':trueque_id', $truequeId);
        $stmt->bindParam(':remitente_id', $remitenteId);
        $stmt->bindParam(':mensaje', $mensaje);
        
        return $stmt->execute();
    }
    
    /**
     * Contar trueques por estado
     */
    public function contarPorEstado($usuarioId, $estado) {
        $sql = "SELECT COUNT(*) as total 
                FROM trueques 
                WHERE (usuario_ofrece_id = :usuario_id OR usuario_recibe_id = :usuario_id)
                AND estado = :estado";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':usuario_id', $usuarioId);
        $stmt->bindParam(':estado', $estado);
        $stmt->execute();
        
        $resultado = $stmt->fetch();
        return $resultado['total'] ?? 0;
    }
    
    /**
     * Registrar transacción de puntos
     */
    private function registrarTransaccion($usuarioId, $cantidad, $tipo, $concepto, $truequeId = null) {
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
}
