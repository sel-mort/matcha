<?php

use App\Controllers\ActivateController;

$app->group("/activate", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "activate.twig");
    })->setName('activate');

    $app->post("", ActivateController::class . ':activate')->setName('activate');
});
