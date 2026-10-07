<?= $this->extend('Layout\Views\template'); ?>
<?= $this->section('title'); ?><?= $title ?? 'RM | Catatan Berkas' ?><?= $this->endSection(); ?>

<?= $this->section('konten'); ?>
<style>
    #tableLogBerkas tbody tr.row-overdue {
        background-color: rgba(255, 99, 99, 0.15) !important; 
    }

    #tableLogBerkas tbody tr.row-overdue:hover {
        background-color: rgba(255, 99, 99, 0.25) !important;
    }
 
    .cardDark #tableLogBerkas tbody tr.row-overdue {
        background-color: rgba(220, 53, 69, 0.18) !important;
        color: #f8d7da !important;
    }
</style>

<section class="rmDashboard">    

    <?= view('Layout\Partials\cardFilter', [
        'filterId' => 'FilerLogBerkas',
        'judul'    => 'Pencarian',
        'fields'   => [
            ['type' => 'date', 'id' => 'filterTglMulai', 'label' => 'Tgl Awal', 'col' => 'col-md-2'],
            ['type' => 'date', 'id' => 'filterTglSelesai', 'label' => 'Tgl Akhir', 'col' => 'col-md-2'],
            [
                'type'        => 'select',
                'id'          => 'filterJenis',
                'label'       => 'Jenis Berkas',
                'col'         => 'col-md-3',
                'placeholder' => '-- Semua Status --',
                'options'     => [
                    'dipinjam'      => 'Dipinjam',
                    'diterima'      => 'Diterima',
                    'belumKembali'  => 'Belum Kembali',
                ],
            ],
            [
                'type'        => 'select',
                'id'          => 'filterPeminjam',
                'label'       => 'Peminjam', 
                'col'         => 'col-md-3',
                'placeholder' => '-- Semua Peminjam --',
                'options'     => [
                    'Poli1A' => 'Poli 1 Gedung A', 
                    'Poli2A' => 'Poli 2 Gedung A',
                    'Poli1B' => 'Poli 1 Gedung B',
                ],
            ],
            ['type' => 'button-group', 'col' => 'col-md-2'],
        ],
    ]) ?>

    <div class="cardDark card mb-3">
        <div class="card-header border-bottom-0 p-3"
            role="button" data-bs-toggle="collapse"
            data-bs-target="#cardAction"
            aria-expanded="true"
            aria-controls="cardAction"
            style="cursor:pointer;">
            <h6 class="fw-bold mb-0 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-hand me-2"></i>Tombol Aksi</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </h6>
        </div>
        <div class="collapse show" id="cardAction">
            <div class="card-body p-4">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm btnAddLogBerkas" title="Tambah Log Berkas" data-bs-toggle="modal" data-bs-target="#modalAddLogBerkas">
                        <i class="fa-solid fa-plus me-2"></i> Tambah Data
                    </button>

                    <button type="button" class="btn btn-primary btn-sm btnBackupData" title="Backup Data">
                        <i class="fa-solid fa-database me-2"></i> Backup Data
                    </button> 
                </div>
            </div>
        </div>
    </div>

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tableLogBerkas',
        'title'   => null,
        'columns' => [
            ['label' => 'Tgl Pinjam',  'width' => '10%'],  
            ['label' => 'Tgl Terima',  'width' => '10%'],  
            ['label' => 'Nama Pasien',  'width' => '20%'], 
            ['label' => 'Peminjam', 'width' => '10%'],
            ['label' => 'Status', 'width' => '10%'],
            ['label' => 'Aksi',    'width' => '20%'],
        ],
    ]) ?>
</section>

