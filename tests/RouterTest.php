<?php
class RouterTest extends PHPUnit_Framework_TestCase
{
   function testSettings(){
       $base = 'http://localhost/site';
       $router = new \MicroPHP\Router([
           'base' => $base
       ]);
       
       $this->assertEquals($base, $router->base);
       $this->assertNull($router->injector);
   }

    function testSettings_WithInjector(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base,
            'injector' => new \MicroPHP\Injector()
        ]);

        $this->assertEquals($base, $router->base);
        $this->assertNotNull($router->injector);
    }

    function testAddRoute(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $result = $router->map(['GET'],'/posts', function () {});
        $this->assertTrue($result instanceof \MicroPHP\RouteConfig);
        $this->assertEquals('GET', $result->methods[0]);
        $this->assertEquals('/posts', $result->path);
        $this->assertEquals(1, count($router->routeConfigs));
    }

    function testAddRoute_WithMultipleMethods(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $result = $router->map(['GET','POST'],'/posts', function (){});
        $this->assertEquals('GET', $result->methods[0]);
        $this->assertEquals('POST', $result->methods[1]);
        $this->assertEquals('/posts', $result->path);
    }

    function testAddRoutes(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->get('/a',function(){});
        $router->post('/b',function(){});
        $router->put('/c',function(){});
        $router->delete('/d',function(){});
        $router->options('/e',function(){});
        $router->patch('/g',function(){});

        $this->assertEquals(6, count($router->routeConfigs));
        $this->assertEquals('/a', $router->routeConfigs[0]->path);
        $this->assertEquals('/b', $router->routeConfigs[1]->path);
        $this->assertEquals('/c', $router->routeConfigs[2]->path);
        $this->assertEquals('/d', $router->routeConfigs[3]->path);
        $this->assertEquals('/e', $router->routeConfigs[4]->path);
        $this->assertEquals('/g', $router->routeConfigs[5]->path);
        $this->assertEquals('GET', $router->routeConfigs[0]->methods[0]);
        $this->assertEquals('POST', $router->routeConfigs[1]->methods[0]);
        $this->assertEquals('PUT', $router->routeConfigs[2]->methods[0]);
        $this->assertEquals('DELETE', $router->routeConfigs[3]->methods[0]);
        $this->assertEquals('OPTIONS', $router->routeConfigs[4]->methods[0]);
        $this->assertEquals('PATCH', $router->routeConfigs[5]->methods[0]);
    }

    function testCouldAddMiddleWare_AfterAddroute(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $result = $router->map(['GET'],'/posts', function () {})->add(function(){})->add(function(){});
        $this->assertEquals(2, count($result->middlewares));
    }

    function testCouldSetName_AfterAddroute(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $name ='myroute';
        $result = $router->map(['GET'],'/posts', function () {})->setName($name)->add(function(){});
        $this->assertEquals($name, $result->name);
        $this->assertEquals(1, count($result->middlewares));
    }

    function testGroup(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->group('/posts', function(){
            $this->get('/:id', function(){});
            $this->post('/create', function(){});
        });

        $this->assertEquals(2, count($router->routeConfigs));
        $this->assertEquals('/posts/:id', $router->routeConfigs[0]->path);
        $this->assertEquals('/posts/create', $router->routeConfigs[1]->path);
        $this->assertEquals('GET', $router->routeConfigs[0]->methods[0]);
        $this->assertEquals('POST', $router->routeConfigs[1]->methods[0]);
    }

    function testGroup_CouldAddMiddleware_ForGroup(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $group = $router->group('/posts', function(){
            $this->get('/:id', function(){});
            $this->post('/create', function(){});
        })->add(function(){
            return true;
        })->add(function(){
            return true;
        });

        $this->assertEquals(2, count($group->middlewares));
        $this->assertEquals(2, count($router->routeConfigs));
        $this->assertEquals(0, count($router->middlewares));
        $this->assertEquals(2, count($router->routeConfigs[0]->middlewares));
        $this->assertEquals(2, count($router->routeConfigs[1]->middlewares));
        
    }

    function testMergeMiddlewares(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $result = $router->mergeMiddlewares(['a','b'],['c','d','e']);
        $this->assertEquals(5, count($result));
        $this->assertSame(['a','b','c','d','e'], $result);
    }

    function testPathFor(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);
        
        $router->get('/posts',function(){})->setName('posts.index');

        $result = $router->pathFor('posts.index');
        $this->assertEquals('http://localhost/site/posts', $result);
    }

    function testPathFor_WithParams(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->get('/posts/:a/:b',function(){})->setName('posts.detail');

        $result = $router->pathFor('posts.detail',['a' => 10, 'b' => 20]);
        $this->assertEquals('http://localhost/site/posts/10/20', $result);
    }

    function testPathFor_WithParamsAndQuery(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->get('/posts/:a/:b',function(){})->setName('posts.detail');

        $result = $router->pathFor('posts.detail',['a' => 10, 'b' => 20], ['q' => 'abc', 'cat' => '50']);
        $this->assertEquals('http://localhost/site/posts/10/20?q=abc&cat=50', $result);
    }

    function testGetRouteConfigByName(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->get('/',function(){});
        $router->get('/posts',function(){})->setName('posts.index');
        $router->get('/posts/:a/:b',function(){})->setName('posts.detail');

        $result = $router->getRouteConfigByName($router->routeConfigs,'posts.index');
        $this->assertEquals('/posts', $result->path);
    }

    function testGetRouteConfigByName_Fail(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $router->get('/',function(){});
        $router->get('/posts',function(){})->setName('posts.index');
        $router->get('/posts/:a/:b',function(){})->setName('posts.detail');

        $result = $router->getRouteConfigByName($router->routeConfigs,'posts.create');
        $this->assertNull($result);
    }

    function testResolveControllerActionString(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);
        
        $result = $router->resolveControllerActionString('MyController:index');
        $this->assertEquals('MyController', $result[0]);
        $this->assertEquals('index', $result[1]);
    }
    

    function testCallFunction(){
        $base = 'http://localhost/site';
        $router = new \MicroPHP\Router([
            'base' => $base
        ]);

        $t = new TESTClosure();
        $router->callFunction($t,'route');
        $this->assertTrue($t->called);
    }

    function testCallInjection(){
        $base = 'http://localhost/site';
        $injector = new \MicroPHP\Injector();
        $router = new \MicroPHP\Router([
            'base' => $base,
            'injector' => $injector
        ]);

        $injector->register('A',TESTClosure::class);
        $router->callInjection('A:index','route');
        
        $t = $injector->get('A');
        $this->assertTrue($t->called);
    }
}


class TESTClosure {
    public $called;

    public function __construct()
    {
        $this->called = false;
    }
    
    public function index(){
        $this->called = true;
    }

    public function __invoke()
    {
        $this->called = true;
        return true;
    }
}

