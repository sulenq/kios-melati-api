<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->get('/', 'Home::index');

$routes->get('user', 'UserController::index');
$routes->get('user/(:any)', 'UserController::read/$1');
$routes->post('user-create', 'UserController::create');
$routes->post('signin', 'AuthController::signin');
$routes->post('verify-token', 'AuthController::verifyToken');

$routes->get('store', 'StoreController::index');
$routes->get('store/(:any)', 'StoreController::read/$1');
$routes->post('store-create', 'StoreController::create');
$routes->patch('store-update/(:any)', 'StoreController::update/$1');
$routes->delete('store-delete/(:any)', 'StoreController::delete/$1');