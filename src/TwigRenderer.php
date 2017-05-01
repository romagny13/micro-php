<?php
namespace MicroPHP;

use Twig_Environment;
use Twig_Loader_Filesystem;

class TwigRenderer
{
    public $twig;
    public $router;

    public function __construct($templateDirectory, $router = null)
    {
        $loader = new Twig_Loader_Filesystem($templateDirectory);
        $this->twig = new Twig_Environment($loader);
        if(isset($router)){
            $this->setDefaults($router);
        }
    }

    public function setDefaults($router){
        $this->router = $router;
        $router = $this->router;

        // {{ base_url }}
        $this->twig->addGlobal('base_url', $router->base);

        // {{ path_for('home') }}
        $this->twig->addFunction(new \Twig_SimpleFunction('path_for', function ($routeName, $params = [], $query = []) use ($router) {
            return $router->pathFor($routeName, $params, $query);
        }));
    }

    public function render($viewPath, $params=[]){
        echo $this->twig->render($viewPath,$params);
    }
}