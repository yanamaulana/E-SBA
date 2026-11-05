<?php
$statusLabels = [
    'ehrmnew' => 'New',
    'new' => 'New',
    'ehrmopen' => 'Open',
    'open' => 'Open',
    'ehrmclose' => 'Closed',
    'closed' => 'Closed',
    'close' => 'Closed',
    'ehrmawaiting' => 'Awaiting',
    'awaiting' => 'Awaiting',
    'ehrmapproved' => 'Approved',
    'approved' => 'Approved',
    'ehrmrejected' => 'Rejected',
    'rejected' => 'Rejected',
    'ehrmrevising' => 'Revising',
    'revising' => 'Revising',
    'ehrmnotrequested' => 'Not Requested',
    'notrequested' => 'Not Requested',
];
$statusOptions = ['DOC' => [], 'APPROVAL' => []];
foreach ($statuses as $status) {
    $key = strtoupper($status['KEY_ID']);
    $remark = trim((string) $status['STATUS_REMARK']);
    $label = $statusLabels[strtolower($remark)] ?? trim(preg_replace('/([a-z])([A-Z])/', '$1 $2', preg_replace('/^eHRM/i', '', $remark)));
    $statusOptions[$key][(string) $status['STATUS_CODE']] = $label;
}
?>
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title fw-bold">List Of Cahbook Entry</h3>
    </div>
    <div class="card-body">
        <form id="cashbook-filters">
            <?php $this->load->view('partials/csrf_field'); ?>
            <div class="row g-4 mb-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="alert alert-primary p-4 mb-0 h-100">
                        <label for="entry_type" class="form-label">Cash Book Type</label>
                        <select id="entry_type" name="entry_type" class="form-select form-select-sm">
                            <option value="BR">Bank Receipt</option>
                            <option value="CR">Cash Receipt</option>
                            <option value="BD" selected>Bank Payment</option>
                            <option value="CD">Cash Payment</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="alert alert-info p-4 mb-0 h-100">
                        <div class="fw-bold mb-3">Date Range</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="text" id="date_from" name="date_from" class="form-control form-control-sm date-picker text-center" value="<?= date('Y-m-01') ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="text" id="date_to" name="date_to" class="form-control form-control-sm date-picker text-center" value="<?= date('Y-m-t') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <?php foreach (['DOC' => ['document_status', 'Document Filter Status', 'warning'], 'APPROVAL' => ['approval_status', 'Approval Filter Status', 'success']] as $key => $group): ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <fieldset class="alert alert-<?= $group[2] ?> p-4 mb-0 h-100">
                            <legend class="float-none w-auto fs-6 fw-bold mb-3"><?= $group[1] ?></legend>
                            <div class="d-flex flex-wrap gap-3">
                                <?php foreach ($statusOptions[$key] as $code => $label): ?>
                                    <label class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input type="checkbox" class="form-check-input" name="<?= $group[0] ?>[]" value="<?= html_escape($code) ?>">
                                        <span class="form-check-label text-dark"><?= html_escape($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div class="fs-8 mt-3">No selection = all statuses.</div>
                        </fieldset>
                    </div>
                <?php endforeach; ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="alert alert-success p-4 mb-0 h-100">
                        <label for="posted" class="form-label">Posted</label>
                        <select id="posted" name="posted" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1">Posted</option>
                            <option value="0">Not Posted</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="alert alert-danger p-4 mb-0 h-100">
                        <label for="void" class="form-label">Is Void</label>
                        <select id="void" name="void" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="alert alert-primary p-4 mb-0 h-100">
                        <label for="account" id="account-label" class="form-label">Filter Bank</label>
                        <select id="account" name="account" class="form-select form-select-sm">
                            <option value="">All</option>
                        </select>
                    </div>
                </div>
            </div>
            <details class="alert alert-secondary p-4 mb-4" open>
                <summary class="fw-bold mb-3">Advanced Search</summary>
                <div id="cashbook-criteria"></div>
                <button type="button" id="add-criterion" class="btn btn-sm btn-light-primary mt-2"><i class="fas fa-plus"></i> Add condition</button>
                <span class="text-muted ms-2">Maximum 5 conditions</span>
            </details>
            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Search</button>
                <button type="button" id="reset-filters" class="btn btn-sm btn-light">Reset Filters</button>
            </div>
        </form>
        <div id="cashbook-error" class="alert alert-danger d-none" role="alert"></div>
        <table id="TableData" class="display compact table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5">
            <thead style="background-color: #3B6D8C;">
                <tr>
                    <th>#</th>
                    <th>No.</th>
                    <th>Document No.</th>
                    <th>Payee</th>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Currency</th>
                    <th>Bank Payment</th>
                    <th>Memo</th>
                    <th>Status</th>
                    <th>Approval Status</th>
                    <th>void</th>
                    <th>Cash Flow Type</th>
                    <th>Account (Detail)</th>
                    <th>Deb/Cred</th>
                    <th>Amount Detail</th>
                    <th>Detail Desc</th>
                    <th>Reff No.</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold"></tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="cashbook-attachment-modal" tabindex="-1" aria-labelledby="cashbook-attachment-title" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="cashbook-attachment-title">Cashbook Attachments</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light-primary border border-primary mb-4">
                    <strong id="attachment-journal-label"></strong>
                    <span id="attachment-header-info" class="ms-3"></span>
                </div>
                <div id="attachment-message" class="alert d-none" role="status"></div>
                <h5 class="mb-3">Saved Attachments</h5>
                <div id="attachment-list" class="mb-5" aria-live="polite"></div>
                <hr>
                <form id="cashbook-attachment-form" enctype="multipart/form-data">
                    <?php $this->load->view('partials/csrf_field'); ?>
                    <input type="hidden" name="journal" id="attachment-journal">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Upload Attachments</h5>
                        <button type="button" id="attachment-add" class="btn btn-sm btn-light-primary"><i class="fas fa-plus"></i> Add File</button>
                    </div>
                    <p class="text-muted fs-7">
                        PDF, JPG, PNG, GIF, WEBP. Maksimal <?= (int) $upload_limits['max_files'] ?> file,
                        <?= round($upload_limits['max_file_bytes'] / 1048576, 1) ?> MB per file,
                        total <?= round($upload_limits['max_total_bytes'] / 1048576, 1) ?> MB per upload.
                    </p>
                    <div id="attachment-upload-rows"></div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" id="attachment-save" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save Attachments</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="cashbook-options">
    <?= json_encode([
        'accounts' => $accounts,
        'currencies' => array_column($currencies, 'Currency_ID'),
        'statuses' => $statusOptions,
        'uploadLimits' => $upload_limits,
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
</script>