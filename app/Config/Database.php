<?php

namespace Config;

use CodeIgniter\Database\Config; 
class Database extends Config { 
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR; 
    public string $defaultGroup = 'default'; 

    public array $default   = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => '',
        'password'     => '',
        'database'     => 'erp_rsr',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8mb4',
        'DBCollat'     => 'utf8mb4_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
        'foundRows'    => false,
        'dateFormat'   => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ]; 
    public array $medinPro  = [
        'DSN'        => 'TrustServerCertificate=yes;',
        'hostname'   => '192.168.80.11',
        'username'   => 'sa',
        'password'   => 'Kerupuk#0126@',
        'database'   => 'MS_RSRS',
        'schema'     => 'dbo',
        'DBDriver'   => 'SQLSRV', 
        'DBPrefix'   => '',
        'pConnect'   => false,
        'DBDebug'    => (ENVIRONMENT !== 'production'),
        'charset'    => 'utf8',
        'swapPre'    => '',
        'encrypt'    => false,
        'failover'   => [],
        'port'       => 1433, 
        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];  
    public array $EMRPro    = [
        'DSN'        => '',
        'hostname'   => '192.168.80.28',
        'username'   => 'sa',
        'password'   => 'Kerupuk#0126@',
        'database'   => 'ROYAL_EMR',
        'schema'     => 'dbo',
        'DBDriver'   => 'SQLSRV', 
        'DBPrefix'   => '',
        'pConnect'   => false,
        'DBDebug'    => (ENVIRONMENT !== 'production'),
        'charset'    => 'utf8',
        'swapPre'    => '',
        'encrypt'    => false,
        'failover'   => [],
        'port'       => 1433, 
        'dateFormat' => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];  
    public array $tests     = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
        'synchronous' => null,
        'dateFormat'  => [
            'date'     => 'Y-m-d',
            'datetime' => 'Y-m-d H:i:s',
            'time'     => 'H:i:s',
        ],
    ];

    public function __construct() {
        parent::__construct(); 
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}
