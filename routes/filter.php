<?php

use App\Middleware\RedirectIfUnauthenticated;

$app->group("/filter", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "filter.twig");
    })->setName('filter')->add(new RedirectIfUnauthenticated);
});
