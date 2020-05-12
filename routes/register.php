<?php

use App\Controllers\RegisterController;
use App\Middleware\RedirectIfAuthenticated;

$app->group("/register", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "register.twig");
    })->setName('register')->add(new RedirectIfAuthenticated);

    $app->post("", RegisterController::class . ':register')->setName('register')->add(new RedirectIfAuthenticated);
});
