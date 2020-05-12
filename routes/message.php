<?php

use App\Controllers\MessageController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/message", function () use ($app) {
    $app->post("", MessageController::class . ':get_message')->setName('message')->add(new RedirectIfUnauthenticated);
});
