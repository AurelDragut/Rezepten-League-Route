<?php

namespace App\Models;

use App\Classes\Modelable;

class Link extends Model implements Modelable
{
    public const TABLE = 'links';
    public const FILLABLE = ['name', 'link', 'parent', 'order_nr'];
    public const LIST = ['name', 'link', 'parent', 'order_nr'];
    public const HIDDEN = null;
    public static object $_instance;
    protected int $nr;

    public function read_parent_list():array
    {
        $query = 'select * from links where parent = ?';
        return $this->getDatabase()->MultiSelect($query, [0], get_class($this));
    }

    public function parent(): string
    {
        $parent = $this->belongsTo(get_class($this), self::TABLE);
        return $parent->name ?? 'N/A';
    }

    public function rules($method): array
    {
        return [
            'name' => [self::RULE_REQUIRED],
            'link' => [self::RULE_REQUIRED],
            'order_nr' => [self::RULE_REQUIRED],
        ];
    }

    public static function getInstance ()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function getMenu():array
    {
        $query = "SELECT * from `links` order by `parent`";
        $menu_items = $this->getDatabase()->MultiSelect($query);
        $items = [];
        foreach ($menu_items as $menu_item) {
            $parent = $menu_item['parent'];
            $nr = $menu_item['nr'];
            if ($parent == 0) $items[(int)$nr] = $menu_item; else $items[(int)$parent]['children'][] = $menu_item;
        }
        return $items;
    }

    public function fill($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, Link::FILLABLE)) {
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
                    $keys[] = $key;
                    $values[] = $value;
                }
            }
        }
        $values = array_map(function ($m) {
            return '\'' . $m . '\'';
        }, $values);
        $sql = "INSERT INTO " . self::TABLE . " (" . implode(', ', $keys) . ") VALUE (" . implode(', ', $values) . ")";
        return $this->getDatabase()->Insert($sql);
    }

    public function update():int
    {
        $keys = [];
        $values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, Link::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    unset($this->$key);
                } else {
                    $keys[] = $key;
                    $values[] = $value;
                }
            }
        }
        $query_values = array_combine($keys, $values);
        $key_values = [];
        foreach ($query_values as $key => $value) {
            $key_values[] = "`$key` = '$value'";
        }
        $sql = "UPDATE links SET " . implode(', ', $key_values) . " WHERE `nr` = '$this->nr' ";
        $this->getDatabase()->Update($sql);
        return $this->nr;
    }

    public function delete()
    {
        $query = "DELETE FROM links WHERE nr= ?";
        $this->getDatabase()->Remove($query, [$this->nr]);
        header('Location:/admin/links/index');
    }

}
