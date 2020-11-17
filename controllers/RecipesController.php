<?php
namespace App\Controllers;

use App\Classes\Controllable;
use App\Classes\PDO\Database;
use App\Classes\View;
use App\Models\Recipe;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;

class RecipesController implements Controllable
{
    public string $model = Recipe::class;
    public string $table = 'recipes';

    /**
     * Display public list of the recipes.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $result = [];
        $object = [];
        $objects_list = Recipe::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                $object['nr'] = $object_list->getNr();
                if (in_array($key, Recipe::LIST)) {
                    if (method_exists($this->model, $key)) {
                        $object[$key] = $object_list->$key();
                    } else {
                        $object[$key] = $value;
                    }
                }
            }
            $methods = get_class_methods($object_list);
            foreach ($methods as $method) {
                if (strpos($method, 'related_') !== false) {
                    $key = str_replace(['related_', '_list'], ['', ''], $method);
                    if (in_array($key, Recipe::FILLABLE)) {
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
            $result['items'][] = $object;
        }
        $result['model'] = $this->table;

        $body     = View::getInstance()->render('index.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }
    /**
     * Display admin list of the recipes.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function display(ServerRequestInterface $request) : ResponseInterface
    {
        $result = [];
        $object = [];
        $objects_list = Recipe::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                $object['nr'] = $object_list->getNr();
                if (in_array($key, Recipe::LIST)) $object[$key] = $value;
            }
            $methods = get_class_methods($object_list);
            foreach ($methods as $method) {
                if (strpos($method, 'related_') !== false) {
                    $key = str_replace(['related_', '_list'], ['', ''], $method);
                    if (in_array($key, Recipe::FILLABLE)) {
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
            $result['items'][] = $object;
        }
        $result['model'] = $this->table;

        $body     = View::getInstance()->render('display.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    /**
     * Display public/admin read page for recipe.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function read(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('title') ?? $request->getAttribute('id');
        $search_key = (intval($nr) === 0) ? 'schnecke' : 'nr';
        $result = Recipe::getInstance()->all($this->table)->where(["$search_key = $nr"])->first($this->model);
        $methods = get_class_methods($result);
        foreach ($methods as $method) {
            if (strpos($method, 'related_') !== false) {
                $key = str_replace(['related_', '_list'], ['', ''], $method);
                if (in_array($key, Recipe::FILLABLE)) {
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
            if (in_array($key, Recipe::FILLABLE)) {
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

        $body     = View::getInstance()->render('read.html.twig', ['result' => $results]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    /**
     * Display create form for recipes.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array $errors
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $request_uri = parse_url($_SERVER['REQUEST_URI']);
        $action = str_replace('create', 'save', rawurldecode($request_uri['path']));
        $formular['fields'] = $this->formFields();

        $formular['action'] = $action;
        if (isset($errors)) $formular['errors'] = $errors;

        $body     = View::getInstance()->render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    /**
     * Get list of recipes available fields
     *
     * @return array
     */
    public function formFields(): array
    {
        $sql = "SHOW FIELDS FROM `".Recipe::TABLE."`";
        $table_fields = Database::getInstance()->MultiSelect($sql);

        $fields = [];
        foreach ($table_fields as $key => $value) {
            if (in_array($value['Field'], Recipe::FILLABLE)) $fields[] = $value;
        }

        if (in_array('password', Recipe::FILLABLE)) {
            $fields[] = array("Field" => "confirm_password", "Type" => "varchar(255)", "Null" => "NO", "Key" => "", "Default" => NULL, "Extra" => "");
        }

        foreach (Recipe::FILLABLE as $method_field) {
            if (method_exists(Recipe::class, 'related_' . $method_field . '_list')) {
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

    /**
     * Display edit form for choosen recipe.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param array $errors
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function edit(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $table_fields = $this->formFields();

        $object = Recipe::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
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
        $formular['action'] = '/admin/' . $this->table . '/update/' . $nr;
        $formular['inhalt'] = (array)$object;

        if (isset($errors)) $formular['errors'] = $errors;

        $body = View::getInstance()->render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    /**
     * Save a recipe
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function save(ServerRequestInterface $request) : ResponseInterface
    {
        $data = $request->getParsedBody();
        $object = new Recipe();
        $object->fill($data);

        foreach ($_FILES as $key => $value) {
            if ($_FILES[$key]["size"] > 0) {
                $filePath = $this->uploadFile($key, $object->name);
                if ($filePath !== false) $object->$key = $filePath;
            }
        }

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

    /**
     * Updates a recipe.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $data = $request->getParsedBody();
        $object = Recipe::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $object->fill($data);
        $object->setRelations($object);

        foreach ($_FILES as $key => $value) {
            if ($_FILES[$key]["size"] > 0) {
                $file_path = $this->uploadFile($key, $object->name);
                if ($file_path !== false) $object->$key = $file_path;
            }
        }
        if ($object->validate('update')) {
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

    /**
     * Deletes a recipe.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $object = Recipe::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $stmt = $object->delete();
        return new RedirectResponse('/admin/recipes/index');
    }

    /**
     * Image upload method.
     *
     * @param string $key
     *
     * @param string $object_name
     *
     * @return string
     */
    public function uploadFile($key, $object_name)
    {
        $target_dir = "img/uploads/";
        $target_file = $target_dir . basename($_FILES[$key]["name"]);
        $uploadOk = 1;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["bild"]["tmp_name"]);
            if ($check !== false) $uploadOk = 1; else $uploadOk = 0;
        }

// Allow certain file formats
        if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"
            && $image_file_type != "gif") {
            echo "$image_file_type Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

// Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
        } else {
            $object_name = str_replace(' ', '-', strtolower($object_name));
            $object_name = str_replace(['Ä', 'Ö', 'Ü', 'ä', 'ü', 'ö'], ['AE', 'OE', 'UE', 'ae', 'ue', 'oe'], $object_name);
            $object_name = preg_replace('/[^A-Za-z0-9\-]/', '', $object_name);
            $target_file = $target_dir . $object_name . '.' . $image_file_type;
            if (!move_uploaded_file($_FILES["bild"]["tmp_name"], $target_file)) {
                return "Sorry, there was an error uploading your file.";
            } else {
                return '/' . $target_file;
            }
        }
        return false;
    }

