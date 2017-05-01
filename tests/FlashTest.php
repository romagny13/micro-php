<?php

class FlashTest extends PHPUnit_Framework_TestCase
{
    function testAddMessage()
    {
        $flashKey = '__micro__flash_message';
        $storage = new FakeStorage();
        $flash = new \MicroPHP\Flash($storage);

        $status = 'success';
        $message = 'my message';
        $flash->addMessage($status, $message);

        $storage->has($flashKey);
        $this->assertTrue($flash->hasFlashMessage());
    }

    function testHasMessage()
    {
        $flashKey = '__micro__flash_message';
        $storage = new FakeStorage();
        $flash = new \MicroPHP\Flash($storage);

        $status = 'success';
        $message = 'my message';
        $flash->addMessage($status, $message);
        $this->assertTrue($flash->hasMessage($status));
    }

    function testGetMessage()
    {
        $flashKey = '__micro__flash_message';
        $storage = new FakeStorage();
        $flash = new \MicroPHP\Flash($storage);

        $status = 'success';
        $message = 'my message';
        $flash->addMessage($status, $message);

        $result = $flash->getMessage($status);
        $this->assertEquals($message, $result);
    }

    function testGetMessage_MessageIsDeleted()
    {
        $flashKey = '__micro__flash_message';
        $storage = new FakeStorage();
        $flash = new \MicroPHP\Flash($storage);

        $status = 'success';
        $message = 'my message';
        $flash->addMessage($status, $message);

        $result = $flash->getMessage($status);
        $this->assertFalse($flash->hasFlashMessage());
        $this->assertFalse($storage->has($flashKey));
    }


}