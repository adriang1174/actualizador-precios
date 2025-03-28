<?php
/**
 * Tests unitarios para el modelo Precio
 * 
 * @author Claude
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class PrecioTest extends TestCase
{
    /**
     * @var Precio
     */
    private $precio;
    
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
        $this->precio = new Precio($this->helperDbMock);
    }
    
    /**
     * Test para verificar que el método buscarArticulo encuentra un artículo por su equivalente
     */
    public function testBuscarArticuloPorEquivalenteExistente()
    {
        // Datos de prueba
        $codigoEquivalente = 'ABC123';
        $articulo = [
            'CODART' => '001',
            'EQUART' => 'ABC123',
            'FAMART' => 'FAM01',
            'PCOART' => 100.00
        ];
        
        // Configuramos el mock para que retorne un artículo
        $this->helperDbMock->expects($this->once())
            ->method('query')
            ->with($this->stringContains("SELECT * FROM F_ART WHERE EQUART = '$codigoEquivalente'"))
            ->willReturn([$articulo]);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->buscarArticulo($codigoEquivalente);
        
        // Verificamos el resultado
        $this->assertEquals($articulo, $resultado);
    }
    
    /**
     * Test para verificar que el método buscarArticulo retorna null cuando no encuentra un artículo
     */
    public function testBuscarArticuloPorEquivalenteInexistente()
    {
        // Datos de prueba
        $codigoEquivalente = 'XYZ999';
        
        // Configuramos el mock para que retorne un array vacío
        $this->helperDbMock->expects($this->once())
            ->method('query')
            ->with($this->stringContains("SELECT * FROM F_ART WHERE EQUART = '$codigoEquivalente'"))
            ->willReturn([]);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->buscarArticulo($codigoEquivalente);
        
        // Verificamos el resultado
        $this->assertNull($resultado);
    }
    
    /**
     * Test para verificar que el método actualizarPrecio actualiza correctamente el costo
     */
    public function testActualizarPrecioCosto()
    {
        // Datos de prueba
        $codigoPrecio = '001';
        $coeficiente = 1.10;
        $actualizarTarifaA = false;
        $actualizarTarifaB = false;
        $actualizarDolarPapel = false;
        $actualizarCosto = true;
        
        // Artículo actual
        $articulo = [
            'CODART' => '001',
            'PCOART' => 100.00
        ];
        
        // Configuramos el mock para que retorne el artículo
        $this->helperDbMock->expects($this->once())
            ->method('queryOne')
            ->with($this->stringContains("SELECT * FROM F_ART WHERE CODART = '$codigoPrecio'"))
            ->willReturn($articulo);
        
        // Esperamos que se ejecute una actualización para el costo
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->with(
                $this->stringContains("UPDATE F_ART SET PCOART = 110.00 WHERE CODART = '$codigoPrecio'")
            )
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->actualizarPrecio(
            $codigoPrecio, 
            $coeficiente, 
            $actualizarTarifaA, 
            $actualizarTarifaB, 
            $actualizarDolarPapel, 
            $actualizarCosto
        );
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método actualizarPrecio actualiza correctamente la tarifa A
     */
    public function testActualizarPrecioTarifaA()
    {
        // Datos de prueba
        $codigoPrecio = '001';
        $coeficiente = 1.20;
        $actualizarTarifaA = true;
        $actualizarTarifaB = false;
        $actualizarDolarPapel = false;
        $actualizarCosto = false;
        
        // Precio actual en tarifa A
        $precioTarifaA = [
            'ARTLTA' => '001',
            'TARLTA' => 1,
            'PRELTA' => 200.00
        ];
        
        // Configuramos el mock para que retorne el precio de la tarifa A
        $this->helperDbMock->expects($this->once())
            ->method('queryOne')
            ->with($this->stringContains("SELECT * FROM F_LTA WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 1"))
            ->willReturn($precioTarifaA);
        
        // Esperamos que se ejecute una actualización para la tarifa A
        $this->helperDbMock->expects($this->once())
            ->method('execute')
            ->with(
                $this->stringContains("UPDATE F_LTA SET PRELTA = 240.00 WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 1")
            )
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->actualizarPrecio(
            $codigoPrecio, 
            $coeficiente, 
            $actualizarTarifaA, 
            $actualizarTarifaB, 
            $actualizarDolarPapel, 
            $actualizarCosto
        );
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método actualizarPrecio actualiza correctamente múltiples tarifas
     */
    public function testActualizarPrecioMultiplesTarifas()
    {
        // Datos de prueba
        $codigoPrecio = '001';
        $coeficiente = 1.30;
        $actualizarTarifaA = true;
        $actualizarTarifaB = true;
        $actualizarDolarPapel = true;
        $actualizarCosto = true;
        
        // Artículo actual (para el costo)
        $articulo = [
            'CODART' => '001',
            'PCOART' => 100.00
        ];
        
        // Precios actuales en diferentes tarifas
        $precioTarifaA = ['ARTLTA' => '001', 'TARLTA' => 1, 'PRELTA' => 200.00];
        $precioTarifaB = ['ARTLTA' => '001', 'TARLTA' => 2, 'PRELTA' => 220.00];
        $precioDolarPapel = ['ARTLTA' => '001', 'TARLTA' => 3, 'PRELTA' => 20.00];
        
        // Configuramos los mocks para las consultas
        $this->helperDbMock->expects($this->exactly(4))
            ->method('queryOne')
            ->withConsecutive(
                [$this->stringContains("SELECT * FROM F_ART WHERE CODART = '$codigoPrecio'")],
                [$this->stringContains("SELECT * FROM F_LTA WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 1")],
                [$this->stringContains("SELECT * FROM F_LTA WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 2")],
                [$this->stringContains("SELECT * FROM F_LTA WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 3")]
            )
            ->willReturnOnConsecutiveCalls($articulo, $precioTarifaA, $precioTarifaB, $precioDolarPapel);
        
        // Esperamos que se ejecuten 4 actualizaciones (una por cada tarifa y el costo)
        $this->helperDbMock->expects($this->exactly(4))
            ->method('execute')
            ->withConsecutive(
                [$this->stringContains("UPDATE F_ART SET PCOART = 130.00 WHERE CODART = '$codigoPrecio'")],
                [$this->stringContains("UPDATE F_LTA SET PRELTA = 260.00 WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 1")],
                [$this->stringContains("UPDATE F_LTA SET PRELTA = 286.00 WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 2")],
                [$this->stringContains("UPDATE F_LTA SET PRELTA = 26.00 WHERE ARTLTA = '$codigoPrecio' AND TARLTA = 3")]
            )
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->actualizarPrecio(
            $codigoPrecio, 
            $coeficiente, 
            $actualizarTarifaA, 
            $actualizarTarifaB, 
            $actualizarDolarPapel, 
            $actualizarCosto
        );
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método getFamiliasList obtiene correctamente la lista de artículos de una familia
     */
    public function testGetFamiliasList()
    {
        // Datos de prueba
        $familia = 'FAM01';
        $articulos = [
            ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'],
            ['CODART' => '002', 'EQUART' => 'DEF456', 'FAMART' => 'FAM01'],
            ['CODART' => '003', 'EQUART' => 'GHI789', 'FAMART' => 'FAM01']
        ];
        
        // Configuramos el mock para que retorne la lista de artículos
        $this->helperDbMock->expects($this->once())
            ->method('query')
            ->with($this->stringContains("SELECT * FROM F_ART WHERE FAMART = '$familia'"))
            ->willReturn($articulos);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->getFamiliasList($familia);
        
        // Verificamos el resultado
        $this->assertEquals($articulos, $resultado);
        $this->assertCount(3, $resultado);
    }
    
    /**
     * Test para verificar el manejo de errores en actualizarPrecio
     */
    public function testActualizarPrecioError()
    {
        // Datos de prueba
        $codigoPrecio = '001';
        $coeficiente = 1.10;
        $actualizarTarifaA = true;
        $actualizarTarifaB = false;
        $actualizarDolarPapel = false;
        $actualizarCosto = false;
        
        // Configuramos el mock para que simule un error al obtener el precio
        $this->helperDbMock->expects($this->once())
            ->method('queryOne')
            ->willReturn(null);
        
        // Ejecutamos el método a probar
        $resultado = $this->precio->actualizarPrecio(
            $codigoPrecio, 
            $coeficiente, 
            $actualizarTarifaA, 
            $actualizarTarifaB, 
            $actualizarDolarPapel, 
            $actualizarCosto
        );
        
        // Verificamos que el resultado sea falso debido al error
        $this->assertFalse($resultado);
    }
}
