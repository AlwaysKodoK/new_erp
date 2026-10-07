<?= $this->extend('Layout\Views\template'); ?>
<?= $this->section('title'); ?><?= $title ?? 'RM | Catatan Berkas' ?><?= $this->endSection(); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="keuanganDashboard">   

    <?= view('Layout\Partials\cardFilter', [
        'filterId'  => 'FilterKeuangan',
        'title'     => 'Pengaturan',
        'isShow'    => 'show',
        'fields'    => [
            ['type' => 'date', 'id' => 'filterTglMulai', 'label' => 'Tgl Awal', 'col' => 'col-md-2'],
            ['type' => 'date', 'id' => 'filterTglSelesai', 'label' => 'Tgl Akhir', 'col' => 'col-md-2'],
            [
                'type'    => 'select',
                'id'      => 'filterJenis',
                'label'   => 'Jenis Registrasi',
                'col'     => 'col-md-2',
                'value'   => 'RD',
                'options' => [ 
                    'RD' => 'Rawat Darurat',
                    'RJ' => 'Rawat Jalan',
                    'RI' => 'Rawat Inap',
                    'MD' => 'Penunjang',
                ],
            ],
            ['type' => 'button-group', 'col' => 'col-md-3', 
                'extraButtons' => [
                [
                    'id'         => 'btnCetakKwitansi',
                    'label'      => 'Buat Kwitansi',
                    'icon'       => 'fa-solid fa-print',
                    'class'      => 'btn-success',
                    'extraClass' => 'btnCetakKwitansi', 
                    'modal'      => 'modalCetakKwitansi',
                ],
            ]],
            
        ],
    ]) ?>

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tableKeuangan',
        'title'   => null,
        'columns' => [
            ['label' => 'No RM',   'width' => '10%'],
            ['label' => 'Pasien',  'width' => '20%'],
            ['label' => 'No Reg',  'width' => '10%'],
            ['label' => 'No SEP',  'width' => '15%'],
            ['label' => 'Instansi',  'width' => '15%'],
            ['label' => 'Aksi',    'width' => '20%'],
        ],
    ]) ?>
</section>


