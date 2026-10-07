<?php

namespace gizi\Controllers;

use App\Controllers\BaseController;
use FPDF;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

class giziController extends BaseController {

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
        // header('Content-Type: application/json');
        // echo json_encode('dskvnlkdnvlkdn');
        // exit;

        $data = [
            'title' => 'Gizi | Dashboard'
        ]; 
        return view('gizi\Views\dashboard', $data);
    } 

    public function dataPasien(){
        $dateNow  = date('Y-m-d');
        $dateNext = date('Y-m-d', strtotime('+1 day')); 

        $sqlPasienGizi  = "SELECT 
                noreg AS noReg,
                LTRIM(RTRIM(a.norm)) AS noRM,
                a.pesan AS PesanPasien,
                CONVERT(VARCHAR(11), b.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(b.nama)) AS namaPasien,
                b.kdseks AS kdSeks,
                c.nmruang AS namaRuang,
                LTRIM(RTRIM(a.nott)) AS kdBed
            FROM ri_reg a
            LEFT JOIN Pasien b ON a.norm = b.norm
            LEFT JOIN ruang c ON a.kdruang = c.kdruang
            WHERE a.keluar = 0 
                AND a.batal = 0

            UNION ALL 

            SELECT 
                rdd.noreg AS noReg, 
                LTRIM(RTRIM(rdd.norm)) AS noRM, 
                rdd.Pesan AS PesanPasien, 
                CONVERT(VARCHAR(11), pas.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(pas.nama)) AS namaPasien, 
                pas.kdseks AS kdSeks, 
                'INSTALASI GAWAT DARURAT' AS namaRuang, 
                '-' AS kdBed
            FROM rd_reg rdd 
            LEFT JOIN pasien pas ON pas.norm = rdd.norm
            WHERE rdd.batal = '0'
                AND rdd.tglinsert >= ?
                AND rdd.tglinsert <  ?
                AND rdd.kdpengirim = '17' 

            ORDER BY namaRuang ASC, kdBed ASC
        ";
        $resPasienGizi  = $this->medinPro->query($sqlPasienGizi, [$dateNow, $dateNext]) ->getResult();
        $noRegList      = array_column($resPasienGizi, 'noReg'); 
        $dietMap        = [];

        if (!empty($noRegList)) {
            $placeholders = implode(',', array_fill(0, count($noRegList), '?')); 
            $sqlDiet = "SELECT
                    noreg, 
                    data_gizi
                FROM EMR_DATA_DIIT_GIZI
                WHERE noreg IN ($placeholders)
                    AND status = 'BARU'
                ORDER BY tgl_insert DESC
            "; 
            $resDiet = $this->EMRPro->query($sqlDiet, $noRegList)->getResultArray();

            foreach ($resDiet as $d) {
                $dietMap[$d['noreg']] = $d['data_gizi'];
            }
        }
    
        foreach ($resPasienGizi as $row) {
            $row->diet = $dietMap[$row->noReg] ?? '-';
        }

        return $this->response->setJSON([
            'data' => $resPasienGizi,
        ]);
    }

    public function updateDiet() {
        $noReg      = $this->request->getPost('noReg');
        $diet       = $this->request->getPost('diet');
        $userid     = session()->get('userid');   
        $timeNow    = date('Y-m-d H:i:s');
 
        $updateDiet = [
            'status'      => 'REVISI',
            'tgl_update'  => $timeNow,
            'user_update' => $userid
        ];
        $this->EMRPro->table('EMR_DATA_DIIT_GIZI')
            ->where('noreg', $noReg)
            ->update($updateDiet);
 
        $insertDiet = [
            'noreg'         => $noReg,
            'data_gizi'     => $diet,
            'status'        => 'BARU', 
            'tgl_insert'    => $timeNow, 
            'user_insert'   => $userid,  
        ];
        $this->EMRPro->table('EMR_DATA_DIIT_GIZI')->insert($insertDiet);
 
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Diet berhasil diperbarui'
        ]);
    }

    public function cetakLabelGizi() {  
        $dateNow  = date('Y-m-d'); 
        $dateNext = date('Y-m-d', strtotime('+1 day')); 

        $sqlPasienGizi  = "SELECT 
                noreg AS noReg,
                LTRIM(RTRIM(a.norm)) AS noRM,
                a.pesan AS PesanPasien,
                CONVERT(VARCHAR(11), b.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(b.nama)) AS namaPasien,
                b.kdseks AS kdSeks,
                c.nmruang AS namaRuang,
                LTRIM(RTRIM(a.nott)) AS kdBed,
                b.umurtahun AS umurTahun
            FROM ri_reg a
            LEFT JOIN Pasien b ON a.norm = b.norm
            LEFT JOIN ruang c ON a.kdruang = c.kdruang
            WHERE a.keluar = 0 
                AND a.batal = 0

            UNION ALL 

            SELECT 
                rdd.noreg AS noReg, 
                LTRIM(RTRIM(rdd.norm)) AS noRM, 
                rdd.Pesan AS PesanPasien, 
                CONVERT(VARCHAR(11), pas.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(pas.nama)) AS namaPasien, 
                pas.kdseks AS kdSeks, 
                'INSTALASI GAWAT DARURAT' AS namaRuang, 
                '-' AS kdBed,
                pas.umurtahun AS umurTahun
            FROM rd_reg rdd 
            LEFT JOIN pasien pas ON pas.norm = rdd.norm
            WHERE rdd.batal = 0 
                AND rdd.tglinsert >= ?
                AND rdd.tglinsert <  ?
                AND rdd.kdpengirim = '17' 
            ORDER BY namaRuang ASC, kdBed ASC
        "; 
        $resPasienGizi = $this->medinPro->query($sqlPasienGizi, [$dateNow, $dateNext])->getResult();
 
        if (empty($resPasienGizi)) {
            return $this->response->setBody('Tidak ada data pasien untuk dicetak hari ini.')->setStatusCode(404);
        }

        $pdf = new \FPDF('L', 'mm', array(30, 50));
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->SetLeftMargin(4);
        $pdf->SetTopMargin(2);
        $pdf->SetAutoPageBreak(false);

        foreach ($resPasienGizi as $data) {
            $sqlGizi    = "SELECT a.* 
                FROM EMR_DATA_DIIT_GIZI a 
                WHERE a.noreg = ? 
                    AND a.status ='BARU'
            ";
            $resGizi    = $this->EMRPro->query($sqlGizi, [$data->noReg]);

            $pdf->AddPage();
            $pdf->SetXY(4, 2); 
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10, 2.2, $data->noRM . "       " . $data->kdBed, 0, 0, 'L');
            $pdf->Ln(4); 
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->MultiCell(48, 2.2, $data->namaPasien, '', 'L');
            $pdf->Ln(1); 
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(10, 2.2, $data->kdSeks . " | " . $data->tglLahir . " | " . $data->umurTahun . " tahun ", 0, 0, 'L');
            $pdf->Ln(4);

            if ($resGizi->getNumRows() > 0) {
                $rowGizi = $resGizi->getRow();
                $pdf->MultiCell(45, 2.6, $rowGizi->data_gizi, '', 'L');
            } else {
                $pdf->MultiCell(45, 2.6, ' ', '', 'L');
            } 

            $local_time = new \DateTime(); 
            $jam = "";
            
            if ($local_time < new \DateTime('9am')) {
                $jam = "09.00";
            } elseif ($local_time < new \DateTime('2pm')) {
                $jam = "14.00";
            } elseif ($local_time < new \DateTime('6pm')) {
                $jam = "18.00";
            } else {
                $jam = "09.00";
            }

            $pdf->Ln(1);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->MultiCell(45, 2.6, "HARAP DIKONSUMSI SEBELUM PUKUL " . $jam, '', 'L');
        }

        $this->response->setContentType('application/pdf');
        $pdf->Output('I', 'Label_Gizi.pdf');
        exit; 
    }

    public function cetakPenunggu() {  
 
        $sqlPenunggu  = "SELECT 
                LTRIM(RTRIM(a.noreg)) AS NOREG,
                LTRIM(RTRIM(a.norm)) AS NORM,
                CONVERT(VARCHAR(11), b.tgllahir, 106) AS TGLLAHIR,
                LTRIM(RTRIM(b.nama)) AS NAMA_PASIEN,
                b.kdseks AS JK,
                c.nmruang AS NAMA_RUANG,
                a.nott AS NO_BED 
            FROM ri_reg a
            LEFT JOIN Pasien b ON a.norm = b.norm
            LEFT JOIN ruang c ON a.kdruang = c.kdruang
            WHERE a.keluar = 0 AND a.batal = 0
                AND a.kdruang IN ('RCR', 'RCRV', 'RCS','RCR5','RCR6','RPS')
        "; 
        $resPenunggu = $this->medinPro->query($sqlPenunggu)->getResult(); 
 
        if (empty($resPenunggu)) {
            return $this->response->setBody('Tidak ada data penunggu untuk dicetak saat ini.')->setStatusCode(404);
        }
 
        $pdf = new \FPDF('L', 'mm', array(30, 50));
        $pdf->SetFont('Arial', 'B', 5);
        $pdf->SetLeftMargin(4);
        $pdf->SetTopMargin(2);
        $pdf->SetAutoPageBreak(false);

        foreach ($resPenunggu as $data) {
            $pdf->AddPage();
            $pdf->SetXY(4, 2);
 
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->Cell(21, 3, $data->NORM, 0, 0, 'L');
            $pdf->Cell(21, 3, $data->NO_BED, 0, 1, 'R');
 
            $pdf->SetFont('Arial', 'B', 7.5);
            $pdf->MultiCell(42, 3, $data->NAMA_PASIEN, 0, 'L');
 
            $pdf->Ln(0.5); 
            $pdf->SetFont('Arial', 'BI', 7.5); 
            $pdf->Cell(42, 3, "(Penunggu Pasien)", 0, 1, 'L');
 
            $local_time = new \DateTime(); 
            $jam = "";
            if ($local_time < new \DateTime('9am')) {
                $jam = "09.00";
            } elseif ($local_time < new \DateTime('2pm')) {
                $jam = "14.00";
            } elseif ($local_time < new \DateTime('6pm')) {
                $jam = "18.00";
            } else {
                $jam = "09.00";
            }
 
            $pdf->Ln(1.5);
            $pdf->SetFont('Arial', 'BI', 6);
            $pdf->Cell(42, 3, "Harap dikonsumsi sebelum pukul " . $jam, 0, 1, 'L');
        }
 
        $this->response->setContentType('application/pdf');
        $pdf->Output('I', 'Label_Penunggu.pdf');
        exit;
    }

    public function cetakForm() { 
        $dateNow  = date('Y-m-d');
        $dateNext = date('Y-m-d', strtotime('+1 day')); 

        $sqlPasienGizi  = "SELECT 
                noreg AS noReg,
                LTRIM(RTRIM(a.norm)) AS noRM,
                a.pesan AS PesanPasien,
                CONVERT(VARCHAR(11), b.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(b.nama)) AS namaPasien,
                b.kdseks AS kdSeks,
                c.nmruang AS namaRuang,
                LTRIM(RTRIM(a.nott)) AS kdBed
            FROM ri_reg a
            LEFT JOIN Pasien b ON a.norm = b.norm
            LEFT JOIN ruang c ON a.kdruang = c.kdruang
            WHERE a.keluar = 0 
                AND a.batal = 0

            UNION ALL 

            SELECT 
                rdd.noreg AS noReg, 
                LTRIM(RTRIM(rdd.norm)) AS noRM, 
                rdd.Pesan AS PesanPasien, 
                CONVERT(VARCHAR(11), pas.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(pas.nama)) AS namaPasien, 
                pas.kdseks AS kdSeks, 
                'INSTALASI GAWAT DARURAT' AS namaRuang, 
                '-' AS kdBed
            FROM rd_reg rdd 
            LEFT JOIN pasien pas ON pas.norm = rdd.norm
            WHERE rdd.batal = 0 
                AND rdd.tglinsert >= ?
                AND rdd.tglinsert <  ?
                AND rdd.kdpengirim = '17' 
            ORDER BY namaRuang ASC, kdBed ASC
        ";
        $resPasienGizi  = $this->medinPro->query($sqlPasienGizi, [$dateNow, $dateNext])->getResult();

        if (empty($resPasienGizi)) {
            return $this->response->setBody('Tidak ada data pasien untuk dicetak hari ini.')->setStatusCode(404);
        }
 
        foreach ($resPasienGizi as &$pasien) {
            $sqlGizi = "SELECT data_gizi FROM EMR_DATA_DIIT_GIZI WHERE noreg = ? AND status ='BARU'";
            $resGizi = $this->EMRPro->query($sqlGizi, [$pasien->noReg])->getRow(); 
            $pasien->data_gizi = $resGizi ? $resGizi->data_gizi : '';
        }
 
        $data = [
            'title'         => 'Form Gizi Pasien',
            'dateNow'       => $dateNow,
            'pasienGizi'    => $resPasienGizi
        ];
 
        $options = new Options();
        $options->set('isRemoteEnabled', true); 
        $dompdf = new Dompdf($options);
 
        $html = view('gizi\Views\cetakForm', $data); 
        $dompdf->loadHtml($html);
 
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
 
        $dompdf->stream('Form_Gizi_Pasien.pdf', ["Attachment" => false]);
        exit;
    }

    public function cetakExcel()  {
        date_default_timezone_set('Asia/Jakarta');
        $dateNow  = date('Y-m-d');
        $dateNext = date('Y-m-d', strtotime('+1 day')); 
 
        $sqlPasienGizi  = "SELECT 
                noreg AS noReg,
                LTRIM(RTRIM(a.norm)) AS noRM,
                a.pesan AS PesanPasien,
                CONVERT(VARCHAR(11), b.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(b.nama)) AS namaPasien,
                b.kdseks AS kdSeks,
                c.nmruang AS namaRuang,
                LTRIM(RTRIM(a.nott)) AS kdBed
            FROM ri_reg a
            LEFT JOIN Pasien b ON a.norm = b.norm
            LEFT JOIN ruang c ON a.kdruang = c.kdruang
            WHERE a.keluar = 0 
                AND a.batal = 0

            UNION ALL 

            SELECT 
                rdd.noreg AS noReg, 
                LTRIM(RTRIM(rdd.norm)) AS noRM, 
                rdd.Pesan AS PesanPasien, 
                CONVERT(VARCHAR(11), pas.tgllahir, 106) AS tglLahir,
                LTRIM(RTRIM(pas.nama)) AS namaPasien, 
                pas.kdseks AS kdSeks, 
                'INSTALASI GAWAT DARURAT' AS namaRuang, 
                '-' AS kdBed
            FROM rd_reg rdd 
            LEFT JOIN pasien pas ON pas.norm = rdd.norm
            WHERE rdd.batal = 0 
                AND rdd.tglinsert >= ?
                AND rdd.tglinsert <  ?
                AND rdd.kdpengirim = '17' 
            ORDER BY namaRuang ASC, kdBed ASC
        ";
        $resPasienGizi  = $this->medinPro->query($sqlPasienGizi, [$dateNow, $dateNext])->getResult();

        if (empty($resPasienGizi)) {
            return $this->response->setBody('Tidak ada data pasien untuk dicetak hari ini.')->setStatusCode(404);
        }
 
        foreach ($resPasienGizi as &$pasien) {
            $sqlGizi = "SELECT data_gizi FROM EMR_DATA_DIIT_GIZI WHERE noreg = ? AND status ='BARU'";
            $resGizi = $this->EMRPro->query($sqlGizi, [$pasien->noReg])->getRow(); 
            $pasien->data_gizi = $resGizi ? $resGizi->data_gizi : '';
        }
 
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
 
        $sheet->setCellValue('A1', 'Ruang');
        $sheet->setCellValue('B1', 'Bed');
        $sheet->setCellValue('C1', 'Tgl Lahir');
        $sheet->setCellValue('D1', 'Nama Pasien');
        $sheet->setCellValue('E1', 'JK');
        $sheet->setCellValue('F1', 'Diet / Data Gizi');
        $sheet->setCellValue('G1', 'TTD 1');
        $sheet->setCellValue('H1', 'TTD 2'); 
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
 
        $row = 2;
        foreach ($resPasienGizi as $data) {
            $sheet->setCellValue('A' . $row, $data->namaRuang);
            $sheet->setCellValue('B' . $row, $data->kdBed);
            $sheet->setCellValue('C' . $row, $data->tglLahir);
            $sheet->setCellValue('D' . $row, $data->namaPasien);
            $sheet->setCellValue('E' . $row, $data->kdSeks);
            $sheet->setCellValue('F' . $row, $data->data_gizi); 
            $row++;
        }

        $lastRow = $row - 1; 
        
        $styleBorder = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], 
                ],
            ],
        ];
         
        $sheet->getStyle('A1:H' . $lastRow)->applyFromArray($styleBorder);
 
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
 
        $writer = new Xlsx($spreadsheet);
        $filename = 'Form_Gizi_Pasien_' . date('Ymd_His') . '.xlsx';
 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
 
        $writer->save('php://output');
        exit;
    }
}