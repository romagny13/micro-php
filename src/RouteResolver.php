<?php

namespace MicroPHP;


class RouteResolver
{
    protected $server;

    public function __construct(ServerInterface $server = null)
    {
        $this->server = isset($server) ? $server: new ServerInfos();
    }
    
    public function replaceParamsByValues($path, $params) {
        return preg_replace_callback('/:(\w+)(\([^\)]+\))?/',function($matches) use($params) {
            $name = $matches[1];
            if(isset($matches[2])){
                if(isset($params[$name]) && preg_match($matches[2], $params[$name])){
                    return $params[$name];
                }
            }
            else if(isset($params[$name])){
                return $params[$name];
            }
            throw new \Exception('No params '. $name. ' match');
        },$path);
    }

    public function getQueryStringFromArray($query) {
        $result = [];
       foreach ($query as $key=>$value){
           $current = $key .'='.$value;
           array_push($result, $current);
       }

        if(count($result)>0){
            return '?'. join('&', $result);
        }
        return '';
    }

    public function replaceParamsByRegex($path){
        return preg_replace_callback('/:(\w+)(\([^\)]+\))?/',function($matches){
            // 0 => :b([a-z]+)
            // 1 => b
            // 2 => ([a-z]+)
            if(isset($matches[2])){
                return $matches[2];
            }
            else {
                // default number
                return '([0-9]+)';
            }
        },$path);
    }

    public function getMatched($routes, $method, $path)
    {
        // GET /posts/:id => /posts/([0-9]+)
        foreach ($routes as $route) {
            if(in_array($method, $route->methods)){
                $pattern = $this->replaceParamsByRegex($route->path);
                if(preg_match("#^$pattern$#i", $path)){
                    return $route;
                } 
            }
        }
        return null;
    }

    public function getParams($routePath, $toPath){
        $paramNames = [];
        $params = [];
        $pattern = preg_replace_callback('/:(\w+)(\([^\)]+\))?/',function($matches) use(&$paramNames){
            // 0 => :b([a-z]+)
            // 1 => b
            // 2 => ([a-z]+)
            array_push($paramNames,$matches[1]);
            if(isset($matches[2])){
                return $matches[2];
            }
            else {
                // default number
                return '([0-9]+)';
            }
        },$routePath);
        if(preg_match("#^$pattern$#i", $toPath, $matches)){
            array_shift($matches);
            foreach ($matches as $i=>$match) {
                $params[$paramNames[$i]] = $match;
            }
        }
        return (object) $params;
    }
    
    public function getQuery($queryString) {
        $result = [];
        if(is_string($queryString) && $queryString !== ''){
            // remove ? if present
            if($queryString[0] === '?'){
                $queryString = substr($queryString,1);
            }
            $splits =  preg_split('/&/', $queryString);
            foreach ($splits as $keyValueString){
                $keyValue = preg_split('/=/', $keyValueString);
                $result[$keyValue[0]] = urldecode($keyValue[1]);
            }
        }
        return (object) $result;
    }

    public function getQueryString() {
        return $this->server->getQueryString();
    }

    public function getData(){
        return $this->server->getContent();
    }
    
    public function resolve($routes,$method, $path, $url, $router){
        $routeConfig = $this->getMatched($routes, $method, $path);
        if(isset($routeConfig)){
            $params = $this->getParams($routeConfig->path, $path);
            $queryString = $this->getQueryString();
            $query = $this->getQuery($queryString);
            $data = $this->getData();
            
            return new Route($routeConfig, $method, $data,$url, $path, $params, $queryString, $query, $router);
        }
        return null;
    }

}