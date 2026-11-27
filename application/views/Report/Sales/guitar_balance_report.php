<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div id="gbr-report" class="row gx-5 gx-xl-10">
    <div class="col-xl-12 min-w-0">
        <div class="card card-flush overflow-hidden mb-5">
            <form id="GuitarBalanceReportForm" method="get" rel="noopener" action="<?= html_escape(site_url('Report/GuitarBalanceReport')) ?>">
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <?php foreach (['from' => ['From Period', $from_month, $from_year], 'to' => ['Until Period', $to_month, $to_year]] as $prefix => $filter): ?>
                                <div class="row mb-6">
                                    <label for="gbr_<?= $prefix ?>_month" class="col-sm-2 col-form-label-sm col-form-label"><?= $filter[0] ?> :</label>
                                    <div class="col-sm-5">
                                        <div class="row g-3">
                                            <div class="col-7">
                                                <select class="form-select form-select-sm" name="<?= $prefix ?>_month" id="gbr_<?= $prefix ?>_month" aria-label="<?= $filter[0] ?> month" required>
                                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                                        <option value="<?= $i ?>" <?= $i === (int) $filter[1] ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $i, 1, 2000)) ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <input class="form-control form-control-sm text-center" type="number" name="<?= $prefix ?>_year" id="gbr_<?= $prefix ?>_year" aria-label="<?= $filter[0] ?> year" min="2000" max="2100" step="1" value="<?= (int) $filter[2] ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="row mb-6">
                                <label class="col-sm-2 col-form-label-sm col-form-label">Currency :</label>
                                <div class="col-sm-5"><div class="form-control form-control-sm bg-light" aria-label="Currency">USD</div></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-10 offset-sm-2 text-muted fs-7">
                                    Select a production period of 1 to 120 months. Downloads retrieve current ERP data using the selected filters.
                                    <?php if (!$zipAvailable): ?>
                                        <div class="text-warning mt-2">Excel download requires the PHP zip extension. CSV remains available.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <button type="submit" name="action" value="preview" formtarget="_blank" class="btn btn-sm btn-light-danger"><i class="fas fa-file-pdf"></i> Display Report</button>
                        <a href="<?= html_escape(base_url('Report/Navigation')) ?>" class="btn btn-danger btn-sm ms-auto"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
