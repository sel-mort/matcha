<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Crypt\Crypt;
use App\Models\LoginModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LoginController extends Controller
{
    public function login(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $latitude = $request->getParam('latitude');
        $longitude = $request->getParam('longitude');

        // Check coordinates
        if (!Validator::check_latitude($latitude) || !Validator::check_longitude($longitude)) {
            array_push($error, "Invalid coordinates !)");
            $error_count++;
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Check params (array input)
        if (!Validator::check_array($username) || !Validator::check_array($password) || !Validator::check_array($latitude) || !Validator::check_array($longitude)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $username = trim($username);

        if (!Validator::check_lenght($username, 5, 100)) {
            array_push($error, "Invalid Username lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_password($password)) {
            array_push($error, "Invalid Password ! (>5, 1 upper case, 1 lower case, 1 special char)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if ($error_count === 0) {
            $loginModel = new LoginModel();
            $login_res = $loginModel->login($pdo, $username, Crypt::crypt_str($password));
            if ($login_res === 1) {
                $verification_res = $loginModel->check_verification($pdo, $username, Crypt::crypt_str($password));
                if ($verification_res === 1) {
                    $position_res = $loginModel->update_position($pdo, $username, $latitude, $longitude);
                    $connection_res = $loginModel->update_connection_status($pdo, $username);
                    if ($position_res === false || $connection_res === false) {
                        array_push($error, "Something went wrong !");
                        $status = Status::BAD_REQUEST;
                    } else {
                        Session::start();
                        Session::serialize("user", array("username" => $username, "password" => $password));
                        array_push($alert, "Login successful !");
                        $status = Status::OK;
                    }
                } else if ($verification_res === 0) {
                    array_push($error, "Account not activated !");
                    $status = Status::BAD_REQUEST;
                } else {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;
                }

            } else if ($login_res === 0) {
                array_push($error, "Invalid Username or Password !");
                $status = Status::BAD_REQUEST;
            } else {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;
            }
        }

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
