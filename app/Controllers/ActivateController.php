<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ActivateModel;
use App\Status\Status;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ActivateController extends Controller
{
    public function activate(Request $request, Response $response)
    {
        $pdo = $this->container['pdo'];
        $status = Status::OK;

        $verification_code = $request->getParam('verification_code');
        $error = array();
        $alert = array();
        $data = array();

        $activateModel = new ActivateModel();
        if ($activateModel->verification_code_exists($pdo, $verification_code) === 0) {
            array_push($error, "Invalid link !");
            $status = Status::BAD_REQUEST;
        } else {
            if ($activateModel->activate($pdo, $verification_code)) {
                array_push($alert, "Account activated !");
                $status = Status::OK;
            } else {
                array_push($error, "Something gone wrong !");
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
