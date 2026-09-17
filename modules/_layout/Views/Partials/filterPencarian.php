<?php 
    $filterId       = $filterId ?? 'collapseFilter';
    $showJenisForm  = $showJenisForm ?? true;
    $fields         = $fields ?? []; 
?> 
<style>
    #iconFilterToggle {
        transition: transform 0.3s ease;
    }
    
    .cardFilter .card-header[aria-expanded="false"] #iconFilterToggle {
        transform: rotate(-90deg);
    }

    .cardFilter {
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .cardFilter .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .cardFilter h6 {
        color: #ffffff !important;
    }
    .cardFilter .form-label {
        color: #ffffff !important;
    }
    .cardFilter .form-control,
    .cardFilter .form-select {
        background-color: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }
    .cardFilter .form-control:focus,
    .cardFilter .form-select:focus {
        background-color: rgba(255, 255, 255, 0.09);
        border-color: #6c8cff;
        color: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(108, 140, 255, 0.15);
    }
    .cardFilter .form-control::placeholder {
        color: #6b7178;
    }
    
    .cardFilter input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        opacity: 0.7;
    }
    .cardFilter .btn-primary {
        background-color: #6c8cff;
        border-color: #6c8cff;
    }
    .cardFilter .btn-primary:hover {
        background-color: #5a7aff;
        border-color: #5a7aff;
    }
    .cardFilter .btn-light {
        background-color: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.12) !important;
        color: #e4e6eb;
    }
    .cardFilter .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.14);
        color: #ffffff;
    }
    .cardFilter #iconFilterToggle {
        color: #9aa0a8;
    }
</style>
<div class="cardFilter card border-0 shadow-sm mb-4 rounded-3">
    <div class="card-header border-bottom-0 p-3" 
        role="button" data-bs-toggle="collapse" 
        data-bs-target="#<?= esc($filterId) ?>" 
        aria-expanded="true" 
        aria-controls="<?= esc($filterId) ?>"
        style="cursor:pointer;">
        <h6 class="fw-bold mb-0 d-flex justify-content-between align-items-center">
            <span><i class="fa-solid fa-gear me-2"></i>Pengaturan</span>
            <i class="fa-solid fa-chevron-down small" id="iconFilterToggle"></i>
        </h6>
    </div>

    <div class="collapse" id="<?= esc($filterId) ?>">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-uppercase">Tgl Awal</label>
                    <input type="date" id="filterTglMulai" class="form-control form-control-sm px-3 py-2">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-uppercase">Tgl Akhir</label>
                    <input type="date" id="filterTglSelesai" class="form-control form-control-sm px-3 py-2">
                </div>

                <?php if ($showJenisForm): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-uppercase">Jenis Form</label>
                    <select id="filterJenisForm" class="form-select form-select-sm px-3 py-2">
                        <option value="">-- Semua Jenis Form --</option>
                        <option value="medis">Medis</option>
                        <option value="nonMedis">Non Medis</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="button" id="btnFilter" class="btn btn-primary text-white btn-sm px-3 py-2 flex-grow-1 shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                    <button type="button" id="btnReset" class="btn btn-light btn-sm px-3 py-2 border flex-grow-1 shadow-sm" title="Reset Filter">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="button" id="btnAdd" class="btn btn-success btn-sm px-3 py-2 flex-grow-1 shadow-sm" title="Tambah Data" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                        <i class="fa-solid fa-plus"></i> Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>