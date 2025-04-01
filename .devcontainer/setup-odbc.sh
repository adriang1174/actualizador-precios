#!/bin/bash

# Instalar dependencias adicionales necesarias para MS Access
sudo apt-get update
sudo apt-get install -y unixodbc unixodbc-dev odbcinst1debian2

# Descargar e instalar el controlador FreeTDS (compatible con MS Access)
sudo apt-get install -y tdsodbc freetds-bin freetds-common

# Configurar controladores ODBC
sudo bash -c 'cat > /etc/odbcinst.ini << EOF
[FreeTDS]
Description = FreeTDS Driver
Driver = /usr/lib/x86_64-linux-gnu/odbc/libtdsodbc.so
Setup = /usr/lib/x86_64-linux-gnu/odbc/libtdsS.so
FileUsage = 1
EOF'

# Configurar DSN (ajusta según tus parámetros)
sudo bash -c 'cat > /etc/odbc.ini << EOF
[MSAccessDSN]
Driver = FreeTDS
Description = MS Access Database
Server = localhost
Port = 1433
Database = factusol
EOF'

# Habilitar extensión PDO_ODBC en PHP
sudo docker-php-ext-install pdo_odbc
sudo docker-php-ext-enable pdo_odbc

# Reiniciar Apache si es necesario
if command -v apache2 &> /dev/null; then
    sudo service apache2 restart
fi

echo "Configuración de ODBC para MS Access completada"