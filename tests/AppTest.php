<?php

class AppTest extends PHPUnit_Framework_TestCase
{
    function testCreateApp_ConfigureAppCorrectly()
    {
        $settings =[
            'templates' => __DIR__,
            'base' => 'http://localhost/site'
        ];

        $app = new \MicroPHP\App($settings);
        
        $injector = $app->injector;
        $router = $app->router;
        
        $this->assertNotNull($injector);
        $this->assertNotNull($router);
        $this->assertNotNull($app->renderer);

        $this->assertTrue($injector->has('router'));
        $this->assertTrue($injector->has('renderer'));
        $this->assertEquals($settings['base'], $router->base);
        $this->assertSame($injector, $router->injector);
        $this->assertSame($router, $app->renderer->router);
    }
    
}