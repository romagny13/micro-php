<?php
class RouteConfigTest extends PHPUnit_Framework_TestCase
{
    function testAdd_Chain(){
        $config = new \MicroPHP\RouteConfig(['GET','POST'],'/posts', function(){});
        $config->add('1')->add('2')->setName('A')->add('3');
        $this->assertEquals(3, count($config->middlewares));
        $this->assertEquals('1', $config->middlewares[0]);
        $this->assertEquals('2', $config->middlewares[1]);
        $this->assertEquals('3', $config->middlewares[2]);
        $this->assertEquals('A', $config->name);
    }

}
