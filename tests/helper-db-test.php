<?php
/**
 * Tests unitarios para la clase HelperDB
 * 
 * @author Claude
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class HelperDBTest extends TestCase
{
    /**
     * @var HelperDB
     */
    private $helperDB;
    
    /**
     * @var PDO Mock
     */
    private $pdoMock;
    
    /**
     * @var PDOStatement Mock
     */
    private $statementMock;
    
    /**
     * Configuración inicial para cada test
     */
    protected function setUp(): void
    {
        // Creamos un mock para PDO
        $this->pdoMock = $this->createMock(PDO::class);
        
        // Creamos un mock para PDOStatement
        $this->statementMock = $this->createMock(PDOStatement::class);
        
        // Creamos una instancia de HelperDB con nuestro PDO mock
        $this->helperDB = $this->getMockBuilder(HelperDB::class)
            ->setConstructorArgs([])
            ->onlyMethods(['getConnection'])
            ->getMock();
        
        // Configuramos el mock para que retorne nuestro PDO mock
        $this->helperDB->method('getConnection')
            ->willReturn($this->pdoMock);
    }
    
    /**
     * Test para verificar que el método query ejecuta la consulta y retorna los resultados
     */
    public function testQuery()
    {
        // Consulta SQL de prueba
        $sql = "SELECT * FROM F_ART WHERE FAMART = 'FAM01'";
        
        // Datos esperados
        $resultadosEsperados = [
            ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'],
            ['CODART' => '002', 'EQUART' => 'DEF456', 'FAMART' => 'FAM01']
        ];
        
        // Configuramos los mocks
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $this->statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($resultadosEsperados);
        
        // Ejecutamos el método a probar
        $resultados = $this->helperDB->query($sql);
        
        // Verificamos los resultados
        $this->assertEquals($resultadosEsperados, $resultados);
    }
    
    /**
     * Test para verificar que el método queryOne ejecuta la consulta y retorna un único resultado
     */
    public function testQueryOne()
    {
        // Consulta SQL de prueba
        $sql = "SELECT * FROM F_ART WHERE CODART = '001'";
        
        // Dato esperado
        $resultadoEsperado = ['CODART' => '001', 'EQUART' => 'ABC123', 'FAMART' => 'FAM01'];
        
        // Configuramos los mocks
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $this->statementMock->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($resultadoEsperado);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->queryOne($sql);
        
        // Verificamos el resultado
        $this->assertEquals($resultadoEsperado, $resultado);
    }
    
    /**
     * Test para verificar que el método queryScalar ejecuta la consulta y retorna un único valor
     */
    public function testQueryScalar()
    {
        // Consulta SQL de prueba
        $sql = "SELECT COUNT(*) FROM F_ART WHERE FAMART = 'FAM01'";
        
        // Valor esperado
        $valorEsperado = 10;
        
        // Configuramos los mocks
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $this->statementMock->expects($this->once())
            ->method('fetchColumn')
            ->with(0)
            ->willReturn($valorEsperado);
        
        // Ejecutamos el método a probar
        $valor = $this->helperDB->queryScalar($sql);
        
        // Verificamos el resultado
        $this->assertEquals($valorEsperado, $valor);
    }
    
    /**
     * Test para verificar que el método execute ejecuta la sentencia SQL correctamente
     */
    public function testExecute()
    {
        // Sentencia SQL de prueba
        $sql = "UPDATE F_ART SET PCOART = 120.00 WHERE CODART = '001'";
        
        // Configuramos los mocks
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->execute($sql);
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método lastInsertId retorna el último ID insertado
     */
    public function testLastInsertId()
    {
        // ID esperado
        $idEsperado = 123;
        
        // Configuramos el mock
        $this->pdoMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn($idEsperado);
        
        // Ejecutamos el método a probar
        $id = $this->helperDB->lastInsertId();
        
        // Verificamos el resultado
        $this->assertEquals($idEsperado, $id);
    }
    
    /**
     * Test para verificar el manejo de errores en el método query
     */
    public function testQueryError()
    {
        // Consulta SQL de prueba
        $sql = "SELECT * FROM TABLA_INEXISTENTE";
        
        // Configuramos los mocks para simular un error
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        
        // Configuramos el mock para que retorne información sobre el error
        $this->statementMock->expects($this->once())
            ->method('errorInfo')
            ->willReturn(['HY000', 1, 'Error en la consulta SQL']);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->query($sql);
        
        // Verificamos que el resultado sea un array vacío en caso de error
        $this->assertEmpty($resultado);
    }
    
    /**
     * Test para verificar el manejo de errores en el método execute
     */
    public function testExecuteError()
    {
        // Sentencia SQL de prueba
        $sql = "UPDATE TABLA_INEXISTENTE SET campo = 'valor'";
        
        // Configuramos los mocks para simular un error
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);
        
        // Configuramos el mock para que retorne información sobre el error
        $this->statementMock->expects($this->once())
            ->method('errorInfo')
            ->willReturn(['HY000', 1, 'Error en la sentencia SQL']);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->execute($sql);
        
        // Verificamos que el resultado sea falso en caso de error
        $this->assertFalse($resultado);
    }
    
    /**
     * Test para verificar que el método beginTransaction inicia una transacción correctamente
     */
    public function testBeginTransaction()
    {
        // Configuramos el mock
        $this->pdoMock->expects($this->once())
            ->method('beginTransaction')
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->beginTransaction();
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método commit confirma una transacción correctamente
     */
    public function testCommit()
    {
        // Configuramos el mock
        $this->pdoMock->expects($this->once())
            ->method('commit')
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->commit();
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
    
    /**
     * Test para verificar que el método rollBack deshace una transacción correctamente
     */
    public function testRollBack()
    {
        // Configuramos el mock
        $this->pdoMock->expects($this->once())
            ->method('rollBack')
            ->willReturn(true);
        
        // Ejecutamos el método a probar
        $resultado = $this->helperDB->rollBack();
        
        // Verificamos el resultado
        $this->assertTrue($resultado);
    }
}
