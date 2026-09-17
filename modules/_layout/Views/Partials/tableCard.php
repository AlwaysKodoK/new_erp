<?php
    /** 
     * @var string $tableId 
     * @var string $title 
     * @var array  $columns 
    */

    $tableId = $tableId ?? 'dataTable';
    $title   = $title ?? null;
?>
<style>
    .cardTable {
        background: rgba(255, 255, 255, 0.04);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .cardTable .card-header {
        background-color: transparent !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .cardTable h6 {
        color: #e4e6eb !important;
    }

    /* Table itself */
    .cardTable table {
        color: #e4e6eb;
    }
    .cardTable thead tr {
        background-color: rgba(255, 255, 255, 0.06);
    }
    .cardTable thead th {
        color: #e4e6eb;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        font-weight: 600;
    }
    .cardTable tbody tr {
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .cardTable tbody td {
        color: #e4e6eb;
        border: none;
    }
    .cardTable tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        color: #ffffff;
    }  
    .cardTable .table-hover > tbody > tr:hover > * {
        --bs-table-hover-bg: rgba(255, 255, 255, 0.06);
        --bs-table-hover-color: #ffffff;
    }
    .cardTable .table > :not(caption) > * > * {
        background-color: transparent;
    } 

    .cardTable .dataTables_wrapper .dataTables_length,
    .cardTable .dataTables_wrapper .dataTables_filter,
    .cardTable .dataTables_wrapper .dataTables_info,
    .cardTable .dataTables_wrapper .dataTables_paginate {
        color: #9aa0a8;
    }
    .cardTable .dataTables_wrapper .dataTables_length select,
    .cardTable .dataTables_wrapper .dataTables_filter input {
        background-color: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        color: #e4e6eb;
        border-radius: 0.4rem;
        padding: 0.25rem 0.5rem;
    }
    .cardTable .dataTables_wrapper .dataTables_filter input:focus,
    .cardTable .dataTables_wrapper .dataTables_length select:focus {
        outline: none;
        border-color: #6c8cff;
        box-shadow: 0 0 0 0.15rem rgba(108, 140, 255, 0.15);
    }
    .cardTable .dataTables_wrapper .dataTables_length select option {
        background-color: #1a1d20;
        color: #e4e6eb;
    }
 
    .cardTable .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #e4e6eb !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        background: rgba(255, 255, 255, 0.04) !important;
        border-radius: 0.4rem;
        margin: 0 2px;
    }
    .cardTable .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    .cardTable .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .cardTable .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #6c8cff !important;
        border-color: #6c8cff !important;
        color: #ffffff !important;
    }
    .cardTable .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #5a5f66 !important;
        background: rgba(255, 255, 255, 0.02) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
 
    .cardTable table.dataTable thead .sorting:before,
    .cardTable table.dataTable thead .sorting:after,
    .cardTable table.dataTable thead .sorting_asc:before,
    .cardTable table.dataTable thead .sorting_asc:after,
    .cardTable table.dataTable thead .sorting_desc:before,
    .cardTable table.dataTable thead .sorting_desc:after {
        opacity: 0.4;
    }
 
    .cardTable .dataTables_processing {
        background: rgba(26, 29, 32, 0.9) !important;
        color: #e4e6eb !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0.5rem;
    }
</style>

<div class="cardTable card border-0 shadow-sm mb-4 rounded-3">
    <?php if ($title): ?>
    <div class="card-header border-bottom-0 pt-4 pb-0 px-4">
        <h6 class="fw-bold mb-0"><?= esc($title) ?></h6>
    </div>
    <?php endif; ?>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="<?= esc($tableId) ?>" class="table table-hover align-middle w-100 mb-0">
                <thead>
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th><?= esc($col['label']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>
    </div>
</div>