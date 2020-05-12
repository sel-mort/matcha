<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Crypt\Crypt;
use App\Mail\Mail;
use App\Models\RegisterModel;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class RegisterController extends Controller
{
    public function register(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        $username = $request->getParam('username');
        $firstname = $request->getParam('firstname');
        $lastname = $request->getParam('lastname');
        $password = $request->getParam('password');
        $email = $request->getParam('email');

        // Check params
        if (!Validator::check_array($username) || !Validator::check_array($password) || !Validator::check_array($firstname) || !Validator::check_array($lastname) || !Validator::check_array($email)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $username = trim($username);
        $firstname = trim($firstname);
        $lastname = trim($lastname);
        $password = trim($password);
        $email = trim($email);
        $verification_code = uniqid();

        if (!Validator::check_lenght($username, 5, 100)) {
            array_push($error, "Invalid Username lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_username($username)) {
            array_push($error, "Invalid Username ! (alphanumeric chars only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_not_empty($firstname) || !Validator::check_lenght($firstname, 1, 100)) {
            array_push($error, "Invalid First name lenght !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_alpha($firstname)) {
            array_push($error, "Invalid First name ! (alpha characters only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_not_empty($lastname) || !Validator::check_lenght($lastname, 1, 100)) {
            array_push($error, "Invalid Last name lenght !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_alpha($lastname)) {
            array_push($error, "Invalid Last name ! (alpha characters only)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_password($password)) {
            array_push($error, "Invalid Password !  (>5, 1 upper case, 1 lower case, 1 number, 1 special char)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_email($email)) {
            array_push($error, "Invalid Email !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if ($error_count === 0) {
            $registerModel = new RegisterModel();
            $username_count = $registerModel->username_count($pdo, $username);
            if ($username_count === 0) {
                $email_count = $registerModel->email_count($pdo, $email);
                if ($email_count === 0) {
                    if ($registerModel->register($pdo, $username, $firstname, $lastname, Crypt::crypt_str($password), $email, $verification_code)) {
                        $verification_link = "http://localhost/activate?verification_code=" . $verification_code;
                        Mail::send($email, "Account activation", "<a href=" . $verification_link . ">Click to activate your account !</a>");
                        array_push($alert, "Registration successful !");
                        array_push($alert, "A verification link has been sent to your email account !");
                        Status::CREATED;
                    } else {
                        array_push($error, "Something gone wrong !");
                        Status::BAD_REQUEST;
                    }
                } else if ($email_count === 1) {
                    array_push($error, "Email already used !");
                    Status::BAD_REQUEST;
                } else {
                    array_push($error, "Something gone wrong !");
                    Status::BAD_REQUEST;
                }
            } else if ($username_count === 1) {
                array_push($error, "Username already used !");
                Status::BAD_REQUEST;
            } else {
                array_push($error, "Something gone wrong !");
                Status::BAD_REQUEST;
            }
        }

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
