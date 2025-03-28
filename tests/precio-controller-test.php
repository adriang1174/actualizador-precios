<?php
/**
 * Tests unitarios para el controlador PrecioController
 * 
 * @author Claude
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class PrecioControllerTest extends TestCase
{
    /**
     * @var PrecioController
     */
    private $precioController;
    
    /**
     * @var Precio Mock
     */
    private $precioModelMock;
    
    /**
     * @var Log Mock
     */
    private $logModelMock;
    
    /**
     * Configuración inicial para cada test
     */
    protected function setUp(): void
    {
        // Creamos mocks para los modelos que utiliza el controlador
        $this->precioModelMock = $this->createMock(Precio::class);
        $this->logModelMock = $this->createMock(Log::class);
        
        // Creamos una instancia del controlador con los mocks
        $this->precioController = new PrecioController($this->precioModelMock, $this->logModelMock);
    }
    
    /**
     * Test para verificar que el método actualizarPorPlanilla procesa correctamente una planilla
     */
    public function testActualizarPorPlanilla()
    {
        // Datos de prueba
        $datosPost = [
            'fileData' => [
                ['equiv' => 'ABC123', 'valor' => 100],
                ['equiv' => 'DEF456', 'valor' => 200]
            ],
            'coeficiente' => 1.2,
            'actualizarTarifaA' => true,
            'actualizarTarifaB' => false,
            'actualizarDolarPapel' => false,
            'actualizarCosto' => true
        ];
        
        // Log ID para la operación
        $logId = 123;
        
        // Artículos encontrados en la base de datos
        $articulo1 = ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'];
        $articulo2 = ['CODART' => '002', 'EQUART' => 'DEF456', 'FAMART' => 'FAM02'];
        
        // Configuramos los mocks
        // 1. Crear registro de log
        $this->logModelMock->expects($this->once())
            ->method('crearRegistro')
            ->willReturn($logId);
        
        // 2. Registrar total de filas
        $this->logModelMock->expects($this->once())
            ->method('totalFilasFile')
            ->with($logId, 2)
            ->willReturn(true);
        
        // 3. Buscar artículos y actualizar precios
        $this->precioModelMock->expects($this->exactly(2))
            ->method('buscarArticulo')
            ->withConsecutive(['ABC123'], ['DEF456'])
            ->willReturnOnConsecutiveCalls($articulo1, $articulo2);
        
        $this->precioModelMock->expects($this->exactly(2))
            ->method('actualizarPrecio')
            ->withConsecutive(
                ['001', 1.2, true, false, false, true],
                ['002', 1.2, true, false, false, true]
            )
            ->willReturnOnConsecutiveCalls(true, true);
        
        // 4. Registrar actualizaciones
        $this->logModelMock->expects($this->exactly(2))
            ->method('registrarActualizacion')
            ->withConsecutive(
                [$logId, '001', 'ABC123', 'FAM01', 1],
                [$logId, '002', 'DEF456', 'FAM02', 1]
            )
            ->willReturn(true);
        
        // Simulamos la llamada al método (normalmente se llamaría a través de una solicitud HTTP)
        $respuesta = $this->precioController->actualizarPorPlanilla($datosPost);
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($logId, $respuesta['logId']);
        $this->assertEquals(2, $respuesta['totalActualizados']);
    }
    
    /**
     * Test para verificar que el método actualizarPorFamilia procesa correctamente todos los artículos de una familia
     */
    public function testActualizarPorFamilia()
    {
        // Datos de prueba
        $datosPost = [
            'familia' => 'FAM01',
            'coeficiente' => 1.15,
            'actualizarTarifaA' => true,
            'actualizarTarifaB' => true,
            'actualizarDolarPapel' => false,
            'actualizarCosto' => false
        ];
        
        // Log ID para la operación
        $logId = 124;
        
        // Artículos de la familia
        $articulosFamilia = [
            ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'],
            ['CODART' => '002', 'EQUART' => 'DEF456', 'FAMART' => 'FAM01'],
            ['CODART' => '003', 'EQUART' => 'GHI789', 'FAMART' => 'FAM01']
        ];
        
        // Configuramos los mocks
        // 1. Crear registro de log
        $this->logModelMock->expects($this->once())
            ->method('crearRegistro')
            ->willReturn($logId);
        
        // 2. Obtener artículos de la familia
        $this->precioModelMock->expects($this->once())
            ->method('getFamiliasList')
            ->with('FAM01')
            ->willReturn($articulosFamilia);
        
        // 3. Actualizar precios de los artículos
        $this->precioModelMock->expects($this->exactly(3))
            ->method('actualizarPrecio')
            ->withConsecutive(
                ['001', 1.15, true, true, false, false],
                ['002', 1.15, true, true, false, false],
                ['003', 1.15, true, true, false, false]
            )
            ->willReturnOnConsecutiveCalls(true, true, true);
        
        // 4. Registrar actualizaciones
        $this->logModelMock->expects($this->exactly(3))
            ->method('registrarActualizacion')
            ->withConsecutive(
                [$logId, '001', 'ABC123', 'FAM01', 1],
                [$logId, '002', 'DEF456', 'FAM01', 1],
                [$logId, '003', 'GHI789', 'FAM01', 1]
            )
            ->willReturn(true);
        
        // 5. Obtener total de artículos en la familia
        $this->logModelMock->expects($this->once())
            ->method('totalArticulosFamilia')
            ->with($logId, 'FAM01')
            ->willReturn(3);
        
        // Simulamos la llamada al método
        $respuesta = $this->precioController->actualizarPorFamilia($datosPost);
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($logId, $respuesta['logId']);
        $this->assertEquals(3, $respuesta['totalActualizados']);
    }
    
    /**
     * Test para verificar que el método obtenerResumen retorna el resumen de operaciones correctamente
     */
    public function testObtenerResumen()
    {
        // Datos de prueba
        $logId = 123;
        $familia = 'FAM01';
        
        // Resumen esperado
        $resumenEsperado = [
            'totalArticulosFamilia' => 10,
            'totalFilasPlanilla' => 8,
            'totalArticulosFamiliaSinActualizar' => 2,
            'totalArticulosActualizados' => 8,
            'listadoArticulosFamiliaSinActualizar' => [
                ['CODART' => '009', 'EQUART' => 'YZA987', 'FAMART' => 'FAM01'],
                ['CODART' => '010', 'EQUART' => 'XYZ654', 'FAMART' => 'FAM01']
            ]
        ];
        
        // Configuramos los mocks
        // 1. Total de artículos en la familia
        $this->logModelMock->expects($this->once())
            ->method('totalArticulosFamilia')
            ->with($logId, $familia)
            ->willReturn(10);
        
        // 2. Total de filas en la planilla (solo para actualización por planilla)
        $this->logModelMock->expects($this->once())
            ->method('getTotalFilasLog')
            ->with($logId)
            ->willReturn(8);
        
        // 3. Total de artículos sin actualizar
        $this->logModelMock->expects($this->once())
            ->method('totalArticulosFamiliaSinActualizar')
            ->with($familia, $logId)
            ->willReturn(2);
        
        // 4. Total de artículos actualizados
        $this->logModelMock->expects($this->once())
            ->method('totalArticulosActualizados')
            ->with($logId)
            ->willReturn(8);
        
        // 5. Listado de artículos sin actualizar
        $this->logModelMock->expects($this->once())
            ->method('listadoArticulosFamiliaSinActualizar')
            ->with($familia, $logId)
            ->willReturn([
                ['CODART' => '009', 'EQUART' => 'YZA987', 'FAMART' => 'FAM01'],
                ['CODART' => '010', 'EQUART' => 'XYZ654', 'FAMART' => 'FAM01']
            ]);
        
        // Simulamos la llamada al método
        $respuesta = $this->precioController->obtenerResumen(['logId' => $logId, 'familia' => $familia]);
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($resumenEsperado, $respuesta['data']);
    }
    
    /**
     * Test para verificar que el método obtenerFamilias retorna la lista de familias correctamente
     */
    public function testObtenerFamilias()
    {
        // Familias esperadas
        $familiasEsperadas = [
            ['FAMART' => 'FAM01', 'DESFAM' => 'Familia 1'],
            ['FAMART' => 'FAM02', 'DESFAM' => 'Familia 2'],
            ['FAMART' => 'FAM03', 'DESFAM' => 'Familia 3']
        ];
        
        // Configuramos el mock
        $this->precioModelMock->expects($this->once())
            ->method('getFamilias')
            ->willReturn($familiasEsperadas);
        
        // Simulamos la llamada al método
        $respuesta = $this->precioController->obtenerFamilias();
        
        // Verificamos la respuesta
        $this->assertIsArray($respuesta);
        $this->assertEquals('success', $respuesta['status']);
        $this->assertEquals($familiasEsperadas, $respuesta['data']);
    }
    
    /**
     * Test para verificar el manejo de errores en actualizarPorPlanilla
     */
    public function testActualizarPorPlanillaError()
    {
        // Datos de prueba con error (sin datos de planilla)
        $datosPost = [
            'coeficiente' => 1.2,
            'actualizarTarifaA' => true
        ];
        
        // Simulamos la llamada al método
        $respuesta = $this->precioController->actualizarPorPlanilla($datosPost);
        
        // Verificamos que se retorne un error
        $this->assertIsArray($respuesta);
        $this->assertEquals('error', $respuesta['status']);
        $this->assertNotEmpty($respuesta['message']);
    }
    
    /**
     * Test para verificar el manejo de errores en actualizarPorFamilia
     */
    public function testActualizarPorFamiliaError()
    {
        // Datos de prueba con error (sin familia)
        $datosPost = [
            'coeficiente' => 1.15,
            'actualizarTarifaA' => true
        ];
        
        // Simulamos la llamada al método
        $respuesta = $this->precioController->actualizarPorFamilia($datosPost);
        
        // Verificamos que se retorne un error
        $this->assertIsArray($respuesta);
        $this->assertEquals('error', $respuesta['status']);
        $this->assertNotEmpty($respuesta['message']);
    }
}
