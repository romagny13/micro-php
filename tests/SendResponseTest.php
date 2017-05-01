<?php

class SendResponseTest extends PHPUnit_Framework_TestCase
{
    function testSendResponses_Correctly()
    {
        $fake = new FakeResponse();
        $response = new \MicroPHP\SendResponse($fake);
        $response->location('url');
        $this->assertEquals('location', $fake->result);
        $response->badRequest();
        $this->assertEquals('badRequest', $fake->result);
        $response->created('created');
        $this->assertEquals('created', $fake->result);
        $response->json('json');
        $this->assertEquals('json', $fake->result);
        $response->noContent();
        $this->assertEquals('noContent', $fake->result);
        $response->unauthorized();
        $this->assertEquals('unauthorized', $fake->result);
    }

}