<?= $this->extend('Layout\Views\template'); ?>
<?= $this->section('title'); ?><?= $title ?? 'RM | Catatan Berkas' ?><?= $this->endSection(); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="rmDashboard">  

    <?= view('Layout\Partials\cardFilter', [
        'filterId' => 'FilterRM',
        'title'    => 'Pengaturan',
        'fields'   => [
            ['type' => 'date', 'id' => 'filterTglMulai', 'label' => 'Tgl Awal', 'col' => 'col-md-2'],
            ['type' => 'date', 'id' => 'filterTglSelesai', 'label' => 'Tgl Akhir', 'col' => 'col-md-2'],
            [
                'type'    => 'select',
                'id'      => 'filterJenis',
                'label'   => 'Jenis Registrasi',
                'col'     => 'col-md-3',
                'value'   => 'RI',
                'options' => [ 
                    'RD' => 'Rawat Darurat',
                    'RJ' => 'Rawat Jalan',
                    'RI' => 'Rawat Inap',
                    'MD' => 'Penunjang',
                ],
            ],
            ['type' => 'button-group', 'col' => 'col-md-2'],
        ],
    ]) ?>

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tableRM',
        'title'   => null,
        'columns' => [
            ['label' => 'No Reg',  'width' => '15%'],
            ['label' => 'Pasien',  'width' => '20%'], 
            ['label' => 'Alamat', 'width' => '45%'],
            ['label' => 'Aksi',    'width' => '20%'],
        ],
    ]) ?>
</section>
 
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function () {
        const csrfName = "<?= csrf_token() ?>";
        const csrfHash = "<?= csrf_hash() ?>";

        const table = $('#tableRM').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?= route_to('rm.dataPasien') ?>",
                type: "POST",
                data: function (d) {
                    d[csrfName] = csrfHash;
                    d.tglMulai   = $('#filterTglMulai').val();
                    d.tglSelesai = $('#filterTglSelesai').val();
                    d.jenis      = $('#filterJenis').val();
                },
                dataSrc: "data"
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.noReg}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<b>${row.noRM} </b><br> ${row.namaPasien} <br> ${row.tglLahir}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.Alamat ?? '-'}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        const baseLabel = "<?= site_url('rm/cetakLabel') ?>";
                        const baseBarcode = "<?= site_url('rm/cetakBarcode') ?>";
                        return `
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ticket me-1"></i>Cetak Label
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="${baseLabel}/${row.noReg}/dua" target="_blank">
                                        <i class="fa-solid fa-ticket me-2"></i>2 Label</a></li>
                                    <li><a class="dropdown-item" href="${baseLabel}/${row.noReg}/empat" target="_blank">
                                        <i class="fa-solid fa-ticket me-2"></i>4 Label</a></li>
                                    <li><a class="dropdown-item" href="${baseLabel}/${row.noReg}/enam" target="_blank">
                                        <i class="fa-solid fa-ticket me-2"></i>6 Label</a></li>
                                    <li><a class="dropdown-item" href="${baseLabel}/${row.noReg}/delapan" target="_blank">
                                        <i class="fa-solid fa-ticket me-2"></i>8 Label</a></li>
                                </ul>
                            </div>

                            <a href="${baseBarcode}/${row.noReg}" target="_blank" 
                            class="btn btn-sm btn-primary ms-1" title="Cetak Barcode">
                                <i class="fa-solid fa-barcode me-2"></i> Barcode
                            </a>
                        `;
                    }
                },
            ]
        });
 
        const btnFilterOriginalHtml = $('#btnFilter').html();

        $('#btnFilter').on('click', function () {
            const $btn = $(this);
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Mencari...');

            table.ajax.reload(function () { 
                $btn.prop('disabled', false);
                $btn.html(btnFilterOriginalHtml);
            });
        });

        $('#btnReset').on('click', function () {
            $('#filterTglMulai').val('');
            $('#filterTglSelesai').val('');
            $('#filterJenis').val('');

            const $btn = $('#btnFilter');
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memuat...');

            table.ajax.reload(function () {
                $btn.prop('disabled', false);
                $btn.html(btnFilterOriginalHtml);
            });
        });
    });
</script>
<?= $this->endSection(); ?>