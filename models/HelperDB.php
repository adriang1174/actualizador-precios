<?php
/**
 * Clase HelperDB
 * 
 * Proporciona métodos auxiliares para interactuar con la base de datos
 * Implementa el patrón Singleton para mantener una única instancia de conexión
 */
class HelperDB {
    
    /**
     * Instancia única de la clase (patrón Singleton)
     * @var HelperDB
     */
    private static $instance = null;
    
    /**
     * Conexión PDO a la base de datos
     * @var PDO
     */
    private $connection;
    
    /**
     * Constructor privado para evitar instanciación directa
     */
    private function __construct() {
        $this->connection = getDbConnection();
    }
    
    /**
     * Obtiene la instancia única de la clase (implementación Singleton)
     * 
     * @return HelperDB La instancia única de HelperDB
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Ejecuta una consulta SELECT y retorna los resultados
     * 
     * @param string $query Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta preparada
     * @return array Resultados de la consulta
     */
    public function select($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE
     * 
     * @param string $query Consulta SQL a ejecutar
     * @param array $params Parámetros para la consulta preparada
     * @return int Número de filas afectadas
     */
    public function execute($query, $params = []) {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * Inicia una transacción
     */
    public function beginTransaction() {
        $this->connection->beginTransaction();
    }
    
    /**
     * Confirma una transacción
     */
    public function commit() {
        $this->connection->commit();
    }
    
    /**
     * Revierte una transacción
     */
    public function rollback() {
        $this->connection->rollBack();
    }
    
    /**
     * Obtiene el ID del último registro insertado
     * 
     * @return string El ID del último registro insertado
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Escapa un identificador (nombre de columna o tabla) para uso seguro en SQL
     * 
     * @param string $identifier El identificador a escapar
     * @return string El identificador escapado
     */
    public function escapeIdentifier($identifier) {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
