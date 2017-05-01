<?php
use \MicroPHP\Injector;

class A {
    public $my_var;

    public function __construct()
    {
        $this->my_var = 'var value';
    }

    public function test(){
        return 'Ok';
    }
}

class B {
    public $a;
    public $b;
    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }
}

class InjectorTest extends PHPUnit_Framework_TestCase
{
    function testRegister_WithRegistered_IsRegistered(){
        $name ='a';
        $injector = new Injector();
        $injector->register($name,'value a');
        $this->assertTrue($injector->has($name));
    }

    function testRegister_WithNotRegistered_IsNotRegistered(){
        $name ='a';
        $injector = new Injector();
        $this->assertFalse($injector->has($name));
    }

    function testGetInjection_WithRegistered_IsCorrectlyRegistered(){
        $name ='a';
        $value = 'value a';
        $injector = new Injector();
        $injector->register($name,$value);
        $injection = $injector->getInjection($name);
        $this->assertEquals($name, $injection->name);
        $this->assertEquals($value, $injection->value);
        $this->assertEquals(0, count($injection->injections));
        $this->assertTrue($injection->isCachable);
    }

    function testGetInjection_WithInjections_IsCorrectlyRegistered(){
        $name ='a';
        $value = 'value a';
        $injections = ['x', 'y', 'z'];
        $injector = new Injector();
        $injector->register($name,$value, $injections);
        $injection = $injector->getInjection($name);
        $this->assertEquals($name, $injection->name);
        $this->assertEquals($value, $injection->value);
        $this->assertEquals(3, count($injection->injections));
        $this->assertEquals('x', $injection->injections[0]);
        $this->assertEquals('y', $injection->injections[1]);
        $this->assertEquals('z', $injection->injections[2]);
        $this->assertTrue($injection->isCachable);
    }

    function testGetInjection_WithNotCachable_IsCorrectlyRegistered(){
        $name ='a';
        $value = 'value a';
        $injector = new Injector();
        $injector->register($name,$value, [], false);
        $injection = $injector->getInjection($name);
        $this->assertEquals($name, $injection->name);
        $this->assertEquals($value, $injection->value);
        $this->assertEquals(0, count($injection->injections));
        $this->assertFalse($injection->isCachable);
    }

    function testGetInjectedParams_WithSimpleValues_ReturnsValues(){
        $injector = new Injector();
        $params = $injector->getInjectedParams(['a', 2, true]);
        $this->assertEquals(3, count($params));
        $this->assertEquals('a', $params[0]);
        $this->assertEquals(2, $params[1]);
        $this->assertEquals(true, $params[2]);
    }

    function testGetInjectedParams_WithClass_ReturnsClass(){
        $injector = new Injector();
        $injector->register('a',A::class);
        $params = $injector->getInjectedParams(['a']);
        $instance = $params[0];
        $result = $instance->test();
        $this->assertEquals('Ok', $result);
    }

    function testGetNewInstance_WithClass_ReturnsClass(){
        $name = 'a';
        $injector = new Injector();
        $injector->register($name,A::class);
        $instance = $injector->getNew($name);
        $result = $instance->test();
        $this->assertEquals('Ok', $result);
    }

    function testGetNewInstance_WithIsCachable_CacheInstance(){
        $name = 'a';
        $value = 'value a';
        $injector = new Injector();
        $injector->register($name, $value);
        $instance = $injector->getNew($name);
        $this->assertTrue($injector->isCached($name));
        $this->assertEquals(1, $injector->cacheLength());
    }

    function testGetInstance_WithClass_ReturnsClass(){
        $name = 'a';
        $injector = new Injector();
        $injector->register($name,A::class);
        $instance = $injector->get($name);
        $result = $instance->test();
        $this->assertEquals('Ok', $result);
    }

    function testGetInstance_WithCachedInstance_ReturnsCached(){
        $name = 'a';
        $newValue = 'updated value';
        $injector = new Injector();
        $injector->register($name,A::class);
        $instance = $injector->getNew($name);
        $instance->my_var = $newValue;
        $this->assertTrue($injector->isCached($name));
        $cached = $injector->get($name);
        $result = $cached->test();
        $this->assertEquals('Ok', $result);
        $this->assertEquals($newValue, $cached->my_var);
    }

    function testGetInstance_WithNotCachableInstance_ReturnsNewInstance(){
        $name = 'a';
        $newValue = 'updated value';
        $injector = new Injector();
        $injector->register($name,A::class,[], false);
        $instance = $injector->getNew($name);
        $instance->my_var = $newValue;
        $this->assertFalse($injector->isCached($name));
        $instance2 = $injector->get($name);
        $result = $instance2->test();
        $this->assertEquals('Ok', $result);
        $this->assertEquals('var value', $instance2->my_var);
    }

    function testGetInstance_WithInjections_ReturnsInjectionsAndOtherValues(){
        $injector = new Injector();
        $injector->register('a',A::class);
        // create class B with first param class A and second param simple value 2
        $injector->register('b',B::class, ['a', 2]);
        $instance = $injector->get('b');
        $this->assertEquals('Ok', $instance->a->test());
        $this->assertEquals(2, $instance->b);
    }

    function testInjectParams_WithClass_ReturnsClass(){
        $name = 'b';
        $injector = new Injector();
        $instance = $injector->injectParameters(B::class,['value a', 'value b']);
        $this->assertEquals('value a', $instance->a);
        $this->assertEquals('value b', $instance->b);
    }


    function testClearCache(){
        $name = 'a';
        $value = 'value a';
        $injector = new Injector();
        $injector->register($name, $value);
        $instance = $injector->getNew($name);
        $this->assertEquals(1, $injector->cacheLength());
        $injector->clearCache();
        $this->assertEquals(0, $injector->cacheLength());
    }

    function testUnregister_WithValue_ReturnsClass(){
        $name = 'a';
        $injector = new Injector();

        $injector->register($name,'value a');
        $this->assertTrue($injector->has($name));

        $instance = $injector->get($name);
        $this->assertTrue($injector->isCached($name));

        $hasRemove = $injector->unregister($name);
        $this->assertTrue($hasRemove);
        $this->assertFalse($injector->has($name));
        $this->assertFalse($injector->isCached($name));
    }

    function testInvoke_WithCallable_ReturnsClass(){
        $name = 'a';
        $injector = new Injector();
        $injector->register($name, function($a, $b){
            $this->assertEquals('value a', $a);
            $this->assertEquals('value b', $b);
            return 'Ok';
        },['value a', 'value b']);

        $result = $injector->invoke($name);
        $this->assertEquals('Ok', $result);
    }

    function testChain_Registration(){
        $injector = new Injector();
        $injector
            ->register('a','value a')
            ->register('b', 'value b')
            ->register('c', 'value c');

        $this->assertTrue($injector->has('a'));
        $this->assertTrue($injector->has('b'));
        $this->assertTrue($injector->has('c'));
    }
}
