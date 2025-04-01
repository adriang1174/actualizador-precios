#!/bin/bash

# Este script crea una configuración limpia de Apache para resolver problemas persistentes

echo "Comenzando configuración limpia de Apache..."

# Detener Apache
sudo service apache2 stop

# Remover y reinstalar Apache completamente (si es posible)
if [ -x "$(command -v apt-get)" ]; then
    echo "Reinstalando Apache..."
    sudo apt-get purge -y apache2 apache2-utils apache2-bin
    sudo apt-get autoremove -y
    sudo apt-get clean
    sudo apt-get update
    sudo apt-get install -y apache2 libapache2-mod-php
fi

# Obtener la ruta del repositorio
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "Ruta del repositorio: $REPO_PATH"

# Crear estructura mínima para pruebas
echo "Creando estructura mínima..."
mkdir -p /tmp/apache-test
echo "<html><body><h1>Apache funciona!</h1></body></html>" > /tmp/apache-test/index.html
echo "<?php phpinfo(); ?>" > /tmp/apache-test/info.php
sudo chmod -R 755 /tmp/apache-test

# Crear una configuración mínima de Apache
echo "Configurando Apache mínimo..."
sudo bash -c "cat > /etc/apache2/sites-available/minimal.conf << EOF
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /tmp/apache-test
    
    <Directory /tmp/apache-test>
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all granted
    </Directory>
    
    ErrorLog /var/log/apache2/minimal-error.log
    CustomLog /var/log/apache2/minimal-access.log combined
</VirtualHost>
EOF"

# Configurar sitio real
echo "Configurando sitio real..."
sudo bash -c "cat > /etc/apache2/sites-available/real-site.conf << EOF
<VirtualHost *:81>
    ServerName app.localhost
    DocumentRoot $REPO_PATH
    
    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog /var/log/apache2/real-error.log
    CustomLog /var/log/apache2/real-access.log combined
</VirtualHost>
EOF"

# Asegurarse de que Apache escuche en los puertos requeridos
sudo bash -c "cat > /etc/apache2/ports.conf << EOF
Listen 80
Listen 81

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>
EOF"

# Habilitar módulos necesarios
sudo a2enmod rewrite
sudo a2enmod headers

# Asignar permisos correctos a los directorios
echo "Configurando permisos..."
sudo chown -R www-data:www-data /tmp/apache-test

# Ajustar permisos para el directorio del repositorio
sudo find $REPO_PATH -type d -exec chmod 755 {} \;
sudo find $REPO_PATH -type f -exec chmod 644 {} \;

# Habilitar sitios y reiniciar Apache
sudo a2dissite 000-default
sudo a2ensite minimal
sudo a2ensite real-site

# Verificar configuración
sudo apache2ctl configtest

# Reiniciar Apache
echo "Reiniciando Apache..."
sudo service apache2 restart

echo "Configuración limpia completada."
echo ""
echo "Prueba las siguientes URLs:"
echo "1. http://localhost - Debería mostrar 'Apache funciona!'"
echo "2. http://localhost/info.php - Debería mostrar información PHP"
echo "3. http://localhost:81 - Debería mostrar tu aplicación"
echo ""
echo "Verifica los logs en:"
echo "sudo tail -f /var/log/apache2/minimal-error.log"
echo "sudo tail -f /var/log/apache2/minimal-access.log"
echo "sudo tail -f /var/log/apache2/real-error.log"
echo "sudo tail -f /var/log/apache2/real-access.log"