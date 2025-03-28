<?php
/**
 * Configuración de conexión a la base de datos Microsoft Access
 * 
 * Este archivo contiene las constantes y configuraciones necesarias para
 * conectarse a la base de datos Access de Factusol mediante ODBC
 */

// Parámetros de conexión a la base de datos Access (Factusol)
define('DB_DSN', 'odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=' . ROOT_PATH . '/database/factusol.mdb');
define('DB_USER', '');  // No suele necesitarse para Access
define('DB_PASS', '');  // No suele necesitarse para Access

/**
 * Obtiene una conexión PDO a la base de datos
 * 
 * @return PDO Objeto de conexión PDO
 * @throws PDOException Si ocurre un error durante la conexión
 */
function getDbConnection() {
    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Registrar el error y mostrar un mensaje amigable
        error_log('Error de conexión a la base de datos: ' . $e->getMessage());
        throw new PDOException('Error de conexión a la base de datos. Por favor, contacte al administrador.');
    }
}
