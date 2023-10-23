<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->get('/', 'Home::index');

$routes->get('api/user', 'UserController::readAll');
$routes->get('api/user/(:any)', 'UserController::read/$1');
$routes->post('api/user-create', 'UserController::create');

$routes->post('api/signin', 'AuthController::signin');
$routes->post('api/verify-token', 'AuthController::verifyToken');

$routes->get('api/outlet', 'OutletController::readAll', ['filter' => 'authJwt']);
$routes->get('api/outlet/(:any)', 'OutletController::read/$1', ['filter' => 'authJwt']);
$routes->get('api/outlet-by-user', 'OutletController::readAllByUser', ['filter' => 'authJwt']);
$routes->post('api/outlet-create', 'OutletController::create', ['filter' => 'authJwt']);
$routes->put('api/outlet-update/(:any)', 'OutletController::update/$1', ['filter' => 'authOwner']);
$routes->delete('api/outlet-delete/(:any)', 'OutletController::delete/$1', ['filter' => 'authOwner']);

$routes->get('api/employee', 'EmployeeController::readAll');
$routes->get('api/employee-by-store/(:any)', 'EmployeeController::readByOutlet/$1', ['filter' => 'authJwt']);
$routes->get('api/employee/(:any)', 'EmployeeController::read/$1', ['filter' => 'authJwt']);
$routes->get('api/work/(:any)/(:any)', 'EmployeeController::readWork/$1/$2', ['filter' => 'authJwt']);
$routes->post('api/employee-create/(:any)', 'EmployeeController::create/$1', ['filter' => 'authOwner']);
$routes->put('api/employee-update/(:any)/(:any)', 'EmployeeController::update/$1/$2', ['filter' => 'authOwner']);
$routes->delete('api/employee-delete/(:any)', 'EmployeeController::delete/$1', ['filter' => 'authJwt']);

$routes->get('api/retailproduct', 'RetailProductController::readAll');
$routes->get('api/retailproduct/(:any)', 'RetailProductController::read/$1');
$routes->get('api/retailproduct-by-outlet/(:any)', 'RetailProductController::readAllByOutlet/$1');
$routes->post('api/retailproduct-create/(:any)', 'RetailProductController::create/$1', ['filter' => 'authAdmin']);
$routes->put('api/retailproduct-update/(:any)/(:any)', 'RetailProductController::update/$1/$2', ['filter' => 'authAdmin']);
$routes->delete('api/retailproduct-delete/(:any)/(:any)', 'RetailProductController::delete/$1/$2', ['filter' => 'authAdmin']);