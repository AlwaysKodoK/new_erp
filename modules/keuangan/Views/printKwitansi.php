<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $this->renderSection('title') ?: 'ERP SIMRS' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>  
        :root {
            --bs-font-sans-serif: 'Poppins', sans-serif;
            --bs-body-font-family: 'Poppins', sans-serif;
            --font-white: white;
            --bg-dark: rgba(255, 255, 255, 0.04);
        } 
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: var(--bs-body-font-family); 
            padding: 1.5rem;
        }
        p{
            margin: 0;
            padding: 0;
            font-size: 14px;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <p>Rumah sakit royal surabaya</p>
    <p>Jalan Rungkut Industri I/I Surabaya</p>
    <p>Telp. 031 - 8484111,031 - 8476111</p>
    <p>No. NPWP : 02.007.933.1-615.000</p>

    <div style="text-align: center; margin-top: 1rem;">
        <h1>KWITANSI</h1>
        <h3>No. <?= esc($data['no_kwitansi']) ?></h3>
        <h4 style="text-align: right;">Status : Asli</h4>
    </div>
    <table>
        <tr>
            <td style="width: 20%;">Sudah Terima Dari </td>
            <td style="width: 5%; text-align: center;">:</td>
            <td style="width: 60%;"><?= esc($data['nama_pasien']) ?></td>
        </tr>
        <tr>
            <td>Nomor Registrasi </td>
            <td style="text-align: center;">:</td>
            <td><?= esc($data['noreg']) ?></td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td style="text-align: center;">:</td>
            <td><?= esc($data['keterangan']) ?></td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td style="text-align: center;">:</td>
            <td>Rp. <?= number_format((int) $data['price'], 0, ',', '.') ?>,-</td>
        </tr>
        <tr>
            <td>Terbilang</td>
            <td style="text-align: center;">:</td>
            <td><?= esc(ucfirst(trim(preg_replace('/\s+/', ' ', terbilang($data['price']))))) ?> rupiah</td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td style="text-align: center;">:</td>
            <td style="color: red;">( di bayar pada <?= esc($data['created_at']) ?> )</td>
        </tr>
        
    </table>

    <table style="width: 100%; margin-top: 2rem; border-collapse: collapse;">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <p style="font-size: 10px;">di cetak pada : <?= date('Y-m-d H:i:s') ?></p>
            </td>
            <td style="width: 40%; text-align: center; vertical-align: top;">
                <p>Surabaya, <?= tgl_indo($data['created_at']) ?></p>
                <p>Petugas</p>
                <p style="margin-top: 3rem;"><?= esc($data['nama_karyawan']) ?></p>
            </td>
        </tr>
    </table>
</body>
</html>