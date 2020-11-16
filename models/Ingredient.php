<?php

namespace App\Models;

use App\Classes\Modelable;
use App\Classes\PDO\Database;

class Ingredient extends Model implements Modelable
{
    public const TABLE = 'ingredients';
    public const FILLABLE = ['name', 'rezepten'];
    public const LIST = ['name', 'rezepten'];
    public const HIDDEN = ['rezepten'];
    public static object $_instance;
    protected int $nr;

    public function rules($method): array
    {
        return [
            'name' => [self::RULE_REQUIRED],
        ];
    }

    public function related_rezepten_list():array
    {
        $list = [];
        $list['relations_table'] = 'ingredients_recipes';
        $list['relation_table'] = 'recipes';
        $list['own_field'] = 'ingredient_nr';
        $list['relation_field'] = 'recipe_nr';
        $list['relation_name'] = 'name';
        $list['extra_data'] = 'amount';
        $list['relations'] = $this->belongsToMany($list['relations_table'], $list['relation_table'], $list['own_field'], $list['relation_field'], $list['relation_name'], $list['extra_data']);
        return $list;
    }

    public static function getInstance ()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function fill($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, Recipe::FILLABLE)) {
                $this->$key = $value;
            }
        }
    }

    public function save():int
    {
        $keys = [];
        $values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, self::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    continue;
                } else {
                    if ($key == 'confirm_password') {
                        continue;
                    } else {
                        $keys[] = $key;
                        if ($key == 'password') $value = password_hash($value, PASSWORD_BCRYPT);
                        $values[] = $value;
                    }
                }
            }
        }
        $values = array_map(function ($m) {
            return '\'' . $m . '\'';
        }, $values);
        $sql = "INSERT INTO ingredients (" . implode(', ', $keys) . ") VALUE (" . implode(', ', $values) . ")";
        return Database::getInstance()->Insert($sql);
    }

    public function update():int
    {
        $keys = [];
        $values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, Ingredient::FILLABLE)) {
                $keys[] = $key;
                $values[] = $value;
            }
        }
        $query_values = array_combine($keys, $values);
        $key_values = [];
        foreach ($query_values as $key => $value) {
            $key_values[] = "`$key` = '$value'";
        }
        $sql = "UPDATE `ingredients` SET " . implode(', ', $key_values) . " WHERE `nr` = '$this->nr' ";
        Database::getInstance()->Update($sql);
        return $this->nr;
    }

    public function delete()
    {
        $query = "DELETE FROM ingredients WHERE nr= ?";
        Database::getInstance()->Remove($query, [$this->nr]);
        header('Location:/admin/ingredients/index');
    }

}
