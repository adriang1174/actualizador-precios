#!/bin/bash

# Configurar el ServerName global para suprimir la advertencia
echo "Configurando ServerName global..."
sudo bash -c 'cat > /etc/apache2/conf-available/servername.conf << EOF
# Configuración global de ServerName
ServerName codespace.local
EOF'

# Habilitar la configuración
sudo a2enconf servername

# Crear configuración para redireccionar logs a la terminal cuando es necesario
echo "Configurando logs para ambiente de desarrollo..."
sudo bash -c 'cat > /etc/apache2/conf-available/log-to-console.conf << EOF
# En entornos de desarrollo, asegurarse de que los logs sean visibles
ErrorLog ${APACHE_LOG_DIR}/error.log
CustomLog ${APACHE_LOG_DIR}/access.log combined

# Crear un enlace simbólico del log de errores a stdout para verlo en la terminal
# Esto es útil para depuración en entornos como Codespaces
# CustomLog /dev/stdout combined
# ErrorLog /dev/stderr
EOF'

sudo a2enconf log-to-console

# Actualizar la configuración de VirtualHost para ser más permisiva
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "Actualizando configuración de VirtualHost..."
sudo bash -c "cat > /etc/apache2/sites-available/000-default.conf << EOF
<VirtualHost *:80>
    ServerName codespace.local
    ServerAdmin webmaster@localhost
    DocumentRoot $REPO_PATH
    
    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>
    
    <Directory $REPO_PATH>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF"

# Verificar la configuración antes de reiniciar
echo "Verificando sintaxis de la configuración..."
sudo apache2ctl configtest

# Reiniciar Apache
echo "Reiniciando Apache..."
sudo systemctl restart apache2 || sudo service apache2 restart

# Verificar que no aparezca la advertencia
echo "Estado de Apache después del reinicio:"
sudo systemctl status apache2 || sudo service apache2 status

echo "Si encuentras errores 403 Forbidden, ejecuta los siguientes comandos para ajustar permisos:"
echo "sudo chown -R $(whoami):www-data $REPO_PATH"
echo "sudo chmod -R 755 $REPO_PATH"

echo "Para ver los logs de Apache en tiempo real, ejecuta:"
echo "sudo tail -f /var/log/apache2/error.log"
echo "sudo tail -f /var/log/apache2/access.log"

echo "Configuración completada."