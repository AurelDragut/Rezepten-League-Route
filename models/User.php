<?php
namespace App\Models;

use App\Classes\Modelable;
use Laminas\Diactoros\Response\RedirectResponse;

class User extends Model implements Modelable
{
    const TABLE = 'users';
    public const FILLABLE = ['name','username','email','password','confirm_password'];
    public const LIST = ['name','username','email','password','confirm_password'];
    public static object $_instance;
    protected int $nr;

    public function rules($method): array
    {
        $rules = [
                'name' => [self::RULE_REQUIRED],
                'username' => [self::RULE_REQUIRED],
                'email' => [self::RULE_REQUIRED, self::RULE_EMAIL]
            ];
        if ($method == 'create') {
            $rules['password'] = [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]];
            $rules['confirm_password'] = [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']];
        }
        return $rules;
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function fill($data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, self::FILLABLE)) {
                $this->$key = $value;
            }
        }
    }

    public function save():int
    {
        $keys = [];
        $values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, User::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    continue;
                } else {
                    if ($key == 'confirm_password') {
                        continue;
                    } else {
                        $keys[] = $key;
                        if ($key == 'password') {
                            $value = password_hash($value, PASSWORD_BCRYPT);
                        }
                        $values[] = $value;
                    }
                }
            }
        }
        $values = array_map(function ($m) {
            return '\'' . $m . '\'';
        }, $values);
        $sql = "INSERT INTO users (" . implode(', ', $keys) . ") VALUE (" . implode(', ', $values) . ")";
        return $this->getDatabase()->Insert($sql);
    }

    public function update():int
    {
        $keys = [];
        $values = [];
        foreach ($this as $key => $value) {
            if (in_array($key, User::FILLABLE)) {
                if (method_exists($this, 'related_' . $key . '_list')) {
                    unset($this->$key);
                } else {
                    if ($key == 'confirm_password') {
                        continue;
                    } else {
                        $keys[] = $key;
                        if ($key == 'password') {
                            $value = password_hash($value, PASSWORD_BCRYPT);
                        }
                        $values[] = $value;
                    }
                }
            }
        }
        $query_values = array_combine($keys, $values);
        $key_values = [];
        foreach ($query_values as $key => $value) {
            $key_values[] = "`$key` = '$value'";
        }
        $sql = "UPDATE users SET " . implode(', ', $key_values) . " WHERE `nr` = '$this->nr' ";
        $this->getDatabase()->Update($sql);
        return $this->nr;
    }

    public function delete()
    {
        $query = "DELETE FROM users WHERE nr= ?";
        $this->getDatabase()->Remove($query, [$this->nr]);
        header('Location:/admin/users/index');
    }

    public function login($login)
    {
        $query = "select * from `users` where `email` = ?";
        $user = $this->getDatabase()->Select($query, [$login['email']], self::class);
        if (password_verify($login['password'], $user->password)) {
            if (isset($login['keep_me_logged_in'])) {
                $cookie_lifetime = time()+3600*24*30*12*100;
            } else {
                $cookie_lifetime = time()+3600;
            }
            setcookie('logged_in', true, $cookie_lifetime, '/');
            setcookie('cookie_lifetime', $cookie_lifetime, $cookie_lifetime, '/');
            header('Location:/');
            die();
        } else {
            return new RedirectResponse('/login');
        }
    }
}
