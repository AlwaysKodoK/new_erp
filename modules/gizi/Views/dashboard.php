<?= $this->extend('Layout\Views\template'); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="giziDashboard">  

    <div class="card cardDark mb-3">
        <div class="card-header">
            <h4 class="mb-0">Print Gizi</h4>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2"> 
                <a href="<?= route_to('gizi.cetakLabelGizi') ?>" target="_blank" class="btn btn-primary">
                    <i class="fa-solid fa-print me-2"></i>Print Label
                </a>
                <a href="<?= route_to('gizi.cetakPenunggu') ?>" target="_blank" class="btn btn-primary">
                    <i class="fa-solid fa-print me-2"></i>Print Penunggu
                </a>
                <a href="<?= route_to('gizi.cetakForm') ?>" target="_blank" class="btn btn-primary">
                    <i class="fa-solid fa-print me-2"></i>Print Form
                </a>
                <a href="<?= route_to('gizi.cetakExcel') ?>" target="_blank" class="btn btn-success">
                    <i class="fa-solid fa-file-excel me-2"></i>Print Excel
                </a>
            </div>
        </div>
    </div>

    <?= view('Layout\Partials\tableCard', [
        'tableId' => 'tablePasienGizi',
        'title'   => null,
        'columns' => [
            ['label' => 'No Reg',  'width' => '10%'],
            ['label' => 'Pasien',  'width' => '15%'],
            ['label' => 'Ruangan', 'width' => '15%'],
            ['label' => 'Pesan',   'width' => '35%'],
            ['label' => 'Diet',    'width' => '10%'],
            ['label' => 'Aksi',    'width' => '5%'],
        ],
    ]) ?>
</section>

<div class="modal fade modalDark" id="modalEditDiet" tabindex="-1" aria-labelledby="modalEditDietLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditDietLabel">
                    <i class="fa-solid fa-pencil me-2"></i>Rubah Data
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditDiet">
                <div class="modal-body row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small fw-bold text-uppercase">Nama Pasien</label>
                        <input type="text" name="namaPasien" id="namaPasien" class="form-control form-control-sm px-3 py-2" required readonly>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small fw-bold text-uppercase">No Registrasi</label>
                        <input type="text" name="noReg" id="noReg" class="form-control form-control-sm px-3 py-2" required readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase">Pesan</label>
                        <textarea name="pesanPasien" id="pesanPasien" class="form-control form-control-sm px-3 py-2" rows="12" placeholder="" readonly></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-uppercase">Diet</label>
                        <textarea name="dietPasien" id="dietPasien" class="form-control form-control-sm px-3 py-2" rows="12" placeholder="Masukkan Diet pasien"></textarea>
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
        $('#tablePasienGizi').DataTable({
            processing: true,
            serverSide: false, 
            ajax: {
                url: "<?= route_to('gizi.dataPasien') ?>",
                type: "POST",
                data: { <?= csrf_token() ?>: "<?= csrf_hash() ?>" },
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
                        return `${row.noRM} <br> ${row.namaPasien} <br> ${row.tglLahir}`;
                    }
                },  
                { 
                    data: null,
                    render: function (data, type, row) { 
                        return `${row.kdBed} <br> ${row.namaRuang}`;
                    }
                },  
                { 
                    data: null,
                    render: function (data, type, row) { 
                        return `${row.PesanPasien ?? '-'}`;
                    }
                },  
                { 
                    data: null,
                    render: function (data, type, row) { 
                        return `${row.diet ?? '-'}`;
                    }
                },  
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        return `<button type="button" 
                                    class="btn btn-warning btn-sm btnEditDiet" 
                                    title="Edit Diet" 
                                    data-bs-toggle="modal"  data-bs-target="#modalEditDiet"
                                    data-noreg="${row.noReg}" data-nama="${row.namaPasien}" data-pesan="${row.PesanPasien}" data-diet="${row.diet ?? ''}">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>`;
                    }
                },
            ]
        });

        $(document).on('show.bs.modal', '#modalEditDiet', function (event) {
            const button = event.relatedTarget; 
            const noReg   = button.getAttribute('data-noreg');
            const nama    = button.getAttribute('data-nama');
            const pesan   = button.getAttribute('data-pesan');
            const diet    = button.getAttribute('data-diet');

            $('#noReg').val(noReg);
            $('#namaPasien').val(nama);
            $('#pesanPasien').val(pesan);
            $('#dietPasien').val(diet);
        });

        $(document).on('hidden.bs.modal', '#modalEditDiet', function () {
            $('#formEditDiet')[0].reset();
        });

        $('#formEditDiet').on('submit', function (e) {
            e.preventDefault();

            const noReg = $('#noReg').val();
            const diet  = $('#dietPasien').val();

            $.ajax({
                url: "<?= route_to('gizi.updateDiet') ?>",
                type: "POST",
                dataType: "json", 
                data: {
                    noReg: noReg,
                    diet: diet, 
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>" 
                },
                success: function(response) { 
                    $('#modalEditDiet').modal('hide'); 
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message, 
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {   
                        $('#tablePasienGizi').DataTable().ajax.reload(null, false);  
                    });
                },
                error: function(xhr, status, thrownError) {
                    alert('Gagal menyimpan diet. Silakan coba lagi.');
                    console.error(xhr.responseText);
                }
            });
        });

    });
</script>
<?= $this->endSection(); ?>