<?php

namespace MicroPHP;


class Route
{
    public $matched;
    public $method;
    public $args;
    public $url;
    public $path;
    public $params;
    public $queryString;
    public $query;
    public $router;

    public function __construct($matched, $method, $args, $url, $path, $params, $queryString, $query, $router)
    {
        $this->matched = $matched;
        $this->method = $method;
        $this->args = $args;
        $this->url = $url;
        $this->path = $path;
        $this->params = $params;
        $this->queryString = $queryString;
        $this->query = $query;
        $this->router = $router;
    }
}