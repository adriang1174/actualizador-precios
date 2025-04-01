#!/bin/bash

# Función para mostrar mensajes de error
show_error() {
  echo "=========== ERROR ==========="
  echo "$1"
  echo "============================"
}

# Instalar dependencias
echo "Instalando dependencias..."
sudo apt-get update
sudo apt-get install -y apache2 libapache2-mod-php unixodbc unixodbc-dev

# Configurar PDO_ODBC (si no está instalado)
if php -m | grep -q "pdo_odbc"; then
  echo "PDO_ODBC ya está instalado"
else
  echo "Instalando PDO_ODBC..."
  sudo docker-php-ext-install pdo_odbc || show_error "No se pudo instalar PDO_ODBC"
  sudo docker-php-ext-enable pdo_odbc || show_error "No se pudo habilitar PDO_ODBC"
fi

# Habilitar módulos de Apache
echo "Habilitando módulos de Apache..."
sudo a2enmod rewrite || show_error "No se pudo habilitar mod_rewrite"
sudo a2enmod headers || show_error "No se pudo habilitar mod_headers"

# Verificar si Apache está instalado correctamente
if ! command -v apache2ctl &> /dev/null; then
  show_error "Apache no está instalado correctamente. Instalando..."
  sudo apt-get install -y apache2
fi

# Guardar la configuración original como respaldo
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sudo cp /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.bak
  echo "Respaldo de configuración de Apache creado en 000-default.conf.bak"
fi

# Crear configuración personalizada para el sitio por defecto
echo "Configurando Apache..."
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot $REPO_PATH

    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF"

# Comprobar la sintaxis de la configuración
echo "Comprobando sintaxis de la configuración..."
if ! sudo apache2ctl configtest; then
  show_error "Configuración de Apache inválida. Ver detalles arriba."
  # Restaurar configuración anterior si hay errores
  if [ -f /etc/apache2/sites-available/000-default.conf.bak ]; then
    sudo cp /etc/apache2/sites-available/000-default.conf.bak /etc/apache2/sites-available/000-default.conf
    echo "Restaurada la configuración anterior"
  fi
fi

# Crear archivo .htaccess en la raíz del proyecto
echo "Creando archivo .htaccess..."
sudo bash -c "cat > $REPO_PATH/.htaccess << EOF
# Configuración Apache para Actualizador de Precios Factusol
# 
# Este archivo configura el servidor Apache para permitir
# URLs amigables y redireccionar todas las solicitudes
# al archivo index.php para el enrutamiento de la aplicación.

# Activar el motor de reescritura
RewriteEngine On

# Si la solicitud no es para un archivo o directorio existente
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Redirigir todas las solicitudes a index.php
RewriteRule ^(.*)$ index.php [QSA,L]

# Impedir el acceso a archivos y directorios sensibles
<FilesMatch \"^\.htaccess|composer\.json|README\.md|composer\.lock\">
  Order Allow,Deny
  Deny from all
</FilesMatch>

# Impedir listado de directorios
Options -Indexes

# Configuración PHP específica para este directorio
php_flag display_errors off
php_value max_execution_time 300
php_value memory_limit 256M
php_value post_max_size 20M
php_value upload_max_filesize 20M

# Forzar HTTPS (descomenta si es necesario)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
EOF"

# Configurar PHP
echo "Configurando PHP..."
sudo bash -c 'cat > /usr/local/etc/php/conf.d/custom-php.ini << EOF
display_errors = Off
max_execution_time = 300
memory_limit = 256M
post_max_size = 20M
upload_max_filesize = 20M
EOF'

# Reiniciar Apache si todo está bien
echo "Intentando reiniciar Apache..."
if sudo service apache2 restart; then
  echo "✅ Apache reiniciado correctamente"
else
  show_error "No se pudo reiniciar Apache. Mostrando diagnósticos:"
  echo ""
  echo "--- Estado de Apache ---"
  sudo service apache2 status
  echo ""
  echo "--- Últimas 20 líneas del log de error ---"
  sudo tail -n 20 /var/log/apache2/error.log
  echo ""
  echo "--- Verificando puertos en uso ---"
  sudo netstat -tulpn | grep 80
  echo ""
  echo "--- Verificando si httpd está corriendo con otro nombre ---"
  ps aux | grep -E 'apache|httpd'
fi

echo "Proceso de configuración finalizado"cd 