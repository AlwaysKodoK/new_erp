<?= $this->extend('Layout\Views\template'); ?>

<?= $this->section('konten'); ?>
<style>
    .modalDark .modal-content {
        background: #1a1d20;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #e4e6eb;
    }
    .modalDark .modal-header,
    .modalDark .modal-footer {
        border-color: rgba(255, 255, 255, 0.08);
    }
    .modalDark .modal-title {
        color: #e4e6eb;
        font-weight: 700;
    }
    .modalDark .form-label {
        color: #9aa0a8;
    }
    .modalDark .form-control,
    .modalDark textarea.form-control {
        background-color: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #e4e6eb;
    }
    .modalDark .form-control:focus {
        background-color: rgba(255, 255, 255, 0.09);
        border-color: #6c8cff;
        color: #e4e6eb;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    .modalDark .form-control::placeholder {
        color: #6b7178;
    }
    .modalDark .btn-light {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12);
        color: #e4e6eb;
    }
    .modalDark .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.14);
        color: #ffffff;
    } 
    .modal-backdrop.show {
        opacity: 0.6;
    }
</style>
<section class="PengadaanPengajuan"> 
    <?= $this->include('Layout\Partials\filterPencarian') ?> 

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tablePengajuan',
        'title'   => null,
        'columns' => [
            ['label' => 'No Form'],
            ['label' => 'Jenis'],
            ['label' => 'Tgl Pengajuan'],
            ['label' => 'Status'],
            ['label' => 'Aksi'],
        ],
    ]) ?>
</section>

<div class="modal fade modalDark" id="modalTambahData" tabindex="-1" aria-labelledby="modalTambahDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTambahDataLabel">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTambahData">
                <div class="modal-body row">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Tanggal Pengajuan</label>
                        <input type="date" name="tglPengajuan" id="tglPengajuan" class="form-control form-control-sm px-3 py-2" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-uppercase">Jenis Pengajuan</label>
                        <select class="form-control form-control-sm px-3 py-2" name="" id="JenisPengajuan" id="JenisPengajuan" required>
                            <option value="">Semua Jenis</option> 
                            <option value="medis">Medis</option> 
                            <option value="nonMedis">Non Medis</option> 
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-uppercase">Keterangan</label>
                        <textarea name="keterangan" class="form-control form-control-sm px-3 py-2" rows="3" placeholder="Masukkan keterangan"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm px-3 py-2 border" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-3 py-2 shadow-sm">
                        <i class="fa-solid fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?> 

<?= $this->section('script'); ?>
<script> 
    document.querySelector('.cardFilter .card-header').addEventListener('click', function () {
        const expanded = this.getAttribute('aria-expanded') === 'false';
        this.setAttribute('aria-expanded', !expanded);
    });

    $(document).ready(function () {
        $('#tablePengajuan').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= route_to('pengadaan.dataPengajuan') ?>",
                type: "POST",
                data: { <?= csrf_token() ?>: "<?= csrf_hash() ?>" }  
            },
            columns: [
                { data: 'no_form' },
                { data: 'jenis' },
                { data: 'tgl_pengajuan' },
                { 
                    data: 'status',
                    render: function (data) {
                        const badge = data === 'Selesai' ? 'success' : 'warning';
                        return `<span class="badge bg-${badge}">${data}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (row) {
                        return `<a href="/simrs/detail/${row.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>`;
                    }
                },
            ]
        });
    });
</script>
<?= $this->endSection(); ?>