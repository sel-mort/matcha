<?php
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

// Display error details
$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => false,
    ],
]);
$container = $app->getContainer();

// Database
$container['pdo'] = function () {
    $pdo = new PDO("mysql:host=localhost;dbname=matcha;", 'root', 'rootpw');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../views', [
        'cache' => false,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

$container['notFoundHandler'] = function ($container) {
    return function ($request, $response) use ($container) {
        $container->view->render($response, 'errors/404.twig');
        return $response->withStatus(404);
    };
};

$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        if ($request->getMethod() == 'GET') {
            return $response->withRedirect((string) $uri, 301);
        } else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Routes
require __DIR__ . '/../routes/web.php';
require __DIR__ . '/../routes/login.php';
require __DIR__ . '/../routes/reset.php';
require __DIR__ . '/../routes/register.php';
require __DIR__ . '/../routes/logout.php';
require __DIR__ . '/../routes/activate.php';
require __DIR__ . '/../routes/profile.php';
require __DIR__ . '/../routes/home.php';
require __DIR__ . '/../routes/search.php';
require __DIR__ . '/../routes/filter.php';
require __DIR__ . '/../routes/user.php';
require __DIR__ . '/../routes/status.php';
require __DIR__ . '/../routes/notification.php';
require __DIR__ . '/../routes/chat.php';
require __DIR__ . '/../routes/message.php';
require __DIR__ . '/../routes/save.php';
require __DIR__ . '/../routes/session.php';
require __DIR__ . '/../routes/alert.php';
require __DIR__ . '/../routes/matches.php';
/* $container['App\Controller\Controller'] = function ($c) {
return new App\Controller\Controller($c);
}; */
