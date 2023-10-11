<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->get('/', 'Home::index');

$routes->get('user', 'UserController::index');
$routes->post('user', 'UserController::create');
$routes->post('signin', 'AuthController::signin');
$routes->post('verify-token', 'AuthController::verifyToken');