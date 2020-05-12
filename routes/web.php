<?php

/* $app->get("/", function ($request, $response) use ($app) {
return $this->view->render($response, "home.twig");
})->setName('home'); */

$app->group("/", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $res->withRedirect('/login'); 
        /* return $this->view->render($res, "home.twig"); */
    })->setName('root');
});
