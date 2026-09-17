<?php

namespace simrs\Controllers;

use App\Controllers\BaseController;

class simrsController extends BaseController {

    protected $default;
    protected $medinPro;
    protected $EMRPro;
    protected $ERP;

    public function __construct() { 
        $this->default  = \Config\Database::connect('default'); 
        $this->medinPro = \Config\Database::connect('medinPro'); 
        $this->EMRPro   = \Config\Database::connect('EMRPro'); 
        $this->ERP      = \Config\Database::connect();
    }

    public function dashboard() {   
        $data = [
            'title' => 'SIMRS'
        ]; 
        return view('simrs\Views\dashboard', $data);
    }

    public function pengajuan() {   
        $data = [
            'title' => 'SIMRS'
        ]; 
        return view('simrs\Views\pengajuan', $data);
    }

    public function datapengajuan() {
        
    }
}