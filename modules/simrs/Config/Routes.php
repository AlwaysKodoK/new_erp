<?php
 
/**
 * @var RouteCollection $routes
 */

$routes->get('simrs/dashboard', '\simrs\Controllers\simrsController::dashboard', ['as' => 'simrs.dashboard']); 
$routes->get('simrs/pengajuan', '\simrs\Controllers\simrsController::pengajuan', ['as' => 'simrs.pengajuan']); 