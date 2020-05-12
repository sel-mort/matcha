<?php

use App\Controllers\SearchController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/search", function () use ($app) {
    /* $app->get("", function ($req, $res) {
        return $this->view->render($res, "search.twig");
    })->setName('search')->add(new RedirectIfUnauthenticated); */

    $app->post("", SearchController::class . ':search')->setName('search')->add(new RedirectIfUnauthenticated);
});