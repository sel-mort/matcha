<?php

use App\Middleware\RedirectIfUnauthenticated;
use App\Middleware\RedirectIfProfileNotFull;

$app->group("/home", function () use ($app) {
    /* $app->get("", function ($req, $res) {
        return $this->view->render($res, "home.twig");
    })->setName('home')->add(new RedirectIfUnauthenticated)->add(new RedirectIfProfileNotFull($app->getContainer())); */
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "home.twig");
    })->setName('home')->add(new RedirectIfUnauthenticated);
});
