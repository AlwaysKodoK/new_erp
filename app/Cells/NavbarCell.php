<?php

namespace App\Cells;
class NavbarCell {
    public function render() {
        $session     = session(); 
        $rawUsername = $session->get('username') ?? 'User';
        $userLevel   = $session->get('level') ?? 'Guest'; 
        $displayName = ucwords(strtolower($rawUsername));
        
        $data = [
            'displayName' => $displayName,
            'userLevel'   => $userLevel,
            'avatarUrl'   => "https://ui-avatars.com/api/?name=" . urlencode($displayName) . "&background=eeb61d&color=1a1d20&bold=true"
        ];
        
        // Kembalikan ke view navbar
        return view('Layout\Views\Partials\navbar', $data);
    }
}