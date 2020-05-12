<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\LogoutModel;
use App\Session\Session;
use App\Status\Status;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class LogoutController extends Controller
{
    public function logout(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::FOUND;

        $error = array();
        $alert = array();
        $data = array();

        Session::start();
        $connected_username = Session::unserialize("user")["username"];

        $logoutModel = new LogoutModel();
        $res = $logoutModel->logout($pdo, $connected_username);
        if ($res === false) {
            array_push($error, "Something went wrong !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        } else {
            Session::destroy();
            return $response->withRedirect('/login')->withStatus($status);
        }
    }
}
