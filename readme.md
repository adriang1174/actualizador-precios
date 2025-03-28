# Actualizador de Precios Factusol

Sistema de actualización masiva de precios para el software Factusol desarrollado para SB Argentina.

## Descripción

Esta aplicación web permite actualizar de forma masiva los precios (costo y precio de artículos) en la base de datos de Factusol, ofreciendo dos métodos principales:

1. **Actualización por Planilla**: Carga de una planilla de Excel con los códigos equivalentes y valores a actualizar.
2. **Actualización por Familia**: Selección de una familia de productos para actualización conjunta.

## Características

- Interfaz web desarrollada con PHP, JavaScript y Bootstrap
- Diseño responsive con paleta de colores azul/violeta
- Carga de planillas Excel para actualización masiva
- Actualización selectiva por familia de productos
- Posibilidad de aplicar coeficientes de actualización
- Actualización selectiva de diferentes tipos de precios (Tarifa A, Tarifa B, DolarPapel, Costo)
- Resumen detallado de las operaciones realizadas
- Indicador de progreso durante la actualización

## Requisitos

- PHP 7.4 o superior
- Servidor web Apache con mod_rewrite habilitado
- Base de datos Microsoft Access (Factusol)
- Extensiones PHP: PDO, PDO_ODBC
- Composer para gestión de dependencias

## Instalación

1. Clonar o descargar este repositorio en el directorio web de su servidor
2. Ejecutar `composer install` para instalar las dependencias
3. Configurar la conexión a la base de datos en `config/database.php`
4. Asegurarse que el servidor web tenga permisos de escritura en el directorio de logs

## Estructura del Proyecto

```
/actualizador-precios/
│
├── assets/                     # Recursos estáticos
│   ├── css/                    # Hojas de estilo CSS
│   ├── js/                     # Archivos JavaScript
│   └── img/                    # Imágenes
│
├── config/                     # Configuraciones
│   └── database.php            # Configuración de conexión a la base de datos
│
├── controllers/                # Controladores
│   ├── IndexController.php     # Controlador principal
│   ├── PrecioController.php    # Controlador para actualización de precios
│   └── LogController.php       # Controlador para logs/resumen
│
├── models/                     # Modelos
│   ├── Precio.php              # Modelo para precios
│   ├── Log.php                 # Modelo para logs
│   └── HelperDB.php            # Ayudante para interactuar con la base de datos
│
├── tests/                      # Tests unitarios
│   ├── PrecioTest.php          # Tests para el modelo Precio
│   └── LogTest.php             # Tests para el modelo Log
│
├── vendor/                     # Dependencias (generado por Composer)
│
├── views/                      # Vistas
│   ├── templates/              # Plantillas
│   │   ├── header.php          # Cabecera común
│   │   └── footer.php          # Pie común
│   ├── index.php               # Página principal
│   ├── planilla.php            # Vista de actualización por planilla
│   ├── familia.php             # Vista de actualización por familia
│   └── resumen.php             # Vista de resumen de operaciones
│
├── composer.json               # Configuración de Composer
├── .htaccess                   # Configuración de Apache
├── index.php                   # Punto de entrada de la aplicación
└── README.md                   # Documentación
```

## Uso

1. Acceder a la aplicación a través de un navegador web
2. Seleccionar el método de actualización deseado (Por Planilla o Por Familia)
3. Configurar los parámetros de actualización según necesidades
4. Ejecutar la actualización y revisar el resumen de operaciones

## Tests

Para ejecutar los tests unitarios:

```
composer test
```

## Contacto

Para soporte o consultas, contactar a:
- Adrian Garcia - adriang_1174@hotmail.com

## Licencia

Desarrollado exclusivamente para SB Argentina.
