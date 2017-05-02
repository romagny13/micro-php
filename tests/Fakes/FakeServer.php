<?php


use MicroPHP\ServerInterface;

class FakeServer implements ServerInterface
{

    public function getMethod()
    {
        return 'GET';
    }

    public function getFullUrl()
    {
        return 'http://localhost/site';
    }

    public function getQueryString()
    {
        return '?q=abc&cat=10';
    }

    public function getContentType()
    {
        return 'application/json';
    }

    public function hasContentType()
    {
        return true;
    }

    public function getContent()
    {
        return json_decode('{ "id" : 10 }');
    }
}
