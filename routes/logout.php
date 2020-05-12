<?php

use App\Controllers\LogoutController;

$app->group("/logout", function () use ($app) {
    $app->get("", LogoutController::class . ':logout')->setName('logout');
});
