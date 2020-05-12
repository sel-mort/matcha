<?php

use App\Controllers\ChatController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/chat", function () use ($app) {
    $app->get("", ChatController::class . ':get_matched_user')->setName('chat')->add(new RedirectIfUnauthenticated);
});
