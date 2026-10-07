<?php

namespace rm\Controllers;

use App\Controllers\BaseController;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Picqer\Barcode\BarcodeGeneratorPNG;

class rmController extends BaseController {

    protected $default;
    protected $medinPro;
    protected $EMRPro;
    protected $ERP; 
    protected $custom;

    public function __construct() { 
        $this->default  = \Config\Database::connect('default'); 
        $this->medinPro = \Config\Database::connect('medinPro'); 
        $this->EMRPro   = \Config\Database::connect('EMRPro');  
        $this->custom    = \Config\Database::connect('custom'); 
        $this->ERP      = \Config\Database::connect();
    }


    // ==== RM DASHBOARD ====
        public function dashboard() {   
            // header('Content-Type: application/json');
            // echo json_encode('dskvnlkdnvlkdn');
            // exit;

            $data = [
                'title' => 'RM | Cetak Data'
            ]; 
            return view('rm\Views\dashboard', $data);
        } 

        public function dataPasien() {
            $tglMulai   = $this->request->getPost('tglMulai') ?: date('Y-m-d');
            $tglSelesai = $this->request->getPost('tglSelesai') ?: date('Y-m-d');
            $tglSelesai = date('Y-m-d', strtotime($tglSelesai . ' +1 day'));
            $jenis      = $this->request->getPost('jenis') ?: 'RI';


            $jenisReg = null;
            switch ($jenis) {
                case 'RD':
                    $jenisReg = '%EP%';
                    break;
                case 'RJ':
                    $jenisReg = '%OP%';
                    break;
                case 'RI':
                    $jenisReg = '%RI%';
                    break;
                case 'MD':
                    $jenisReg = '%MD%';
                    break;
            }

            $params = [$tglMulai, $tglSelesai];

            if ($jenis !== 'MD') {
                $sqlPasienRM = "SELECT
                        egdt.noreg AS noReg,
                        egdt.norm AS noRM,
                        egdt.nama_pasien AS namaPasien,
                        CONVERT(VARCHAR(11), egdt.tgl_lahir, 103) AS tglLahir,  
                        egdt.jalan AS Alamat
                    FROM EMR_GET_DATA_KUNJUNGAN egdt
                    WHERE egdt.tgl_reg >= ?
                    AND egdt.tgl_reg <  ?
                    AND egdt.batal = '0'";

                if ($jenisReg !== null) {
                    $sqlPasienRM .= " AND egdt.noreg LIKE ?";
                    $params[] = $jenisReg;
                }

                $sqlPasienRM .= " GROUP BY
                        egdt.noreg, egdt.norm, egdt.nama_pasien, egdt.kdseks,
                        egdt.jamreg, egdt.nama_dokter, egdt.nminstansi,
                        egdt.tgl_reg, egdt.tgl_lahir, egdt.jalan
                    ORDER BY egdt.noreg DESC";

                $resPasienRM = $this->EMRPro->query($sqlPasienRM, $params)->getResult();
            } else {
                $sqlPasienRM = "SELECT 
                        a.noreg AS noReg, 
                        a.norm AS noRM, 
                        a.nama_pasien AS namaPasien, 
                        a.tgllahir AS tglLahir,   
                        a.jalan AS Alamat
                    FROM (
                        SELECT 
                            egdt.noreg, 
                            egdt.norm, 
                            egdt.nama AS nama_pasien,
                            egdt.kdseks, 
                            egdt.jamtrans AS jamreg, 
                            b.nama AS nama_dokter, 
                            CONVERT(VARCHAR(11), egdt.tgltrans, 103) AS tgl_reg,
                            ins.nminstansi,
                            CONVERT(VARCHAR(11), egdt.tgllahir, 103) AS tgllahir,
                            egdt.jalan
                        FROM md_reg egdt
                        LEFT JOIN instansi ins ON egdt.kdinstansi = ins.kdinstansi
                        LEFT JOIN medis b ON egdt.kddokter = b.kode
                        WHERE egdt.tgltrans >= ?
                        AND egdt.tgltrans <  ?
                        AND egdt.batal = '0' 
                        AND egdt.noreg LIKE ?
                        GROUP BY 
                            egdt.noreg, egdt.norm, egdt.nama, egdt.kdseks,
                            egdt.jamtrans, b.nama, ins.nminstansi, 
                            egdt.tgltrans, egdt.tgllahir, egdt.jalan
                    ) a
                    ORDER BY a.noreg DESC";

                $params[] = $jenisReg; 
                
                $resPasienRM = $this->medinPro->query($sqlPasienRM, $params)->getResult();
            }

            

            return $this->response->setJSON([
                'data' => $resPasienRM,
            ]);
        }

