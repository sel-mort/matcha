<?php

namespace App\Controllers;

use Slim\Container;
use App\Status\Status;

class Controller
{
    public $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($var)
    {
        return $this->container->{$var};
    }

    public function render($response, $file, $data)
    {
        $this->container->view->render($response, $file, $data);
    }

    public function redirect($response, $name)
    {
        return $response->withStatus(Status::FOUND)->withHeader('Location', $this->router->pathFor($name));
    }
}
