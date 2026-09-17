<?php
 
/**
 * @var RouteCollection $routes
 */

$routes->get('pengadaan/dashboard', '\pengadaan\Controllers\pengadaanController::dashboard', ['as' => 'pengadaan.dashboard']);  
$routes->get('pengadaan/pengajuanBarang', '\pengadaan\Controllers\pengadaanController::pengajuanBarang', ['as' => 'pengadaan.pengajuanBarang']);  
$routes->post('pengadaan/dataPengajuan', '\pengadaan\Controllers\pengadaanController::dataPengajuan', ['as' => 'pengadaan.dataPengajuan']);  