<div class="modal fade modalDark" id="modalCetakKwitansi" tabindex="-1" aria-labelledby="modalCetakKwitansiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCetakKwitansiLabel">
                    <i class="fa-solid fa-print me-2"></i> Cetak Kwitansi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formCetakKwitansi">
                <div class="modal-body">
                    <div class="row g-3"> 
                        <input type="hidden" name="jenis" id="jenisKwitansi" value="manual">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> No Reg </label>
                            <input type="text" name="noregPx" id="noregPx" class="form-control form-control-sm px-3 py-2 fw-bold" required>
                        </div> 
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> nama pasien </label>
                            <input type="text" name="namaPx" id="namaPx" class="form-control form-control-sm px-3 py-2 fw-bold" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> Nominal </label>
                            <input type="text" name="nominal" id="nominal"
                                class="form-control form-control-sm px-3 py-2 fw-bold"
                                inputmode="numeric" autocomplete="off" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">Keterangan</label>
                            <textarea name="keterangan" class="form-control form-control-sm px-3 py-2" rows="3" placeholder="Masukkan keterangan"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm px-3 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-x me-2"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-save me-2"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade modalDark" id="modalProsesLabIGD" tabindex="-1" aria-labelledby="modalProsesLabIGDLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProsesLabIGDLabel">
                    <i class="fa-solid fa-key me-2"></i> Proses Lab IGD
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formProsesLabIGD">
                <div class="modal-body">
                    <div class="row g-3">  
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase"> No Reg </label>
                            <input type="text" name="noregigd" id="noregigd" class="form-control form-control-sm px-3 py-2 fw-bold" required>
                        </div> 
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase"> No Lab </label>
                            <input type="text" name="nolabigd" id="nolabigd" class="form-control form-control-sm px-3 py-2 fw-bold" required>
                        </div> 
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase"> Status </label>
                            <input type="text" name="statusigd" id="statusigd" class="form-control form-control-sm px-3 py-2 fw-bold" required>
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm px-3 py-2" data-bs-dismiss="modal">
                        <i class="fa-solid fa-x me-2"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning btn-sm px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-edit me-2"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
 
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function () {
        const csrfName = "<?= csrf_token() ?>";
        const csrfHash = "<?= csrf_hash() ?>";

        function esc(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        const table = $('#tableKeuangan').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?= route_to('keuangan.dataPasien') ?>",
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
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `<b>${row.noRM}`;
                    }
                }, 
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.namaPasien}`;
                    }
                }, 
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `${row.noReg}`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `${row.noSEPBPJS}`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `${row.nminstansi}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) { 
                        return `
                            <button type="button"
                                class="btn btn-success btn-sm btnKwitansiAuto"
                                title="Cetak Kwitansi"
                                data-bs-toggle="modal"
                                data-bs-target="#modalCetakKwitansi"
                                data-noreg="${esc(row.noReg)}"
                                data-nama="${esc(row.namaPasien)}"
                                data-jenis="auto">
                                <i class="fa-solid fa-print me-2"></i> Kwitansi
                            </button>

                            <button type="button"
                                class="btn btn-warning btn-sm btnProsesLab"
                                title="Proses Lab IGD"
                                data-bs-toggle="modal"
                                data-bs-target="#modalProsesLabIGD"
                                data-noregigd="${esc(row.noReg)}"
                                >
                                <i class="fa-solid fa-key me-2"></i> Lab IGD
                            </button>
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

        // ==== MODAL ADD KWITANSI ====
        function formatRibuan(value) {
            const angka = String(value).replace(/\D/g, '');
            return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function unformatRibuan(value) {
            return String(value).replace(/\./g, '');
        }
        
        $('#nominal').on('input', function () {
            const el = this;
            const posisiDariKanan = el.value.length - el.selectionStart;

            el.value = formatRibuan(el.value);

            const posisiBaru = el.value.length - posisiDariKanan;
            el.setSelectionRange(posisiBaru, posisiBaru);
        });
 
        $('#modalCetakKwitansi').on('show.bs.modal', function (event) {
            const btn = $(event.relatedTarget);
            const jenis = btn.data('jenis') === 'auto' ? 'auto' : 'manual';

            $('#jenisKwitansi').val(jenis);

            if (jenis === 'auto') {
                $('#noregPx').val(btn.data('noreg')).prop('readonly', true);
                $('#namaPx').val(btn.data('nama')).prop('readonly', true);
            } else {
                $('#noregPx').val('').prop('readonly', false);
                $('#namaPx').val('').prop('readonly', false);
            }
        });

        $('#modalCetakKwitansi').on('hidden.bs.modal', function () {
            $('#formCetakKwitansi')[0].reset();
            $('#noregPx, #namaPx').prop('readonly', false);
            $('#jenisKwitansi').val('manual');
        });

        $('#formCetakKwitansi').on('submit', function (e) {
            e.preventDefault();

            const $form     = $(this);
            const $btnSub   = $form.find('button[type="submit"]'); 
            const tabPrint  = window.open('', '_blank');
            if (tabPrint) tabPrint.document.write('<p style="font-family:sans-serif">Menyiapkan kwitansi...</p>');

            const tutupTab  = () => { if (tabPrint && !tabPrint.closed) tabPrint.close(); }; 
            const payload   = $form.serializeArray().map(f =>
                f.name === 'nominal' ? { name: 'nominal', value: unformatRibuan(f.value) } : f
            );

            $btnSub.prop('disabled', true);

            $.ajax({
                url: '<?= route_to('keuangan.createKwitansi') ?>',
                type: 'POST',
                data: $.param(payload),
                dataType: 'json',
                success: function (res) {
                    if (!res || !res.status) {
                        tutupTab();
                        Swal.fire('Gagal', (res && res.message) || 'Gagal menyimpan kwitansi.', 'error');
                        return;
                    }
                    const urlPrint = '<?= site_url('keuangan/printKwitansi') ?>/' + encodeURIComponent(res.no_kwitansi);

                    if (tabPrint && !tabPrint.closed) {
                        tabPrint.location.href = urlPrint;
                    } else {
                        window.open(urlPrint, '_blank');
                    }

                    $('#modalCetakKwitansi').modal('hide');
                    $('#tabelAnda').DataTable().ajax.reload(null, false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Kwitansi ' + res.no_kwitansi + ' tersimpan.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    tutupTab();
                    const msg = xhr.responseJSON?.message || 'Terjadi kesalahan pada server.';
                    Swal.fire('Gagal', msg, 'error');
                    console.error(xhr.responseText);
                },
                complete: function () {
                    $btnSub.prop('disabled', false);
                }
            });
        });

        const urlProsesLab = "<?= route_to('keuangan.prosesLabIGD', 'NOREG_PLACEHOLDER') ?>";
        $('#modalProsesLabIGD').on('show.bs.modal', function (event) {
            const btn   = $(event.relatedTarget);
            const noreg = btn.data('noregigd');

            $('#noregigd').val(noreg).prop('readonly', true);
            $('#nolabigd').val('Memuat...').prop('readonly', true);
            $('#statusigd').val('Memuat...').prop('readonly', true);

            $.ajax({
                url: urlProsesLab.replace('NOREG_PLACEHOLDER', encodeURIComponent(noreg)),
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        $('#nolabigd').val(res.data.nolab);
                        $('#statusigd').val(res.data.statusLab);
                    }
                },
                error: function (xhr) {
                    $('#nolabigd').val('');
                    $('#statusigd').val('');
                    const msg = xhr.responseJSON?.message || 'Gagal mengambil data lab.';
                    alert(msg);
                }
            });
        });

        $('#modalProsesLabIGD').on('hidden.bs.modal', function () {
            $('#formProsesLabIGD')[0].reset();
            $('#noregigd, #nolabigd, #statusigd').prop('readonly', false);
        });

        $('#formProsesLabIGD').on('submit', function (e) {
            e.preventDefault();

            const $form   = $(this);
            const $btn    = $form.find('button[type="submit"]');
            const btnHtml = $btn.html();

            Swal.fire({
                title: 'Buka Lab IGD?',
                text: 'Status lab akan diubah menjadi Open.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Buka',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: '<?= route_to('keuangan.openLabIGD') ?>',
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    beforeSend: function () {
                        $btn.prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm me-2"></span> Memproses...');
                    },
                    success: function (res) {
                        if (res.success) {
                            $('#modalProsesLabIGD').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Lab IGD berhasil dibuka.',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $('#tabelAnda').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: res.message || 'Gagal membuka Lab IGD.'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses data.'
                        });
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(btnHtml);
                    }
                });
            });
        });
        
    });
</script>
<?= $this->endSection(); ?>