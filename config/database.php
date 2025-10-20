<?php
/**
 * Configuración de Base de Datos
 * Runa Maki - Plataforma de Trueque de Habilidades
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Configuración de conexión
    private $host = 'localhost';
    private $database = 'runamaki';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Habilitar emulación de prepared statements permite reutilizar
                // el mismo parámetro nombrado varias veces en una consulta.
                // Esto evita errores PDO SQLSTATE[HY093] cuando el código
                // repite nombres como :usuario_id en subconsultas.
                PDO::ATTR_EMULATE_PREPARES   => true,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Prevenir clonación del objeto
    private function __clone() {}
    
    // Prevenir deserialización del objeto
    public function __wakeup() {
        throw new Exception("No se puede deserializar el singleton de Database");
    }
}
