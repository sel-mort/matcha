<?php

use App\Controllers\ResetController;
use App\Middleware\RedirectIfAuthenticated;

$app->group("/reset", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "reset.twig");
    })->setName('reset')->add(new RedirectIfAuthenticated);

    $app->post("", ResetController::class . ':reset')->setName('reset')->add(new RedirectIfAuthenticated);
});
