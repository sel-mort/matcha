<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ChatModel;
use App\Models\ProfileModel;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ChatController extends Controller
{
    public function get_matched_user(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        Session::start();
        $username = Session::unserialize("user")["username"];
        $profileModel = new ProfileModel();
        $res = $profileModel->get_user_profile($pdo, $username);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        if (!isset($res[0][0])) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $connected_user_id = $res[0][0];

        // Get user activated notifications
        $chatModel = new ChatModel();

        $matched_users_res = $chatModel->get_matched_users($pdo, $connected_user_id);
        if ($matched_users_res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        // Get users data
        $res = array();
        for ($i = 0; $i < count($matched_users_res); $i++) {
            $profile_res = $profileModel->get_user_profile_by_id($pdo, $matched_users_res[$i][0]);
            if ($profile_res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            array_push($res, $profile_res);
        }
        return $this->render($response, "chat.twig", array("data" => json_encode($res)));
    }

    public function matches(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();
        $error_count = 0;

        // Get the two usernames
        $username0 = $request->getParam('username0');
        $username1 = $request->getParam('username1');

        if (!Validator::check_array($username0) || !isset($username0) || empty($username0)
            || !Validator::check_array($username1) || !isset($username1) || empty($username1)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Get users ids
        $profileModel = new ProfileModel();
        $res = $profileModel->get_user_profile($pdo, $username0);
        if ($res === false || !isset($res[0][0])) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $user0_id = $res[0][0];

        $res = $profileModel->get_user_profile($pdo, $username1);
        if ($res === false || !isset($res[0][0])) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        $user1_id = $res[0][0];

        // Test if user0 matches user1
        $chatModel = new ChatModel();
        $res = $chatModel->matches($pdo, $user0_id, $user1_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        array_push($data, $res);

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
