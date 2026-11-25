<?php
$show = static function ($value) {
    return $value === null || $value === '' ? '&ndash;' : html_escape((string) $value);
};
$period = $start . ' s.d. ' . $end;
?>
<?php if ($print): ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Audit Akses VPR <?= html_escape($period) ?></title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font: 10pt Arial, sans-serif; color: #192836; margin: 1rem auto; max-width: 1100px; }
        h1 { font-size: 17pt; margin: 0 0 5px; }
        h2 { font-size: 12pt; margin: 0 0 5px; }
        p { margin: 0 0 8px; }
        .audit-document { border-top: 1px solid #8c9ca7; margin-top: 14px; padding-top: 10px; }
        .audit-heading { break-after: avoid; }
        .audit-finding-danger {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            background-color: #b42318;
            color: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { break-inside: avoid; }
        td, th { padding: 4px 6px; border: 1px solid #bac4cb; text-align: left; }
        th { background: #e9f0f5; }
        .text-end { text-align: right; }
        .audit-summary { margin: 10px 0; }
        .audit-summary strong { margin-right: 16px; }
        @media print { .audit-print-action { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button type="button" class="audit-print-action" onclick="window.print()">Cetak / Simpan PDF</button>
    <h1>Audit Akses Vendor Pricing Request</h1>
    <p>Periode input <?= html_escape($period) ?></p>
<?php else: ?>
<div class="card mb-5">
    <div class="card-header align-items-center">
        <h1 class="card-title fs-3">Audit Akses Vendor Pricing Request</h1>
        <a class="btn btn-sm btn-light-primary" href="<?= site_url('Report/MIS/ERP_AccessPermission') ?>">Hak Akses ERP</a>
    </div>
    <div class="card-body">
        <form method="get" action="<?= site_url('Report/Vpr_audit') ?>" class="row gy-3 gx-3 align-items-end">
            <div class="col-sm-6 col-lg-3">
                <label for="vpr-start" class="form-label">Tanggal mulai</label>
                <input type="text" class="form-control vpr-date" name="start" id="vpr-start" value="<?= html_escape($start) ?>" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" autocomplete="off" required>
            </div>
            <div class="col-sm-6 col-lg-3">
                <label for="vpr-end" class="form-label">Tanggal akhir</label>
                <input type="text" class="form-control vpr-date" name="end" id="vpr-end" value="<?= html_escape($end) ?>" placeholder="YYYY-MM-DD" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" autocomplete="off" required>
            </div>
            <div class="col-lg-6 d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit">Tampilkan</button>
                <button class="btn btn-light-danger" type="submit" name="format" value="pdf" formtarget="_blank">Cetak PDF</button>
                <button class="btn btn-light-success" type="submit" name="format" value="excel">Unduh Excel</button>
            </div>
        </form>
        <p class="text-muted mb-0 mt-3">Filter berdasarkan tanggal input VPR. Izin dicocokkan hingga jam input.</p>
        <?php if ($error !== ''): ?>
            <div class="alert alert-warning mb-0 mt-3" role="alert"><?= html_escape($error) ?></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php if ($error === ''): ?>
    <div class="audit-summary d-flex flex-wrap gap-4 mb-4">
        <strong><?= count($reports) ?> VPR perlu ditinjau</strong>
        <strong><?= $userCount ?> user teridentifikasi</strong>
        <strong><?= $itemCount ?> baris detail</strong>
    </div>
    <p class="text-muted mb-5">Temuan menunjukkan tidak adanya izin sementara VPR yang cocok saat input. Periksa akses tetap dan riwayat izin sebelum menyimpulkan pelanggaran.</p>
    <?php if (!$reports): ?>
        <div class="alert alert-info">Tidak ada temuan pada periode ini.</div>
    <?php endif; ?>
    <?php foreach ($reports as $report):
        $h = $report['header'];
        $missingAccess = $h['Finding'] === Vpr_audit_model::NO_ACCESS_FINDING;
        $findingClass = $missingAccess
            ? ($print ? 'audit-finding-danger' : 'badge badge-light-danger')
            : 'badge badge-light-warning';
    ?>
        <section class="audit-document card card-bordered mb-4">
            <div class="card-body">
                <header class="audit-heading">
                    <h2 class="fs-4 mb-2"><?= $show($h['VPR_NUMBER']) ?> <span class="<?= $findingClass ?>"><?= $show($h['Finding']) ?></span></h2>
                    <p class="mb-1"><strong>Input</strong> <?= $show($h['DATE_ADD']) ?> &middot; <?= $show($h['Input_Name']) ?> (ID <?= $show($h['USER_ADD']) ?>)</p>
                    <p class="mb-1"><strong>Vendor</strong> <?= $show($h['Vendor_Name']) ?> (ID <?= $show($h['ACCOUNT_ID']) ?>) &middot; <strong>Lokasi</strong> <?= $show($h['LOCATION_ID']) ?></p>
                    <p class="mb-1"><strong>Tanggal VPR</strong> <?= $show($h['VPR_DATE']) ?> &middot; <strong>Status</strong> <?= $show($h['VPR_STATUS']) ?> &middot; <strong>Approval</strong> <?= $show($h['APPROVAL_STATUS']) ?></p>
                    <?php if ($h['Access_Start'] !== null): ?>
                        <p class="mb-1"><strong>Izin terakhir</strong> <?= $show($h['Access_Start']) ?> s.d. <?= $show($h['Access_End']) ?></p>
                    <?php endif; ?>
                    <?php if ($h['VPR_NOTES'] !== null && $h['VPR_NOTES'] !== ''): ?>
                        <p class="mb-2"><strong>Catatan</strong> <?= nl2br($show($h['VPR_NOTES'])) ?></p>
                    <?php endif; ?>
                </header>
                <?php if ($report['items']): ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-row-bordered align-middle mb-0">
                            <thead>
                                <tr><th>Kode item</th><th>Tipe</th><th>Dimensi</th><th>Mata uang</th><th class="text-end">Harga</th><th>Tanggal efektif</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['items'] as $d): ?>
                                    <tr>
                                        <td><?= $show($d['ITEM_CODE']) ?></td><td><?= $show($d['ITEM_TYPE']) ?></td><td><?= $show($d['Dimension_ID']) ?></td>
                                        <td><?= $show($d['currency_id']) ?></td>
                                        <td class="text-end"><?= $d['PRICE'] === null ? '&ndash;' : rtrim(rtrim(number_format((float) $d['PRICE'], 4, ',', '.'), '0'), ',') ?></td>
                                        <td><?= $show($d['EFFECTIVE_DATE']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0 mt-3">Belum ada baris detail VPR.</p>
                <?php endif; ?>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>
<?php if ($print): ?>
    <script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
<?php else: ?>
    <script>
        window.addEventListener('load', function () {
            if (window.flatpickr) {
                window.flatpickr('.vpr-date', {dateFormat: 'Y-m-d', allowInput: true, disableMobile: true});
            }
        });
    </script>
<?php endif; ?>
