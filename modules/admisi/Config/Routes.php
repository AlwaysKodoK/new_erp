<?php
 
/**
 * @var RouteCollection $routes
 */

// ==== DASHBOARD ADMISI ====
$routes->get('admisi/antrianAdmisi', '\admisi\Controllers\admisiController::antrianAdmisi', ['as' => 'admisi.antrianAdmisi']); 
$routes->post('admisi/dataAdmisi', '\admisi\Controllers\admisiController::dataAdmisi', ['as' => 'admisi.dataAdmisi']); 
$routes->post('admisi/updateLoket', '\admisi\Controllers\admisiController::updateLoket', ['as' => 'admisi.updateLoket']); 
$routes->post('admisi/takeAntrean', '\admisi\Controllers\admisiController::takeAntrean', ['as' => 'admisi.takeAntrean']); 
$routes->post('admisi/showAntrean', '\admisi\Controllers\admisiController::showAntrean', ['as' => 'admisi.showAntrean']); 
$routes->post('admisi/doneAntrean', '\admisi\Controllers\admisiController::doneAntrean', ['as' => 'admisi.doneAntrean']); 

// ==== UPDATE SEP ====
$routes->get('admisi/updateSEP', '\admisi\Controllers\admisiController::updateSEP', ['as' => 'admisi.updateSEP']); 
$routes->post('admisi/dataUpdateSEP', '\admisi\Controllers\admisiController::dataUpdateSEP', ['as' => 'admisi.dataUpdateSEP']); 
$routes->post('admisi/simpanUpdateSEP', '\admisi\Controllers\admisiController::simpanUpdateSEP', ['as' => 'admisi.simpanUpdateSEP']); 