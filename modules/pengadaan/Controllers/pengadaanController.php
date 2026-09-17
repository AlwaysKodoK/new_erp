<?php

namespace pengadaan\Controllers;

use App\Controllers\BaseController;

class pengadaanController extends BaseController {

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
            'title' => 'Pengadaan'
        ]; 
        return view('pengadaan\Views\dashboard', $data);
    }

    public function pengajuanBarang() {   
        $data = [
            'title' => 'Pengadaan | Pengajuan Barang'
        ]; 
        return view('pengadaan\Views\pengajuan', $data);
    }

    public function dataPengajuan() {
        header('Content-Type: application/json');
        echo json_encode('dskvnlkdnvlkdn');
        exit;
    }
}