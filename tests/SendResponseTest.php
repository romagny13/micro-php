<?php

function getResponse($code, \MicroPHP\SendResponse $response){
    $message = $response->messages[$code];
    return "HTTP/1.1 $code $message";
}

class SendResponseTest extends PHPUnit_Framework_TestCase
{
    function testGetDefaultMessage_ReturnsMessage()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $result = $response->getDefaultMessage(200);
        $this->assertEquals('OK', $result);
    }

    function testShortCuts_ReturnsMessage()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $response->badRequest();
        $this->assertEquals(getResponse(400, $response), $strategy->result[0]);
        $response->created();
        $this->assertEquals(getResponse(201, $response), $strategy->result[1]);
        $response->noContent();
        $this->assertEquals(getResponse(204, $response), $strategy->result[2]);
        $response->ok();
        $this->assertEquals(getResponse(200, $response), $strategy->result[3]);
        $response->notFound();
        $this->assertEquals(getResponse(404, $response), $strategy->result[4]);
        $response->unauthorized();
        $this->assertEquals(getResponse(401, $response), $strategy->result[5]);
        $response->internalServerError();
        $this->assertEquals(getResponse(500, $response), $strategy->result[6]);
        $response->redirect('my url');
        $this->assertEquals('Location:my url', $strategy->result[7]);

    }

    function testChainSetHeaderString_AddHeader()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $response
            ->setHeaderString('1')
            ->setHeaderString('2');
        
        $this->assertEquals(2, count($strategy->result));
        $this->assertEquals('1', $strategy->result[0]);
        $this->assertEquals('2', $strategy->result[1]);
    }

    function testChainSetHeader_AddHeader()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $response
            ->setHeader(200)
            ->setHeader(204);

        $this->assertEquals(2, count($strategy->result));
        $this->assertEquals(getResponse(200, $response), $strategy->result[0]);
        $this->assertEquals(getResponse(204, $response), $strategy->result[1]);
    }

    function testSetHeader_WithMessage()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $response->setHeader(200,'My message');
        
        $this->assertEquals("HTTP/1.1 200 My message", $strategy->result[0]);
    }

    function testShortCuts_WithMessages()
    {
        $strategy = new FakeHeaderStrategy();
        $response = new \MicroPHP\SendResponse($strategy);
        $response->badRequest('My message 400');
        $this->assertEquals("HTTP/1.1 400 My message 400", $strategy->result[0]);
        $response->created(null,'My message 201');
        $this->assertEquals("HTTP/1.1 201 My message 201", $strategy->result[1]);
        $response->noContent('My message 204');
        $this->assertEquals("HTTP/1.1 204 My message 204", $strategy->result[2]);
        $response->ok(null,'My message 200');
        $this->assertEquals("HTTP/1.1 200 My message 200", $strategy->result[3]);
        $response->notFound('My message 404');
        $this->assertEquals("HTTP/1.1 404 My message 404", $strategy->result[4]);
        $response->unauthorized('My message unauthorized');
        $this->assertEquals("HTTP/1.1 401 My message unauthorized", $strategy->result[5]);
    }

    function testJson()
    {
        $strategy = new FakeHeaderStrategy();
        $json = new FakeJsonStrategy();
        $response = new \MicroPHP\SendResponse($strategy, $json);
        $response->json([
            'id' => 10
        ]);
        $this->assertEquals('{"id":10}',$json->result);
    }
}