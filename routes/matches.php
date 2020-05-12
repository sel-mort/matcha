<?php

use App\Controllers\chatController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/matches", function () use ($app) {
    $app->post("", chatController::class . ':matches')->setName('matches');
});
