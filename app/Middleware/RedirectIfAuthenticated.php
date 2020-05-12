<?php

namespace App\Middleware;

use App\Session\Session;
use App\Status\Status;

class RedirectIfAuthenticated
{
    public function __invoke($request, $response, $next)
    {
        Session::start();
        $username = Session::unserialize("user")["username"];
        if (isset($username) && !empty(trim($username))) {
            $response = $response->withRedirect("/profile")->withStatus(Status::FOUND);
        }
        return $next($request, $response);
    }
}
