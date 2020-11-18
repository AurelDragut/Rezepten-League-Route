<?php

namespace App\Models;

use App\Classes\Container;

abstract class Model
{
    protected string $statement;
    protected array $parameters;
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public array $errors = [];
    private array $relations;
    protected int $nr;
    public int $parent;
    private Container $container;

    public function getDatabase() {
        if (!isset($this->container)) $this->container = new Container();
        return $this->container->container->get('App\Classes\DatabaseConnectable');
    }

    public function validate($method = 'create'):bool
    {
        foreach ($this->rules($method) as $attribute => $rules) {
            if (isset($this->{$attribute})) $value = $this->{$attribute};

            foreach ($rules as $rule) {
                $rule_name = $rule;
                if (!is_string($rule_name)) {
                    $rule_name = $rule[0];
                }
                if ($rule_name === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($rule_name === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($rule_name === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($rule_name === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                if ($rule_name === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages():array
    {
        return [
            self::RULE_REQUIRED => 'Dieses Feld ist ein Pflichtfeld.',
            self::RULE_EMAIL => 'Dieses Feld muss eine gültige E-Mail-Adresse sein.',
            self::RULE_MIN => 'Die Mindestlänge dieses Feldes muss {min} sein',
            self::RULE_MAX => 'Die maximale Länge dieses Feldes muss {max} sein',
            self::RULE_MATCH => 'Dieses Feld muss dasselbe wie {Match} sein.',
        ];
    }

    public function hasErrors($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    public function getFirstError($attribute):string
    {
        return $this->errors[$attribute][0] ?? false;
    }

    public function belongsTo($class, $relation_table)
    {
        $query = "SELECT name from $relation_table WHERE nr= ?";
        return $this->getDatabase()->Select($query, [$this->parent], $class);
    }

    public function hasMany($class, $relation_table, $relation_field):array
    {
        $query = "SELECT * from `$relation_table` WHERE `" . $relation_field . "nr`='" . $this->nr . "'";
        return $this->getDatabase()->MultiSelect($query, [], $class);
    }

    public function belongsToMany($table, $related_table, $own_key, $related_key, $related_field, $extra_data = ''):array
    {
        $query = "SELECT `$related_field`, `$extra_data` from `$table` JOIN `$related_table` on `$related_table`.`nr` = `$table`.`$related_key` WHERE `$own_key`='" . $this->getNr() . "'";
        return $this->getDatabase()->MultiSelect($query);
    }

    /**
     * @return int
     */
    public function getNr(): int
    {
        return $this->nr;
    }

    /**
     * @param $nr
     * @return int
     */
    public function setNr($nr): int
    {
        return $this->nr = $nr;
    }

    public function all($table, $fields = []):object
    {
        if (count($fields) > 0) $fieldsList = implode(',',$fields); else $fieldsList = '*';
        $this->setStatement("select $fieldsList from $table");
        $this->setParameters([]);
        return $this;
    }

    public function where(array $params, $separator = ' AND '): object
    {
        $items = [];
        foreach ($params as $param) {
            $item = explode(' ',$param);
            $arguments['params'][] = $item[2];
            $this->setParameters($arguments['params']);
            $item[2] = '?';
            $items[] = implode(' ',$item);
        }
        $statement = $this->getStatement() . ' WHERE ' . implode($separator, $items)." ";
        $this->setStatement($statement);
        return $this;
    }

    public function get($class = '') {
        return $this->getDatabase()->MultiSelect($this->getStatement(), $this->getParameters(), $class);
    }

    public function first($class = '') {
        return $this->getDatabase()->Select($this->getStatement(), $this->getParameters(), $class);
    }

    public function setRelations(): object
    {
        $relations = [];
        foreach ($this as $field => $value) {
            $method = 'related_' . $field . '_list';
            if (method_exists($this, $method)) {
                $results = $this->$method();
                $results['relations'] = $value;
                $relations[$field] = $results;
            }
        }
        $this->relations = $relations;
        return $this;
    }

    public function insertRelationsValues()
    {
        foreach ($this->relations as $relation) {
            $values = explode(', ', $relation['relations']);
            foreach ($values as $value) {
                $value = explode(' - ', $value);
                if (count($value) > 1) {
                    $relation_name = $value[1];
                    $relation_quantity = $value[0];
                } else {
                    $relation_name = $value[0];
                    $relation_quantity = '';
                }
                $sql = "select * from " . $relation['relation_table'] . " where name = ?";
                $check_relations = $this->getDatabase()->executeStatement($sql, [trim($relation_name)]);
                if ($this->getDatabase()->numRows($check_relations) > 0) {
                    $result = $this->getDatabase()->Select($sql, [trim($relation_name)]);
                    $relation_nr = $result['nr'];
                } else {
                    $sql = "Insert into " . $relation['relation_table'] . " set name = ?";
                    $this->getDatabase()->Insert($sql, [trim($relation_name)]);
                    $relation_nr = $this->getDatabase()->lastInsertId();
                }
                $sql = "insert into `" . $relation['relations_table'] . "` (`" . $relation['own_field'] . "`, `" . $relation['relation_field'] . "`, `" . $relation['extra_data'] . "`) VALUES ('$this->nr', '$relation_nr', ?) ON DUPLICATE KEY UPDATE `" . $relation['extra_data'] . "`= ?";
                $this->getDatabase()->Insert($sql, [trim($relation_quantity), trim($relation_quantity)]);
            }
        }
    }

    public function updateRelationsValues()
    {
        $this->deleteRelationsValues();
        $this->insertRelationsValues();
    }

    public function deleteRelationsValues()
    {
        if (isset($this->relations)) {
            foreach ($this->relations as $relation_pair) {
                $names = [];
                $relation_pair['relations'] = explode(',', $relation_pair['relations']);
                foreach ($relation_pair['relations'] as $relation) {
                    $name = explode(' - ', $relation);
                    $names[] = end($name);
                }
                $questionMarks = '';
                for ($i=0;$i<count($names);$i++) $questionMarks .= '?,';
                $questionMarks = rtrim($questionMarks, ',');
                $sql = "DELETE FROM " . $relation_pair['relations_table'] . " 
            WHERE " . $relation_pair['own_field'] . " = '$this->nr' 
            and " . $relation_pair['relation_field'] . " not in 
            (SELECT nr from " . $relation_pair['relation_table'] . " where name in ($questionMarks))";
                $this->getDatabase()->Remove($sql, $names);
            }
        }
    }

    /**
     * @return string
     */
    public function getStatement(): string
    {
        return $this->statement;
    }

    /**
     * @param string $statement
     */
    public function setStatement(string $statement): void
    {
        $this->statement = $statement;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
