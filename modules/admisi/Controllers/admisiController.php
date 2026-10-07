<?php

namespace admisi\Controllers;

use App\Controllers\BaseController;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Picqer\Barcode\BarcodeGeneratorPNG;
use LZCompressor\LZString;

class admisiController extends BaseController {

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

    private function bpjsDecrypt($encrypted_string, $key) {
        if (!class_exists('\\LZCompressor\\LZString')) {
            $p = APPPATH . 'ThirdParty/LZCompressor/';
            require_once $p . 'LZString.php';
            require_once $p . 'LZReverseDictionary.php';
            require_once $p . 'LZData.php';
            require_once $p . 'LZUtil.php';
        }

        $key_hash = hex2bin(hash('sha256', $key));
        $iv       = substr($key_hash, 0, 16);
        $output   = openssl_decrypt(base64_decode($encrypted_string), "AES-256-CBC", $key_hash, OPENSSL_RAW_DATA, $iv);

        if ($output === false) return false;

        $decompressed = \LZCompressor\LZString::decompressFromEncodedURIComponent($output);
        return json_decode($decompressed, true);
    }
    
    private function sendGetBPJS($urlSEP, $consId, $secret, $userKey, $tStamp) {
        $signature = hash_hmac('sha256', $consId . "&" . $tStamp, $secret, true);
        $encodedSignature = base64_encode($signature);

        $headers = [
            "X-cons-id: " . $consId,
            "X-timestamp: " . $tStamp,
            "X-signature: " . $encodedSignature,
            "user_key: " . $userKey,
            "Content-Type: application/json; charset=utf-8"
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $urlSEP,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }


    // ==== ANTREAN ADMISI ====
        public function antrianAdmisi() {
            // header('Content-Type: application/json');
            // echo json_encode(session()->get());
            // exit; 

            $usernik    = session()->get('usernik');
            $username   = session()->get('username');
            $timeStart  = date('Y-m-d 00:00:00');
            $timeEnd    = date('Y-m-d 23:59:59');

            $sqlLoket   = "SELECT
                    a.id,
                    a.loket_name
                FROM admisi_master_loket a
                WHERE a.loket_status = '1'
            ";
            $resLoket = $this->ERP->query($sqlLoket, [])->getResult();

            $sqlCheckLoket = "SELECT
                    a.usernik,
                    a.loket_id,
                    a.open_at
                FROM admisi_config_loket a
                WHERE a.created_at BETWEEN ? AND ?
                    AND a.usernik = ?
                LIMIT 1
            ";
            $resCheckLoket = $this->ERP->query($sqlCheckLoket, [$timeStart, $timeEnd, $usernik])->getRowArray();

            $data = [
                'title'     => 'Admisi | Antrian Admisi',
                'usernik'   => $usernik,
                'username'  => $username,
                'loket'     => $resLoket,
                'CheckLoket'=> $resCheckLoket,
            ]; 
 
            return view('admisi\Views\antrianAdmisi', $data);
        } 

        public function updateLoket() {
            if (!$this->request->is('post')) {
                return $this->response->setStatusCode(405)->setJSON([
                    'status'  => 'error',
                    'message' => 'Method tidak diizinkan',
                ]);
            }

            $usernik    = $this->request->getPost('usernik');
            $loketId    = $this->request->getPost('loket_id'); 
            $timeNow    = date('Y-m-d H:i:s');

            if (empty($usernik) || empty($loketId)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data tidak lengkap',
                ]);
            }

            $timeStart = date('Y-m-d 00:00:00');
            $timeEnd   = date('Y-m-d 23:59:59');

