<?php


class FakeResponse implements \MicroPHP\SendResponseInterface
{

    public $result;
    
    public function notFound()
    {
        $this->result = 'notFound';
    }

    public function created($result)
    {
        $this->result = 'created';
    }

    public function noContent()
    {
        $this->result = 'noContent';
    }

    public function unauthorized()
    {
        $this->result = 'unauthorized';
    }

    public function badRequest()
    {
        $this->result = 'badRequest';
    }

    public function json($result)
    {
        $this->result = 'json';
    }

    public function location($url)
    {
        $this->result = 'location';
    }
}