<?php
/**
 * Controlador principal
 * 
 * Maneja las solicitudes a la página principal
 */
class IndexController {
    
    /**
     * Muestra la página principal
     */
    public function index() {
        include 'views/templates/header.php';
        include 'views/index.php';
        include 'views/templates/footer.php';
    }
}
