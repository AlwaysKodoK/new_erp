<?php
namespace App\Cells;

use App\Models\MenuModel;

class SidebarCell {
    public function render() {
        $model          = new MenuModel(); 
        $level2         = session()->get('level2');   
        $request        = \Config\Services::request();
        $activeModule   = $request->getUri()->getSegment(1);  
        $data['menus']  = $model->getSidebarMenu($level2, $activeModule); 
        
        return view('Layout\Views\Partials\sidebar', $data);
    }
}