            $sqlCheck = "SELECT id 
                FROM admisi_config_loket
                WHERE usernik = ? 
                    AND created_at BETWEEN ? AND ?
                LIMIT 1
            ";
            $existing = $this->ERP->query($sqlCheck, [$usernik, $timeStart, $timeEnd])->getRowArray();
            if (empty($existing)) {
                $dataInsert = [
                    'usernik'    => $usernik,
                    'loket_id'   => $loketId, 
                    'open_at'    => $timeNow,
                    'created_at' => $timeNow,
                ];

                $this->ERP->table('admisi_config_loket')->insert($dataInsert);
                $message = 'Loket berhasil dibuka';
            } else {
                $dataUpdate = [
                    'loket_id'   => $loketId, 
                    'updated_at' => $timeNow,
                ];

                $this->ERP->table('admisi_config_loket')
                    ->where('id', $existing['id'])
                    ->update($dataUpdate);
                $message = 'Loket berhasil ditutup';
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'message' => $message,
            ]);
        }

        public function dataAdmisi() {
            $tglMulai   = $this->request->getPost('tglMulai') ?: date('Y-m-d');
            $tglSelesai = $this->request->getPost('tglSelesai') ?: date('Y-m-d');
            $tglSelesai = date('Y-m-d', strtotime($tglSelesai . ' +1 day'));
            $usernik    = session()->get('usernik');

            $sqlAntrean = "SELECT
                    a.*,
                    b.loket_name,
                    c.nama_karyawan
                FROM admisi_antrean_loket a
                LEFT JOIN admisi_master_loket b ON b.id = a.loket_id
                LEFT JOIN _master_karyawan c ON c.usernik = a.take_by
                WHERE a.created_at >= ?
                    AND a.created_at < ?
                    AND (a.take_by = ? OR a.take_by IS NULL)
            ";
            $resAntrean = $this->ERP->query($sqlAntrean, [$tglMulai, $tglSelesai, $usernik])->getResult();

            if (!empty($resAntrean)) { 
                $noAppList = array_values(array_unique(array_map(function ($row) {
                    return $row->no_app;
                }, $resAntrean)));

                if (!empty($noAppList)) { 
                    $placeholders = implode(',', array_fill(0, count($noAppList), '?'));

                    $sqlPasien = "SELECT
                            a.noapp,
                            a.norm,
                            TRIM(b.nama) AS namaPasien,
                            a.tgllahir
                        FROM rj_appointment a
                        LEFT JOIN Pasien b ON b.norm = a.norm
                        WHERE a.noapp IN ($placeholders)
                            AND a.batal = '0'
                    ";
                    $resPasien = $this->medinPro->query($sqlPasien, $noAppList)->getResult();
 
                    $pasienMap = [];
                    foreach ($resPasien as $p) {
                        $pasienMap[$p->noapp] = $p;
                    }
 
                    foreach ($resAntrean as $row) {
                        $p = $pasienMap[$row->no_app] ?? null;
                        $row->norm       = $p->norm ?? null;
                        $row->namaPasien = $p->namaPasien ?? null;
                        $row->tgllahir   = $p->tgllahir ?? null;
                    }
                }
            }

            return $this->response->setJSON([
                'data' => $resAntrean,
            ]);
        }

        public function takeAntrean(){
            $idAntrean = $this->request->getPost('idAntrean');
            $idLoket   = $this->request->getPost('idLoket');
            $usernik   = session()->get('usernik');
            $timeNow   = date('Y-m-d H:i:s');

            // Validasi input dasar
            if (empty($idAntrean) || empty($idLoket)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data antrean atau loket tidak valid.',
                ]);
            }

            if (empty($usernik)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi anda telah berakhir, silakan login kembali.',
                ]);
            }

            try {
                // Cek dulu apakah antrean masih tersedia (belum diambil orang lain)
                $sqlCheck = "SELECT id, take_by, take_at
                            FROM admisi_antrean_loket
                            WHERE id = ?
                            LIMIT 1";
                $resCheck = $this->ERP->query($sqlCheck, [$idAntrean])->getRowArray();

                if (empty($resCheck)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data antrean tidak ditemukan.',
                    ]);
                }

                if (!empty($resCheck['take_by']) || !empty($resCheck['take_at'])) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Antrean ini sudah diambil oleh petugas lain.',
                    ]);
                }

                $dataUpdate = [
                    'loket_id' => $idLoket,
                    'take_by'  => $usernik,
                    'take_at'  => $timeNow,
                ];

                $this->ERP->table('admisi_antrean_loket')
                        ->where('id', $idAntrean)
                        ->update($dataUpdate);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Antrean berhasil diambil.',
                ]);

            } catch (\Throwable $e) {
                log_message('error', 'takeAntrean error: ' . $e->getMessage());

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan pada server, silakan coba lagi.',
                ]);
            }
        }

        public function showAntrean(){
            $idAntrean = $this->request->getPost('idAntrean');
            $idLoket   = $this->request->getPost('idLoket');
            $usernik   = session()->get('usernik');
            $timeNow   = date('Y-m-d H:i:s');
 
            if (empty($idAntrean) || empty($idLoket)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data antrean atau loket tidak valid.',
                ]);
            }

            if (empty($usernik)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi anda telah berakhir, silakan login kembali.',
                ]);
            }

            try { 
                $sqlCheck = "SELECT id, take_by, take_at
                    FROM admisi_antrean_loket
                    WHERE id = ?
                    LIMIT 1
                ";
                $resCheck = $this->ERP->query($sqlCheck, [$idAntrean])->getRowArray();

                if (empty($resCheck)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data antrean tidak ditemukan.',
                    ]);
                }

                if (!empty($resCheck['is_show']) || !empty($resCheck['show_at'])) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Antrean ini sudah dipanggil oleh petugas lain.',
                    ]);
                }

                $dataUpdate = [ 
                    'is_show'  => '1',
                    'show_at'  => $timeNow,
                ];

                $this->ERP->table('admisi_antrean_loket')
                    ->where('id', $idAntrean)
                    ->update($dataUpdate);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Antrean berhasil di panggil.',
                ]);

            } catch (\Throwable $e) {
                log_message('error', 'takeAntrean error: ' . $e->getMessage());

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan pada server, silakan coba lagi.',
                ]);
            }
        }

        public function doneAntrean(){
            $idAntrean = $this->request->getPost('idAntrean');
            $idLoket   = $this->request->getPost('idLoket');
            $usernik   = session()->get('usernik');
            $timeNow   = date('Y-m-d H:i:s');
 
            if (empty($idAntrean) || empty($idLoket)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Data antrean atau loket tidak valid.',
                ]);
            }

            if (empty($usernik)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Sesi anda telah berakhir, silakan login kembali.',
                ]);
            }

            try { 
                $sqlCheck = "SELECT id, take_by, take_at
                    FROM admisi_antrean_loket
                    WHERE id = ?
                    LIMIT 1
                ";
                $resCheck = $this->ERP->query($sqlCheck, [$idAntrean])->getRowArray();

                if (empty($resCheck)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data antrean tidak ditemukan.',
                    ]);
                }

                if (!empty($resCheck['is_done']) || !empty($resCheck['done_at'])) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Antrean ini sudah diselesaikan     oleh petugas lain.',
                    ]);
                }

                $dataUpdate = [ 
                    'is_done'  => '1',
                    'done_at'  => $timeNow,
                ];

                $this->ERP->table('admisi_antrean_loket')
                    ->where('id', $idAntrean)
                    ->update($dataUpdate);

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Antrean berhasil diselesaikan.',
                ]);

            } catch (\Throwable $e) {
                log_message('error', 'takeAntrean error: ' . $e->getMessage());

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan pada server, silakan coba lagi.',
                ]);
            }
        }
    // ==== ANTREAN ADMISI ====

    // ==== UPDATE SEP ====
        public function updateSEP() {
            // header('Content-Type: application/json');
            // echo json_encode(session()->get());
            // exit; 

            $data = [
                'title'     => 'Admisi | Antrian Admisi', 
            ]; 
 
            return view('admisi\Views\updateSEP', $data);
        } 

        public function dataUpdateSEP() { 

            $jenis      = $this->request->getPost('jenis') ?: 'RJ';
            $tglMulai   = $this->request->getPost('tglMulai') ?: date('Y-m-d');
            $tglSelesai = $this->request->getPost('tglSelesai') ?: date('Y-m-d');
            $tglSelesai = date('Y-m-d', strtotime($tglSelesai . ' +1 day'));

            switch ($jenis) {
                case 'RJ':
                    $sql = "SELECT
                                a.noreg,
                                TRIM(a.norm) AS NoRM,
                                b.NoPesertaBPJS,
                                a.noSEPBPJS,
                                TRIM(b.nama) AS NamaPasien,
                                a.tglregistrasi AS tgl,
                                c.nminstansi
                            FROM rj_reg a
                            LEFT JOIN Pasien b ON a.norm = b.norm
                            LEFT JOIN instansi c ON a.kdinstansi = c.kdinstansi
                            WHERE a.batal = 0
                            AND a.tglregistrasi >= ? AND a.tglregistrasi < ?
                            AND a.kdinstansi = 'BPJ02'";
                    break;

                case 'RI':
                    $sql = "SELECT
                                a.noreg,
                                TRIM(a.norm) AS NoRM,
                                b.NoPesertaBPJS,
                                a.noSEPBPJS,
                                TRIM(b.nama) AS NamaPasien,
                                a.tglmasuk AS tgl,
                                c.nminstansi
                            FROM ri_reg a
                            LEFT JOIN Pasien b ON a.norm = b.norm
                            LEFT JOIN ri_penjaminBayar rpb ON rpb.noreg = a.noreg
                            LEFT JOIN instansi c ON c.kdinstansi = rpb.kdinstansi
                            WHERE a.batal = 0
                            AND a.tglmasuk >= ? AND a.tglmasuk < ?
                            AND c.nminstansi LIKE '%BPJS%'";
                    break;

                case 'RD':
                    $sql = "SELECT
                                a.noreg,
                                TRIM(a.norm) AS NoRM,
                                b.NoPesertaBPJS,
                                a.noSEPBPJS,
                                TRIM(b.nama) AS NamaPasien,
                                a.tgldatang AS tgl,
                                i.nminstansi
                            FROM rd_reg a
                            LEFT JOIN Pasien b ON a.norm = b.norm
                            LEFT JOIN rd_penjaminBayar p ON a.noreg = p.noreg
                            LEFT JOIN instansi i ON p.kdinstansi = i.kdinstansi
                            WHERE a.batal = 0
                            AND a.tgldatang >= ? AND a.tgldatang < ?";
                    break;

                default:
                    return $this->response->setStatusCode(400)->setJSON([
                        'data' => [],
                        'csrf' => csrf_hash(),
                    ]);
            }

            $data = $this->medinPro->query($sql, [$tglMulai, $tglSelesai])->getResult();

            return $this->response->setJSON([
                'data' => $data,
                'csrf' => csrf_hash(),
            ]);
        }

        public function simpanUpdateSEP() {
            $jenis   = $this->request->getPost('jenis');
            $noreg   = trim((string) $this->request->getPost('noreg'));
            $noSEP   = trim((string) $this->request->getPost('noSEP'));
            $noKartu = trim((string) $this->request->getPost('noKartu'));

            $balas = fn(bool $status, string $pesan, int $kode = 200) =>
                $this->response->setStatusCode($kode)->setJSON([
                    'status'  => $status,
                    'message' => $pesan,
                    'csrf'    => csrf_hash(),
                ]);

            // Whitelist tabel registrasi per jenis
            $tabelReg = ['RJ' => 'rj_reg', 'RI' => 'ri_reg', 'RD' => 'rd_reg'][$jenis] ?? null;

            if ($noreg === '' || $noSEP === '' || !$tabelReg) {
                return $balas(false, 'Data tidak lengkap', 400);
            }

            try {
                // ===== Ambil data BPJS =====
                $resBridging = $this->ERP->query(
                    "SELECT a.url, a.customer_id, a.customer_secret, a.user_key
                    FROM _master_url_bridging a
                    WHERE a.keterangan = 'vclaim-rest'", []
                )->getRowArray();

                if (!$resBridging) {
                    return $balas(false, 'Konfigurasi bridging BPJS tidak ditemukan');
                }

                $dataSEP = $this->checkSEP($noSEP, $resBridging);
                if (empty($dataSEP['noSep'])) {
                    return $balas(false, 'SEP tidak ditemukan di BPJS, periksa kembali nomor SEP');
                }

                $noRujukan   = $dataSEP['noRujukan'] ?? '';
                $dataRujukan = $this->checkRujukan($noRujukan, $resBridging) ?: [];
                $dataPeserta = $dataRujukan['peserta'] ?? [];

                // ===== Data registrasi lokal =====
                $kdPoliBPJ = $nmPoliBPJS = null;

                switch ($jenis) {
                    case 'RJ':
                        $sqlRegis = "SELECT TRIM(a.norm) AS norm, TRIM(b.nama) AS nama, b.noktpsim, b.kdseks,
                                            b.tgllahir, a.tglinsert, a.usrinsert
                                    FROM rj_reg a
                                    INNER JOIN Pasien b ON b.norm = a.norm
                                    WHERE a.noreg = ? AND a.batal = '0'";

                        $resPoli = $this->medinPro->query(
                            "SELECT a.kdpoliBPJS, a.nmpolibpjs FROM rj_poliklinik a WHERE a.nmpolibpjs = ?",
                            [$dataSEP['poli'] ?? '']
                        )->getRowArray();

                        $kdPoliBPJ  = $resPoli['kdpoliBPJS'] ?? '';
                        $nmPoliBPJS = $resPoli['nmpolibpjs'] ?? '';
                        break;

                    case 'RD':
                        $sqlRegis = "SELECT TRIM(a.norm) AS norm, TRIM(b.nama) AS nama, b.noktpsim, b.kdseks,
                                            b.tgllahir, a.tgldatang AS tglinsert, a.usrinsert
                                    FROM rd_reg a
                                    INNER JOIN Pasien b ON b.norm = a.norm
                                    WHERE a.noreg = ? AND a.batal = '0'";
                        $kdPoliBPJ  = 'IGD';
                        $nmPoliBPJS = 'INSTALASI GAWAT DARURAT';
                        break;

                    default: // RI
                        $sqlRegis = "SELECT TRIM(a.norm) AS norm, TRIM(b.nama) AS nama, b.noktpsim, b.kdseks,
                                            b.tgllahir, a.tglinsert, a.usrinsert
                                    FROM ri_reg a
                                    INNER JOIN Pasien b ON b.norm = a.norm
                                    WHERE a.noreg = ? AND a.batal = '0'";
                }

                $resRegis = $this->medinPro->query($sqlRegis, [$noreg])->getRowArray();
                if (!$resRegis) {
                    return $balas(false, 'Data registrasi tidak ditemukan');
                }

                $kdKelas  = $dataPeserta['hakKelas']['kode']       ?? '';
                $nmKelas  = $dataPeserta['hakKelas']['keterangan'] ?? '';
                $tglReg   = $resRegis['tglinsert'] ?? '';
                $usr      = $resRegis['usrinsert'] ?? '';

                $insertBPJS = [
                    'Noreg'           => $noreg,
                    'AsalPasien'      => $jenis,
                    'norm'            => $resRegis['norm'] ?? '',
                    'NoKartu'         => $noKartu,
                    'NIK'             => ($dataPeserta['nik']      ?? '') ?: ($resRegis['noktpsim'] ?? ''),
                    'Nama'            => ($dataPeserta['nama']     ?? '') ?: ($resRegis['nama']     ?? ''),
                    'Sex'             => ($dataPeserta['sex']      ?? '') ?: ($resRegis['kdseks']   ?? ''),
                    'TglLahir'        => ($dataPeserta['tglLahir'] ?? '') ?: ($resRegis['tgllahir'] ?? ''),
                    'pisat'           => $dataPeserta['pisa'] ?? '',
                    'NoSEP'           => $dataSEP['noSep'],
                    'TglSEP'          => $dataSEP['tglSep'] ?? '',
                    'NoRujukan'       => $noRujukan,
                    'TglRujukan'      => $dataRujukan['tglKunjungan'] ?? '',
                    'KdRujukan'       => $dataRujukan['provPerujuk']['kode'] ?? '',
                    'NmRujukan'       => $dataRujukan['provPerujuk']['nama'] ?? '',
                    'JnsPelayanan'    => $dataRujukan['pelayanan']['nama'] ?? '',
                    'JnsPeserta'      => $dataPeserta['jenisPeserta']['keterangan'] ?? '',
                    'KdKlsTanggungan' => $kdKelas,
                    'NmKlsTanggungan' => $nmKelas,
                    'KdPoliklinik'    => $kdPoliBPJ ?? '',
                    'NmPoliklinik'    => $nmPoliBPJS ?? '',
                    'KdDiagnosa'      => 'Z09.8',
                    'NmDiagnosa'      => 'Follow-up examination after other treatment for other conditions',
                    'Keluhan'         => '',
                    'Catatan'         => '',
                    'usrInsert'       => $usr,
                    'tglInsert'       => $tglReg,
                    'usrUpdate'       => $usr,
                    'tglUpdate'       => $tglReg,
                    'isDeleted'       => '0',
                    'KlsBPJS'         => $kdKelas,
                    'NmKlsBPJS'       => $nmKelas,
                    'tglPulang'       => $tglReg,
                    'SKTM'            => '',
                    'KdInstansi'      => '',
                    'isCOB'           => '0',
                    'isKasus'         => '0',
                    'PPKTK1'          => '',
                    'tglCetakKartu'   => $dataPeserta['tglCetakKartu'] ?? '',
                    'TMT'             => $dataPeserta['tglTMT'] ?? '',
                    'TAT'             => $dataPeserta['tglTAT'] ?? '',
                    'LokasiKasus'     => '',
                    'usrPrint'        => $usr,
                    'tglLastPrint'    => $tglReg,
                    'notlp'           => $dataPeserta['mr']['noTelepon'] ?? '',
                    'isEksekutif'     => $dataSEP['poliEksekutif'] ?? '',
                    'SKDP'            => $dataSEP['kontrol']['noSurat'] ?? '',
                    'kdDPJP'          => $dataSEP['dpjp']['kdDPJP'] ?? '',
                    'nmDPJP'          => $dataSEP['dpjp']['nmDPJP'] ?? '',
                    'noKunjungan'     => $dataRujukan['noKunjungan'] ?? '',
                    'TujKunjungan'    => $dataSEP['tujuanKunj']['kode'] ?? '',
                    'fProsedur'       => $dataSEP['flagProcedure']['kode'] ?? '',
                    'kdPenunjang'     => $dataSEP['kdPenunjang']['kode'] ?? '',
                    'assesmentPel'    => $dataSEP['assestmenPel']['kode'] ?? '',
                    'noSurat'         => $dataSEP['kontrol']['noSurat'] ?? '',
                ];

                // ===== Simpan (satu transaksi untuk DB medinPro) =====
                $this->medinPro->transStart();

                // Hindari data ganda: hapus catatan lama untuk noreg ini, lalu insert ulang
                $this->medinPro->table('BPJS_reg')->where('Noreg', $noreg)->delete();
                $this->medinPro->table('BPJS_reg')->insert($insertBPJS);

                $this->medinPro->table($tabelReg)
                    ->where('noreg', $noreg)
                    ->update(['noSEPBPJS' => $dataSEP['noSep']]);

                $this->medinPro->transComplete();

                if ($this->medinPro->transStatus() === false) {
                    return $balas(false, 'Gagal menyimpan data ke database');
                }

                // DB EMR terpisah, jadi di luar transaksi di atas
                $this->EMRPro->table('EMR_GET_DATA_KUNJUNGAN')
                    ->where('noreg', $noreg)
                    ->update(['noSEPBPJS' => $dataSEP['noSep']]);

                return $balas(true, 'SEP berhasil diperbarui');

            } catch (\Throwable $e) {
                log_message('error', 'simpanUpdateSEP: ' . $e->getMessage());
                return $balas(false, 'Terjadi kesalahan pada server', 500);
            }
        }

        private function checkSEP($noSEP,$resBridging) {
            date_default_timezone_set("UTC");
            $tStamp         = strval(time()); 
            $linkBridging   = $resBridging['url'];
            $consId 	    = $resBridging['customer_id'];
            $secret         = $resBridging['customer_secret'];
            $userKey 	    = $resBridging['user_key'];
            $keyDecrypt     = $consId . $secret . $tStamp; 
            $urlSEP         = $linkBridging . "SEP/" . $noSEP;

            $resSEP         = $this->sendGetBPJS($urlSEP, $consId, $secret, $userKey, $tStamp);
            $resP           = json_decode($resSEP, true);
            
            if (isset($resP['metaData']['code']) && $resP['metaData']['code'] == "200") {
                $decSEP = $this->bpjsDecrypt($resP['response'], $keyDecrypt);

                if (!empty($decSEP)) {
                    $dataSEP = $decSEP;
                }
            }
            
            return $dataSEP;
        }

        private function checkRujukan($noRujukan, $resBridging) { 
            if(empty($noRujukan)) {
                return false;
            }

            date_default_timezone_set("UTC");
            $tStamp         = strval(time()); 
            $linkBridging   = $resBridging['url'];
            $consId 	    = $resBridging['customer_id'];
            $secret         = $resBridging['customer_secret'];
            $userKey 	    = $resBridging['user_key'];
            $keyDecrypt     = $consId . $secret . $tStamp;   
            $urlPCare       = $linkBridging . "Rujukan/" . $noRujukan;

            $resPCare       = $this->sendGetBPJS($urlPCare, $consId, $secret, $userKey, $tStamp);
            $resP           = json_decode($resPCare, true);
            
            if (isset($resP['metaData']['code']) && $resP['metaData']['code'] == "200") {
                $decryptedString = $this->bpjsDecrypt($resP['response'], $keyDecrypt);  
                $dataP = is_string($decryptedString) ? json_decode($decryptedString, true) : $decryptedString;

                if (isset($dataP['rujukan'])) {
                    return $dataP['rujukan']; 
                }
            }
            
            return false;
        }

        
    // ==== UPDATE SEP ====
}