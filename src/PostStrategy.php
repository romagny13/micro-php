<?php

namespace MicroPHP;


class PostStrategy implements DataStrategyInterface
{
    public function has($key){
        return isset($_POST) && isset($_POST[$key]);
    }

    public function getData($key){
        if($this->has($key)) {
            return $_POST[$key];
        }
        return null;
    }
}