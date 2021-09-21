<?php

namespace App\Controllers\Apis;

use stdClass;

class UserController extends \Core\BaseController
{

    private $userModel;

    public function __construct()
    {
        // header('Access-Control-Allow-Origin: *');
        // header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        // header('Access-Control-Allow-Headers: X-Requested-With');
        //header('Content-Type: application/json');
        // header('Content-Type: x-www-form-urlencoded');

        $this->userModel = new \App\Models\UserModel();
    }

    function index(...$req)
    {
        switch ($_SERVER['REQUEST_METHOD']) {
                /**
             * user
             * user/page/:id
             * user/:id
             */
            case "GET":
                $req = array_filter($req, function ($val) {
                    return !empty($val);
                });
                $paramCount = count($req);

                switch ($paramCount) {
                    case 0:
                        $this->all();
                        break;
                    case 1:
                        $this->userById($req[0]);
                        break;
                    case 2:
                        if ((strtolower($req[0]) === 'page') && is_numeric($req[1]))
                            $this->all($req[1]);
                        else
                            $this->badRequest('Request is invalid');
                        break;
                    default:
                        $this->badRequest('Request is not found');
                        break;
                };
                break;

            case "POST":
                $this->store();
                break;

            case "PUT":
                if (count($req) == 1 && is_numeric($req[0]))
                    $this->update($req[0]);
                else
                    $this->badRequest('Request PUT is invalid');
                break;

            case "DELETE":
                if (count($req) == 1 && is_numeric($req[0]))
                    $this->delete($req[0]);
                else
                    $this->badRequest('Request DELETE is invalid');
                break;
            default:
                $this->badRequest("Request Method" . $_SERVER['REQUEST_METHOD'] . " is not exist");
                break;
        };
    }

    private

    function badRequest($message)
    {
        $output = new stdClass();
        $output->status = 400;
        $output->message = $message ?? 'method ' . $_SERVER['REQUEST_METHOD'] . ' not exist';
        echo json_encode($output);
        die();
    }

    function userById($id)
    {
        if (!is_numeric($id)) {
            $this->badRequest("Request id $id is invalid");
            die();
        }

        $output = new stdClass();
        $output->statsus = 200;
        $output->message = 'successfully';
        $output->data = $this->userModel->findById($id);
        echo json_encode($output);
    }

    function store()
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : ''
        ];

        $this->userModel->store($data);
        $output = new stdClass();
        $output->statsus = 200;
        $output->message = 'Added user successfully';
        $output->data = $data;
        echo json_encode($output);
    }

    function update($id)
    {

        if (!$this->userModel->findById($id)) {
            $this->badRequest("Data user with id $id not found");
            die();
        }

        # get data with method PUT
        parse_str(file_get_contents('php://input'), $data);

        $this->userModel->save($id, $data);
        $output = new stdClass();
        $output->statsus = 200;
        $output->message = 'Updated successfully';
        $output->data = $data;
        echo json_encode($output);
    }

    function delete($id)
    {

        if (!$this->userModel->findById($id)) {
            $this->badRequest("Data user with id $id not found");
            die();
        }

        $this->userModel->destroy($id);

        $output = new stdClass();
        $output->statsus = 200;
        $output->message = 'Deleted successfully';
        $output->userId = $id;
        echo json_encode($output);
    }

    function all($page = 1)
    {
        $output = new stdClass();

        if ($page < 1) $page = 1;
        $perPage = 2;
        $total = $this->userModel->getTotal();
        $pagesTotal = ceil($total / $perPage);
        $offset = $perPage * ($page - 1);

        $output->statsus = 200;
        $output->message = 'successfully';
        $output->pagesTotal = ceil($total / $perPage);
        $output->pageIndex = $page;
        $output->usersCount = $total;

        if ($page > $pagesTotal) {
            $output->data = [];
            $output->message = 'Data not found';
        } else {
            $output->data =  $this->userModel->getAll(
                [
                    'limit' => $offset . ", " . $perPage,
                    'order by' => 'name asc',
                ]
            );
        }
        echo json_encode($output);
    }
}
