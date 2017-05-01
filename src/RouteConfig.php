<?php

namespace MicroPHP;


class RouteConfig
{
    public $methods;
    public $path;
    public $callable;
    public $name;
    public $middlewares;

    public function __construct($methods, $path, $callable)
    {
        $this->methods = $methods;
        $this->path = $path;
        $this->callable = $callable;
        $this->middlewares = [];
        $this->name = '';
    }

    public function add($middleware){
        array_push($this->middlewares, $middleware);
        return $this;
    }

    public function setName($name){
        $this->name = $name;
        return $this;
    }
}