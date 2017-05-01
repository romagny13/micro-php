<?php
class RouteGroupTest extends PHPUnit_Framework_TestCase
{
    function testFormatPath_ConcatGroupBasePathAndPath(){
        $groupBase = '/auth';
        $g = new \MicroPHP\RouteGroup(new \MicroPHP\Router([ 'base' => 'http://localhost/site' ]), $groupBase);
        $result = $g->formatPath($groupBase,'/signin');
        $this->assertEquals('/auth/signin', $result);
    }

//    function testFormatPath_ConcatGroupBasePathAndPath(){
//        $groupBase = '/auth';
//        $g = new \MicroPHP\RouteGroup(new \MicroPHP\Router([ 'base' => 'http://localhost/site' ]), $groupBase);
//        $result = $g->formatPath($groupBase,'/signin');
//        $this->assertEquals('/auth/signin', $result);
//    }
}
