<?php
 
/**
 * @var RouteCollection $routes
 */

// ==== DASHBOARD KEUANGAN ====
$routes->get('keuangan/dashboard', '\keuangan\Controllers\keuanganController::dashboard', ['as' => 'keuangan.dashboard']); 
$routes->post('keuangan/dataPasien', '\keuangan\Controllers\keuanganController::dataPasien', ['as' => 'keuangan.dataPasien']);  
$routes->post('keuangan/createKwitansi', '\keuangan\Controllers\keuanganController::createKwitansi', ['as' => 'keuangan.createKwitansi']);  
$routes->get('keuangan/printKwitansi/(:segment)', '\keuangan\Controllers\keuanganController::printKwitansi/$1', ['as' => 'keuangan.printKwitansi']);
$routes->get('keuangan/prosesLabIGD/(:segment)', '\keuangan\Controllers\keuanganController::prosesLabIGD/$1', ['as' => 'keuangan.prosesLabIGD']);
$routes->post('keuangan/openLabIGD', '\keuangan\Controllers\keuanganController::openLabIGD', ['as' => 'keuangan.openLabIGD']);  