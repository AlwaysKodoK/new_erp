<?= $this->extend('Layout\Views\template'); ?>
<?= $this->section('title'); ?><?= $title ?? 'ERP SIMRS' ?><?= $this->endSection(); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="admisiDashboard">  

    <div class="card cardDark mb-3">
        <div class="card-header border-bottom-0 p-3"
            role="button" data-bs-toggle="collapse" data-bs-target="#openLoketAdmisi"
            aria-expanded="true" aria-controls="openLoketAdmisi" style="cursor:pointer;">
            <h6 class="fw-bold mb-0 d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-sliders me-2"></i>Konfigurasi Loket</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </h6>
        </div>
        <div class="collapse" id="openLoketAdmisi">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold"> Username </label>
                        <input type="text" id="username" class="form-control form-control-sm px-3 py-2" value="<?= esc($username) ?>" readonly>
                        <input type="hidden" id="usernik" value="<?= esc($usernik) ?>">
                    </div> 
                    <div class="col-md-2">
                        <label class="form-label small fw-bold"> Loket Admisi </label>
                        <select id="pilihLoket" class="form-select form-select-sm px-3 py-2">
                            <?php foreach ($loket as $row): ?>
                                <option value="<?= esc($row->id) ?>"
                                    <?= (!empty($CheckLoket) && $CheckLoket['loket_id'] == $row->id) ? 'selected' : '' ?>>
                                    <?= esc($row->loket_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold"> Status Loket </label>
                        <select id="statusLoket" class="form-select form-select-sm px-3 py-2">
                            <?php if (empty($CheckLoket)): ?> 
                                <option value="1" selected>Aktif</option>
                            <?php else: ?> 
                                <option value="0" selected>Non Aktif</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="d-flex align-items-end gap-2 h-100">
                            <button type="button" id="btnUpdateLoket"
                                class="btn btn-primary text-white btn-sm px-3 py-2 flex-grow-1 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Update Loket
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if (!empty($CheckLoket)): ?>
        <?= view('Layout\Partials\tableCard', [
            'tableId' => 'tableAntrianAdmisi',
            'title'   => null,
            'columns' => [
                ['label' => 'No',         'width' => '5%'],
                ['label' => 'No Antrian', 'width' => '5%'],
                ['label' => 'No App',     'width' => '10%'],
                ['label' => 'No Kartu',   'width' => '10%'], 
                ['label' => 'Pasien',     'width' => '10%'], 
                ['label' => 'Loket',      'width' => '20%'],
                ['label' => 'Aksi',       'width' => '5%'],
            ],
        ]) ?>
    <?php else: ?>
        <div class="cardDark text-center mt-3 p-4">
            Silakan buka loket terlebih dahulu untuk melihat daftar antrian.
        </div>
    <?php endif; ?>
</section>
 
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script>
    $(document).ready(function () {
        const csrfName = "<?= csrf_token() ?>";
        const csrfHash = "<?= csrf_hash() ?>"; 
        const currentLoketId = <?= (!empty($CheckLoket) && isset($CheckLoket['loket_id'])) ? json_encode($CheckLoket['loket_id']) : 'null' ?>; 

        const table = $('#tableAntrianAdmisi').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?= route_to('admisi.dataAdmisi') ?>",
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
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'no_antrean',
                    className: 'text-center',
                    render: function (data) {
                        return `<b>${data ?? '-'}</b>`;
                    }
                },
                {
                    data: 'no_app',
                    render: function (data) {
                        return data ?? '-';
                    }
                },
                {
                    data: 'ko_kartu',
                    render: function (data) {
                        return data ?? '-';
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <b>${row.norm ?? '-'}</b><br>
                            ${row.namaPasien ?? '-'}
                        `;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return `
                            <b>${row.loket_name ?? '-'}</b><br>
                            ${row.nama_karyawan ?? '-'}
                        `;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        let buttons = '';

                        if (row.take_by === null && row.take_at === null) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-primary btnAmbil me-1" 
                                    data-id="${row.id}" data-loket="${currentLoketId}">
                                    <i class="fa-solid fa-hand me-1"></i>Ambil
                                </button>`;
                        }

                        if (row.take_by != null && row.is_show === null && row.show_at === null) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-warning btnShow me-1" 
                                    data-id="${row.id}" data-loket="${currentLoketId}">
                                    <i class="fa-solid fa-bullhorn me-1"></i>Panggil
                                </button>`;
                        }

                        if (row.take_by != null && row.is_show != null && row.is_done === null && row.done_at === null) {
                            buttons += `
                                <button type="button" class="btn btn-sm btn-success btnDone" 
                                    data-id="${row.id}" data-loket="${currentLoketId}">
                                    <i class="fa-solid fa-check me-1"></i>Selesai
                                </button>`;
                        }

                        return buttons || '<span class="text-muted">Selesai</span>';
                    }
                }
            ],
        });
 
        const btnFilterOriginalHtml = $('#btnFilter').html(); 

        $('#btnUpdateLoket').on('click', function () {
            const $btn = $(this);
            const usernik     = $('#usernik').val();
            const loketId     = $('#pilihLoket').val();
            const statusLoket = $('#statusLoket').val();
            const csrfToken   = $('meta[name="csrf-token"]').attr('content');

            $btn.prop('disabled', true)
                .html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Menyimpan...');

            $.ajax({
                url: '<?= route_to('admisi.updateLoket') ?>',
                method: 'POST',
                data: {
                    usernik: usernik,
                    loket_id: loketId,
                    status: statusLoket,
                    '<?= csrf_token() ?>': csrfToken
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message ?? 'Loket berhasil diperbarui',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            location.reload(); 
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal',
                            text: res.message ?? 'Gagal memperbarui loket',
                        });
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    let msg = 'Terjadi kesalahan saat menghubungi server';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg,
                    });
                },
                complete: function () {
                    $btn.prop('disabled', false)
                        .html('<i class="fa-solid fa-floppy-disk me-2"></i> Update Loket');
                }
            });
        });

        $('#tableAntrianAdmisi').on('click', '.btnAmbil', function () {
            const btn = $(this);
            const idAntrean = btn.data('id');
            const idLoket = btn.data('loket');

            if (!idLoket) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Loket Belum Diset',
                    text: 'Silakan set loket admisi anda terlebih dahulu sebelum mengambil antrean.'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin ambil antrean ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ambil',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...');

                $.ajax({
                    url: "<?= route_to('admisi.takeAntrean') ?>",
                    method: 'POST',
                    data: {
                        [csrfName]: csrfHash,
                        idAntrean: idAntrean,
                        idLoket: idLoket,
                    },
                    dataType: 'json',
                    success: function (res) {
                        // update csrf hash baru kalau CI4 mengembalikan token baru
                        if (res.csrfHash) {
                            csrfHash = res.csrfHash;
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Antrean berhasil diambil',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: res.message || 'Antrean gagal diambil'
                            });
                            btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan pada server';

                        if (xhr.status === 403) {
                            msg = 'Sesi keamanan (CSRF) kadaluarsa, silakan muat ulang halaman.';
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                    }
                });
            });
        });

        $('#tableAntrianAdmisi').on('click', '.btnShow', function () {
            const btn = $(this);
            const idAntrean = btn.data('id');
            const idLoket = btn.data('loket');

            if (!idLoket) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Loket Belum Diset',
                    text: 'Silakan set loket admisi anda terlebih dahulu sebelum mengambil antrean.'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin panggil antrean ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Panggil',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...');

                $.ajax({
                    url: "<?= route_to('admisi.showAntrean') ?>",
                    method: 'POST',
                    data: {
                        [csrfName]: csrfHash,
                        idAntrean: idAntrean,
                        idLoket: idLoket,
                    },
                    dataType: 'json',
                    success: function (res) { 
                        if (res.csrfHash) {
                            csrfHash = res.csrfHash;
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Antrean berhasil dipanggil',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: res.message || 'Antrean gagal dipanggil'
                            });
                            btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan pada server';

                        if (xhr.status === 403) {
                            msg = 'Sesi keamanan (CSRF) kadaluarsa, silakan muat ulang halaman.';
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                    }
                });
            });
        });

        $('#tableAntrianAdmisi').on('click', '.btnDone', function () {
            const btn = $(this);
            const idAntrean = btn.data('id');
            const idLoket = btn.data('loket');

            if (!idLoket) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Loket Belum Diset',
                    text: 'Silakan set loket admisi anda terlebih dahulu sebelum mengambil antrean.'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah anda ingin selesaikan antrean ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesaikan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Memproses...');

                $.ajax({
                    url: "<?= route_to('admisi.doneAntrean') ?>",
                    method: 'POST',
                    data: {
                        [csrfName]: csrfHash,
                        idAntrean: idAntrean,
                        idLoket: idLoket,
                    },
                    dataType: 'json',
                    success: function (res) { 
                        if (res.csrfHash) {
                            csrfHash = res.csrfHash;
                        }

                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message || 'Antrean berhasil dipanggil',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Gagal',
                                text: res.message || 'Antrean gagal dipanggil'
                            });
                            btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                        }
                    },
                    error: function (xhr) {
                        let msg = 'Terjadi kesalahan pada server';

                        if (xhr.status === 403) {
                            msg = 'Sesi keamanan (CSRF) kadaluarsa, silakan muat ulang halaman.';
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        btn.prop('disabled', false).html('<i class="fa-solid fa-hand me-1"></i>Ambil');
                    }
                });
            });
        });
    });
</script>
<?= $this->endSection(); ?>