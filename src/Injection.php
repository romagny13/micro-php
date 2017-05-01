<?php

namespace MicroPHP;


class Injection
{
    public $name;
    public $value;
    public $injections;
    public $isCachable;

    public function __construct($name, $value, $injections, $isCachable)
    {
        $this->name = $name;
        $this->value = $value;
        $this->injections = $injections;
        $this->isCachable = $isCachable;
    }
}