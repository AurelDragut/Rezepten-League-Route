<?php
namespace App\Classes\PDO;

use App\Classes\DatabaseConnectable;
use Exception;
use PDO;
use Dotenv\Dotenv;

class Database implements DatabaseConnectable
{

    private ?object $connection = null;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPass;
    public static object $instance;

    public function __construct(){

        try{
            $dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
            $dotenv->load();

            $this->dbHost = $_ENV['DB_HOST'];
            $this->dbName = $_ENV['DB_NAME'];
            $this->dbUser = $_ENV['DB_USER'];
            $this->dbPass = $_ENV['DB_PASS'];

            $this->connection = new PDO("mysql:host={$this->dbHost};dbname={$this->dbName};", $this->dbUser, $this->dbPass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->executeStatement('SET NAMES utf8');

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }

    }

    public static function getInstance()
    {
        if (!isset(self::$instance))
        {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function Insert( $statement = "" , $parameters = [] ):int{
        try{

            $this->executeStatement( $statement , $parameters );
            return $this->connection->lastInsertId();

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function Select( $statement = "" , $parameters = [], $class = ''){
        try{
            $stmt = $this->executeStatement( $statement , $parameters );
            if ($class !== '') return $stmt->fetchObject($class);
            return $stmt->fetch();

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function MultiSelect($statement = "", $parameters = [], $class = '')
    {
        try{

            $stmt = $this->executeStatement( $statement , $parameters );
            if ($class !== '') {
                $results = [];
                while ($result = $stmt->fetchObject($class)) {
                    $results[] = $result;
                }
                return $results;
            }
            return $stmt->fetchAll();

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function Update( $statement = "" , $parameters = [] ){
        try{

            $this->executeStatement( $statement , $parameters );

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function Remove( $statement = "" , $parameters = [] ){
        try{

            $this->executeStatement( $statement , $parameters );

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function executeStatement( $statement = "" , $parameters = [] ){
        try{

            $stmt = $this->connection->prepare($statement);
            $stmt->execute($parameters);
            return $stmt;

        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function lastInsertId():int
    {
        return $this->connection->lastInsertId();
    }

    public function numRows($stmt)
    {
        return $stmt->rowCount();
    }
}
