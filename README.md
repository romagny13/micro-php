# Micro PHP

Micro PHP is a Micro Framework with router, dependency injection, twig support and more. Easy to use to create sites or Api.

* **Router**
* **Middlewares**
* **Injector** (Dependency Injection)
* Renderer (**Twig**)
* **Response** service

Extensions:

* **Flash** messages ([Flash](https://github.com/romagny13/flash))
* **Csrf** Protection ([Csrf](https://github.com/romagny13/csrf))
* **Form Validation** ([PHPValidator](https://packagist.org/packages/romagny13/php-validator))
* And **more** (easy to use with Eloquent ORM for example)

## Installation

```
composer require romagny13/micro-php
```


* [Documentation](https://romagny13.gitbooks.io/micro-php/)


## Example

[Micro demo (blog)](https://github.com/romagny13/micro-demo)

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

session_start();

$settings = [
    'base' => 'http://localhost:8080/',
    'templates' => __DIR__.'/../templates',
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => 'blog',
        'username' => 'root',
        'password' => '',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]
];
$app = new \MicroPHP\App($settings);
$router = $app->router;

// dependencies
require __DIR__ . '/../src/dependencies.php';

// routes
require __DIR__ . '/../src/routes.php';
//
$router->run();
```

**dependencies.php**

```php
<?php
$injector = $app->injector;

// Eloquent
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();


$injector
    ->register('db', $capsule)
    ->register('auth', \App\Auth\Auth::class)
    ->register('AuthMiddleware', \App\Middleware\AuthMiddleware::class, [$injector])
    ->register('csrf', \MicroPHP\Csrf\Csrf::class)
    ->register('CheckCsrfMiddleware', \App\Middleware\CheckCsrfMiddleware::class, [$injector])
    ->register('CsrfMiddleware', \App\Middleware\CsrfMiddleware::class, [$injector])
    ->register('flash', \MicroPHP\Flash\Flash::class)
    ->register('HomeController', \App\Controllers\HomeController::class, [$injector])
    ->register('PostController', \App\Controllers\PostController::class, [$injector])
    ->register('AuthController', \App\Controllers\AuthController::class, [$injector]);


// add variables to twig
$renderer = $app->renderer;
$renderer->twig->addGlobal('flash', $injector->get('flash'));
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

**routes.php**

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

// caution execution order(from start to end)
$router
    ->add($injector->get('CheckCsrfMiddleware'))
    ->add($injector->get('CsrfMiddleware'));
```