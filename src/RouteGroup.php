<?php
namespace MicroPHP;

use MicroPHP\Router as Router;

class RouteGroup
{
    protected $url;
    public $routeGroupConfigs;
    public $middlewares;
    public $groupBasePath;
    public $router;
    public function __construct(Router $router, $groupBasePath)
    {
        $this->router = $router;
        $this->groupBasePath = $groupBasePath;
        $this->middlewares = [];
        $this->routeGroupConfigs = [];
        $this->url = new Url();
    }

    public function formatPath($groupBasePath, $path){
        return $this->url->concatGroupBaseAndPath($groupBasePath, $path);
    }

    public function get($path, $callable){
        return $this->map(['GET'],$path, $callable);
    }

    public function post($path, $callable){
        return $this->map(['POST'],$path, $callable);
    }

    public function put($path, $callable){
        return $this->map(['PUT'],$path, $callable);
    }

    public function delete($path, $callable){
        return $this->map(['DELETE'],$path, $callable);
    }

    public function patch($path, $callable){
        return $this->map(['PATCH'],$path, $callable);
    }

    public function options($path, $callable){
        return $this->map(['OPTIONS'],$path, $callable);
    }

    public function map($methods, $path, $callable){
        $path = $this->formatPath($this->groupBasePath, $path);
        $routeConfig = $this->router->map($methods, $path,$callable);
        array_push($this->routeGroupConfigs, $routeConfig);
        return $routeConfig;
    }

    private function addMiddlewaresToRouteGroupConfigs(){
        if(count($this->middlewares)> 0){
            foreach ($this->middlewares as $middleware){
                foreach ($this->routeGroupConfigs as $routeConfig){
                    if(!in_array($middleware, $routeConfig->middlewares)){
                        $routeConfig->add($middleware);
                    }
                }
            }
        }
    }

    public function add($middleware){
        array_push($this->middlewares, $middleware);
        $this->addMiddlewaresToRouteGroupConfigs();
        return $this;
    }
}