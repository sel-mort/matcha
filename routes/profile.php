<?php

use App\Controllers\ProfileController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/profile", function () use ($app) {
    /* $app->get("", function ($req, $res) {
        return $this->view->render($res, "profile.twig");
    })->setName('profile')->add(new RedirectIfUnauthenticated); */

    $app->get("", ProfileController::class . ':render_profile')->setName('profile')->add(new RedirectIfUnauthenticated);

    $app->post("", ProfileController::class . ':update_profile')->setName('profile')->add(new RedirectIfUnauthenticated);
});
