<?php


class FakeStorage implements \MicroPHP\StorageInterface
{
    private $container;

    public function __construct()
    {
        $this->container = [];
    }

    public function add($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->container[$key]);
    }

    public function get($key)
    {
        return $this->container[$key];
    }

    public function delete($key)
    {
        unset($this->container[$key]);
    }
}