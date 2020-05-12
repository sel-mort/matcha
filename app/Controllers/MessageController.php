<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ChatModel;
use App\Models\MessageModel;
use App\Models\ProfileModel;
use App\Models\UserModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class MessageController extends Controller
{
    public function get_message(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        // Get the matched user from client
        $user_id = $request->getParam('user_id');

        if (!Validator::check_array($user_id)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Get the connected user
        Session::start();
        $username = Session::unserialize("user")["username"];
        $profileModel = new ProfileModel();
        $profile_res = $profileModel->get_user_profile($pdo, $username);
        if ($profile_res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $connected_user_id = $profile_res[0][0];

        // Test if users are matched
        $chatModel = new ChatModel();
        $res = $chatModel->matches($pdo, $connected_user_id, $user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else if ($res === 0) {
            array_push($error, "User not matched !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else {
            $messageModel = new MessageModel();
            $res = $messageModel->get_user_message($pdo, $connected_user_id, $user_id);
            if ($res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
        }

        array_push($data, $res);
        array_push($data, $profile_res);

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }

    public function set_message(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        // Get the matched user from client
        $user_id = $request->getParam('user_id');
        $message = $request->getParam('message');

        if (!Validator::check_array($user_id) || !Validator::check_array($message) || !Validator::check_lenght($message, 1, 1000)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Get the connected user
        Session::start();
        $username = Session::unserialize("user")["username"];
        $profileModel = new ProfileModel();
        $profile_res = $profileModel->get_user_profile($pdo, $username);
        if ($profile_res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $connected_user_id = $profile_res[0][0];

        // Test if users are matched
        $chatModel = new ChatModel();
        $res = $chatModel->matches($pdo, $connected_user_id, $user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else if ((int) $res === 0) {
            array_push($error, "User not matched !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else {
            $messageModel = new MessageModel();
            $res = $messageModel->set_user_message($pdo, $connected_user_id, $user_id, $message);
            if ($res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $userModel = new UserModel();
            $notification_res = $userModel->add_notification($pdo, $connected_user_id, $user_id, "message");
            if ($notification_res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
        }

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
