<?php
namespace App\Classes;

use App\Models\Link;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class View
{
    public static object $twig;
    private static object $instance;

    private function __construct(){
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        self::$twig = new Environment($loader, ['debug' => true]);
        self::$twig->addExtension(new DebugExtension());

        $menu_items = Link::getMenu();

        self::$twig->addGlobal('cookie', $_COOKIE);
        self::$twig->addGlobal('get', $_GET);
        self::$twig->addGlobal('post', $_POST);
        self::$twig->addGlobal('menuItems', $menu_items);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new View();
        }
        return self::$instance;
    }

    public static function render($template, $params):string {
        View::getInstance();
        return self::$twig->render($template, $params);
    }
}
