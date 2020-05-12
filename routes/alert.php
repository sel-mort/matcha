<?php

use App\Controllers\NotificationController;
use App\Middleware\RedirectIfUnauthenticated;

$app->group("/alert", function () use ($app) {
    $app->post("", NotificationController::class . ':get_activated_notification_count')->setName('alert')->add(new RedirectIfUnauthenticated);
});
