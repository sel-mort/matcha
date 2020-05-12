<?php

use App\Controllers\LoginController;
use App\Middleware\RedirectIfAuthenticated;

$app->group("/login", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "login.twig");
    })->setName('login')->add(new RedirectIfAuthenticated);

    $app->post("", LoginController::class . ':login')->setName('login')->add(new RedirectIfAuthenticated);
});