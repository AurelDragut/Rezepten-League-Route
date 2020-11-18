<?php

namespace App\Classes\MySQLi;

use App\Classes\DatabaseConnectable;
use Dotenv\Dotenv;
use Exception;
use mysqli;

class Database implements DatabaseConnectable
{

    private static $instance;
    private mysqli $connection;
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPass;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(dirname(__DIR__)));
        $dotenv->load();

        $this->dbHost = $_ENV['DB_HOST'];
        $this->dbName = $_ENV['DB_NAME'];
        $this->dbUser = $_ENV['DB_USER'];
        $this->dbPass = $_ENV['DB_PASS'];
        $this->connection = new mysqli($this->dbHost, $this->dbUser, $this->dbPass, $this->dbName);
        if ($this->connection->connect_errno) {
            echo "Failed to connect to MySQL: " . $this->connection->connect_error;
        }
        $this->executeStatement('SET NAMES utf8');
    }

    public function executeStatement($statement = "", $parameters = [])
    {
        if (!($stmt = $this->connection->prepare($statement))) {
            echo "Prepare failed: (" . $this->connection->errno . ") " . $this->connection->error;
        }

        if (count($parameters) > 0) {
            $type = '';
            foreach ($parameters as $key => &$value) {
                switch (gettype($value)) {
                    case "integer":
                        $type .= 'i';
                        break;
                    case "double":
                        $type .= 'd';
                        break;
                    case "string":
                        $type .= 's';
                        break;
                    default:
                        $type .= 'b';
                        break;
                }
            }
            array_unshift($parameters, $type);
            call_user_func_array(
                array($stmt, 'bind_param'),
                $parameters);
        }
        $stmt->execute();

        return $stmt;
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function Insert($statement = "", $parameters = [])
    {
        try {

            $this->executeStatement($statement, $parameters);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function Select($statement = "", $parameters = [], string $class = '')
    {
        $stmt = $this->executeStatement($statement, $parameters);
        if ($class !== '') {
            $result = $stmt->get_result()->fetch_object($class);
            foreach ($result as $key => &$value) {
                $method = 'related_' . $key . '_list';
                if (method_exists($result, $method)) {
                    $result->$key = $result->$method();
                }
            }
            return $result;
        }
        return $stmt->get_result()->fetch_assoc();
    }

    public function MultiSelect($statement = "", $parameters = [], string $class = '')
    {
        try {

            $stmt = $this->executeStatement($statement, $parameters);
            if ($class !== '') {
                $objects = [];
                $results = $stmt->get_result();
                while ($result = $results->fetch_object($class)) {
                    foreach ($result as $key => &$value) {
                        if (in_array($key, $class::FILLABLE)) {
                            $method = 'related_' . $key . '_list';
                            if (method_exists($result, $method)) {
                                $result->$key = $result->$method();
                            } else {
                                $result->$key = $value;
                            }
                        }
                    }
                    $objects[] = $result;
                }
                return $objects;
            }
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch
        (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function Update($statement = "", $parameters = [])
    {
        try {

            $this->executeStatement($statement, $parameters);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function Remove($statement = "", $parameters = [])
    {
        try {

            $this->executeStatement($statement, $parameters);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function lastInsertId()
    {
        return $this->connection->insert_id;
    }

    public function numRows($stmt)
    {
        return $stmt->get_result()->num_rows;
    }
}
