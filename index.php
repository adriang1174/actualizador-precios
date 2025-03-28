<?php
/**
 * Punto de entrada principal de la aplicación Actualizador de Precios Factusol
 * Este archivo inicia la sesión, incluye las configuraciones necesarias
 * y dirige las solicitudes al controlador adecuado.
 * 
 * @author Adrian Garcia
 * @version 1.0
 */

// Iniciar sesión
session_start();

// Cargar autoload de Composer
require_once 'vendor/autoload.php';

// Incluir configuración de base de datos
require_once 'config/database.php';

// Definir constantes de la aplicación
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/actualizador-precios/');
define('ROOT_PATH', __DIR__);

// Obtener la solicitud actual
$request = $_SERVER['REQUEST_URI'];
$basePath = '/actualizador-precios/';

// Eliminar la ruta base y los parámetros de consulta
$request = str_replace($basePath, '', $request);
$request = strtok($request, '?');

// Enrutamiento simple
switch ($request) {
    case '':
    case 'index':
    case 'home':
        require_once 'controllers/IndexController.php';
        $controller = new IndexController();
        $controller->index();
        break;
        
    case 'planilla':
        require_once 'controllers/PrecioController.php';
        $controller = new PrecioController();
        $controller->showPlanillaForm();
        break;
        
    case 'familia':
        require_once 'controllers/PrecioController.php';
        $controller = new PrecioController();
        $controller->showFamiliaForm();
        break;
        
    case 'actualizar-planilla':
        require_once 'controllers/PrecioController.php';
        $controller = new PrecioController();
        $controller->actualizarPorPlanilla();
        break;
        
    case 'actualizar-familia':
        require_once 'controllers/PrecioController.php';
        $controller = new PrecioController();
        $controller->actualizarPorFamilia();
        break;
        
    case 'resumen':
        require_once 'controllers/LogController.php';
        $controller = new LogController();
        $controller->showResumen();
        break;
        
    case 'api/familias':
        require_once 'controllers/PrecioController.php';
        $controller = new PrecioController();
        $controller->getFamilias();
        break;
        
    case 'api/log-totales':
        require_once 'controllers/LogController.php';
        $controller = new LogController();
        $controller->getTotales();
        break;
        
    case 'api/log-records':
        require_once 'controllers/LogController.php';
        $controller = new LogController();
        $controller->getRecords();
        break;
        
    default:
        http_response_code(404);
        require_once 'views/404.php';
        break;
}
