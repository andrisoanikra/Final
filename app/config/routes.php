<?php

use app\controllers\LivraisonsController;
use app\middlewares\SecurityHeadersMiddleware;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$router->group('/', function (Router $router) use ($app) {

    // Accueil
   

}, [SecurityHeadersMiddleware::class]);
