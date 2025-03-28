<?php
/**
 * Tests unitarios para el controlador LogController
 * 
 * @author Claude
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class LogControllerTest extends TestCase
{
    /**
     * @var LogController
     */
    private $logController;
    
    /**
     * @var Log Mock
     */
    private $logModelMock;
    
    /**
     * Configuración inicial para cada test
     */
    protected function setUp(): void
    {
        // Creamos un mock para el modelo Log
        $this->logModelMock = $this->createMock(Log::class);
        
        // Creamos una instancia del controlador con el mock
        $this->logController = new LogController($this->logModelMock);
    }
    
    /**
     * Test para verificar que el método obtenerLogs retorna los logs correctamente
     */
    public function testObtenerLogs()
    {
        // Datos esperados
        $logsEsperados = [
            [
                'ID' => 123,
                'FECHA' => '2024-08-10 15:30:00',
                'TOTAL_FILAS' => 50,
                'TOTAL_ACTUALIZADOS' => 48
            ],
            [
                'ID' => 124,
                'FECHA' => '2024-08-11 09:45:00',
                'TOTAL_FILAS' => 30,
                'TOTAL_ACTUALIZADOS' => 30
            ]
        ];
        
        // Configuramos el mock
        $this->logModelMock->expects($this->once())
            ->method('obtenerLogs')
            ->willReturn($logsEsperados);
        
        // Simulamos la llamada al método
        $respuesta = $this->logController->obtenerLogs();
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($logsEsperados, $respuesta['data']);
    }
    
    /**
     * Test para verificar que el método obtenerDetalleLog retorna el detalle de un log correctamente
     */
    public function testObtenerDetalleLog()
    {
        // Datos de prueba
        $logId = 123;
        
        // Datos esperados
        $detalleEsperado = [
            [
                'ID' => 1,
                'ID_LOG' => 123,
                'COD_PRECIO' => '001',
                'EQUIVALENTE' => 'ABC123',
                'FAMILIA' => 'FAM01',
                'STATUS' => 1,
                'FECHA' => '2024-08-10 15:30:01'
            ],
            [
                'ID' => 2,
                'ID_LOG' => 123,
                'COD_PRECIO' => '002',
                'EQUIVALENTE' => 'DEF456',
                'FAMILIA' => 'FAM01',
                'STATUS' => 1,
                'FECHA' => '2024-08-10 15:30:02'
            ]
        ];
        
        // Configuramos el mock
        $this->logModelMock->expects($this->once())
            ->method('obtenerDetalleLog')
            ->with($logId)
            ->willReturn($detalleEsperado);
        
        // Simulamos la llamada al método
        $respuesta = $this->logController->obtenerDetalleLog(['logId' => $logId]);
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($detalleEsperado, $respuesta['data']);
    }
    
    /**
     * Test para verificar que el método obtenerResumenLog retorna el resumen de un log correctamente
     */
    public function testObtenerResumenLog()
    {
        // Datos de prueba
        $logId = 123;
        
        // Datos esperados
        $resumenEsperado = [
            'TOTAL_FILAS' => 50,
            'TOTAL_ACTUALIZADOS' => 48,
            'TOTAL_ERRORES' => 2,
            'FECHA' => '2024-08-10 15:30:00'
        ];
        
        // Configuramos el mock
        $this->logModelMock->expects($this->once())
            ->method('obtenerResumenLog')
            ->with($logId)
            ->willReturn($resumenEsperado);
        
        // Simulamos la llamada al método
        $respuesta = $this->logController->obtenerResumenLog(['logId' => $logId]);
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($resumenEsperado, $respuesta['data']);
    }
    
    /**
     * Test para verificar que el método exportarLog exporta correctamente los datos de un log
     */
    public function testExportarLog()
    {
        // Datos de prueba
        $logId = 123;
        $formato = 'csv';
        
        // Datos esperados para la exportación
        $datosPlanillaEsperados = [
            [
                'Cod. Artículo' => '001',
                'Equivalente' => 'ABC123',
                'Familia' => 'FAM01',
                'Estado' => 'Actualizado',
                'Fecha' => '2024-08-10 15:30:01'
            ],
            [
                'Cod. Artículo' => '002',
                'Equivalente' => 'DEF456',
                'Familia' => 'FAM01',
                'Estado' => 'Actualizado',
                'Fecha' => '2024-08-10 15:30:02'
            ]
        ];
        
        // Configuramos el mock
        $this->logModelMock->expects($this->once())
            ->method('obtenerDatosExportacion')
            ->with($logId)
            ->willReturn($datosPlanillaEsperados);
        
        // Simulamos la llamada al método (sin testear la generación real del archivo)
        $resultado = $this->callProtectedMethod($this->logController, 'prepararDatosExportacion', [$logId, $formato]);
        
        // Verificamos que los datos se preparen correctamente
        $this->assertIsArray($resultado);
        $this->assertEquals($datosPlanillaEsperados, $resultado['datos']);
        $this->assertEquals($formato, $resultado['formato']);
    }
    
    /**
     * Test para verificar el manejo de errores en obtenerDetalleLog
     */
    public function testObtenerDetalleLogError()
    {
        // Datos de prueba con error (sin logId)
        $datosGet = [];
        
        // Simulamos la llamada al método
        $respuesta = $