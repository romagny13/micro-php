# Micro PHP

Micro PHP is a Micro Framework with router, dependency injection, twig support and more. Easy to use to create sites or Api.

* **Router**
* **Middlewares**
* **Dependency injection**
* **Twig** renderer
* **Flash** messages
* **Csrf** protection
* **Validation** with [PHPValidator](https://packagist.org/packages/romagny13/php-validator)
* And **more** (easy to use with Eloquent ORm for example)

## Installation

```
composer require romagny13/micro-php
```

## Documentation

* [Gitbook](https://romagny13.gitbooks.io/micro-php/)

## Example

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = [
    'base' => 'http://localhost/micro-demo/',
    'templates' => __DIR__.'/../templates',
];
$app = new \MicroPHP\App($settings);
$router = $app->router;

// dependencies
require __DIR__ . '/../src/dependencies.php';

// routes
require __DIR__ . '/../src/routes.php';

// run the app
$router->run();
```


dependencies.php

```php
<?php


$injector = $app->injector;

// Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$injector->register('db', $capsule);
$injector->register('auth', \App\Auth\Auth::class);
$injector->register('AuthMiddleware', \App\Middleware\AuthMiddleware::class, [$injector]);
$injector->register('csrf', \MicroPHP\Csrf::class);
$injector->register('CheckCsrfMiddleware', \App\Middleware\CheckCsrfMiddleware::class, [$injector]);
$injector->register('CsrfMiddleware', \App\Middleware\CsrfMiddleware::class, [$injector]);


$flash = new \MicroPHP\Flash();
$injector->register('flash', $flash);

// DI Controllers
$injector->register('HomeController', \App\Controllers\HomeController::class, [$injector]);
$injector->register('PostController', \App\Controllers\PostController::class, [$injector]);
$injector->register('AuthController', \App\Controllers\AuthController::class, [$injector]);


// add variables to twig
$renderer = $app->renderer;
$renderer->twig->addGlobal('flash', $flash);
$renderer->twig->addGlobal('auth',[
    'isLogged' => $injector->get('auth')->isLogged(),
    'user' => $injector->get('auth')->user()
]);

$renderer->twig->addFunction(new Twig_SimpleFunction('canAddPost',function() use($injector){
    return $injector->get('auth')->canAddPost();
}));

$renderer->twig->addFunction(new Twig_SimpleFunction('canEditPost',function($post) use($injector){
    return $injector->get('auth')->canEditPost($post);
}));
```


routes.php

```php

<?php

$router->get('/', 'HomeController:index')->setName('home');

$router->group('/auth', function(){
    $this->get('/signup', 'AuthController:getSignup')->setName('auth.signup');
    $this->post('/signup', 'AuthController:postSignup');
    $this->get('/signin', 'AuthController:getSignin')->setName('auth.signin');
    $this->post('/signin', 'AuthController:postSignin');
    $this->get('/signout', 'AuthController:getSignout')->setName('auth.signout');
});

$router->group('/posts', function(){
    $this->get('', 'PostController:index')->setName('posts.index');
    $this->get('/create', 'PostController:getCreate')->setName('posts.create')->add('AuthMiddleware');
    $this->post('/create', 'PostController:postCreate');
    $this->post('/delete', 'PostController:deletePost')->setName('posts.delete');
});

$router->get('.*', function($route){
    $route->router->go('home');
});

$router
    ->add($injector->get('CheckCsrfMiddleware'))
    ->add($injector->get('CsrfMiddleware'));
```