<?php
 /**
 * @var RouteCollection $routes
 */

$routes->post('login', '\Login\Controllers\AuthController::prosesLogin', ['as' => 'login.prosesLogin']);
$routes->get('login/dashboard', '\Login\Controllers\AuthController::dashboardLogin', ['as' => 'login.dashboardLogin']);
$routes->get('logout', '\Login\Controllers\AuthController::logout', ['as' => 'login.logout']);