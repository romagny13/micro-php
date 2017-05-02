<?php

namespace MicroPHP;

class Router
{
    protected $routeResolver;
    protected $url;
    protected $response;
    public $middlewares;
    public $injector;
    public $base;
    public $routeConfigs;

    public function __construct($settings = null)
    {
        /*
            settings :
             - base
             - injector
            for Tests:
             - server
             - SendResponse
        */

        $this->middlewares = [];
        $this->routeConfigs = [];
        $this->routeResolver = new RouteResolver();
        // url infos
        $this->url = isset($settings['server']) ? new Url($settings['server']):new Url() ;
        if(isset($settings)){
            // base url
            $this->base = isset($settings['base']) ? $settings['base'] : $this->url->getOrigin();
            // injector
            $this->injector = isset($settings['injector']) && $settings['injector'] instanceof Injector ? $settings['injector']: null;
            // responses
            $this->response = isset($settings['SendResponse']) && $settings['SendResponse'] instanceof SendResponseInterface ? $settings['SendResponse']: new SendResponse();
        }
        else {
            $this->base = $this->url->getOrigin();
            $this->response = new SendResponse();
        }
    }
    
    public function get($path, $callable){
      return $this->map(['GET'], $path, $callable);
    }

    public function post($path, $callable){
       return $this->map(['POST'], $path, $callable);
    }

    public function put($path, $callable){
       return $this->map(['PUT'], $path, $callable);
    }

    public function delete($path, $callable){
       return $this->map(['DELETE'], $path, $callable);
    }

    public function patch($path, $callable){
       return $this->map(['PATCH'], $path, $callable);
    }

    public function options($path, $callable){
       return $this->map(['OPTIONS'], $path, $callable);
    }

    public function map($methods, $path, $callable){
        if(!is_array($methods)){ throw new \Exception('Array required'); }
        if(!$this->url->validMethods($methods)) { throw new \Exception('Invalid methods (GET|POST|PUT|DELETE|PATCH|OPTIONS):');}
        
        $routeConfig = new RouteConfig($methods, $path, $callable);
        array_push($this->routeConfigs, $routeConfig);
        return $routeConfig;
    }

    public function group($path, $callable){
        if(!is_callable($callable)){ throw new \Exception('Require a callable'); }
        $group = new RouteGroup($this, $path);
        $callable = \Closure::bind($callable,$group);
        $callable();

        return $group;
    }
    
    public function add($middleware){
        array_push($this->middlewares, $middleware);
        return $this;
    }

    public function resolveControllerActionString($callableString){
        if(strpos(':',$callableString)) { throw new \Exception('Invalid controller string '.$callableString .' (example: MyController:index)'); }
        return explode(':', $callableString);
    }

    public function callInjection($key, $route){
        if(!isset($this->injector)) { throw new \Exception('No injector, cannot resolve '.$key );}
        $nameAndAction = $this->resolveControllerActionString($key);
        $controller =  $this->injector->get($nameAndAction[0]);
        call_user_func(array($controller, $nameAndAction[1]), $route);
    }

    public function callFunction($callable, $route){
        if(!is_callable($callable)){ throw new \Exception('Route require a callable'); }
        call_user_func($callable, $route);
    }

    public function callRouteCallable($route){
        if(is_string($route->matched->callable)){
           $this->callInjection($route->matched->callable, $route);
        }
        else {
            $this->callFunction($route->matched->callable,$route);
        }
    }

    public function mergeMiddlewares($routeMiddlewares, $middlewares){
       return array_merge($routeMiddlewares,$middlewares);
    }

    public function callMiddlewares($middlewares, $route,$next, $onAbort){
        foreach ($middlewares as $middleware){
            // resolve
            if(is_string($middleware)){
                if(!isset($this->injector)) { throw new \Exception('No injector, cannot resolve '.$route->matched->callable );}
                // injector
                $callable = $this->injector->get($middleware);
                $result = $callable($route);
                if(!$result){
                    return $onAbort();
                }
            }
            else if(is_callable($middleware)){
                $result = $middleware($route);
                if(!$result){
                    return $onAbort();
                }
            }
        }
        return $next();
    }

    public function getRouteConfigByName($routeConfigs, $routeName){
        foreach ($routeConfigs as $routeConfig){
            if($routeConfig->name !== '' && $routeConfig->name === $routeName){
                return $routeConfig;
            }
        }
        return null;
    }

    public function pathFor($routeName, $params=[], $queryParams=[]){
        $routeConfig = $this->getRouteConfigByName($this->routeConfigs, $routeName);
        if(!isset($routeConfig)) { throw new \Exception('No route found for '. $routeName); }

        $path = $this->routeResolver->replaceParamsByValues($routeConfig->path,$params);
        $path .= $this->routeResolver->getQueryStringFromArray($queryParams);
        $base = preg_replace('/\/$/', '',$this->base);
        return $base  . $path;
    }
    
    public function go($routeName, $params=[], $queryParams=[]){
        $url = $this->pathFor($routeName, $params, $queryParams);
        $this->response->redirect($url);
    }

    public function run($onError = null){
        // get url infos
        $method = $this->url->getMethod();
        $url = $this->url->getFullUrl();
        $path = $this->url->getPath($this->base, $url);
        // resolve route
        $resolvedRoute = $this->routeResolver->resolve($this->routeConfigs,$method, $path, $url, $this);
        if($resolvedRoute){
            // middlewares
            $middlewares = $this->mergeMiddlewares($resolvedRoute->matched->middlewares, $this->middlewares);
            $this->callMiddlewares($middlewares, $resolvedRoute, function() use($resolvedRoute){
                // on success
                $this->callRouteCallable($resolvedRoute);
            }, function() use($onError,$path, $url){
                // on abort
                $this->raiseError('Abort', $onError,$url, $path);
            });
        }
        else {
            // no route found
            $this->raiseError('NotFound', $onError, $url, $path);
        }
    }

    private function raiseError($type, $callable, $url, $path){
        if(is_callable($callable)){
            call_user_func($callable, $type, $url, $path);
        }
    }
}