<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\NotificationModel;
use App\Models\ProfileModel;
use App\Session\Session;
use App\Status\Status;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class NotificationController extends Controller
{
    public function get_notification(Request $request, Response $response)
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
        if (isset($res[0][0])) {
            $connected_user_id = $res[0][0];
        } else {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Get user activated notifications
        $notificationModel = new NotificationModel();

        $res = $notificationModel->get_user_notification($pdo, $connected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $notification_res = array();
        $notification = array();
        for ($i = 0; $i < count($res); $i++) {
            // Setting the array of each notification
            $notification = array();
            $action = $res[$i][4];
            $activated = $res[$i][5];
            $user_res = $profileModel->get_user_profile_by_id($pdo, $res[$i][2]);
            if ($user_res === false) {
                array_push($error, "Something went wrong !");
                $status = Status::BAD_REQUEST;

                return $response->withJson([
                    'error' => $error,
                    'alert' => $alert,
                    'data' => $data,
                ])->withStatus($status);
            }
            $username0 = $user_res[0][1];
            array_push($notification, $username0, $action, $activated);
            array_push($notification_res, $notification);
        }
        // Set notifications as seen
        $res = $notificationModel->deactivate_user_notification($pdo, $connected_user_id);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        return $this->render($response, "notification.twig", array("data" => json_encode($notification_res)));
    }

    public function get_activated_notification_count(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $error = array();
        $alert = array();
        $data = array();

        sleep(1);
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
        if (isset($res[0][0])) {
            $connected_user_id = $res[0][0];
        } else {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        // Get user activated notifications count
        $notificationModel = new NotificationModel();

        $res = $notificationModel->get_activated_user_notification_count($pdo, $connected_user_id);
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
