<?php

namespace keuangan\Controllers;

use App\Controllers\BaseController;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Picqer\Barcode\BarcodeGeneratorPNG;

class keuanganController extends BaseController {

    protected $default;
    protected $medinPro;
    protected $EMRPro;
    protected $ERP; 
    protected $custom;

    public function __construct() { 
        $this->default  = \Config\Database::connect('default'); 
        $this->medinPro = \Config\Database::connect('medinPro'); 
        $this->EMRPro   = \Config\Database::connect('EMRPro');  
        $this->custom   = \Config\Database::connect('custom'); 
        $this->ERP      = \Config\Database::connect();
    }

    public function dashboard() {   
        // header('Content-Type: application/json');
        // echo json_encode('dskvnlkdnvlkdn');
        // exit;

        $data = [
            'title' => 'Keuangan | Dashboard'
        ]; 
        return view('keuangan\Views\dashboard', $data);
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
                    a.noreg AS noReg,
                    a.norm AS noRM,
                    a.nama_pasien AS namaPasien,
                    a.noSEPBPJS,
                    a.nminstansi
                FROM EMR_GET_DATA_KUNJUNGAN a
                WHERE a.tgl_reg >= ?
                AND a.tgl_reg <  ?
                AND a.batal = '0'";

            if ($jenisReg !== null) {
                $sqlPasienRM .= " AND a.noreg LIKE ?";
                $params[] = $jenisReg;
            }

            $sqlPasienRM .= " GROUP BY
                    a.noreg, a.norm, a.nama_pasien, a.noSEPBPJS, a.nminstansi
                ORDER BY a.noreg DESC";

            $resPasienRM = $this->EMRPro->query($sqlPasienRM, $params)->getResult();
        } else {
            $sqlPasienRM = "SELECT 
                    a.noreg AS noReg, 
                    a.norm AS noRM, 
                    a.nama_pasien AS namaPasien
                FROM (
                    SELECT 
                        a.noreg, 
                        a.norm, 
                        a.nama AS nama_pasien,
                        a.kdseks, 
                        a.jamtrans AS jamreg, 
                        b.nama AS nama_dokter, 
                        CONVERT(VARCHAR(11), a.tgltrans, 103) AS tgl_reg,
                        ins.nminstansi,
                        CONVERT(VARCHAR(11), a.tgllahir, 103) AS tgllahir,
                        a.jalan
                    FROM md_reg a
                    LEFT JOIN instansi ins ON a.kdinstansi = ins.kdinstansi
                    LEFT JOIN medis b ON a.kddokter = b.kode
                    WHERE a.tgltrans >= ?
                    AND a.tgltrans <  ?
                    AND a.batal = '0' 
                    AND a.noreg LIKE ?
                    GROUP BY 
                        a.noreg, a.norm, a.nama, a.kdseks,
                        a.jamtrans, b.nama, ins.nminstansi, 
                        a.tgltrans, a.tgllahir, a.jalan
                ) a
                ORDER BY a.noreg DESC";

            $params[] = $jenisReg; 
            
            $resPasienRM = $this->medinPro->query($sqlPasienRM, $params)->getResult();
        }

