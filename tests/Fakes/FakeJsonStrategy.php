<?php

class FakeJsonStrategy implements \MicroPHP\JsonStrategyInterface
{
    public $result;

    public function encode($content)
    {
        $this->result = json_encode($content);
    }
}