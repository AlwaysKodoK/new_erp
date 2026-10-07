<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <style>
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 9pt; 
            margin: 0;
            padding: 0;
        }
        h3 {
            text-align: center; 
            font-size: 14pt;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 6px; 
            vertical-align: middle;
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
            text-align: center; 
            font-size: 10pt; 
        }
         
        .col-ruang { width: 17%; text-align: center; }
        .col-bed   { width: 7%; text-align: center; font-weight: bold; font-size: 11pt; }
        .col-tgl   { width: 11%; text-align: center; }
        .col-nama  { width: 23%; font-weight: bold; font-size: 10pt; }
        .col-jk    { width: 4%; text-align: center; }
        .col-diet  { width: 26%; font-weight: bold; }
        .col-ttd   { width: 6%; }
    </style>
</head>
<body>

    <h3>Form Instruksi Diet Gizi Pasien</h3>
    <h4 style="text-align: center;margin-top: -15px;">Tanggal Cetak : <?= esc($dateNow) ?></h4>

    <table>
        <thead>
            <tr>
                <th class="col-ruang">Ruang</th>
                <th class="col-bed">Bed</th>
                <th class="col-tgl">Tgl Lahir</th>
                <th class="col-nama">Nama Pasien</th>
                <th class="col-jk">JK</th>
                <th class="col-diet">Diet / Data Gizi</th>
                <th class="col-ttd">TTD 1</th>
                <th class="col-ttd">TTD 2</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pasienGizi as $row): ?>
            <tr>
                <td class="col-ruang"><?= esc($row->namaRuang) ?></td>
                <td class="col-bed"><?= esc($row->kdBed) ?></td>
                <td class="col-tgl"><?= esc($row->tglLahir) ?></td>
                <td class="col-nama"><?= esc($row->namaPasien) ?></td>
                <td class="col-jk"><?= esc($row->kdSeks) ?></td>
                <td class="col-diet"><?= esc($row->data_gizi) ?></td> 
                <td></td>
                <td></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>