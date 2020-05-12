<?php

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../bootstrap/app.php';

require __DIR__ . '/../config/database.php';

$container['db'] = function () {
    return new PDO("mysql:host=$DB_DSN;dbname=$DB_NAME;", $DB_USER, $DB_PASSWORD);
};

use App\Models\User;
$app->get("/users", function ($request, $response) {
    return $this->view->render($response, "users.twig", [
        'user0' => 'Ayoub',
    ]);
    //phpinfo();
    /* $user = new user;
    var_dump($user); */
})->setName('users.index');

/* $app->get("/contact", function ($request, $response) {
return $this->view->render($response, "contact.twig");
});

$app->post("/contact", function ($request, $response) {
print($request->getParam('email'));
})->setName('contact'); */

$app->group("/contact", function () use ($app) {
    $app->get("", function ($req, $res) {
        return $this->view->render($res, "contact.twig");
    })->setName('contact');

    $app->post("", function ($req, $res) {
        print($req->getParam('email'));
    })->setName('contact');
});

$app->run();