        private array $jumlahLabelMap = [
            'dua'     => 1,
            'empat'   => 2,
            'enam'    => 3,
            'delapan' => 4,
        ];

        public function cetakLabel($noreg, $cetak) {
            $poli = substr($noreg, 0, 2);
    
            if (!isset($this->jumlahLabelMap[$cetak])) {
                return $this->response->setStatusCode(400)->setBody('Parameter cetak tidak valid.');
            }
            $jumlahLabel = $this->jumlahLabelMap[$cetak];

            switch ($poli) {
                case 'RI':
                    $sqlPasienRM = "SELECT
                            LTRIM(RTRIM(a.noreg)) AS noreg,
                            LTRIM(RTRIM(a.norm)) AS norm,
                            CAST(tgllahir AS DATE) AS tgllahir,
                            LTRIM(RTRIM(nama)) AS nama,
                            LTRIM(RTRIM(kdseks)) AS kdseks,
                            LTRIM(RTRIM(b.jalan)) AS jalan,
                            LTRIM(RTRIM(c.nminstansi)) AS nminstansi,
                            b.umurtahun,
                            b.umurbulan,
                            b.umurhari
                        FROM ri_reg a
                        LEFT JOIN Pasien b ON a.norm=b.norm
                        LEFT JOIN(
                            SELECT a.noreg,
                            b.nminstansi 
                            FROM ri_penjaminBayar a 
                            LEFT JOIN instansi b ON a.kdinstansi= b.kdinstansi
                        ) c ON a.noreg=c.noreg
                        WHERE a.batal=0
                            AND a.noreg = ?
                    ";
                    break;

                case 'OP':
                    $sqlPasienRM = "SELECT 
                            a.*,
                            c.nminstansi  
                        FROM (
                            SELECT 
                                LTRIM(RTRIM(a.noreg)) AS noreg,
                                LTRIM(RTRIM(a.norm)) AS norm,
                                LTRIM(RTRIM(b.nama)) AS nama,
                                LTRIM(RTRIM(b.kdseks)) AS kdseks,
                                CAST(tgllahir AS DATE) AS tgllahir,
                                DATEDIFF(year,b.tgllahir,GETDATE()) AS tlahir,
                                DATEDIFF(month,tgllahir,GETDATE()) AS blahir,
                                DATEDIFF(day,tgllahir,GETDATE()) AS tllahir,
                                LTRIM(RTRIM(b.jalan)) AS jalan,
                                LTRIM(RTRIM(a.kdinstansi)) AS kdinstansi,
                                LTRIM(RTRIM(a.kdtipevisit)) AS kdtipevisit,
                                b.umurtahun,
                                b.umurbulan,
                                b.umurhari
                            FROM rj_reg a
                            LEFT JOIN Pasien b ON a.norm=b.norm
                        ) a			
                        LEFT JOIN instansi c on a.kdinstansi=c.kdinstansi 
                        WHERE a.noreg = ?
                    ";
                    break;

                case 'EP':
                    $sqlPasienRM = "SELECT 
                            a.*,
                            c.nminstansi  
                        FROM (
                            SELECT 
                                LTRIM(RTRIM(a.noreg)) AS noreg,
                                LTRIM(RTRIM(a.norm)) AS norm,
                                LTRIM(RTRIM(b.nama)) AS nama,
                                LTRIM(RTRIM(b.kdseks)) AS kdseks,
                                CAST(tgllahir AS DATE) AS tgllahir,
                                LTRIM(RTRIM(b.jalan)) AS jalan,
                                b.umurtahun,
                                b.umurbulan,
                                b.umurhari
                            FROM rd_reg a
                            LEFT JOIN Pasien b ON a.norm=b.norm
                            WHERE a.batal=0
                        ) a
                        LEFT JOIN (
                            SELECT 
                                LTRIM(RTRIM(a.noreg)) AS noreg,
                                LTRIM(RTRIM(b.nminstansi)) AS nminstansi
                            FROM rd_penjaminBayar a 
                            LEFT JOIN instansi b ON a.kdinstansi= b.kdinstansi
                        ) c ON a.noreg=c.noreg
                        WHERE a.noreg = ?
                    ";
                    break;

                default:
                    $sqlPasienRM = "SELECT 
                            LTRIM(RTRIM(a.noreg)) AS noreg,
                            LTRIM(RTRIM(a.norm)) AS norm,
                            LTRIM(RTRIM(a.nama)) AS nama,
                            LTRIM(RTRIM(a.kdseks)) AS kdseks,
                            CAST(a.tgllahir AS DATE) as tgllahir,
                            LTRIM(RTRIM(b.jalan)) AS jalan,
                            b.umurtahun,
                            b.umurbulan,
                            b.umurhari,
                            c.nminstansi
                        FROM md_reg a
                        LEFT JOIN Pasien b on a.norm = b.norm
                        LEFT JOIN instansi c on a.kdinstansi = c.kdinstansi
                        WHERE a.noreg = ?
                        AND a.batal = 0
                    ";
                    break;
            }

            $resPasienRM = $this->medinPro->query($sqlPasienRM, [$noreg])->getRowArray();

            if (!$resPasienRM) {
                return $this->response->setStatusCode(404)->setBody('Data pasien tidak ditemukan.');
            }

            $norm     = $resPasienRM['norm'];
            $nama     = $resPasienRM['nama'];
            $tgllahir = $resPasienRM['tgllahir'];
            $alamat   = $resPasienRM['jalan'];

            $penjamin = (empty(trim((string) $resPasienRM['nminstansi'])))
                ? 'Pribadi'
                : $resPasienRM['nminstansi'];

            $umur = $resPasienRM['kdseks'] . " / "
                . $resPasienRM['umurtahun'] . "Th "
                . $resPasienRM['umurbulan'] . "Bl "
                . $resPasienRM['umurhari'] . "Hr ";

            // ==== Generate PDF ====
            $pdf = new FPDF();
            $pdf->SetLeftMargin(7);
            $pdf->SetTopMargin(3);
            $pdf->AddPage();

            for ($i = 0; $i < $jumlahLabel; $i++) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 4.1, $norm);
                $pdf->Cell(50, 4.1, $noreg);
                $pdf->Cell(30, 4.1, $norm);
                $pdf->Cell(50, 4.1, $noreg);
                $pdf->Ln();

