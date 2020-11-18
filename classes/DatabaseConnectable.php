<?php
namespace App\Classes;

interface DatabaseConnectable
{
    public static function getInstance();

    public function Insert($statement = "", $parameters = []);

    public function Select( $statement = "" , $parameters = [], string $class = '');

    public function MultiSelect($statement = "", $parameters = [], string $class = '');

    public function Update($statement = "", $parameters = []);

    public function Remove($statement = "", $parameters = []);

    public function executeStatement($statement = "", $parameters = []);

    public function lastInsertId();
}
