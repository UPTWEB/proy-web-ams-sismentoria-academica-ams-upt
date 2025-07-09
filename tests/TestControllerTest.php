<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../core/TestController.php';

/**
 * @covers TestController
 */
class TestControllerTest extends TestCase
{
    /**
     * @covers TestController::holaGet
     */
    public function testHandleLlamaMetodoGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $controller = new TestController();
        $resultado = $controller->handle('hola');
        $this->assertEquals("Hola desde GET", $resultado);
    }
}
