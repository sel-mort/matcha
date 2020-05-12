<?php

namespace App\Middleware;

use App\Models\ProfileModel;
use App\Session\Session;
use App\Status\Status;

class RedirectIfProfileNotFull
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $next)
    {
        $pdo = $this->container['pdo'];

        Session::start();
        $username = Session::unserialize("user")["username"];
        if (!isset($username) || empty(trim($username))) {
            $response = $response->withRedirect("/")->withStatus(Status::FOUND);
        } else {
            $profileModel = new ProfileModel();
            $res = $profileModel->get_user_profile($pdo, $username);

            if ($res === false) {
                $response = $response->withRedirect("/")->withStatus(Status::FOUND);
            } else {
                $bio = $res[0][11];
                $latitude = $res[0][15];
                $longitude = $res[0][16];
                $avatar = $res[0][17];

                if ($bio === null || $avatar === null || $longitude === null || $avatar === null) {
                    $response = $response->withRedirect("/")->withStatus(Status::FOUND);
                }
            }
        }
        return $next($request, $response);
    }
}
