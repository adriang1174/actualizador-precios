#!/bin/bash

# Determinar la ruta del repositorio
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "Directorio del repositorio: $REPO_PATH"

# Verificar permisos actuales
echo "Permisos actuales:"
ls -la $REPO_PATH

# Asegurarse de que www-data (usuario de Apache) pueda leer los archivos
echo "Ajustando permisos..."
sudo chown -R $(whoami):www-data $REPO_PATH
sudo find $REPO_PATH -type d -exec chmod 755 {} \;
sudo find $REPO_PATH -type f -exec chmod 644 {} \;

# Verificar la existencia de index.php
if [ ! -f "$REPO_PATH/index.php" ]; then
    echo "ADVERTENCIA: No se encontró index.php en el directorio raíz"
    echo "Archivos en el directorio raíz:"
    ls -la $REPO_PATH
else
    echo "✅ index.php encontrado"
fi

# Actualizar la configuración de Apache con permisos más permisivos
echo "Actualizando configuración de Apache..."
sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot $REPO_PATH
    
    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        
        # Permitir acceso a todos los archivos (quitar en producción)
        <FilesMatch \"\.(php|html|htm|js|css)$\">
            Require all granted
        </FilesMatch>
    </Directory>

    # Habilitar el uso de .htaccess
    AccessFileName .htaccess
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF"

# Verificar si existe .htaccess y ajustar permisos
if [ -f "$REPO_PATH/.htaccess" ]; then
    echo "✅ Archivo .htaccess encontrado"
    sudo chmod 644 $REPO_PATH/.htaccess
    echo "Contenido de .htaccess:"
    cat $REPO_PATH/.htaccess
else
    echo "ADVERTENCIA: No se encontró archivo .htaccess"
fi

# Reiniciar Apache
echo "Reiniciando Apache..."
sudo systemctl restart apache2 || sudo service apache2 restart

# Verificar si Apache está corriendo
if pgrep apache2 > /dev/null; then
    echo "✅ Apache está ejecutándose"
else
    echo "❌ Apache no está ejecutándose"
fi

# Mostrar los logs de Apache para detectar errores
echo "Últimas líneas del log de error de Apache:"
sudo tail -n 20 /var/log/apache2/error.log

echo "Últimas líneas del log de acceso de Apache:"
sudo tail -n 20 /var/log/apache2/access.log

echo "Para ver los logs en tiempo real mientras accedes a la aplicación, ejecuta:"
echo "sudo tail -f /var/log/apache2/error.log"
echo "sudo tail -f /var/log/apache2/access.log"

echo "Proceso completado."