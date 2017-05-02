<?php
namespace MicroPHP;


class HeaderStrategy implements HeaderStrategyInterface
{
    public function setHeaderString($string)
    {
        header($string);
    }
}