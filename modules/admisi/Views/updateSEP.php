<?= $this->extend('Layout\Views\template'); ?>
<?= $this->section('title'); ?><?= $title ?? 'ERP SIMRS' ?><?= $this->endSection(); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="">   
    <?= view('Layout\Partials\cardFilter', [
        'filterId' => 'FilterUpdateSEP',
        'title'    => 'Pengaturan',
        'isShow'    => 'show',
        'fields'   => [
            ['type' => 'date', 'id' => 'filterTglMulai', 'label' => 'Tgl Awal', 'col' => 'col-md-2'],
            ['type' => 'date', 'id' => 'filterTglSelesai', 'label' => 'Tgl Akhir', 'col' => 'col-md-2'],
            [
                'type'    => 'select',
                'id'      => 'filterJenis',
                'label'   => 'Jenis Registrasi',
                'col'     => 'col-md-3',
                'value'   => 'RJ',
                'options' => [ 
                    'RD' => 'Rawat Darurat',
                    'RJ' => 'Rawat Jalan',
                    'RI' => 'Rawat Inap', 
                ],
            ],
            ['type' => 'button-group', 'col' => 'col-md-2'],
        ],
    ]) ?>

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tableUpdateSEP',
        'title'   => null,
        'columns' => [
            ['label' => 'No',       'width' => '5%'],
            ['label' => 'Pasien',   'width' => '25%'],
            ['label' => 'No Reg',   'width' => '12%'],
            ['label' => 'No SEP',   'width' => '18%'],
            ['label' => 'Tanggal',  'width' => '12%'],
            ['label' => 'Penjamin', 'width' => '15%'],
            ['label' => 'Aksi',     'width' => '13%'],
        ],
    ]) ?>
</section>


<div class="modal fade modalDark" id="modalUpdateSEP" tabindex="-1" aria-labelledby="modalUpdateSEPLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateSEPLabel">
                    <i class="fa-solid fa-edit me-2"></i> Perubahan SEP
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formUpdateSEP">
                <div class="modal-body">
                    <div class="row g-3"> 
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> No reg </label>
                            <input type="text" name="noregPx" id="noregPx" class="form-control form-control-sm px-3 py-2 fw-bold" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> nama pasien </label>
                            <input type="text" name="namaPx" id="namaPx" class="form-control form-control-sm px-3 py-2 fw-bold" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase">No Kartu </label>
                            <input type="text" name="kartuPx" id="kartuPx" class="form-control form-control-sm px-3 py-2 fw-bold" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase"> No SEP </label>
                            <input type="text" name="sepPx" id="sepPx" class="form-control form-control-sm px-3 py-2 fw-bold" required >
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
        let csrfName = "<?= csrf_token() ?>";
        let csrfHash = "<?= csrf_hash() ?>";

        const table = $('#tableUpdateSEP').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "<?= route_to('admisi.dataUpdateSEP') ?>",
                type: "POST",
                data: function (d) {
                    d[csrfName]  = csrfHash;
                    d.tglMulai   = $('#filterTglMulai').val();
                    d.tglSelesai = $('#filterTglSelesai').val();
                    d.jenis      = $('#filterJenis').val();
                },
                dataSrc: function (json) {
                    if (json.csrf) csrfHash = json.csrf; 
                    return json.data;
                }
            },
            columns: [
                {
                    data: null, orderable: false, className: 'text-center',
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: null,
                    render: (data, type, row) => `
                        <b>${row.NoRM ?? '-'}</b><br>
                        ${row.NamaPasien ?? '-'}<br>
                        ${row.NoPesertaBPJS ?? '-'}`
                },
                { data: 'noreg', className: 'text-center', render: d => `<b>${d ?? '-'}</b>` },
                { data: 'noSEPBPJS',className: 'text-center', render: d => d ?? '-' },
                {
                    data: 'tgl',
                    className: 'text-center',
                    render: function (d, type) {
                        if (!d) return '-'; 
                        const [y, m, day] = d.substring(0, 10).split('-');
                        const formatted = `${day}-${m}-${y}`; 
                        return (type === 'display') ? formatted : d;
                    }
                },
                { data: 'nminstansi', render: d => d ?? '-' },
                {
                    data: null, orderable: false, className: 'text-center',
                    render: function (data, type, row) {
                        const esc = s => $('<div>').text(s ?? '').html().replace(/"/g, '&quot;');

                        return `
                            <button type="button"
                                class="btn btn-warning btn-sm btnUpdateSEP"
                                title="Update SEP"
                                data-bs-toggle="modal"
                                data-bs-target="#modalUpdateSEP"
                                data-noreg="${esc(row.noreg)}"
                                data-nama="${esc(row.NamaPasien)}"
                                data-sep="${esc(row.noSEPBPJS)}"
                                data-kartu="${esc(row.NoPesertaBPJS)}"
                                data-jenis="${esc($('#filterJenis').val())}">
                                <i class="fa-solid fa-edit me-2"></i> Update
                            </button>`;
                    }
                }
            ],
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

        $('#modalUpdateSEP').on('show.bs.modal', function (e) {
            const btn = $(e.relatedTarget); 

            $('#noregPx').val(btn.data('noreg'));
            $('#namaPx').val(btn.data('nama'));
            $('#sepPx').val(btn.data('sep'));
            $('#kartuPx').val(btn.data('kartu'));
 
            $(this).data('jenis', btn.data('jenis'));
        });

        // fokus ke input SEP setelah modal tampil 
        $('#modalUpdateSEP').on('shown.bs.modal', function () {
            $('#sepPx').trigger('focus').select();
        });

        $('#formUpdateSEP').on('submit', function (e) {
            e.preventDefault();

            const $btn = $(this).find('button[type="submit"]').prop('disabled', true);

            const payload = {
                noreg:   $('#noregPx').val(),
                noSEP:   $('#sepPx').val().trim(),
                noKartu: $('#kartuPx').val().trim(),
                jenis:   $('#modalUpdateSEP').data('jenis')
            };
            payload[csrfName] = csrfHash;

            if (!payload.noSEP) {
                $btn.prop('disabled', false);
                Swal.fire({ icon: 'warning', title: 'No SEP wajib diisi' });
                return;
            }

            // Loader
            Swal.fire({
                title: 'Memproses...',
                text: 'Mengambil data SEP dari BPJS dan menyimpan',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: "<?= route_to('admisi.simpanUpdateSEP') ?>",
                type: "POST",
                data: payload,
                dataType: "json",
                timeout: 60000,
                success: function (res) {
                    if (res.csrf) csrfHash = res.csrf;

                    if (res.status) {
                        $('#modalUpdateSEP').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'SEP berhasil diperbarui',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal memperbarui SEP'
                        });
                    }
                },
                error: function (xhr, textStatus) {
                    if (xhr.responseJSON && xhr.responseJSON.csrf) csrfHash = xhr.responseJSON.csrf;

                    let pesan = 'Terjadi kesalahan (' + xhr.status + ')';
                    if (textStatus === 'timeout') pesan = 'Waktu tunggu habis, koneksi ke BPJS lambat';
                    else if (xhr.responseJSON && xhr.responseJSON.message) pesan = xhr.responseJSON.message;

                    Swal.fire({ icon: 'error', title: 'Gagal', text: pesan });
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>