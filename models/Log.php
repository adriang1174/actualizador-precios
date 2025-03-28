<?php
/**
 * Clase Log
 * 
 * Gestiona los registros de operaciones de actualización de precios
 */
class Log {
    /**
     * Helper para interactuar con la base de datos
     * @var HelperDB
     */
    private $db;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        $this->db = HelperDB::getInstance();
    }
    
    /**
     * Crea un nuevo registro de resumen de operaciones
     * 
     * @param string $tipoOperacion Tipo de operación (planilla o familia)
     * @param string $descripcion Descripción de la operación
     * @return int ID del registro creado
     */
    public function crearRegistro($tipoOperacion, $descripcion) {
        $query = "INSERT INTO AP_LOG (tipoOperacion, descripcion, fechaHora) VALUES (?, ?, NOW())";
        $this->db->execute($query, [$tipoOperacion, $descripcion]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Registra la actualización de un artículo
     * 
     * @param int $codLog ID del registro de log
     * @param string $codPrecio Código del artículo
     * @param string $equivalente Código equivalente
     * @param string $familia Código de familia
     * @param string $status Estado de la actualización
     * @return bool True si se registró correctamente
     */
    public function registrarActualizacion($codLog, $codPrecio, $equivalente, $familia, $status) {
        $query = "INSERT INTO AP_LOG_DETALLE (idLog, codigoArticulo, codigoEquivalente, familia, estado) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $this->db->execute($query, [$codLog, $codPrecio, $equivalente, $familia, $status]);
        return true;
    }
    
    /**
     * Registra el total de artículos de una familia
     * 
     * @param int $codLog ID del registro de log
     * @param string $familia Código de familia
     * @return bool True si se registró correctamente
     */
    public function totalArticulosFamilia($codLog, $familia) {
        // Actualizar el registro de log con la familia y el total de artículos
        $precio = new Precio();
        $articulosFamilia = $precio->getFamiliasList($familia);
        $total = count($articulosFamilia);
        
        $query = "UPDATE AP_LOG SET familia = ?, totalArticulosFamilia = ? WHERE id = ?";
        $this->db->execute($query, [$familia, $total, $codLog]);
        
        return true;
    }
    
    /**
     * Registra el total de filas en el archivo
     * 
     * @param int $codLog ID del registro de log
     * @param int $cant Cantidad de filas
     * @return bool True si se registró correctamente
     */
    public function totalFilasFile($codLog, $cant) {
        $query = "UPDATE AP_LOG SET totalFilasPlanilla = ? WHERE id = ?";
        $this->db->execute($query, [$cant, $codLog]);
        
        return true;
    }
    
    /**
     * Obtiene el total de artículos de una familia que no fueron actualizados
     * 
     * @param string $familia Código de familia
     * @param int $logId ID del registro de log
     * @return int Total de artículos sin actualizar
     */
    public function totalArticulosFamiliaSinActualizar($familia, $logId) {
        // Obtener el total de artículos en la familia
        $precio = new Precio();
        $articulosFamilia = $precio->getFamiliasList($familia);
        $totalFamilia = count($articulosFamilia);
        
        // Obtener el total de artículos actualizados en esta operación
        $query = "SELECT COUNT(*) as total FROM AP_LOG_DETALLE 
                  WHERE idLog = ? AND familia = ? AND estado = 'exitoso'";
        $result = $this->db->select($query, [$logId, $familia]);
        
        $totalActualizados = $result[0]['total'];
        
        return $totalFamilia - $totalActualizados;
    }
    
    /**
     * Obtiene el total de artículos actualizados en una operación
     * 
     * @param int $logId ID del registro de log
     * @return int Total de artículos actualizados
     */
    public function totalArticulosActualizados($logId) {
        $query = "SELECT COUNT(*) as total FROM AP_LOG_DETALLE 
                  WHERE idLog = ? AND estado = 'exitoso'";
        $result = $this->db->select($query, [$logId]);
        
        return $result[0]['total'];
    }
    
    /**
     * Obtiene el listado de artículos de una familia que no fueron actualizados
     * 
     * @param string $familia Código de familia
     * @param int $logId ID del registro de log
     * @return array Listado de artículos sin actualizar
     */
    public function listadoArticulosFamiliaSinActualizar($familia, $logId) {
        $precio = new Precio();
        $articulosFamilia = $precio->getFamiliasList($familia);
        
        // Obtener los códigos de los artículos que fueron actualizados
        $query = "SELECT codigoArticulo FROM AP_LOG_DETALLE 
                  WHERE idLog = ? AND familia = ? AND estado = 'exitoso'";
        $result = $this->db->select($query, [$logId, $familia]);
        
        $actualizados = [];
        foreach ($result as $row) {
            $actualizados[] = $row['codigoArticulo'];
        }
        
        // Filtrar los artículos que no fueron actualizados
        $noActualizados = [];
        foreach ($articulosFamilia as $articulo) {
            if (!in_array($articulo['CODART'], $actualizados)) {
                $noActualizados[] = $articulo;
            }
        }
        
        return $noActualizados;
    }
    
    /**
     * Obtiene el último registro de log
     * 
     * @return array|null Datos del último registro o null si no hay registros
     */
    public function getUltimoLog() {
        $query = "SELECT * FROM AP_LOG ORDER BY id DESC LIMIT 1";
        $result = $this->db->select($query);
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Obtiene un registro de log por su ID
     * 
     * @param int $logId ID del registro de log
     * @return array|null Datos del registro o null si no existe
     */
    public function getLogById($logId) {
        $query = "SELECT * FROM AP_LOG WHERE id = ?";
        $result = $this->db->select($query, [$logId]);
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Obtiene el detalle de un registro de log
     * 
     * @param int $logId ID del registro de log
     * @return array Detalle del registro
     */
    public function getDetalleLog($logId) {
        $query = "SELECT * FROM AP_LOG_DETALLE WHERE idLog = ?";
        return $this->db->select($query, [$logId]);
    }
    
    /**
     * Obtiene los totales de un registro de log
     * 
     * @param int $logId ID del registro de log
     * @return array Datos de totales
     */
    public function getTotalesLog($logId) {
        $log = $this->getLogById($logId);
        
        if (!$log) {
            return null;
        }
        
        $totalActualizados = $this->totalArticulosActualizados($logId);
        $totalSinActualizar = 0;
        $listadoSinActualizar = [];
        
        if (!empty($log['familia'])) {
            $totalSinActualizar = $this->totalArticulosFamiliaSinActualizar($log['familia'], $logId);
            $listadoSinActualizar = $this->listadoArticulosFamiliaSinActualizar($log['familia'], $logId);
        }
        
        return [
            'log' => $log,
            'totalActualizados' => $totalActualizados,
            'totalSinActualizar' => $totalSinActualizar,
            'listadoSinActualizar' => $listadoSinActualizar
        ];
    }
}
