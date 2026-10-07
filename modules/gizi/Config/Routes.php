<?php
 
/**
 * @var RouteCollection $routes
 */

$routes->get('gizi/dashboard', '\gizi\Controllers\giziController::dashboard', ['as' => 'gizi.dashboard']); 
$routes->post('gizi/dataPasien', '\gizi\Controllers\giziController::dataPasien', ['as' => 'gizi.dataPasien']); 
$routes->post('gizi/updateDiet', '\gizi\Controllers\giziController::updateDiet', ['as' => 'gizi.updateDiet']); 
$routes->get('gizi/cetakLabelGizi', '\gizi\Controllers\giziController::cetakLabelGizi', ['as' => 'gizi.cetakLabelGizi']); 
$routes->get('gizi/cetakPenunggu', '\gizi\Controllers\giziController::cetakPenunggu', ['as' => 'gizi.cetakPenunggu']); 
$routes->get('gizi/cetakForm', '\gizi\Controllers\giziController::cetakForm', ['as' => 'gizi.cetakForm']); 
$routes->get('gizi/cetakExcel', '\gizi\Controllers\giziController::cetakExcel', ['as' => 'gizi.cetakExcel']); 