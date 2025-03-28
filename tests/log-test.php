<?php
/**
 * Tests unitarios para el modelo Log
 * 
 * @author Claude
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    /**
     * @var Log
     */
    private $log;
    
    /**
     * @var HelperDB Mock
     */
    private $helperDbMock;
    
    /**
     * Configuración inicial para cada test
     */
    protected function setUp(): void
    {
        // Creamos un mock para HelperDB ya que no queremos acceder a la base de datos real
        $this->helperDbMock = $this->createMock(HelperDB::class);
        $this->log = new Log($this->helperDbMock);
    }
    
    /**
     * Test para verificar que el método crearRegistro crea correctamente un nuevo registro de log
     */
    public function testCrearRegistro()
    {
        // Configuramos el mock para simular la inserción exitosa y retornar un ID
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->with($this->stringContains("INSERT INTO ACTUALIZADOR_LOG"))
            ->willReturn(true);
            
        $this->helperDbMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn(123);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->crearRegistro();
        
        // Verificamos que retorne el ID esperado
        $this->assertEquals(123, $resultado);
    }
    
    /**
     * Test para verificar que el método registrarActualizacion registra correctamente una actualización
     */
    public function testRegistrarActualizacion()
    {
        // Datos de prueba
        $codLog = 123;
        $codPrecio = '001';
        $equivalente = 'ABC123';
        $familia = 'FAM01';
        $status = 1; // 1 = Éxito
        
        // Configuramos el mock para simular la inserción exitosa
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->with($this->stringContains("INSERT INTO ACTUALIZADOR_LOG_DETALLE"))
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->registrarActualizacion($codLog, $codPrecio, $equivalente, $familia, $status);
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método totalArticulosFamilia calcula correctamente el total
     */
    public function testTotalArticulosFamilia()
    {
        // Datos de prueba
        $codLog = 123;
        $familia = 'FAM01';
        $totalEsperado = 10;
        
        // Configuramos el mock para que retorne el total esperado
        $this->helperDbMock->expects($this->once())
            ->method('queryScalar')
            ->with($this->stringContains("SELECT COUNT(*) FROM F_ART WHERE FAMART = '$familia'"))
            ->willReturn($totalEsperado);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->totalArticulosFamilia($codLog, $familia);
        
        // Verificamos el resultado
        $this->assertEquals($totalEsperado, $resultado);
    }
    
    /**
     * Test para verificar que el método totalFilasFile registra y retorna correctamente el total
     */
    public function testTotalFilasFile()
    {
        // Datos de prueba
        $codLog = 123;
        $cantidad = 50;
        
        // Configuramos el mock para simular la actualización exitosa
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->with($this->stringContains("UPDATE ACTUALIZADOR_LOG SET TOTAL_FILAS = $cantidad"))
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->totalFilasFile($codLog, $cantidad);
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método totalArticulosFamiliaSinActualizar calcula correctamente
     */
    public function testTotalArticulosFamiliaSinActualizar()
    {
        // Datos de prueba
        $familia = 'FAM01';
        $codLog = 123;
        $totalEsperado = 5;
        
        // Configuramos el mock para que retorne el total esperado
        $this->helperDbMock->expects($this->once())
            ->method('queryScalar')
            ->with($this->logicalAnd(
                $this->stringContains("SELECT COUNT(DISTINCT F_ART.CODART)"),
                $this->stringContains("FROM F_ART"),
                $this->stringContains("LEFT JOIN ACTUALIZADOR_LOG_DETALLE"),
                $this->stringContains("WHERE F_ART.FAMART = '$familia'"),
                $this->stringContains("AND (ACTUALIZADOR_LOG_DETALLE.ID_LOG != $codLog OR ACTUALIZADOR_LOG_DETALLE.ID_LOG IS NULL)")
            ))
            ->willReturn($totalEsperado);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->totalArticulosFamiliaSinActualizar($familia, $codLog);
        
        // Verificamos el resultado
        $this->assertEquals($totalEsperado, $resultado);
    }
    
    /**
     * Test para verificar que el método totalArticulosActualizados calcula correctamente
     */
    public function testTotalArticulosActualizados()
    {
        // Datos de prueba
        $codLog = 123;
        $totalEsperado = 45;
        
        // Configuramos el mock para que retorne el total esperado
        $this->helperDbMock->expects($this->once())
            ->method('queryScalar')
            ->with($this->stringContains("SELECT COUNT(*) FROM ACTUALIZADOR_LOG_DETALLE WHERE ID_LOG = $codLog AND STATUS = 1"))
            ->willReturn($totalEsperado);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->totalArticulosActualizados($codLog);
        
        // Verificamos el resultado
        $this->assertEquals($totalEsperado, $resultado);
    }
    
    /**
     * Test para verificar que el método listadoArticulosFamiliaSinActualizar retorna correctamente
     */
    public function testListadoArticulosFamiliaSinActualizar()
    {
        // Datos de prueba
        $familia = 'FAM01';
        $codLog = 123;
        $articulosEsperados = [
            ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'],
            ['CODART' => '002', 'EQUART' => 'DEF456', 'FAMART' => 'FAM01']
        ];
        
        // Configuramos el mock para que retorne los artículos esperados
        $this->helperDbMock->expects($this->once())
            ->method('query')
            ->with($this->logicalAnd(
                $this->stringContains("SELECT F_ART.*"),
                $this->stringContains("FROM F_ART"),
                $this->stringContains("LEFT JOIN ACTUALIZADOR_LOG_DETALLE"),
                $this->stringContains("WHERE F_ART.FAMART = '$familia'"),
                $this->stringContains("AND (ACTUALIZADOR_LOG_DETALLE.ID_LOG != $codLog OR ACTUALIZADOR_LOG_DETALLE.ID_LOG IS NULL)")
            ))
            ->willReturn($articulosEsperados);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->listadoArticulosFamiliaSinActualizar($familia, $codLog);
        
        // Verificamos el resultado
        $this->assertEquals($articulosEsperados, $resultado);
        $this->assertCount(2, $resultado);
    }
    
    /**
     * Test para verificar el método crearRegistro cuando ocurre un error
     */
    public function testCrearRegistroError()
    {
        // Configuramos el mock para simular un error en la inserción
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->crearRegistro();
        
        // Verificamos que retorne false o 0 en caso de error
        $this->assertFalse($resultado);
    }
    
    /**
     * Test para verificar el método registrarActualizacion cuando ocurre un error
     */
    public function testRegistrarActualizacionError()
    {
        // Datos de prueba
        $codLog = 123;
        $codPrecio = '001';
        $equivalente = 'ABC123';
        $familia = 'FAM01';
        $status = 1;
        
        // Configuramos el mock para simular un error en la inserción
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        
        // Ejecutamos el método a probar
        $resultado = $this->log->registrarActualizacion($codLog, $codPrecio, $equivalente, $familia, $status);
        
        // Verificamos que retorne false en caso de error
        $this->assertFalse($resultado);
    }
}
