<?php

use App\Controllers\NotificationController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/notification", function () use ($app) {
    $app->get("", NotificationController::class . ':get_notification')->setName('notification')->add(new RedirectIfUnauthenticated);
});
