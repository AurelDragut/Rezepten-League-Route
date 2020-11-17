<?php

namespace App\Models;

use App\Classes\Modelable;
use App\Classes\PDO\Database;

class Recipe extends Model implements Modelable
{
    public const TABLE = 'recipes';
    public const FILLABLE = ["name", "zutaten", "schnecke", "bild", "portionsnummern", "vorbereitungszeit", "vorbereitung_schwierigkeit", "vorbereitung_anweisungen", "kcal", "kj", "prot", "fett", "kh"];
    public const LIST = ["name", "schnecke", "zutaten", "bild", "portionsnummern", "vorbereitungszeit", "vorbereitung_schwierigkeit", "vorbereitung_anweisungen"];
    public static object $_instance;
    protected int $nr;
    public string $name;

    public function related_zutaten_list(): array
    {
        $list = [];
        $list['relations_table'] = 'ingredients_recipes';
        $list['relation_table'] = 'ingredients';
        $list['own_field'] = 'recipe_nr';
        $list['relation_field'] = 'ingredient_nr';
        $list['relation_name'] = 'name';
        $list['extra_data'] = 'amount';
        $list['relations'] = $this->belongsToMany($list['relations_table'], $list['relation_table'], $list['own_field'], $list['relation_field'], $list['relation_name'], $list['extra_data']);
        return $list;
    }

    public function rules($method): array
    {
        $rules = [
                'name' => [self::RULE_REQUIRED],
                'zutaten' => [self::RULE_REQUIRED],
                'portionsnummern' => [self::RULE_REQUIRED],
                'vorbereitungszeit' => [self::RULE_REQUIRED],
                'vorbereitung_schwierigkeit' => [self::RULE_REQUIRED],
                'vorbereitung_anweisungen' => [self::RULE_REQUIRED],
                'kcal' => [self::RULE_REQUIRED],
                'kj' => [self::RULE_REQUIRED],
                'prot' => [self::RULE_REQUIRED],
                'fett' => [self::RULE_REQUIRED],
                'kh' => [self::RULE_REQUIRED]
            ];
        if ($method == 'create') { $rules['bild'] = [self::RULE_REQUIRED]; }
        return $rules;
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new Recipe();
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

    public function save(): int
    {
        $this->schnecke = str_replace(' ', '-', strtolower($this->name));
        $this->schnecke = str_replace(['Ä', 'Ö', 'Ü', 'ä', 'ü', 'ö'], ['AE', 'OE', 'UE', 'ae', 'ue', 'oe'], $this->schnecke);
        $this->schnecke = preg_replace('/[^A-Za-z0-9\-]/', '', $this->schnecke);
        foreach ($this as $key => &$value) {
            if (in_array($key, Recipe::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    unset($this->$key);
                } else {
                    $keys[] = $key;
                    $values[] = "'".$value."'";
                }
            }
        }
        $sql = "INSERT INTO recipes (" . implode(', ', $keys) . ") VALUE (" . implode(', ', $values) . ")";
        return Database::getInstance()->Insert($sql);
    }

    public function update():int
    {
        $this->schnecke = str_replace(' ', '-', strtolower($this->name));
        $this->schnecke = str_replace(['Ä', 'Ö', 'Ü', 'ä', 'ü', 'ö'], ['AE', 'OE', 'UE', 'ae', 'ue', 'oe'], $this->schnecke);
        $this->schnecke = preg_replace('/[^A-Za-z0-9\-]/', '', $this->schnecke);
        $key_values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, Recipe::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    unset($this->$key);
                } else {
                    $key_values[] = "`$key` = '$value'";
                }
            }
        }
        $sql = "UPDATE `recipes` SET " . implode(', ', $key_values) . " WHERE `nr` = '$this->nr' ";
        Database::getInstance()->Update($sql);
        return $this->nr;
    }

    public function delete()
    {
        $sql = "SELECT `bild` from recipes where `nr` = ?";
        $object = Database::getInstance()->Select($sql, [$this->nr], get_class($this));
        unlink($_SERVER['DOCUMENT_ROOT'] . $object->bild);
        $this->deleteRelationsValues();
        $query = "DELETE FROM recipes WHERE nr= ?";
        Database::getInstance()->Remove($query, [$this->nr]);
        header('Location:/admin/recipes/index');
    }
}
