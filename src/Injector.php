<?php
namespace MicroPHP;

class Injector
{
    private $container;
    private $cache;

    public function __construct()
    {
        $this->container = [];
        $this->cache = [];
    }

    public function register($name, $value, $injections = [], $isCachable = true){
        if(!is_string($name)) { throw new \Exception('Name required'); }
        if(isset($this->container[$name])) { throw new \Exception('A item with the name '. $name . ' is already registered'); }

        $injection = new Injection($name, $value, $injections, $isCachable);
        $this->container[$name] = $injection;
    }

    public function unregister($name){
        if(!is_string($name)) { throw new \Exception('Name required'); }
        if($this->has($name)){
            unset($this->container[$name]);
            $this->removeFromCache($name);
            return true;
        }
        return false;
    }

    public function has($name){
        return isset($this->container[$name]);
    }

    public function isCached($name){
        return isset($this->cache[$name]);
    }

    public function injectParameters($class, $params){
        return new $class(...$params);
    }

    public function addToCache($name, $value, $isCachable){
        if($isCachable){
            $this->cache[$name] = $value;
        }
    }

    public function removeFromCache($name){
        if(isset($this->cache[$name])){
            unset($this->cache[$name]);
        }
    }

    public function cacheLength(){
        return count($this->cache);
    }

    public function clearCache(){
        $this->cache = [];
    }

    public function clear(){
        $this->clearCache();
        $this->container = [];
    }

    public function isClass($value){
        return is_string($value) && class_exists($value);
    }

    public function getInjection($name){
        if(!is_string($name)) { throw new \Exception('Name required'); }
        if(!$this->has($name)){ throw new \Exception('No element registered for the name '. $name); }

        return $this->container[$name];
    }

    public function getInjectedParams($injections){
        $params = [];
        if(count($injections) > 0){
            foreach ($injections as $innerInjection){
                // is registered ?
                if(is_string($innerInjection) && $this->has($innerInjection)){
                    $param = $this->get($innerInjection);
                    array_push($params,$param);
                }
                else {
                    array_push($params,$innerInjection);
                }
            }
        }
        return $params;
    }

    public function getNew($name){
        if(!is_string($name)) { throw new \Exception('Name required'); }
        if(!$this->has($name)){ throw new \Exception('No element registered for the name '. $name); }

        $injection = $this->container[$name];
        $value = $injection->value;
        // class
        if($this->isClass($value)){
            // get injected params
            $params = $this->getInjectedParams($injection->injections);
            // create class instance
            $instance = $this->injectParameters($value, $params);
            $this->addToCache($name, $instance, $injection->isCachable);
            return $instance;
        }
        else {
            $this->addToCache($name, $value, $injection->isCachable);
            return $value;
        }
    }

    public function get($name) {
        if(!is_string($name)) { throw new \Exception('Name required'); }

        if(isset($this->cache[$name])){
            return $this->cache[$name];
        }
        else {
            return $this->getNew($name);
        }
    }

    public function invoke($name){
        if(!is_string($name)) { throw new \Exception('Name required'); }
        if(!$this->has($name)){ throw new \Exception('No element registered for the name '. $name); }

        $injection = $this->container[$name];
        $callable = $injection->value;
        if(is_callable($callable)){
            $params = $this->getInjectedParams($injection->injections);
            return call_user_func($callable,...$params);
        }
        else {
            throw new \Exception($name.' is not a callable');
        }
    }
}