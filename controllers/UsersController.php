<?php


namespace App\Controllers;

use App\Classes\Container;
use App\Classes\Controllable;
use App\Classes\View;
use App\Models\User;
use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UsersController implements Controllable
{
    public string $model = User::class;
    public string $table = 'users';

    public function getDatabase()
    {
        if (!isset($this->container)) {
            $this->container = new Container();
        }
        return $this->container->container->get('App\Classes\DatabaseConnectable');
    }

    public function index(ServerRequestInterface $request) : ResponseInterface
    {
        $result = [];
        $object = [];
        $objects_list = User::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                if (in_array($key, User::FILLABLE)) {
                    $method = 'related_' . $key . '_list';
                    if (method_exists($object_list, $method)) {
                        $results = '';
                        $relations = $object_list->$method();
                        foreach ($relations['relations'] as $keys => $values) {
                            $results .= (($values['amount']) ? '(' . $values['amount'] . ') ' : '') . $values['name'] . ', ';
                        }
                        $object[$key] = rtrim($results, ', ');
                    } elseif (method_exists($this->model, $key)) {
                        $object[$key] = $object_list->$key();
                    } else {
                        $object[$key] = $value;
                    }
                }
            }
            $result['items'][] = $object;
        }
        $result['model'] = $this->table;

        $body = View::getInstance()->render('index.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function display(ServerRequestInterface $request) : ResponseInterface
    {
        $result = [];
        $object = [];
        $objects_list = User::getInstance()->all($this->table)->get($this->model);
        foreach ($objects_list as $object_list) {
            $object['nr'] = $object_list->getNr();
            foreach ($object_list as $key => $value) {
                $object['nr'] = $object_list->getNr();
                if (in_array($key, User::LIST)) {
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

        $body = View::getInstance()->render('display.html.twig', ['results' => $result]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function read(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $result = User::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $methods = get_class_methods($result);
        foreach ($methods as $method) {
            if (strpos($method, 'related_') !== false) {
                $key = str_replace(['related_', '_list'], ['', ''], $method);
                if (in_array($key, User::FILLABLE)) {
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
            if (in_array($key, User::FILLABLE)) {
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

        $body = View::getInstance()->render('read.html.twig', ['result' => $results]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function create(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $request_uri = parse_url($_SERVER['REQUEST_URI']);
        $action = str_replace('create', 'save', rawurldecode($request_uri['path']));
        $formular['fields'] = $this->formFields();

        $formular['action'] = $action;
        if (isset($errors)) {
            $formular['errors'] = $errors;
        }

        $body = View::getInstance()->render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function edit(ServerRequestInterface $request, $errors = []) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $table_fields = $this->formFields();

        $object = User::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
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

        if (isset($errors)) {
            $formular['errors'] = $errors;
        }

        $body = View::getInstance()->render('formular.html.twig', ['formular' => $formular]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function save(ServerRequestInterface $request) : ResponseInterface
    {
        $data = $request->getParsedBody();
        $object = new User();
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
        $data = $request->getParsedBody();
        $object = User::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $object->fill($data);
        $object->setRelations($object);

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

    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $nr = $request->getAttribute('id');
        $object = User::getInstance()->all($this->table)->where(["nr = $nr"])->first($this->model);
        $stmt = $object->delete();
        return new Response\RedirectResponse('/admin/users/index');
    }

    public function formFields(): array
    {
        $sql = "SHOW FIELDS FROM `".User::TABLE."`";
        $table_fields = $this->getDatabase()->MultiSelect($sql);

        $fields = [];
        foreach ($table_fields as $key => $value) {
            if (in_array($value['Field'], User::FILLABLE)) {
                $fields[] = $value;
            }
        }

        if (in_array('password', User::FILLABLE)) {
            $fields[] = array("Field" => "confirm_password", "Type" => "varchar(255)", "Null" => "NO", "Key" => "", "Default" => null, "Extra" => "");
        }

        foreach (User::FILLABLE as $method_field) {
            if (method_exists(User::class, 'related_' . $method_field . '_list')) {
                $fields[] = array("Field" => $method_field, "Type" => "text", "Null" => "NO", "Key" => "", "Default" => null, "Extra" => "");
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
                $fields_list[$i]['Values'] = (new User)->$method();
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

    public function login(ServerRequestInterface $request) : ResponseInterface
    {
        $login = [];
        if (isset($request->getParsedBody()['login'])) {
            foreach ($request->getParsedBody() as $key => $value) {
                if ($key !== 'password') {
                    $login[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
            $login['password'] = $_POST['password'];
            (new User)->login($login);
        }
        $content = '<form method="post">
	<div class="form-group">
		<label for="exampleInputEmail1">Email address</label>
		<input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp">
		<small id="emailHelp" class="form-text text-muted">We\'ll never share your email with anyone else.</small>
	</div>
	<div class="form-group">
		<label for="exampleInputPassword1">Password</label>
		<input type="password" class="form-control" id="exampleInputPassword1" name="password">
	</div>
	<div class="form-group form-check">
		<input type="checkbox" class="form-check-input" id="keep_me_logged_in" name="keep_me_logged_in">
		<label class="form-check-label" for="keep_me_logged_in">Keep me logged in</label>
	</div>
	<button type="submit" class="btn btn-primary" name="login">Submit</button>
</form>';

        $body = View::getInstance()->render('blank.html.twig', ['content' => $content]);
        $response = new Response;

        $response->getBody()->write($body);
        return $response->withStatus(200);
    }

    public function logout()
    {
        setcookie('logged_in', false, time()-3600, '/');
        setcookie('cookie_lifetime', false, time()-3600, '/');
        header('Location:/');
    }
    public function checkLoginCookie(): bool
    {
        if (isset($_COOKIE['logged_in'])) {
            setcookie('cookie_lifetime', $_COOKIE['cookie_lifetime'], $_COOKIE['cookie_lifetime'], '/');
            setcookie('logged_in', true, $_COOKIE['cookie_lifetime'], '/');
            return true;
        } else {
            header('Location:/login');
        }
        return false;
    }
}
