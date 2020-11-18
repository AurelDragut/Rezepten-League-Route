<?php declare(strict_types=1);

require_once '../vendor/autoload.php';

use App\Middlewares\AuthMiddleware;
use League\Route\RouteGroup;

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$router = new League\Route\Router;

// map a route
$router->map('GET', '/', 'App\Controllers\RecipesController::display');
$router->group('/admin', function (RouteGroup $route) {
    $route->map('GET', '/recipes/index', 'App\Controllers\RecipesController::index');
    $route->map('GET', '/recipes/create', 'App\Controllers\RecipesController::create');
    $route->map('GET', '/recipes/edit/{id}', 'App\Controllers\RecipesController::edit');
    $route->map('GET', '/recipes/read/{id}', 'App\Controllers\RecipesController::read');
    $route->map('POST', '/recipes/save', 'App\Controllers\RecipesController::save');
    $route->map('POST', '/recipes/update/{id}', 'App\Controllers\RecipesController::update');
    $route->map('GET', '/recipes/delete/{id}', 'App\Controllers\RecipesController::delete');
    $route->map('GET', '/ingredients/index', 'App\Controllers\IngredientsController::index');
    $route->map('GET', '/ingredients/create', 'App\Controllers\IngredientsController::create');
    $route->map('GET', '/ingredients/edit/{id}', 'App\Controllers\IngredientsController::edit');
    $route->map('GET', '/ingredients/read/{id}', 'App\Controllers\IngredientsController::read');
    $route->map('POST', '/ingredients/save', 'App\Controllers\IngredientsController::save');
    $route->map('POST', '/ingredients/update/{id}', 'App\Controllers\IngredientsController::update');
    $route->map('GET', '/ingredients/delete/{id}', 'App\Controllers\IngredientsController::delete');
    $route->map('GET', '/links/index', 'App\Controllers\LinksController::index');
    $route->map('GET', '/links/create', 'App\Controllers\LinksController::create');
    $route->map('GET', '/links/edit/{id}', 'App\Controllers\LinksController::edit');
    $route->map('GET', '/links/read/{id}', 'App\Controllers\LinksController::read');
    $route->map('POST', '/links/save', 'App\Controllers\LinksController::save');
    $route->map('POST', '/links/update/{id}', 'App\Controllers\LinksController::update');
    $route->map('GET', '/links/delete/{id}', 'App\Controllers\LinksController::delete');
    $route->map('GET', '/users/index', 'App\Controllers\UsersController::index');
    $route->map('GET', '/users/create', 'App\Controllers\UsersController::create');
    $route->map('GET', '/users/edit/{id}', 'App\Controllers\UsersController::edit');
    $route->map('GET', '/users/read/{id}', 'App\Controllers\UsersController::read');
    $route->map('POST', '/users/save', 'App\Controllers\UsersController::save');
    $route->map('POST', '/users/update/{id}', 'App\Controllers\UsersController::update');
    $route->map('GET', '/users/delete/{id}', 'App\Controllers\UsersController::delete');
})->middleware(new AuthMiddleware);

$router->map('GET', '/recipes/{title}', 'App\Controllers\RecipesController::read');
$router->map('GET', '/advanced-search', 'App\Controllers\RecipesController::advancedSearch');
$router->map('POST', '/advanced-search', 'App\Controllers\RecipesController::advancedSearch');
$router->map('GET', '/search', 'App\Controllers\RecipesController::search');
$router->map('GET', '/login', 'App\Controllers\UsersController::login');
$router->map('POST', '/login', 'App\Controllers\UsersController::login');
$router->map('GET', '/logout', 'App\Controllers\UsersController::logout');

$response = $router->dispatch($request);

// send the response to the browser
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
