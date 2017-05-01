<?php

namespace MicroPHP;


class App
{
    public $router;
    public $injector;
    public $renderer;
    public $settings;
    
    public function __construct($settings)
    {
        if(!isset($settings['templates'])) { throw new \Exception('templates directory required'); }

        $this->settings = $settings;
        
        // injector
        $this->injector = new Injector();
        $settings['injector'] = $this->injector;
        // router
        $this->router = new Router($settings);
        // renderer
        $this->renderer = new TwigRenderer($settings['templates'],$this->router);

        // DI
        $this->injector->register('router', $this->router);
        $this->injector->register('renderer', $this->renderer);
    }
}
