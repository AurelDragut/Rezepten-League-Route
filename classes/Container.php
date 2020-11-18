<?php

namespace App\Classes;

use App\Classes\PDO\Database;
use DI\ContainerBuilder;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Container
{
    private \DI\Container $container;
    private ContainerBuilder $builder;

    public function __construct()
    {
        $this->container = new \DI\Container;
        $this->builder = new ContainerBuilder;
        $this->builder->addDefinitions($this->setDefinitions());
        $this->container = $this->builder->build();
    }

    private function setDefinitions() {
        return [
            'request' => function() {
                return ServerRequestFactory::fromGlobals(
                    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
                );
            },
            'response' => new Response(),
            'emitter' => new SapiEmitter(),
            DatabaseConnectable::class => function () {
                $database = new Database();
                return $database;
            },
            Environment::class => function() {
                $loader = new FilesystemLoader(__DIR__ . '/templates');
                return new Environment($loader);
            },
        ];
    }
}
