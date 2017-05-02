<?php

class FakeHeaderStrategy implements \MicroPHP\HeaderStrategyInterface
{

    public $result= [];

    public function setHeaderString($string)
    {
       array_push($this->result,$string);
    }
}