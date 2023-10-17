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

$routes->get('api/store', 'StoreController::readAll', ['filter' => 'authJWT']);
$routes->get('api/store/(:any)', 'StoreController::read/$1', ['filter' => 'authJWT']);
$routes->get('api/store-by-admin-user/(:any)', 'StoreController::readByAdminUser/$1', ['filter' => 'authJWT']);
$routes->get('api/store-by-cashier-user/(:any)', 'StoreController::readByCashierUser/$1', ['filter' => 'authJWT']);
$routes->post('api/store-create', 'StoreController::create', ['filter' => 'cors, authJWT']);
$routes->put('api/store-update/(:any)', 'StoreController::update/$1', ['filter' => 'authJWT']);
$routes->delete('api/store-delete/(:any)', 'StoreController::delete/$1', ['filter' => 'authJWT']);

$routes->get('api/employee', 'EmployeeController::readAll');
$routes->get('api/employee-by-store/(:any)', 'EmployeeController::readByStore/$1', ['filter' => 'authJWT']);
$routes->get('api/employee/(:any)', 'EmployeeController::read/$1', ['filter' => 'authJWT']);
$routes->post('api/employee-create/(:any)', 'EmployeeController::create/$1', ['filter' => 'authOwner']);
$routes->put('api/employee-update/(:any)/(:any)', 'EmployeeController::update/$1/$2', ['filter' => 'authOwner']);
$routes->delete('api/employee-delete/(:any)', 'EmployeeController::delete/$1', ['filter' => 'authJWT']);

$routes->get('api/retailstoreproduct', 'RetailStoreProductController::readAll');
$routes->get('api/retailstoreproduct/(:any)', 'RetailStoreProductController::read/$1');
$routes->post('api/retailstoreproduct-create/(:any)', 'RetailStoreProductController::create/$1', ['filter' => 'authAdmin']);
$routes->put('api/retailstoreproduct-update/(:any)/(:any)', 'RetailStoreProductController::update/$1/$2', ['filter' => 'authAdmin']);
$routes->delete('api/retailstoreproduct-delete/(:any)/(:any)', 'RetailStoreProductController::delete/$1/$2', ['filter' => 'authAdmin']);