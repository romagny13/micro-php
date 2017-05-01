<?php

class UrlTest extends PHPUnit_Framework_TestCase
{
    private $url;
    
    protected function setUp()
    {
        parent::setUp();
        $this->url = new \MicroPHP\Url(new FakeServer());
    }

    function testConcatGroupAndPath_WithEmptyEmpty_ReturnsSlash(){

        $groupBase = '';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'');
        $this->assertEquals('/', $result);
    }
    
    function testConcatGroupAndPath_WithEmptySlash_ReturnsSlash(){
        $groupBase = '';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/');
        $this->assertEquals('/', $result);
    }

    function testConcatGroupAndPath_WithSlashEmpty_ReturnsSlash(){
        $groupBase = '/';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'');
        $this->assertEquals('/', $result);
    }

    function testConcatGroupAndPath_WithSlashSlash_ReturnsSlash(){
        $groupBase = '/';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/');
        $this->assertEquals('/', $result);
    }

    function testConcatGroupAndPath_WithEmptyStringChild_DontAddSlash(){
        $groupBase = '/auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'');
        $this->assertEquals('/auth', $result);
    }

    function testConcatGroupAndPath_WithNoSlashBaseAndEmpty_AddSlash(){
        $groupBase = 'auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'');
        $this->assertEquals('/auth', $result);
    }

    function testConcatGroupAndPath_WithStringChild_RemoveSlash(){
        $groupBase = '/auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/');
        $this->assertEquals('/auth', $result);
    }

    function testConcatGroupAndPath_WithNoSlashBaseAndEmptyAndSlash_AddSlash(){
        $groupBase = 'auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/');
        $this->assertEquals('/auth', $result);
    }

    function testConcatGroupAndPath_ConcatCorrectly(){
        $groupBase = '/auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/signin');
        $this->assertEquals('/auth/signin', $result);
    }

    function testConcatGroupAndPath_AddSlashToStartChild(){
        $groupBase = '/auth';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'signin');
        $this->assertEquals('/auth/signin', $result);
    }

    function testConcatGroupAndPath_WithEmptyGroupStringAndNoSlashStartChild_AddSlashToChild(){
        $groupBase = '';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'signin');
        $this->assertEquals('/signin', $result);
    }

    function testConcatGroupAndPath_WithSlashes_FormatCorrectly(){
        $groupBase = '/';
        $result = $this->url->concatGroupBaseAndPath($groupBase,'/signin');
        $this->assertEquals('/signin', $result);
    }
    
    // trimQuery
    function testTrimQueryString_WithFullUrl(){
        $result = $this->url->trimQueryString('http://localhost/site/posts/10?q=10&cat20');
        $this->assertEquals('http://localhost/site/posts/10', $result);
    }

    function testTrimQueryString_WithPath(){
        $result = $this->url->trimQueryString('/posts/10?q=10&cat20');
        $this->assertEquals('/posts/10', $result);
    }
    
    // trimBase
    function testTrimBase(){
        $result = $this->url->trimBase('http://localhost/site', 'http://localhost/site/posts/10');
        $this->assertEquals('/posts/10', $result);
    }

    function testTrimBase_WithEndSlash(){
        $result = $this->url->trimBase('http://localhost/site/', 'http://localhost/site/posts/10');
        $this->assertEquals('/posts/10', $result);
    }
    
    // get path
    function testGetPath_removeQueryString(){
        $result = $this->url->getPath('http://localhost/site', 'http://localhost/site/posts/10?id=10&cat=abc');
        $this->assertEquals('/posts/10', $result);
    }

    function testGetPath_WithEndSlash_removeQueryString(){
        $result = $this->url->getPath('http://localhost/site/', 'http://localhost/site/posts/10?id=10&cat=abc');
        $this->assertEquals('/posts/10', $result);
    }

    // test is valid method
    function testIsValidMethod(){
        $result = $this->url->isValidMethod('GET');
        $this->assertTrue($result);
    }

    function testIsValidMethod_Fail(){
        $result = $this->url->isValidMethod('ABC');
        $this->assertFalse($result);
    }

    function testvalidMethods(){
        $result = $this->url->validMethods(['GET','POST']);
        $this->assertTrue($result);
    }

    function testvalidMethods_Fails(){
        $result = $this->url->validMethods(['GET','POST','ABC']);
        $this->assertFalse($result);
    }

    function testGetMethod(){
        $result = $this->url->getMethod();
        $this->assertEquals('GET', $result);
    }

    function testGetFullUrl(){
        $result = $this->url->getFullUrl();
        $this->assertEquals('http://localhost/site', $result);
    }
    

}
