<?php
/**
 * Controlador de Logs
 * 
 * Maneja las solicitudes relacionadas con los registros de operaciones
 */
class LogController {
    
    /**
     * Modelo de Log
     * @var Log
     */
    private $logModel;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        require_once 'models/Log.php';
        $this->logModel = new Log();
    }
    
    /**
     * Muestra la página de resumen de operaciones
     */
    public function showResumen() {
        // Obtener el ID del log de la URL o de la sesión
        $logId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_SESSION['logId']) ? $_SESSION['logId'] : null);
        
        // Si no hay ID de log, mostrar el último
        if (!$logId) {
            $ultimoLog = $this->logModel->getUltimoLog();
            $logId = $ultimoLog ? $ultimoLog['id'] : null;
        }
        
        // Si todavía no hay ID de log, redirigir a la página principal
        if (!$logId) {
            $_SESSION['error'] = 'No hay operaciones registradas.';
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Obtener los datos del log
        $logData = $this->logModel->getLogById($logId);
        
        include 'views/templates/header.php';
        include 'views/resumen.php';
        include 'views/templates/footer.php';
    }
    
    /**
     * Obtiene los totales de un registro de log (API)
     */
    public function getTotales() {
        $logId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$logId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID de log no especificado.']);
            exit;
        }
        
        $totales = $this->logModel->getTotalesLog($logId);
        
        header('Content-Type: application/json');
        echo json_encode($totales);
        exit;
    }
    
    /**
     * Obtiene los registros detallados de un log (API)
     */
    public function getRecords() {
        $logId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if (!$logId) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID de log no especificado.']);
            exit;
        }
        
        $records = $this->logModel->getDetalleLog($logId);
        
        header('Content-Type: application/json');
        echo json_encode($records);
        exit;
    }
}
