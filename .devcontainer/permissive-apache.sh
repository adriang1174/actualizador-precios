#!/bin/bash

# Configuración muy permisiva (sólo para desarrollo)
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"

# Crear un archivo .htaccess simplificado para pruebas
echo "Creando .htaccess de prueba (temporal)..."
sudo bash -c "cat > $REPO_PATH/.htaccess.test << EOF
# Configuración simplificada para pruebas
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOF"

# Configuración de VirtualHost muy permisiva
echo "Configurando Apache en modo permisivo..."
sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf << EOF
ServerName codespace.local

<VirtualHost *:80>
    DocumentRoot $REPO_PATH
    
    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        Order allow,deny
        Allow from all
    </Directory>
    
    # Establecer index.php como archivo de índice predeterminado
    DirectoryIndex index.php
    
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF"

# Asegurarse de que los módulos necesarios estén habilitados
sudo a2enmod rewrite
sudo a2enmod headers

# Verificar configuración
sudo apache2ctl configtest

# Reiniciar Apache
echo "Reiniciando Apache..."
sudo service apache2 restart

echo "Configuración permisiva aplicada. Si tienes problemas con el .htaccess original,"
echo "puedes probar temporalmente con el archivo .htaccess.test:"
echo "cp $REPO_PATH/.htaccess.test $REPO_PATH/.htaccess"
echo "sudo service apache2 restart"