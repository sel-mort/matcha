<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ProfileModel;
use App\Models\RegisterModel;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use \DateTime;

class StatusController extends Controller
{
    public function get_status(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        $username = $request->getParam("username");
        $client_connection_status = $request->getParam("client_connection_status");

        if (empty($client_connection_status) || !Validator::check_array($client_connection_status)) {
            $client_connection_status = null;
        }

        if (!isset($username) || !Validator::check_array($username)) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $profileModel = new ProfileModel();
        $registerModel = new RegisterModel();

        // Test if user exists
        $res = $registerModel->username_count($pdo, $username);
        if ($res == false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }
        // Get user data
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

        // Extract user data
        $id = $res[0][0];
        $password = $res[0][2];
        $email = $res[0][3];
        $firstname = $res[0][4];
        $lastname = $res[0][5];
        $age = $res[0][6];
        $date = new DateTime($age);
        $now = new DateTime();
        $age = $date->diff($now)->format("%y");

        $gender = $res[0][7];
        $orientation = $res[0][8];
        $bio = $res[0][11];
        $score = $res[0][12];
        $longitude = $res[0][16];
        $latitude = $res[0][15];
        $avatar = $res[0][17];
        $online = $res[0][14];
        $last_connection = $res[0][13];

        set_time_limit(0);
        ini_set('max_execution_time', 0);

        while (true) {
            $server_connection_status = $profileModel->get_user_profile($pdo, $username)[0][14];
            if ($client_connection_status === null || ($server_connection_status !== $client_connection_status)) {
                $client_connection_status = $server_connection_status;
                $result = array(
                    'serverConnectionStatus' => $server_connection_status,
                    'clientConnectionStatus' => $client_connection_status,
                    'lastConnection' => $last_connection
                );
                $json = json_encode($result);
                echo $json;
                break;
            } else {
                sleep(1);
                continue;
            }
        }

    }
}
