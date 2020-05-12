<?php

use App\Controllers\MessageController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/save", function () use ($app) {
    $app->post("", MessageController::class . ':set_message')->setName('save')->add(new RedirectIfUnauthenticated);
});
