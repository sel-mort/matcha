<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Session\Session;
use App\Status\Status;
use App\Validator\Validator;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SessionController extends Controller
{
    public function get_session(Request $request, Response $response)
    {
        $status = Status::OK;

        $sid = $request->getParam('sid');
        $error = array();
        $alert = array();
        $data = array();

        if (!Validator::check_array($sid)) {
            array_push($error, "Invalid Input !");
            $status = Status::BAD_REQUEST;

            return $response->withJson([
                'error' => $error,
                'alert' => $alert,
                'data' => $data,
            ])->withStatus($status);
        }

        $res = null;
        $old_cookie = null;
        if (isset($_COOKIE['PHPSESSID'])) {
            $old_cookie = $_COOKIE['PHPSESSID'];
        }

        session_id($sid);
        Session::start();
        $res = Session::unserialize("user")["username"];
        session_write_close();

        array_push($data, $res);

        session_id($old_cookie);
        Session::start();
        $res = Session::unserialize("user")["username"];
        session_write_close();

        array_push($data, $res);

        return $response->withJson([
            'error' => $error,
            'alert' => $alert,
            'data' => $data,
        ])->withStatus($status);
    }
}
