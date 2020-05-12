<?php

use App\Controllers\SessionController;

$app->group("/session", function () use ($app) {
    /* $app->get("", function ($req, $res) {
        return $this->view->render($res, "search.twig");
    })->setName('search')->add(new RedirectIfUnauthenticated); */

    $app->post("", SessionController::class . ':get_session')->setName('session');
});