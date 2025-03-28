<?php
/**
 * Clase Precio
 * 
 * Gestiona la actualización de precios en Factusol
 */
class Precio {
    /**
     * Helper para interactuar con la base de datos
     * @var HelperDB
     */
    private $db;
    
    /**
     * Constructor de la clase
     */
    public function __construct() {
        $this->db = HelperDB::getInstance();
    }
    
    /**
     * Busca un artículo por su código equivalente
     * 
     * @param string $equivalente Código equivalente del artículo (código del proveedor)
     * @return array|null Información del artículo o null si no se encuentra
     */
    public function buscarArticulo($equivalente) {
        $query = "SELECT CODART, EQUART, FAMART, PCOART FROM F_ART WHERE EQUART = ?";
        $result = $this->db->select($query, [$equivalente]);
        
        return !empty($result) ? $result[0] : null;
    }
    
    /**
     * Actualiza el precio de un artículo
     * 
     * @param string $codigoPrecio Código del artículo
     * @param float $coef Coeficiente de actualización
     * @param bool $tarifaA Indica si se debe actualizar la Tarifa A
     * @param bool $tarifaB Indica si se debe actualizar la Tarifa B
     * @param bool $dolarPapel Indica si se debe actualizar el DolarPapel
     * @param bool $costo Indica si se debe actualizar el costo
     * @return bool True si la actualización fue exitosa, False en caso contrario
     */
    public function actualizarPrecio($codigoPrecio, $coef, $tarifaA, $tarifaB, $dolarPapel, $costo) {
        try {
            $this->db->beginTransaction();
            $actualizacionExitosa = true;
            
            // Actualizar Tarifa A (TARLTA=1)
            if ($tarifaA) {
                $query = "SELECT PRELTA FROM F_LTA WHERE ARTLTA = ? AND TARLTA = 1";
                $result = $this->db->select($query, [$codigoPrecio]);
                
                if (!empty($result)) {
                    $precioActual = $result[0]['PRELTA'];
                    $nuevoPrecio = $precioActual * $coef;
                    
                    $updateQuery = "UPDATE F_LTA SET PRELTA = ? WHERE ARTLTA = ? AND TARLTA = 1";
                    $this->db->execute($updateQuery, [$nuevoPrecio, $codigoPrecio]);
                } else {
                    $actualizacionExitosa = false;
                }
            }
            
            // Actualizar Tarifa B (TARLTA=2)
            if ($tarifaB) {
                $query = "SELECT PRELTA FROM F_LTA WHERE ARTLTA = ? AND TARLTA = 2";
                $result = $this->db->select($query, [$codigoPrecio]);
                
                if (!empty($result)) {
                    $precioActual = $result[0]['PRELTA'];
                    $nuevoPrecio = $precioActual * $coef;
                    
                    $updateQuery = "UPDATE F_LTA SET PRELTA = ? WHERE ARTLTA = ? AND TARLTA = 2";
                    $this->db->execute($updateQuery, [$nuevoPrecio, $codigoPrecio]);
                } else {
                    $actualizacionExitosa = false;
                }
            }
            
            // Actualizar DolarPapel (TARLTA=3)
            if ($dolarPapel) {
                $query = "SELECT PRELTA FROM F_LTA WHERE ARTLTA = ? AND TARLTA = 3";
                $result = $this->db->select($query, [$codigoPrecio]);
                
                if (!empty($result)) {
                    $precioActual = $result[0]['PRELTA'];
                    $nuevoPrecio = $precioActual * $coef;
                    
                    $updateQuery = "UPDATE F_LTA SET PRELTA = ? WHERE ARTLTA = ? AND TARLTA = 3";
                    $this->db->execute($updateQuery, [$nuevoPrecio, $codigoPrecio]);
                } else {
                    $actualizacionExitosa = false;
                }
            }
            
            // Actualizar Costo
            if ($costo) {
                $query = "SELECT PCOART FROM F_ART WHERE CODART = ?";
                $result = $this->db->select($query, [$codigoPrecio]);
                
                if (!empty($result)) {
                    $costoActual = $result[0]['PCOART'];
                    $nuevoCosto = $costoActual * $coef;
                    
                    $updateQuery = "UPDATE F_ART SET PCOART = ? WHERE CODART = ?";
                    $this->db->execute($updateQuery, [$nuevoCosto, $codigoPrecio]);
                } else {
                    $actualizacionExitosa = false;
                }
            }
            
            $this->db->commit();
            return $actualizacionExitosa;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Error al actualizar precio: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene la lista de artículos de una familia
     * 
     * @param string $familia Código de la familia
     * @return array Lista de artículos de la familia
     */
    public function getFamiliasList($familia) {
        $query = "SELECT CODART, EQUART, FAMART, PCOART FROM F_ART WHERE FAMART = ?";
        return $this->db->select($query, [$familia]);
    }
    
    /**
     * Obtiene todas las familias disponibles en el sistema
     * 
     * @return array Lista de familias
     */
    public function getAllFamilias() {
        $query = "SELECT DISTINCT FAMART FROM F_ART ORDER BY FAMART";
        return $this->db->select($query);
    }
    
    /**
     * Actualiza los precios de toda una familia
     * 
     * @param string $familia Código de la familia
     * @param float $coef Coeficiente de actualización
     * @param bool $tarifaA Indica si se debe actualizar la Tarifa A
     * @param bool $tarifaB Indica si se debe actualizar la Tarifa B
     * @param bool $dolarPapel Indica si se debe actualizar el DolarPapel
     * @param bool $costo Indica si se debe actualizar el costo
     * @param int $logId ID del registro de log para esta operación
     * @return array Resultado de la operación
     */
    public function actualizarFamilia($familia, $coef, $tarifaA, $tarifaB, $dolarPapel, $costo, $logId) {
        $articulos = $this->getFamiliasList($familia);
        $totalActualizados = 0;
        $log = new Log();
        
        foreach ($articulos as $articulo) {
            $resultado = $this->actualizarPrecio(
                $articulo['CODART'],
                $coef,
                $tarifaA,
                $tarifaB,
                $dolarPapel,
                $costo
            );
            
            // Registrar en el log
            $estado = $resultado ? 'exitoso' : 'fallido';
            $log->registrarActualizacion(
                $logId,
                $articulo['CODART'],
                $articulo['EQUART'],
                $familia,
                $estado
            );
            
            if ($resultado) {
                $totalActualizados++;
            }
        }
        
        return [
            'total' => count($articulos),
            'actualizados' => $totalActualizados,
            'fallidos' => count($articulos) - $totalActualizados
        ];
    }
    
    /**
     * Actualiza precios a partir de una planilla
     * 
     * @param string $filePath Ruta al archivo de la planilla
     * @param int $columnaCodigo Columna que contiene el código equivalente
     * @param int $columnaValor Columna que contiene el valor
     * @param int $filaInicio Fila a partir de la cual comienzan los datos
     * @param float $coef Coeficiente de actualización
     * @param bool $tarifaA Indica si se debe actualizar la Tarifa A
     * @param bool $tarifaB Indica si se debe actualizar la Tarifa B
     * @param bool $dolarPapel Indica si se debe actualizar el DolarPapel
     * @param bool $costo Indica si se debe actualizar el costo
     * @param int $logId ID del registro de log para esta operación
     * @return array Resultado de la operación
     */
    public function actualizarPorPlanilla($filePath, $columnaCodigo, $columnaValor, $filaInicio, $coef, $tarifaA, $tarifaB, $dolarPapel, $costo, $logId) {
        // Necesitamos la biblioteca PhpSpreadsheet para leer el archivo Excel
        // Se asume que está instalada a través de Composer
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $highestRow = $worksheet->getHighestRow();
        $log = new Log();
        
        $totalFilas = $highestRow - $filaInicio + 1;
        $totalActualizados = 0;
        $familia = null;
        
        // Registrar el total de filas en la planilla
        $log->totalFilasFile($logId, $totalFilas);
        
        for ($row = $filaInicio; $row <= $highestRow; $row++) {
            $codEquivalente = $worksheet->getCellByColumnAndRow($columnaCodigo, $row)->getValue();
            
            // Buscar el artículo por su código equivalente
            $articulo = $this->buscarArticulo($codEquivalente);
            
            if ($articulo) {
                $resultado = $this->actualizarPrecio(
                    $articulo['CODART'],
                    $coef,
                    $tarifaA,
                    $tarifaB,
                    $dolarPapel,
                    $costo
                );
                
                // Guardar la familia para el registro de totales
                if ($familia === null) {
                    $familia = $articulo['FAMART'];
                }
                
                // Registrar en el log
                $estado = $resultado ? 'exitoso' : 'fallido';
                $log->registrarActualizacion(
                    $logId,
                    $articulo['CODART'],
                    $codEquivalente,
                    $articulo['FAMART'],
                    $estado
                );
                
                if ($resultado) {
                    $totalActualizados++;
                }
            }
        }
        
        // Si se encontró al menos un artículo con familia, registrar la información de la familia
        if ($familia !== null) {
            $log->totalArticulosFamilia($logId, $familia);
        }
        
        return [
            'total' => $totalFilas,
            'actualizados' => $totalActualizados,
            'fallidos' => $totalFilas - $totalActualizados
        ];
    }
}
