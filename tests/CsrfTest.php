<?php

class CsrfTest extends PHPUnit_Framework_TestCase
{
    function testCreateToken_ReturnsTokenAndStoreToken()
    {
        $storage = new FakeStorage();
        $csrf = new \MicroPHP\Csrf($storage,new FakeData());
        
        $csrf_token = $csrf->createToken();
        $key = $csrf->getTokenName();
        
        $this->assertTrue(strlen($csrf_token) > 30);
        $this->assertTrue($storage->has($key));
        $this->assertTrue($csrf->hasStoredToken());
        $stored = $csrf->getStoredToken();
        $this->assertEquals($csrf_token, $stored);
    }

    function testGetSubmittedToken(){
        $storage = new FakeStorage();
        $data = new FakeData();
        $csrf = new \MicroPHP\Csrf($storage,$data);

        $key = $csrf->getTokenName();
        $data->setData($key, 'abc');

        $result = $csrf->getSubmittedToken();
        $this->assertEquals('abc', $result);
    }

    function testCheck_WithSameTokens_Success()
    {
        $storage = new FakeStorage();
        $data = new FakeData();
        $csrf = new \MicroPHP\Csrf($storage,$data);

        $csrf_token = $csrf->createToken();
        $key = $csrf->getTokenName();

        $data->setData($key, $csrf_token);

        $result = $csrf->check();
        $this->assertTrue($result);
    }

    function testCheck_WithNotSameTokens_Fail()
    {
        $storage = new FakeStorage();
        $data = new FakeData();
        $csrf = new \MicroPHP\Csrf($storage,$data);

        $csrf_token = $csrf->createToken();
        $key = $csrf->getTokenName();

        $data->setData($key, 'abc');

        $result = $csrf->check();
        $this->assertFalse($result);
    }

}