        return $this->response->setJSON([
            'data' => $resPasienRM,
        ]);
    }

    public function createKwitansi(){
        $noreg      = trim((string) $this->request->getPost('noregPx'));
        $namaPasien = trim((string) $this->request->getPost('namaPx'));
        $nominal    = (int) preg_replace('/\D/', '', (string) $this->request->getPost('nominal'));
        $keterangan = trim((string) $this->request->getPost('keterangan'));
        $usernik    = session()->get('usernik');

        if ($noreg === '' || $namaPasien === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false, 'message' => 'No Reg dan Nama Pasien wajib diisi.'
            ]);
        }
        if ($nominal <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false, 'message' => 'Nominal harus lebih dari 0.'
            ]);
        }

        $db     = $this->ERP;
        $prefix = 'KWM' . date('Ymd');

        $last = $db->table('keuangan_kwitansi')
            ->select('no_kwitansi')
            ->like('no_kwitansi', $prefix, 'after')
            ->orderBy('no_kwitansi', 'DESC')
            ->limit(1)
            ->get();

        if ($last === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false, 'message' => 'SELECT gagal', 'error' => $db->error()
            ]);
        }

        $row        = $last->getRowArray();
        $urut       = $row ? ((int) substr($row['no_kwitansi'], -4)) + 1 : 1;
        $noKwitansi = $prefix . str_pad($urut, 4, '0', STR_PAD_LEFT);

        $ok = $db->table('keuangan_kwitansi')->insert([
            'no_kwitansi'   => $noKwitansi,
            'noreg'         => $noreg,
            'nama_pasien'   => $namaPasien,
            'price'         => $nominal,
            'keterangan'    => $keterangan,
            'created_by'    => $usernik,
        ]);

        if ($ok === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => false, 'message' => 'INSERT gagal', 'error' => $db->error()
            ]);
        }

        return $this->response->setJSON([
            'status'      => true,
            'message'     => 'Kwitansi berhasil disimpan.',
            'id'          => $db->insertID(),
            'no_kwitansi' => $noKwitansi,
        ]);
    }

    public function printKwitansi($noKwitansi = ''){

        $sqlKwitansi = "SELECT
                a.*,
                b.nama_karyawan
            FROM keuangan_kwitansi a
            INNER JOIN _master_karyawan b ON b.usernik = a.created_by
            WHERE a.no_kwitansi = ?
                AND a.is_delete = '0'
        ";
        $resKwitansi = $this->ERP->query($sqlKwitansi,[$noKwitansi])->getRowArray();


        if (!$resKwitansi) {
            return $this->response->setStatusCode(404)->setBody('Data kwitansi tidak ditemukan.');
        }
         helper('terbilang');

        $html = view('keuangan\Views\printKwitansi', ['data' => $resKwitansi]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $resKwitansi['no_kwitansi'] . '.pdf"')
            ->setBody($dompdf->output());
    }

    public function prosesLabIGD($noreg = ''){
        $sqlLab = "SELECT
                noreg,
                TRIM(nolab) AS nolab,
                posting
            FROM lb_trnshdUGD
            WHERE noreg = ?
            ORDER BY nolab ASC
        ";

        $resLab = $this->medinPro->query($sqlLab, [$noreg])->getRowArray();

        if (!$resLab) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'success' => false,
                    'message' => 'Data lab untuk No Reg ini tidak ditemukan.'
                ]);
        }
    
        $resLab['statusLab'] = ((int) $resLab['posting'] === 0) ? 'Open' : 'Closed';

        return $this->response->setJSON([
            'success' => true,
            'data'    => $resLab
        ]);
    }

    public function openLabIGD() {
        $userid  = session()->get('userid');
        $noreg   = trim((string) $this->request->getPost('noregigd'));
        $nolab   = trim((string) $this->request->getPost('nolabigd'));
        $timeNow = date('Y-m-d H:i:s');

        if ($noreg === '' || $nolab === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No Reg dan No Lab wajib diisi.'
            ]);
        }

        $sqlCekLab = "SELECT
                nolab,
                posting
            FROM lb_trnshdUGD
            WHERE noreg = ?
            AND nolab = ?
        ";
        $resCekLab = $this->medinPro->query($sqlCekLab, [$noreg, $nolab])->getRowArray();

        if (!$resCekLab) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data lab tidak ditemukan.'
            ]);
        }

        if ((int) $resCekLab['posting'] === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Lab sudah berstatus Open.'
            ]);
        }

        $sqlUpdate = "UPDATE lb_trnshdUGD SET
                posting = 0,
                lupdate = ?,
                updater = ?
            WHERE noreg = ? AND nolab = ?
        ";
        $resUpdate = $this->medinPro->query($sqlUpdate, [$timeNow, $userid, $noreg, $nolab]);

        if (!$resUpdate) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui data lab.'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lab IGD berhasil dibuka.'
        ]);
    }
}