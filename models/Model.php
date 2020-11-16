<?php

namespace App\Models;

use App\Classes\PDO\Database;

abstract class Model
{
    public static array $arguments;
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public array $errors = [];
    private array $relations;
    protected int $nr;
    public int $parent;

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
        return Database::getInstance()->Select($query, [$this->parent], get_class($class));
    }

    public function hasMany($class, $relation_table, $relation_field):array
    {
        $query = "SELECT * from `$relation_table` WHERE `" . $relation_field . "nr`='" . $this->nr . "'";
        return Database::getInstance()->MultiSelect($query, [], $class);
    }

    public function belongsToMany($table, $related_table, $own_key, $related_key, $related_field, $extra_data = ''):array
    {
        $query = "SELECT `$related_field`, `$extra_data` from `$table` JOIN `$related_table` on `$related_table`.`nr` = `$table`.`$related_key` WHERE `$own_key`='" . $this->getNr() . "'";
        return Database::getInstance()->MultiSelect($query);
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
        self::$arguments['sql'] = "select $fieldsList from $table";
        self::$arguments['params'] = [];
        return $this;
    }

    public function where(array $params, $separator = ' AND '): object
    {
        $items = [];
        foreach ($params as $param) {
            $item = explode(' ',$param);
            self::$arguments['params'][] = $item[2];
            $item[2] = '?';
            $items[] = implode(' ',$item);
        }
        /** @var string $this */
        self::$arguments['sql'] .= ' WHERE ' . implode($separator, $items)." ";
        return $this;
    }

    public function get($class = '') {
        return Database::getInstance()->MultiSelect(self::$arguments['sql'], @self::$arguments['params'], $class);
    }

    public function first($class = '') {
        return Database::getInstance()->Select(self::$arguments['sql'], @self::$arguments['params'], $class);
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
                $check_relations = Database::getInstance()->executeStatement($sql, [trim($relation_name)]);
                if (Database::getInstance()->numRows($check_relations) > 0) {
                    $result = Database::getInstance()->Select($sql, [trim($relation_name)]);
                    $relation_nr = $result['nr'];
                } else {
                    $sql = "Insert into " . $relation['relation_table'] . " set name = ?";
                    Database::getInstance()->Insert($sql, [trim($relation_name)]);
                    $relation_nr = Database::getInstance()->lastInsertId();
                }
                $sql = "insert into `" . $relation['relations_table'] . "` (`" . $relation['own_field'] . "`, `" . $relation['relation_field'] . "`, `" . $relation['extra_data'] . "`) VALUES ('$this->nr', '$relation_nr', ?) ON DUPLICATE KEY UPDATE `" . $relation['extra_data'] . "`= ?";
                Database::getInstance()->Insert($sql, [trim($relation_quantity), trim($relation_quantity)]);
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
                Database::getInstance()->Remove($sql, $names);
            }
        }
    }
}
