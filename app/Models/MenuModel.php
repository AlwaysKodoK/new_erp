<?php

namespace App\Models;
use CodeIgniter\Model;

class MenuModel extends Model {
    protected $table      = '_master_menu';
    protected $primaryKey = 'ID_MENU';
    protected $returnType = 'array';

    protected $default;
    protected $medinPro;
    protected $EMRPro;
    protected $ERP;

    public function __construct() { 
        parent::__construct(); 
        $this->default  = \Config\Database::connect('default'); 
        $this->medinPro = \Config\Database::connect('medinPro'); 
        $this->EMRPro   = \Config\Database::connect('EMRPro'); 
        $this->ERP      = \Config\Database::connect();
    }
 
    public function getSidebarMenu($level2, $activeModule = '')  {  
        $sqlMenu = "SELECT DISTINCT
                b.ID_MENU,
                b.MENU_NAMA,
                b.MENU_PARENT,
                b.MENU_URL,
                b.MENU_ICON
            FROM _master_akses a
            INNER JOIN _master_menu b ON b.ID_MENU = a.ID_MENU
            WHERE a.ID_GROUP = ? 
              AND b.MENU_STATUS = 1
              AND (b.MENU_URL LIKE ? OR b.MENU_URL = '#') 
            ORDER BY b.MENU_NAMA ASC
        ";
        $searchPattern = $activeModule . '%';
        $menus = $this->ERP->query($sqlMenu, [$level2, $searchPattern])->getResultArray();
        $menuTree = [];
        $indexedMenus = [];
 
        foreach ($menus as $menu) {
            $menu['children'] = [];
            $indexedMenus[$menu['ID_MENU']] = $menu;
        }
 
        foreach ($indexedMenus as $id => $menu) {
            if ($menu['MENU_PARENT'] == 0) { 
                $menuTree[$id] = $menu;
            } else { 
                if (isset($indexedMenus[$menu['MENU_PARENT']])) {
                    $menuTree[$menu['MENU_PARENT']]['children'][] = $menu;
                }
            }
        }
        return $menuTree; 
    }
}