    /**
     * Advanced search for recipes.
     *
     * @return string
     */
    public function advancedSearch(ServerRequestInterface $request) : ResponseInterface
    {
        $content = [];
        $search = [];
        if (isset($request->getParsedBody()['search'])) {
            foreach ($request->getParsedBody() as $key => $value) {
                $search[$key] = $value;
            }

            $sql = "select $this->table.* from $this->table 
 join ingredients_recipes on $this->table.nr = ingredients_recipes.recipe_nr 
 join ingredients on ingredients_recipes.ingredient_nr = ingredients.nr 
where ($this->table.name like ? or vorbereitung_anweisungen like ? 
 or amount like ? or ingredients.name like ?)";
            if (isset($search['portionsnummern'])) $sql .= " and (portionsnummern = ?)";
            if (isset($search['vorbereitungszeit'])) $sql .= " and (vorbereitungszeit = ?)";
            if (isset($search['vorbereitung_schwierigkeit'])) $sql .= " and (vorbereitung_schwierigkeit = ?)";
            if (isset($search['ingredient'])) $sql .= " and ingredients.name = ?";
            $sql .= " group by $this->table.name";
            $params = [];
            $params[] = "%".$search['keyword']."%";
            $params[] = "%".$search['keyword']."%";
            $params[] = "%".$search['keyword']."%";
            $params[] = "%".$search['keyword']."%";
            if (isset($search['portionsnummern'])) $params[] = $search['portionsnummern'];
            if (isset($search['vorbereitungszeit'])) $params[] = $search['vorbereitungszeit'];
            if (isset($search['vorbereitung_schwierigkeit'])) $params[] = $search['vorbereitung_schwierigkeit'];
            if (isset($search['ingredient'])) $params[] = $search['ingredient'];

            $content = Database::getInstance()->MultiSelect($sql,$params);
        }

        $formular = [];
        $fields = ['portionsnummern', 'vorbereitungszeit', 'vorbereitung_schwierigkeit'];
        foreach ($fields as $field) {
            $sql = "select distinct `$field` from `$this->table`";
            $formular[$field] = Database::getInstance()->MultiSelect($sql);
        }
        $sql = 'select `name` from `ingredients` where `nr` in (select `ingredient_nr` from ingredients_recipes)';
        $results = Database::getInstance()->MultiSelect($sql);
        foreach ($results as $result) {
            $formular['zutaten'][] = $result['name'];
        }

        $body = View::getInstance()->render('advanced-search.html.twig',
            ['content' => $content, 'formular' => $formular, 'search' => $search]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    /**
     * Recipes search.
     *
     * @return string
     */
    public function search(ServerRequestInterface $request) : ResponseInterface
    {
        $content = [];
        if (isset($request->getQueryParams()['search'])) {
            $search = "%".$request->getQueryParams()['search']."%";
            $sql = "select `$this->table`.* from `$this->table` join `ingredients_recipes` on `$this->table`.`nr` = `ingredients_recipes`.`recipe_nr` join `ingredients` on `ingredients_recipes`.`ingredient_nr` = `ingredients`.`Nr` where `$this->table`.`name` like ? or `vorbereitung_anweisungen` like ? or `amount` like ? or `ingredients`.`name` like ? group by `$this->table`.`name`";
            $content = Database::getInstance()->MultiSelect($sql,[$search, $search, $search, $search]);
        }
        $body = View::getInstance()->render('search.html.twig', ['content' => $content]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }
}
