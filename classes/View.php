<?php
namespace App\Classes;

use App\Models\Link;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class View
{
    public object $twig;
    private static object $instance;

    private function __construct(){
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new Environment($loader, ['debug' => true]);
        $this->twig->addExtension(new DebugExtension());

        $menu_items = Link::getMenu();

        $this->twig->addGlobal('cookie', $_COOKIE);
        $this->twig->addGlobal('get', $_GET);
        $this->twig->addGlobal('post', $_POST);
        $this->twig->addGlobal('menuItems', $menu_items);
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new View();
        }
        return self::$instance;
    }

    public function render($template, $params):string {

        return $this->twig->render($template, $params);
    }
}
