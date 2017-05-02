<?php
namespace MicroPHP;


class JsonStrategy implements JsonStrategyInterface
{
    public function encode($content)
    {
        echo json_encode($content);
    }
}