<div class="modal fade modalDark" id="modalAddLogBerkas" tabindex="-1" aria-labelledby="modalAddLogBerkasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddLogBerkasLabel">
                    <i class="fa-solid fa-plus me-2"></i> Data Peminjaman Berkas RM
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formAddLogBerkas">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">
                                <i class="fa-solid fa-id-card me-1"></i> No RM
                            </label>
                            <input type="text" name="norm" id="norm" class="form-control form-control-sm px-3 py-2 fw-bold" placeholder="00-00-00-00" maxlength="11" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">
                                <i class="fa-solid fa-calendar-day me-1"></i> Tgl Kunjungan
                            </label>
                            <input type="date" name="tglKunj" id="tglKunj" class="form-control form-control-sm px-3 py-2" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-uppercase">
                                <i class="fa-solid fa-hospital me-1"></i> Peminjam
                            </label>
                            <select name="peminjam" id="peminjam" class="form-select form-select-sm px-3 py-2" required>
                                <option value="" selected disabled>-- Pilih Peminjam --</option>
                                <option value="Poli1A">Poli 1 Gedung A</option>
                                <option value="Poli2A">Poli 2 Gedung A</option>
                                <option value="Poli1B">Poli 1 Gedung B</option>
                            </select>
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
 
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script> 
    $(document).ready(function () {
        const csrfName = "<?= csrf_token() ?>";
        const csrfHash = "<?= csrf_hash() ?>";
  
        function hitungLamaBelumKembali(timePinjam) {
            const start = new Date(timePinjam.replace(' ', 'T')); // pastikan ke-parse dengan benar
            const now   = new Date();
            const diffMs = now - start;

            const totalJam = Math.floor(diffMs / (1000 * 60 * 60));
            const hari = Math.floor(totalJam / 24);
            const jam  = totalJam % 24;

            if (hari > 0) {
                return `${hari} hari ${jam} jam`;
            }
            return `${jam} jam`;
        }

        const statusMap = {
            dipinjam:     { label: 'Dipinjam',      class: 'bg-primary' },
            diterima:     { label: 'Diterima',      class: 'bg-success' },
            belumKembali: { label: 'Belum Kembali', class: 'bg-warning text-dark' },
        };

        const table = $('#tableLogBerkas').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?= route_to('rm.dataLogBerkas') ?>",
                type: "POST",
                data: function (d) {
                    d[csrfName]  = csrfHash;
                    d.tglMulai   = $('#filterTglMulai').val();
                    d.tglSelesai = $('#filterTglSelesai').val();
                    d.jenis      = $('#filterJenis').val();
                    d.peminjam   = $('#filterPeminjam').val();
                },
                dataSrc: "data"
            },
            columns: [
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.time_pinjam ?? '-'}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.time_terima ?? '-'}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<b>${row.norm ?? '-'}</b><br> ${row.nama_pasien ?? '-'}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `${row.NamaPeminjam ?? '-'}`;
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        const s = statusMap[row.status] ?? { label: row.status ?? '-', class: 'bg-secondary' };
                        let html = `<span class="badge ${s.class}">${s.label}</span>`;

                        if (row.status === 'belumKembali' && row.time_pinjam) {
                            const lama = hitungLamaBelumKembali(row.time_pinjam);
                            html += `<br><small class="text-light">${lama}</small>`;
                        }

                        return html;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (row.status === 'dipinjam' || row.status === 'belumKembali') {
                            return `
                                <button type="button" class="btn btn-sm btn-success btnTerima" 
                                    data-id="${row.id}" title="Terima Berkas">
                                    <i class="fa-solid fa-check me-1"></i> Terima
                                </button>
                            `;
                        }
                        return '';
                    }
                },
            ],
            rowCallback: function (row, data) {
                if (data.status === 'belumKembali') {
                    $(row).addClass('row-overdue');
                } else {
                    $(row).removeClass('row-overdue');
                }
            }
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
            $('#filterPeminjam').val('');

            const $btn = $('#btnFilter');
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memuat...');

            table.ajax.reload(function () {
                $btn.prop('disabled', false);
                $btn.html(btnFilterOriginalHtml);
            });
        });

        // ==== INSERT LOG BERKAS ====
            $('#modalAddLogBerkas').on('show.bs.modal', function () {
                $('#formAddLogBerkas')[0].reset();
                $('#tglKunj').val(new Date().toISOString().split('T')[0]);
            });
        
            $('#formAddLogBerkas').on('submit', function (e) {
                e.preventDefault();

                const $btn = $(this).find('button[type="submit"]');
                const originalText = $btn.html();

                $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Menyimpan...');

                $.ajax({
                    url: '<?= route_to('rm.saveLogBerkas') ?>', 
                    method: 'POST',
                    data: $(this).serialize(), 
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') { 
                            if (res.csrf_hash) {
                                $('input[name="csrf_test_name"]').val(res.csrf_hash);
                            }

                            $('#modalAddLogBerkas').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Data berhasil disimpan',
                                timer: 2000,
                                showConfirmButton: false
                            });
        
                            if (typeof table !== 'undefined') {
                                table.ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan'
                            });
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan pada server';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.status === 419 || xhr.status === 403) {
                            msg = 'Sesi tidak valid, silakan refresh halaman.';
                        }
                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            $('#tableLogBerkas').on('click', '.btnTerima', function () {
                const idBerkas = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Tandai berkas ini sebagai diterima?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= route_to('rm.terimaLogBerkas') ?>", 
                            method: 'POST',
                            data: {
                                [csrfName]: csrfHash,
                                idBerkas: idBerkas
                            },
                            dataType: 'json',
                            success: function (res) {
                                if (res.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: res.message || 'Berkas berhasil diterima',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload(null, false); 
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Gagal', text: res.message });
                                }
                            },
                            error: function (xhr) {
                                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan pada server';
                                Swal.fire({ icon: 'error', title: 'Error', text: msg });
                            }
                        });
                    }
                });
            });
        // ==== INSERT LOG BERKAS ====
        
    });

    // ==== AUTO CORRECT NORM ====
    document.getElementById('norm').addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); 
        value = value.substring(0, 8); 

        let formatted = value.match(/.{1,2}/g); 
        e.target.value = formatted ? formatted.join('-') : '';
    });
 
    document.getElementById('norm').addEventListener('keypress', function (e) {
        if (!/[0-9]/.test(e.key)) {
            e.preventDefault();
        }
    });

    $(document).on('click', '.btnBackupData', function () {
        if (!confirm('Yakin ingin backup data sekarang?')) return;

        $.ajax({
            url: "<?= route_to('rm.backupData') ?>", 
            method: "GET",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () { 
            },
            success: function (res) {
                alert(res.message ?? 'Backup berhasil');
            },
            error: function (xhr) {
                alert('Backup gagal: ' + xhr.responseText);
            }
        });
    });

</script>
<?= $this->endSection(); ?>