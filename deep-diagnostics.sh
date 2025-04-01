#!/bin/bash

echo "======================= DIAGNÓSTICO COMPLETO ======================="
echo "Fecha y hora: $(date)"

# Verificar si Apache está instalado correctamente
echo "=== Versión de Apache ==="
apache2 -v

# Verificar puertos en uso
echo "=== Puertos en uso (80 y 443) ==="
netstat -tuln | grep -E ':80|:443'

# Verificar si Apache está escuchando
echo "=== Procesos Apache ==="
ps aux | grep apache

# Obtener la ruta del repositorio
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "=== Ruta del repositorio: $REPO_PATH ==="

# Verificar si el directorio existe
echo "=== El directorio existe? ==="
if [ -d "$REPO_PATH" ]; then
    echo "✅ Directorio existe"
else
    echo "❌ Directorio NO existe"
fi

# Verificar estructura de directorios
echo "=== Estructura de directorios ==="
find $REPO_PATH -type d -maxdepth 2 -not -path "*/\.*" | sort

# Verificar archivos clave
echo "=== Archivos clave ==="
if [ -f "$REPO_PATH/index.php" ]; then
    echo "✅ index.php existe"
    ls -la $REPO_PATH/index.php
else
    echo "❌ index.php NO existe"
fi

if [ -f "$REPO_PATH/.htaccess" ]; then
    echo "✅ .htaccess existe"
    ls -la $REPO_PATH/.htaccess
else
    echo "❌ .htaccess NO existe"
fi

# Verificar configuración actual de Apache
echo "=== Configuración actual de Apache ==="
echo "--- /etc/apache2/sites-available/000-default.conf ---"
cat /etc/apache2/sites-available/000-default.conf

# Verificar módulos Apache habilitados
echo "=== Módulos Apache habilitados ==="
apache2ctl -M

# Verificar que el usuario de Apache tenga acceso
echo "=== Permisos de directorio ==="
ls -la $REPO_PATH/

# Probar manualmente un acceso básico
echo "=== Test de acceso HTTP básico ==="
curl -v http://localhost 2>&1

# Crear archivo de prueba
echo "=== Creando archivo de prueba ==="
echo "<?php echo 'Test funcionando correctamente'; ?>" > $REPO_PATH/test.php
sudo chown www-data:www-data $REPO_PATH/test.php
sudo chmod 644 $REPO_PATH/test.php
echo "Archivo test.php creado"

# Verificar destino de logs de Apache
echo "=== Configuración de logs de Apache ==="
grep -r "ErrorLog\|CustomLog" /etc/apache2/

# Crear una configuración de cero como último recurso
echo "=== Creando configuración de cero ==="
sudo bash -c "cat > /etc/apache2/sites-available/test-site.conf << EOF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot $REPO_PATH
    
    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs explícitos
    ErrorLog /var/log/apache2/test-error.log
    CustomLog /var/log/apache2/test-access.log combined
</VirtualHost>
EOF"

# Habilitar el sitio de prueba
sudo a2dissite 000-default
sudo a2ensite test-site
sudo service apache2 restart

echo "Sitio de prueba habilitado. Intenta acceder a http://localhost y http://localhost/test.php"
echo "Verifica los logs de prueba con:"
echo "sudo tail -f /var/log/apache2/test-error.log"
echo "sudo tail -f /var/log/apache2/test-access.log"

echo "======================= FIN DEL DIAGNÓSTICO ======================="