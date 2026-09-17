<?= $this->extend('Layout\Views\template'); ?>

<?= $this->section('konten'); ?>
<style>

</style>

<section class="simrsPengajuan"> 
    <div class="cardPengajuan">

    </div>
    
    <!-- ==== AREA FILTER ==== -->
    <div class="cardFilter card border-0 shadow-sm mb-4 rounded-3">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
            <h6 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-filter me-2"></i>Filter Pencarian</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Tgl Awal</label>
                    <input type="date" id="filterTglMulai" class="form-control form-control-sm px-3 py-2">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Tgl Akhir</label>
                    <input type="date" id="filterTglSelesai" class="form-control form-control-sm px-3 py-2">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Jenis Form</label>
                    <select id="filterJenisForm" class="form-select form-select-sm px-3 py-2">
                        <option value="">-- Semua Jenis Form --</option>
                        <option value="medis">Medis</option>
                        <option value="nonMedis">Non Medis</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="button" id="btnFilter" class="btn btn-primary text-white btn-sm px-3 py-2 flex-grow-1 shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                    <button type="button" id="btnReset" class="btn btn-light btn-sm px-3 py-2 border flex-grow-1 shadow-sm" title="Reset Filter">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==== AREA TABEL ==== -->
    <div class="cardTable card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="table-responsive"> 
                <table id="tablePengajuan" class="table table-hover align-middle w-100">
                    <thead class="table-primary text-center">
                        <tr> 
                            <th width="13%" class="text-center">No Form</th>
                            <th width="5%" class="text-center">Jenis</th>
                            <th width="10%" class="text-center">Tgl Pengajuan</th>
                            <th width="5%" class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('script'); ?>
<script> 
</script>
<?= $this->endSection(); ?>