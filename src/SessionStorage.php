<?php
namespace MicroPHP;


class SessionStorage implements StorageInterface
{
    public function add($key, $value){
        $_SESSION[$key] = $value;
    }
    
    public function has($key){
        return isset($_SESSION[$key]);
    }
    
    public function get($key){
       return $_SESSION[$key];
    }

    public function delete($key){
        if($this->has($key)){
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
}