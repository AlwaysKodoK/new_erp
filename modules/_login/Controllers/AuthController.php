<?php
namespace Login\Controllers;
use App\Controllers\BaseController; 

class AuthController extends BaseController {

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
    
    public function login() { 
        $data = [
            'title' => 'Login'
        ];
        
        return view('Login\Views\index', $data);
    }

    public function prosesLogin() {  
        // header('Content-Type: application/json');
        // echo json_encode($_POST);
        // exit;

        $userid     = $this->request->getPost('userid');
        $password   = $this->request->getPost('password');
        $encPass    = strtoupper(sha1($password));

        $sqlUser = "SELECT TOP (1)
                a.UserID,
                a.UserName,
                a.UserGroupID,
                a.Password
            FROM [User] a
            WHERE a.UserID = ?
                AND a.IsActive = '1'
        "; 
        $resUser = $this->medinPro->query($sqlUser, array($userid))->getRowArray(); 
         
        if (!$resUser) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'UserID tidak ditemukan atau tidak aktif!'
            ]);
        }

        $medinID    = $resUser['UserID'];
        $medinPass  = $resUser['Password'];
        $UserName   = $resUser['UserName'];
        $level      = $resUser['UserGroupID'];
 
        if ($medinID == $userid && $medinPass == $encPass) {  
            $sqlPengguna = "SELECT TOP (1)
                    a.id_m_pengguna,
                    a.id_m_grup_pengguna
                FROM m_pengguna a
                WHERE a.id_m_pengguna = ?
                    AND a.pengguna_aktif = '1'
            "; 
            $resPengguna = $this->EMRPro->query($sqlPengguna, array($userid))->getRowArray(); 

            if ($resPengguna) { 
                $level2 = $resPengguna['id_m_grup_pengguna'];

                $sqlnik = "SELECT
                        a.usernik
                    FROM _master_karyawan a
                    WHERE a.userid = ?
                    LIMIT 1
                "; 
                $resnik = $this->ERP->query($sqlnik, [$userid])->getRowArray(); 
                $usernik = $resnik['usernik'];
                 
                $sessionData = [
                    'statusLogin'   => 'YouGetMeLoginBaby',
                    'userid'        => $userid,
                    'username'      => $UserName,
                    'usernik'       => $usernik,
                    'level'         => $level,
                    'level2'        => $level2
                ];
 
                session()->set($sessionData);

                return $this->response->setJSON([
                    'status'        => 'success',
                    'message'       => 'Login Berhasil!',
                    'sessionData'   => $sessionData, 
                    'redirect'      => url_to('login.dashboardLogin')
                ]);
            } else {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Maaf, anda belum memiliki akses ke System ERP RS Royal Surabaya'
                ]);
            } 
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'UserID atau Password salah!'
            ]);
        }
    }

    public function dashboardLogin()  { 
        // header('Content-Type: application/json');
        // echo json_encode(session()->get());
        // exit;

        $level2     = session()->get('level2');   
        $sqlMenu    = "SELECT DISTINCT
                b.MENU_NAMA,
                b.MENU_URL
            FROM _master_akses a
            INNER JOIN _master_menu b 
            WHERE a.ID_GROUP = ?
                AND b.MENU_PARENT = '0'
            ORDER BY b.MENU_NAMA ASC
        ";  
        $resMenu    = $this->ERP->query($sqlMenu, [$level2])->getResult();

        $data = [
            'UserID'    => session()->get('userid'),
            'UserName'  => session()->get('username'),
            'DataMenu'  => $resMenu,
        ];

        // header('Content-Type: application/json');
        // echo json_encode($data);
        // exit;
        return view('Login\Views\dashboard', $data); 
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}