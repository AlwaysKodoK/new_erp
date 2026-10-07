<?php 
    $filterId       = $filterId   ?? 'FilterDefault';
    $judul          = $judul      ?? 'Pencarian';
    $icon           = $icon       ?? 'fa-solid fa-filter';
    $fields         = $fields     ?? [];
    $btnFilterId    = $btnFilterId ?? 'btnFilter';
    $btnResetId     = $btnResetId  ?? 'btnReset';
?>

<div class="card cardDark mb-3">
    <div class="card-header border-bottom-0 p-3"
        role="button" data-bs-toggle="collapse"
        data-bs-target="#<?= esc($filterId) ?>"
        aria-expanded="true"
        aria-controls="<?= esc($filterId) ?>"
        style="cursor:pointer;">
        <h6 class="fw-bold mb-0 d-flex justify-content-between align-items-center">
            <span><i class="<?= esc($icon) ?> me-2"></i><?= esc($judul) ?></span>
            <i class="fa-solid fa-chevron-down small"></i>
        </h6>
    </div>
    <div class="collapse <?= esc($isShow ?? '') ?>" id="<?= esc($filterId) ?>">
        <div class="card-body p-4">
            <div class="row g-3">
                <?php foreach ($fields as $field): ?>
                    <div class="<?= esc($field['col'] ?? 'col-md-2') ?>">
                        <?php if (($field['type'] ?? 'text') !== 'button-group'): ?>
                            <label class="form-label small fw-bold">
                                <?= esc($field['label'] ?? '') ?>
                            </label>
                        <?php endif; ?>

                        <?php switch ($field['type'] ?? 'text'):
                            case 'date': ?>
                                <input type="date"
                                    id="<?= esc($field['id']) ?>"
                                    class="form-control form-control-sm px-3 py-2"
                                    <?= (isset($field['value']) && $field['value'] == $val) ? 'selected' : '' ?>>
                                <?php break; ?>

                                <?php case 'select': ?>
                                    <select id="<?= esc($field['id']) ?>" class="form-select form-select-sm px-3 py-2">
                                        <?php if (array_key_exists('placeholder', $field)): ?>
                                            <option value="" <?= empty($field['value']) ? 'selected' : '' ?>>
                                                <?= esc($field['placeholder']) ?>
                                            </option>
                                        <?php endif; ?>
                                        <?php foreach ($field['options'] as $val => $lbl): ?>
                                            <option value="<?= esc($val) ?>"
                                                <?= (isset($field['value']) && $field['value'] == $val) ? 'selected' : '' ?>>
                                                <?= esc($lbl) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php break; ?>

                            <?php case 'text': ?>
                                <input type="text"
                                    id="<?= esc($field['id']) ?>"
                                    class="form-control form-control-sm px-3 py-2"
                                    placeholder="<?= esc($field['placeholder'] ?? '') ?>"
                                    <?= !empty($field['value']) ? 'value="' . esc($field['value']) . '"' : '' ?>>
                                <?php break; ?>

                            <?php case 'button-group': ?>
                                <div class="d-flex align-items-end gap-2 h-100">
                                    <button type="button" id="<?= esc($field['btnFilterId'] ?? $btnFilterId ?? 'btnFilter') ?>"
                                        class="btn btn-primary text-white btn-sm px-3 py-2 flex-grow-1 shadow-sm">
                                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                                    </button>
                                    <button type="button" id="<?= esc($field['btnResetId'] ?? $btnResetId ?? 'btnReset') ?>"
                                        class="btn btn-outline-light btn-sm px-3 py-2 flex-grow-1 shadow-sm" title="Reset Filter">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </button>

                                    <?php if (!empty($field['extraButtons'])): ?>
                                        <?php foreach ($field['extraButtons'] as $btn): ?>
                                            <button type="button"
                                                <?= !empty($btn['id']) ? 'id="' . esc($btn['id']) . '"' : '' ?>
                                                class="btn <?= esc($btn['class'] ?? 'btn-success') ?> btn-sm px-3 py-2 shadow-sm <?= esc($btn['extraClass'] ?? '') ?>"
                                                title="<?= esc($btn['title'] ?? '') ?>"
                                                <?php if (!empty($btn['modal'])): ?>
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#<?= esc($btn['modal']) ?>"
                                                <?php endif; ?>>
                                                <?php if (!empty($btn['icon'])): ?>
                                                    <i class="<?= esc($btn['icon']) ?> me-2"></i>
                                                <?php endif; ?>
                                                <?= esc($btn['label'] ?? '') ?>
                                            </button>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <?php break; ?>

                        <?php endswitch; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>