                $pdf->Cell(80, 4.1, $nama);
                $pdf->Cell(80, 4.1, $nama);
                $pdf->Ln();

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 4.1, $tgllahir);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(50, 4.1, $umur);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(30, 4.1, $tgllahir);
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(50, 4.1, $umur);
                $pdf->Ln();

                $pdf->Cell(80, 4.1, $penjamin);
                $pdf->Cell(80, 4.1, $penjamin);
                $pdf->Ln();

                $pdf->Cell(80, 4.1, $alamat);
                $pdf->Cell(80, 4.1, $alamat);
                $pdf->Ln();

                $pdf->Cell(160, 6, "");
                $pdf->Ln();
            }

            $pdf->Output('I', 'label_' . $noreg . '.pdf'); 
            exit; 
        }

        public function cetakBarcode($noreg) {
            $sqlPasienRM = "SELECT  
                    egdt.norm, 
                    egdt.nama_pasien,
                    egdt.kdseks, 
                    egdt.jamreg, 
                    egdt.umurhari,
                    egdt.umurbulan,
                    egdt.umurtahun,
                    egdt.nama_dokter, 
                    egdt.tgl_reg, 
                    egdt.nminstansi,
                    CONVERT(VARCHAR(11),egdt.tgl_lahir,103) as tgllahir,
                    egdt.jalan
                FROM EMR_GET_DATA_KUNJUNGAN egdt
                WHERE 1 = 1
                AND egdt.noreg = ?
            ";
            $resPasienRM = $this->EMRPro->query($sqlPasienRM, [$noreg])->getRowArray();

            if (!$resPasienRM) {
                return $this->response->setStatusCode(404)->setBody('Data pasien tidak ditemukan.');
            }

            $norm           = $resPasienRM['norm'];
            $tglpemeriksaan = $resPasienRM['tgl_reg'];
            $namaPasien     = $resPasienRM['nama_pasien'];
            $tgllahir       = $resPasienRM['tgllahir'];
            $alamat         = $resPasienRM['jalan'];

            $penjamin = (empty(trim((string) $resPasienRM['nminstansi'])))
                ? 'Pribadi'
                : $resPasienRM['nminstansi'];

            $umur = $resPasienRM['kdseks'] . " / "
                . $resPasienRM['umurtahun'] . "Th "
                . $resPasienRM['umurbulan'] . "Bl "
                . $resPasienRM['umurhari'] . "Hr ";

            // ==== Generate gambar barcode Code128 ====
            $generator   = new BarcodeGeneratorPNG();
            $barcodePng  = $generator->getBarcode($norm, $generator::TYPE_CODE_128);
    
            $tempDir  = WRITEPATH . 'uploads/barcode/';
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $tempFile = $tempDir . 'barcode_' . $norm . '_' . uniqid() . '.png';
            file_put_contents($tempFile, $barcodePng);

            // ==== Generate PDF ====
            $pdf = new FPDF();
            $pdf->SetLeftMargin(7);
            $pdf->SetTopMargin(0);
            $pdf->AddPage();

            $pdf->SetFont('Arial', 'B', 10);

            $pdf->SetXY(5, 5);
            $pdf->Cell(30, 4.1, $norm);
            $pdf->Cell(50, 4.1, $tgllahir);
            $pdf->Ln();

            $pdf->SetXY(5, 10);
            $pdf->Cell(80, 4.1, $namaPasien); 

            $pdf->Image($tempFile, 5, 15, 50, 9, 'PNG');
            $pdf->Ln(10); 

            $pdf->Output('I', 'barcode_' . $noreg . '.pdf');
    
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            exit;
        }
    // ==== RM DASHBOARD ====

    // ==== RM LOG BERKAS ====
        public function logBerkas() {
            $data = [
                'title' => 'RM | Catatan Berkas'
            ]; 
            return view('rm\Views\logBerkas', $data);
        } 

        public function dataLogBerkas(){
            $tglMulai   = $this->request->getPost('tglMulai');
            $tglSelesai = $this->request->getPost('tglSelesai');
            $jenis      = $this->request->getPost('jenis');
            $peminjam   = $this->request->getPost('peminjam');
            $usernik    = session()->get('usernik');

            $sqlUpdateOverdue = "UPDATE rm_log_berkas
                SET status = 'belumKembali'
                WHERE status = 'dipinjam'
                    AND time_terima IS NULL
                    AND time_pinjam < DATE_SUB(NOW(), INTERVAL 1 DAY)
            ";
            $this->ERP->query($sqlUpdateOverdue);

            $sqlPasienRM = "SELECT
                    a.id,
                    a.usernik,
                    a.norm,
                    a.nama_pasien,
                    a.peminjam,
                    a.status,
                    a.time_pinjam,
                    a.time_terima, 
                    a.created_at
                FROM rm_log_berkas a
                WHERE 1=1
            ";
            $params = [];

            if (!empty($tglMulai)) {
                $sqlPasienRM .= " AND a.created_at >= ?";
                $params[] = $tglMulai;
            }

            if (!empty($tglSelesai)) {
                $tglSelesaiPlus = date('Y-m-d', strtotime($tglSelesai . ' +1 day'));
                $sqlPasienRM .= " AND a.created_at < ?";
                $params[] = $tglSelesaiPlus;
            }

            if (!empty($jenis)) {
                $sqlPasienRM .= " AND a.status = ?";
                $params[] = $jenis;
            }

            if (!empty($peminjam)) {
                $sqlPasienRM .= " AND a.peminjam = ?";
                $params[] = $peminjam;
            }

            $resPasienRM = $this->ERP->query($sqlPasienRM, $params)->getResult();

            $mapPeminjam = [
                'Poli1A' => 'Poli 1 Gedung A',
                'Poli2A' => 'Poli 2 Gedung A',
                'Poli1B' => 'Poli 1 Gedung B',
            ];

            foreach ($resPasienRM as $row) {
                $row->NamaPeminjam = $mapPeminjam[$row->peminjam] ?? '-';
            }

            return $this->response->setJSON([
                'data' => $resPasienRM,
            ]);
        }

        public function saveLogBerkas() {
            $norm     = $this->request->getPost('norm');
            $tglKunj  = $this->request->getPost('tglKunj') ?: date('Y-m-d');
            $peminjam = $this->request->getPost('peminjam');
            $usernik  = session()->get('usernik');

            if (empty($norm) || empty($peminjam)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'No RM dan Peminjam wajib diisi',
                ]);
            }

            try {
                $sqlKunj = "SELECT
                        a.norm,
                        a.noreg,
                        a.nama_pasien,
                        a.nama_dokter
                    FROM EMR_GET_DATA_KUNJUNGAN a
                    WHERE a.batal = '0'
                        AND a.kdtipevisit = '01'
                        AND a.norm = ?
                        AND a.tgl_reg = ?
                ";
                $resKunj = $this->EMRPro->query($sqlKunj, [$norm, $tglKunj])->getRowArray();

                if (!$resKunj) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Maaf Pasien ini tidak memiliki Kunjungan hari ini',
                    ]);
                }

                $namaPasien = $resKunj['nama_pasien'];
                $timeNow    = date('Y-m-d H:i:s');

                $insertBerkas = [
                    'usernik'     => $usernik,
                    'norm'        => $norm,
                    'nama_pasien' => $namaPasien,
                    'peminjam'    => $peminjam,
                    'status'      => 'dipinjam',
                    'time_pinjam' => $timeNow,
                    'created_at'  => $timeNow,
                ];

                $this->ERP->table('rm_log_berkas')->insert($insertBerkas);

                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => 'Data peminjaman berkas berhasil disimpan',
                    'csrf_hash' => csrf_hash(),
                ]);

            } catch (\Throwable $e) { 
                log_message('error', 'saveLogBerkas: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine()); 
                $debugInfo = (ENVIRONMENT === 'development')
                    ? ' | Detail: ' . $e->getMessage() . ' (Line ' . $e->getLine() . ' di ' . basename($e->getFile()) . ')'
                    : '';

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan sistem, silakan coba lagi' . $debugInfo,
                ]);
            }
        }

        public function terimaLogBerkas() {
            $idBerkas = $this->request->getPost('idBerkas');
            $usernik  = session()->get('usernik');
            $timeNow  = date('Y-m-d H:i:s');

            if (empty($idBerkas)) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'ID berkas tidak valid',
                ]);
            }

            try {
                $builder = $this->ERP->table('rm_log_berkas');
                $builder->where('id', $idBerkas);
                $builder->whereIn('status', ['dipinjam', 'belumKembali']); // hanya boleh terima kalau belum diterima
                $builder->update([
                    'status'      => 'diterima',
                    'time_terima' => $timeNow,
                    'updated_at'  => $timeNow,
                    'updated_by'  => $usernik,
                ]);

                if ($this->ERP->affectedRows() === 0) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => 'Data tidak ditemukan atau sudah diterima sebelumnya',
                    ]);
                }

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Berkas berhasil ditandai diterima',
                ]);

            } catch (\Throwable $e) {
                log_message('error', 'terimaLogBerkas: ' . $e->getMessage() . ' | Line: ' . $e->getLine());

                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Terjadi kesalahan sistem, silakan coba lagi',
                ]);
            }
        } 

        private function formatTanggal(?string $tanggal): ?string {
            if (empty($tanggal)) {
                return null;
            }

            try {
                $date = new \DateTime($tanggal);
                return $date->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
    // ==== RM LOG BERKAS ====
}