<?php
class RouteResolverTest extends PHPUnit_Framework_TestCase
{
    function testGetQuery_WithQueryString()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getQuery('id=10');
        $this->assertEquals('10',$result->id);
    }

    function testGetQuery_WithQueryStringWithMultipleValues()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getQuery('id=10&cat=abc');
        $this->assertEquals('10',$result->id);
        $this->assertEquals('abc',$result->cat);
    }

    function testGetQuery_WithStart()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getQuery('?id=10&cat=abc');
        $this->assertEquals('10',$result->id);
        $this->assertEquals('abc',$result->cat);
    }

    // getParams
    function testGetParams_WithOneParams()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getParams('/posts/:id','/posts/10');
        $this->assertEquals('10',$result->id);
    }

    function testGetParams_WithMultipleParams()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getParams('/posts/:a/:b/xyz/:c','/posts/10/20/xyz/30');
        $this->assertEquals('10',$result->a);
        $this->assertEquals('20',$result->b);
        $this->assertEquals('30',$result->c);
    }

    function testGetParams_WithOneRegex()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getParams('/posts/:a([a-z]+)','/posts/abc');
        $this->assertEquals('abc',$result->a);
    }

    function testGetParams_WithOneRegex_FailIfIncorrect()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getParams('/posts/:a([a-z]+)','/posts/123');
        $this->assertTrue(!isset($result->id));
    }

    function testGetParams_WithMultipRegexAndParams()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getParams('/posts/:a([a-z]+)/:b/xyz/:c([a-z]+)','/posts/abc/20/xyz/efg');
        $this->assertEquals('abc',$result->a);
        $this->assertEquals('20',$result->b);
        $this->assertEquals('efg',$result->c);
    }
    // replace params by regex
    function testReplaceParamsByRegex_WithNoRegex_AddDefaultRegex()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByRegex('/posts/:id');
        $this->assertEquals('/posts/([0-9]+)', $result);
    }

    function testReplaceParamsByRegex_WithRegex_AddRegex()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByRegex('/posts/:id([a-z]+)');
        $this->assertEquals('/posts/([a-z]+)', $result);
    }


    function testReplaceParamsByRegex_WithMultipleRegexes_AddRegexes()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByRegex('/posts/:a([a-z]+)/:b/xyz/:c([a-z]+-[0-9]+)');
        $this->assertEquals('/posts/([a-z]+)/([0-9]+)/xyz/([a-z]+-[0-9]+)', $result);
    }
    
    // getMatched
    function testGetMatched()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $routes = [];
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts', function(){}));
        $result = $resolver->getMatched($routes,'GET','/posts');
        $this->assertEquals('/posts', $result->path);
    }

    function testGetMatched_WithNoMatch_ReturnsNull()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $routes = [];
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts', function(){}));
        $result = $resolver->getMatched($routes,'GET','/posts/10');
        $this->assertNull($result);
    }

    function testGetMatched_ResolveWithsParams()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $routes = [];
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts/:id', function(){}));
        $result = $resolver->getMatched($routes,'GET','/posts/10');
        $this->assertEquals('/posts/:id', $result->path);
    }
    
    // getQueryStringFromArray
    function testGetQueryStringFromArray()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getQueryStringFromArray(['id' => 10]);
        $this->assertEquals('?id=10', $result);
    }

    function testGetQueryStringFromArray_WithMultipleValues()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->getQueryStringFromArray(['id' => 10, 'cat' => 'abc']);
        $this->assertEquals('?id=10&cat=abc', $result);
    }

    // replaceParamsByValues
    function testReplaceParamsByValues()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByValues('/posts/:id',['id' => 10]);
        $this->assertEquals('/posts/10', $result);
    }

    function testReplaceParamsByValues_WithMultipleValues()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByValues('/posts/:a/:b/xyz/:c',['a' => 10, 'b' => 20, 'c' => 30]);
        $this->assertEquals('/posts/10/20/xyz/30', $result);
    }

    function testReplaceParamsByValues_WithMultipleValuesAndRegexes()
    {
        $resolver = new \MicroPHP\RouteResolver();
        $result = $resolver->replaceParamsByValues('/posts/:a([a-z]+)/:b/xyz/:c([a-z]+)',['a' => 'abc', 'b' => 20, 'c' => 'efg']);
        $this->assertEquals('/posts/abc/20/xyz/efg', $result);
    }

    function testReplaceParamsByValues_WithMultipleValuesAndRegexes_FailsIfIncorrect()
    {
        $fail = false;
        $resolver = new \MicroPHP\RouteResolver();
        $result ='';
        try{
            $result = $resolver->replaceParamsByValues('/posts/:a([a-z]+)/:b/xyz/:c([a-z]+)',['a' => 10, 'b' => 20, 'c' => 'efg']);
        }
        catch (Exception $e){
            $fail = true;
        }
        $this->assertTrue($fail);
    }

    function testResolveRoute()
    {
        $resolver = new \MicroPHP\RouteResolver(new FakeServer());
        $url ='http://localhost/site/posts/50';
        $path ='/posts/50';
        $routes = [];
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts/:id', function(){}));
        $router = new \MicroPHP\Router([
            'base' => 'http://localhost/site'
        ]);
        $route = $resolver->resolve($routes,'GET',$path, $url, $router);

        $this->assertEquals(10, $route->data->id);
        $this->assertEquals('GET', $route->method);
        $this->assertEquals(50, $route->params->id);
        $this->assertEquals($path, $route->path);
        $this->assertEquals($url, $route->url);
        $this->assertSame($router, $route->router);
        $this->assertEquals('/posts/:id', $route->matched->path);
    }

    function testResolveRoute_FindNoRoute()
    {
        $resolver = new \MicroPHP\RouteResolver(new FakeServer());
        $url ='http://localhost/site/posts/50';
        $path ='/test';
        $routes = [];
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts', function(){}));
        array_push($routes, new \MicroPHP\RouteConfig(['GET'],'/posts/:id', function(){}));
        $route = $resolver->resolve($routes,'GET',$path, $url, new \MicroPHP\Router([
            'base' => 'http://localhost/site'
        ]));
        $this->assertNull($route);
    }

}