<?php

use App\Controllers\UserController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/user", function () use ($app) {
    /* $app->get("", function ($req, $res) {
        return $this->view->render($res, "profile.twig");
    })->setName('profile')->add(new RedirectIfUnauthenticated); */
    $app->get("", UserController::class . ':render_profile')->setName('user')->add(new RedirectIfUnauthenticated);

    $app->post("", UserController::class . ':link_profile')->setName('user')->add(new RedirectIfUnauthenticated);
});
