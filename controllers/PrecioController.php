<?php
/**
 * Controlador de Precios
 * 
 * Maneja las solicitudes relacionadas con la actualización de precios
 */
class PrecioController {
    
    /**
     * Modelo de Precio
     * @var Precio
     */
    private $precioModel;
    
    /**
     * Modelo de Log
     * @var Log
     */
    private $logModel;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        require_once 'models/Precio.php';
        require_once 'models/Log.php';
        
        $this->precioModel = new Precio();
        $this->logModel = new Log();
    }
    
    /**
     * Muestra el formulario de actualización por planilla
     */
    public function showPlanillaForm() {
        include 'views/templates/header.php';
        include 'views/planilla.php';
        include 'views/templates/footer.php';
    }
    
    /**
     * Muestra el formulario de actualización por familia
     */
    public function showFamiliaForm() {
        include 'views/templates/header.php';
        include 'views/familia.php';
        include 'views/templates/footer.php';
    }
    
    /**
     * Actualiza precios por planilla (procesa el formulario)
     */
    public function actualizarPorPlanilla() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'planilla');
            exit;
        }
        
        // Verificar y procesar el archivo subido
        if (!isset($_FILES['planilla']) || $_FILES['planilla']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al cargar el archivo. Por favor, inténtelo de nuevo.';
            header('Location: ' . BASE_URL . 'planilla');
            exit;
        }
        
        // Procesar los datos del formulario
        $columnaCodigo = isset($_POST['columnaCodigo']) ? (int)$_POST['columnaCodigo'] : 1;
        $columnaValor = isset($_POST['columnaValor']) ? (int)$_POST['columnaValor'] : 2;
        $filaInicio = isset($_POST['filaInicio']) ? (int)$_POST['filaInicio'] : 2;
        $coeficiente = isset($_POST['coeficiente']) ? (float)$_POST['coeficiente'] : 1.0;
        
        // Opciones de actualización
        $tarifaA = isset($_POST['tarifaA']) ? true : false;
        $tarifaB = isset($_POST['tarifaB']) ? true : false;
        $dolarPapel = isset($_POST['dolarPapel']) ? true : false;
        $costo = isset($_POST['costo']) ? true : false;
        
        // Verificar que al menos una opción esté seleccionada
        if (!$tarifaA && !$tarifaB && !$dolarPapel && !$costo) {
            $_SESSION['error'] = 'Debe seleccionar al menos una opción de actualización.';
            header('Location: ' . BASE_URL . 'planilla');
            exit;
        }
        
        // Crear un registro en el log
        $descripcion = "Actualización por planilla. Coeficiente: $coeficiente, Columna Código: $columnaCodigo, Columna Valor: $columnaValor, Fila Inicio: $filaInicio";
        $logId = $this->logModel->crearRegistro('planilla', $descripcion);
        
        // Mover el archivo a una ubicación temporal
        $uploadDir = ROOT_PATH . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $tempFilePath = $uploadDir . basename($_FILES['planilla']['name']);
        move_uploaded_file($_FILES['planilla']['tmp_name'], $tempFilePath);
        
        // Procesar la actualización
        $resultado = $this->precioModel->actualizarPorPlanilla(
            $tempFilePath,
            $columnaCodigo,
            $columnaValor,
            $filaInicio,
            $coeficiente,
            $tarifaA,
            $tarifaB,
            $dolarPapel,
            $costo,
            $logId
        );
        
        // Eliminar el archivo temporal
        unlink($tempFilePath);
        
        // Almacenar el ID del log en la sesión para mostrarlo en el resumen
        $_SESSION['logId'] = $logId;
        
        // Devolver el resultado como JSON para la solicitud Ajax
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'logId' => $logId,
            'message' => 'Actualización completada con éxito.',
            'resultado' => $resultado
        ]);
        exit;
    }
    
    /**
     * Actualiza precios por familia (procesa el formulario)
     */
    public function actualizarPorFamilia() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'familia');
            exit;
        }
        
        // Procesar los datos del formulario
        $familia = isset($_POST['familia']) ? $_POST['familia'] : '';
        $coeficiente = isset($_POST['coeficiente']) ? (float)$_POST['coeficiente'] : 1.0;
        
        // Opciones de actualización
        $tarifaA = isset($_POST['tarifaA']) ? true : false;
        $tarifaB = isset($_POST['tarifaB']) ? true : false;
        $dolarPapel = isset($_POST['dolarPapel']) ? true : false;
        $costo = isset($_POST['costo']) ? true : false;
        
        // Validar datos
        if (empty($familia)) {
            $_SESSION['error'] = 'Debe seleccionar una familia.';
            header('Location: ' . BASE_URL . 'familia');
            exit;
        }
        
        // Verificar que al menos una opción esté seleccionada
        if (!$tarifaA && !$tarifaB && !$dolarPapel && !$costo) {
            $_SESSION['error'] = 'Debe seleccionar al menos una opción de actualización.';
            header('Location: ' . BASE_URL . 'familia');
            exit;
        }
        
        // Crear un registro en el log
        $descripcion = "Actualización por familia. Familia: $familia, Coeficiente: $coeficiente";
        $logId = $this->logModel->crearRegistro('familia', $descripcion);
        
        // Procesar la actualización
        $resultado = $this->precioModel->actualizarFamilia(
            $familia,
            $coeficiente,
            $tarifaA,
            $tarifaB,
            $dolarPapel,
            $costo,
            $logId
        );
        
        // Almacenar el ID del log en la sesión para mostrarlo en el resumen
        $_SESSION['logId'] = $logId;
        
        // Devolver el resultado como JSON para la solicitud Ajax
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'logId' => $logId,
            'message' => 'Actualización completada con éxito.',
            'resultado' => $resultado
        ]);
        exit;
    }
    
    /**
     * Obtiene la lista de familias (API)
     */
    public function getFamilias() {
        $familias = $this->precioModel->getAllFamilias();
        
        header('Content-Type: application/json');
        echo json_encode($familias);
        exit;
    }
}
