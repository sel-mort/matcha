<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Crypt\Crypt;
use App\Mail\Mail;
use App\Models\ResetModel;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ResetController extends Controller
{
    public function reset(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        $username = $request->getParam('username');
        $email = $request->getParam('email');

        // Check params
        if (!Validator::check_array($username) || !Validator::check_array($email)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $username = trim($username);
        $email = trim($email);

        if (!Validator::check_lenght($username, 5, 100)) {
            array_push($error, "Invalid Username lenght ! (>5)");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if (!Validator::check_email($email)) {
            array_push($error, "Invalid Email !");
            $error_count++;
            $status = Status::BAD_REQUEST;
        }

        if ($error_count === 0) {
            $resetModel = new ResetModel();
            $username_count = $resetModel->username_count($pdo, $username, $email);
            if ($username_count === 1) {
                $new_password = "Rpw0_" . uniqid();

                if ($resetModel->reset($pdo, $username, Crypt::crypt_str($new_password)) === true) {
                    Mail::send($email, "Account reset", "Your new password is " . $new_password);
                    array_push($alert, "Done ! Check your Email !");
                    $status = Status::OK;
                } else {
                    array_push($error, "Something went wrong !");
                    $status = Status::BAD_REQUEST;
                }

            } else if ($username_count === 0) {
                array_push($error, "Account not found !");
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
