<?php

use App\Controllers\StatusController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/status", function () use ($app) {
    $app->post("", StatusController::class . ':get_status')->setName('status')->add(new RedirectIfUnauthenticated);
});
