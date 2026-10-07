<?php
 
/**
 * @var RouteCollection $routes
 */

// ==== DASHBOARD RM ====
$routes->get('rm/dashboard', '\rm\Controllers\rmController::dashboard', ['as' => 'rm.dashboard']); 
$routes->post('rm/dataPasien', '\rm\Controllers\rmController::dataPasien', ['as' => 'rm.dataPasien']); 
$routes->get('rm/cetakLabel/(:segment)/(:segment)', '\rm\Controllers\rmController::cetakLabel/$1/$2', ['as' => 'rm.cetakLabel']);
$routes->get('rm/cetakBarcode/(:segment)', '\rm\Controllers\rmController::cetakBarcode/$1', ['as' => 'rm.cetakBarcode']);

$routes->get('rm/logBerkas', '\rm\Controllers\rmController::logBerkas', ['as' => 'rm.logBerkas']); 
$routes->post('rm/dataLogBerkas', '\rm\Controllers\rmController::dataLogBerkas', ['as' => 'rm.dataLogBerkas']); 
$routes->post('rm/saveLogBerkas', '\rm\Controllers\rmController::saveLogBerkas', ['as' => 'rm.saveLogBerkas']); 
$routes->post('rm/terimaLogBerkas', '\rm\Controllers\rmController::terimaLogBerkas', ['as' => 'rm.terimaLogBerkas']); 
$routes->get('rm/backupData', '\rm\Controllers\rmController::backupData', ['as' => 'rm.backupData']); 