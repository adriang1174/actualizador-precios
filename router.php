<?php
// Este archivo simula el comportamiento de .htaccess con mod_rewrite
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si la solicitud es para un archivo o directorio existente, servirlo directamente
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Deja que el servidor maneje la solicitud normalmente
}

// De lo contrario, incluir index.php
include __DIR__ . '/index.php';
