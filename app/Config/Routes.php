<?php

use CodeIgniter\Router\RouteCollection;  

/**
 * @var RouteCollection $routes
 */


// ==== LOGIN ====
    $routes->get('/', '\Login\Controllers\AuthController::login', ['as' => 'login.login']);
    if (is_file(ROOTPATH . 'modules/_login/Config/Routes.php')) {
        require ROOTPATH . 'modules/_login/Config/Routes.php';
    }

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // ==== SIMRS ====
        if (is_file(ROOTPATH . 'modules/simrs/Config/Routes.php')) {
            require ROOTPATH . 'modules/simrs/Config/Routes.php';
        }
    // ==== SIMRS ====

    // ==== Pengadaan ====
        if (is_file(ROOTPATH . 'modules/pengadaan/Config/Routes.php')) {
            require ROOTPATH . 'modules/pengadaan/Config/Routes.php';
        }
    // ==== Pengadaan ====
    
    // ==== gizi ====
        if (is_file(ROOTPATH . 'modules/gizi/Config/Routes.php')) {
            require ROOTPATH . 'modules/gizi/Config/Routes.php';
        }
    // ==== gizi ====

    // ==== rm ====
        if (is_file(ROOTPATH . 'modules/rm/Config/Routes.php')) {
            require ROOTPATH . 'modules/rm/Config/Routes.php';
        }
    // ==== rm ====

    // ==== admisi ====
        if (is_file(ROOTPATH . 'modules/admisi/Config/Routes.php')) {
            require ROOTPATH . 'modules/admisi/Config/Routes.php';
        }
    // ==== admisi ====

    // ==== keuangan ====
        if (is_file(ROOTPATH . 'modules/keuangan/Config/Routes.php')) {
            require ROOTPATH . 'modules/keuangan/Config/Routes.php';
        }
    // ==== keuangan ====
});