#!/bin/bash

# Determinar la ruta del repositorio
REPO_PATH="/workspaces/$(basename $GITHUB_REPOSITORY 2>/dev/null || echo "$PWD")"
echo "Ajustando permisos para: $REPO_PATH"

# Mostrar usuario actual y grupo
echo "Usuario actual: $(whoami)"
echo "Grupos: $(groups)"

# Ajustar permisos para que Apache pueda leer/ejecutar
sudo chown -R $(whoami):www-data $REPO_PATH
sudo find $REPO_PATH -type d -exec chmod 755 {} \;
sudo find $REPO_PATH -type f -exec chmod 644 {} \;

# Asegurarse de que los scripts PHP sean ejecutables si es necesario
sudo find $REPO_PATH -name "*.php" -exec chmod 644 {} \;

# Verificar permisos después de los cambios
echo "Permisos del directorio raíz después de los cambios:"
ls -la $REPO_PATH

echo "Contenido del directorio raíz:"
ls -la $REPO_PATH

# Verificar si existe un índice
if [ -f "$REPO_PATH/index.php" ]; then
    echo "✅ index.php encontrado con permisos: $(stat -c '%A' $REPO_PATH/index.php)"
else
    echo "❌ No se encontró index.php en el directorio raíz"
    echo "Archivos en el directorio raíz:"
    ls -la $REPO_PATH
fi

echo "Permisos ajustados. Reinicia Apache con: sudo service apache2 restart"