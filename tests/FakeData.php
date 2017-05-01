<?php

class FakeData implements \MicroPHP\DataStrategyInterface
{
    private $container;

    public function __construct()
    {
        $this->container = [];
    }

    public function getData($key)
    {
       return $this->container[$key];
    }

    public  function setData($key, $value){
        $this->container[$key] = $value;
    }
}