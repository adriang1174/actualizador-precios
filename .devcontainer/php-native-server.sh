#!/bin/bash

# Este script configura un servidor PHP nativo sin depender de Apache
# Útil para probar si hay problemas específicos con Apache

# Obtener la ruta del repositorio
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "Ruta del repositorio: $REPO_PATH"

# Verificar si PHP está instalado
if ! command -v php &> /dev/null; then
    echo "PHP no está instalado. Instalando..."
    sudo apt-get update
    sudo apt-get install -y php php-cli
fi

# Detener Apache para liberar el puerto 80 (opcional)
echo "¿Deseas detener Apache para liberar el puerto 80? (s/n)"
read response
if [[ "$response" == "s" ]]; then
    sudo service apache2 stop
    echo "Apache detenido"
fi

# Crear script para simular el comportamiento de .htaccess
echo "Creando router.php para simular .htaccess..."
cat > $REPO_PATH/router.php << 'EOF'
<?php
// Este archivo simula el comportamiento de .htaccess con mod_rewrite
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si la solicitud es para un archivo o directorio existente, servirlo directamente
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Deja que el servidor maneje la solicitud normalmente
}

// De lo contrario, incluir index.php
include __DIR__ . '/index.php';
EOF

# Iniciar servidor PHP
echo "Iniciando servidor PHP nativo en el puerto 8000..."
echo "Para acceder: http://localhost:8000"
echo "Presiona Ctrl+C para detener el servidor"
echo ""
echo "Si esto funciona pero Apache no, el problema está en la configuración de Apache"
echo "Si esto también falla, el problema podría estar en tu aplicación PHP"
echo ""

# Iniciar el servidor PHP
cd $REPO_PATH
php -S 0.0.0.0:8000 router.php
