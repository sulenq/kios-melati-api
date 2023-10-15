<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->get('/', 'Home::index');

$routes->get('user', 'UserController::readAll');
$routes->get('user/(:any)', 'UserController::read/$1');
$routes->post('user-create', 'UserController::create');

$routes->post('signin', 'AuthController::signin');
$routes->post('verify-token', 'AuthController::verifyToken');

$routes->get('store', 'StoreController::readAll', ['filter' => 'authJWT']);
$routes->get('store/(:any)', 'StoreController::read/$1', ['filter' => 'authJWT']);
$routes->get('store-by-admin-user/(:any)', 'StoreController::readByAdminUser/$1', ['filter' => 'authJWT']);
$routes->get('store-by-cashier-user/(:any)', 'StoreController::readByCashierUser/$1', ['filter' => 'authJWT']);
$routes->post('store-create', 'StoreController::create', ['filter' => 'authJWT']);
$routes->patch('store-update/(:any)', 'StoreController::update/$1', ['filter' => 'authJWT']);
$routes->delete('store-delete/(:any)', 'StoreController::delete/$1', ['filter' => 'authJWT']);

$routes->get('employee', 'EmployeeController::readAll');
$routes->get('employee-by-store/(:any)', 'EmployeeController::readByStore/$1', ['filter' => 'authJWT']);
$routes->get('employee/(:any)', 'EmployeeController::read/$1', ['filter' => 'authJWT']);
$routes->post('employee-create/(:any)', 'EmployeeController::create/$1', ['filter' => 'authOwner']);
$routes->patch('employee-update/(:any)/(:any)', 'EmployeeController::update/$1/$2', ['filter' => 'authOwner']);
$routes->delete('employee-delete/(:any)', 'EmployeeController::delete/$1', ['filter' => 'authJWT']);