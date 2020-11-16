<?php
namespace App\Controllers;

use App\Classes\Controllable;
use App\Classes\PDO\Database;
use App\Classes\View;
use App\Models\Ingredient;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class IngredientsController implements Controllable
{
    public string $model = Ingredient::class;
    public string $table = 'ingredients';

    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $_SESSION['referer'] = $_SERVER['REQUEST_URI'];
        UsersController::checkLoginCookie();
        $result = [];
        $object = [];
        $objects_list = Ingredient::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                $object['nr'] = $object_list->getNr();
                if (in_array($key, Ingredient::FILLABLE)) {
                    if (method_exists($this->model, $key)) {
                        $object[$key] = $object_list->$key();
                    } else {
                        $object[$key] = $value;
                    }
                }
                $methods = get_class_methods($object_list);
                foreach ($methods as $method) {
                    if (strpos($method, 'related_') !== false) {
                        $key = str_replace(['related_', '_list'], ['', ''], $method);
                        if (in_array($key, Ingredient::FILLABLE)) {
                            $results_list = '';
                            $method = $object_list->$method();
                            foreach ($method['relations'] as $relation) {
                                $results_list .= $relation['amount'] . (($relation['amount']) ? ' - ' : '') . $relation['name'] . ', ';
                            }
                            $results_list = rtrim($results_list, ', ');
                            $object[$key] = $results_list;
                        }
                    }
                }
            }
            $result['items'][] = $object;
        }
        $result['model'] = $this->table;

        $body = View::render('index.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function display(ServerRequestInterface $request) : ResponseInterface
    {
        $result = [];
        $object = [];
        $objects_list = Ingredient::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                if (in_array($key, Ingredient::LIST)) {
                    $method = 'related_' . $key . '_list';
                    if (method_exists($this->model, $method)) {
                        $results = '';
                        $relations = $object_list->$method();
                        foreach ($relations['relations'] as $relation) {
                            $results .= $relation['amount'] . ' ' . $relation['name'] . ', ';
                        }
                        $object[$key] = rtrim($results, ', ');
                    } else {
                        $object[$key] = $value;
                    }
                }
            }
            $result['items'][] = $object;
        }
        $result['model'] = $this->table;

        $body = View::render('display.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function read(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $result = Ingredient::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $methods = get_class_methods($result);
        foreach ($methods as $method) {
            if (strpos($method, 'related_') !== false) {
                $key = str_replace(['related_', '_list'], ['', ''], $method);
                if (in_array($key, Ingredient::FILLABLE)) {
                    $results_list = '';
                    $method = $result->$method();
                    foreach ($method['relations'] as $relation) {
                        $results_list .= $relation['amount'] . (($relation['amount']) ? ' - ' : '') . $relation['name'] . ', ';
                    }
                    $results_list = rtrim($results_list, ', ');
                    $result->$key = $results_list;
                }
            }
        }

        $results = [];
        foreach ($result as $key => $value) {
            if (in_array($key, Ingredient::FILLABLE)) {
                $method = 'related_' . $key . '_list';
                if (method_exists($result, $method)) {
                    $resultList = '';
                    $relations = $result->$method();
                    foreach ($relations['relations'] as $keys => $values) {
                        $resultList .= $values['amount'] . ' ' . $values['name'] . ', ';
                    }
                    $results['items'][$key] = rtrim($resultList, ', ');
                } else {
                    $results['items'][$key] = $value;
                }
            }
        }

        if (count((array)$results) == 0) {
            http_response_code('404');
            die();
        }

        $body = View::render('read.html.twig', ['result' => $results]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function create(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $_SESSION['referer'] = $_SERVER['REQUEST_URI'];
        UsersController::checkLoginCookie();
        $request_uri = parse_url($_SERVER['REQUEST_URI']);
        $action = str_replace('create', 'save', rawurldecode($request_uri['path']));
        $formular['fields'] = $this->formFields();

        $formular['action'] = $action;
        if (isset($errors)) $formular['errors'] = $errors;

        $body = View::render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function formFields(): array
    {
        $sql = "SHOW FIELDS FROM `".Ingredient::TABLE."`";
        $table_fields = Database::getInstance()->MultiSelect($sql);

        $fields = [];
        foreach ($table_fields as $key => $value) {
            if (in_array($value['Field'], Ingredient::FILLABLE)) $fields[] = $value;
        }

        if (in_array('password', Ingredient::FILLABLE)) {
            $fields[] = array("Field" => "confirm_password", "Type" => "varchar(255)", "Null" => "NO", "Key" => "", "Default" => NULL, "Extra" => "");
        }

        foreach (Ingredient::FILLABLE as $method_field) {
            if (method_exists(Ingredient::class, 'related_' . $method_field . '_list') && !in_array($method_field, Ingredient::HIDDEN)) {
                $fields[] = array("Field" => $method_field, "Type" => "text", "Null" => "NO", "Key" => "", "Default" => NULL, "Extra" => "");
            }
        }

        $fields_list = [];
        for ($i = 0; $i < count($fields); $i++) {
            $fields_list[$i]['Field'] = $fields[$i]['Field'];
            if ($fields[$i]['Field'] == 'bild') {
                $fields_list[$i]['Type'] = 'file';
            } elseif (in_array($fields[$i]['Field'], ['password', 'confirm_password'])) {
                $fields_list[$i]['Type'] = 'password';
            } elseif (method_exists($this->model, 'read_' . $fields[$i]['Field'] . '_list')) {
                $fields_list[$i]['Type'] = 'select';
                $method = "read_" . $fields[$i]['Field'] . "_list";
                $fields_list[$i]['Values'] = $this->model->$method();
            } elseif (isset($this->model->hidden)) {
                if (in_array($fields[$i]['Field'], $this->model->hidden)) {
                    unset($fields_list[$i]);
                }
            } else {
                switch ($fields[$i]['Type']) {
                    case 'text':
                        $fields_list[$i]['Type'] = 'textarea';
                        break;
                    default:
                        $fields_list[$i]['Type'] = 'text';
                }
            }
        }
        return $fields_list;
    }

    public function edit(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $_SESSION['referer'] = $_SERVER['REQUEST_URI'];
        UsersController::checkLoginCookie();
        $nr = $request->getAttribute('id');
        $table_fields = $this->formFields();

        $object = Ingredient::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $methods = get_class_methods($object);
        foreach ($methods as $method) {
            if (strpos($method, 'related_') !== false) {
                $key = str_replace(['related_', '_list'], ['', ''], $method);
                $results_list = '';
                $method = $object->$method();
                foreach ($method['relations'] as $result) {
                    $results_list .= $result['amount'] . (($result['amount']) ? ' - ' : '') . $result['name'] . ', ';
                }
                $results_list = rtrim($results_list, ', ');
                $object->$key = $results_list;
            }
        }

        $formular['fields'] = $table_fields;
        $formular['action'] = '/admin/'.$this->table.'/update/' . $nr;
        $formular['inhalt'] = (array)$object;

        if (isset($errors)) $formular['errors'] = $errors;

        $body = View::render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function save(ServerRequestInterface $request) : ResponseInterface
    {
        $data = $this->sanitizeValues();
        $object = new Ingredient();
        $object->fill($data);

        if ($object->validate()) {
            $recipe_nr = $object->save();
            if ($recipe_nr > 0) {
                $object->SetNr($recipe_nr);
                $object->setRelations();
                $object->insertRelationsValues();
                return $this->index($request);
            } else {
                return $this->create($request, $object->errors);
            }
        } else {
            return $this->create($request, $object->errors);
        }
    }

    public function update(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $data = $this->sanitizeValues();
        $object = Ingredient::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $object->fill($data);
        $object->setRelations($object);

        if ($object->validate()) {
            $recipe_nr = $object->update();
            if ($recipe_nr > 0) {
                $object->updateRelationsValues();
                return $this->index($request);
            } else {
                return $this->edit($request, $object->errors);
            }
        } else {
            return $this->edit($request, $object->errors);
        }
    }

    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $_SESSION['referer'] = $_SERVER['REQUEST_URI'];
        UsersController::checkLoginCookie();
        $nr = $request->getAttribute('id');
        $object = Ingredient::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $stmt = $object->delete();
        return new Response\RedirectResponse('/admin/ingredients/index');
    }

    public function sanitizeValues():array
    {
        $relations = [];
        $object = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, Ingredient::FILLABLE)) {
                if (method_exists($this->model, 'related_' . $key . '_list')) {
                    $relations[$key] = $value;
                    $object[$key] = $value;
                }
                if (in_array($key, array('password', 'confirm_password'))) $object[$key] = $value;
                else
                    $object[$key] = $value;
            }
        }
        if (in_array('schnecke', Ingredient::FILLABLE)) {
            $object['schnecke'] = str_replace(' ', '-', strtolower($object['name']));
            $object['schnecke'] = str_replace(['Ä', 'Ö', 'Ü', 'ä', 'ü', 'ö'], ['AE', 'OE', 'UE', 'ae', 'ue', 'oe'], $object['schnecke']);
            $object['schnecke'] = preg_replace('/[^A-Za-z0-9\-]/', '', $object['schnecke']);
        }
        return $object;
    }
}
