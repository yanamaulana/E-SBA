<?php
$categories = [
    ['title' => 'Sales & Marketing', 'image' => 'undraw_Marketing_re_7f1g.png', 'links' => [
        ['Report/Sales/sales_order_report?SelCurr=IDR', 'Sales Order Report'],
        ['Report/Sales/ostpo_rawmaterial', 'Raw Material Outstanding PO'],
        ['Report/Sales/index_hpp', 'Sales HPP Report'],
    ]],
    ['title' => 'Stock Opname', 'image' => 'undraw_conference_call_b0w6.png', 'links' => [
        ['Opname/index', 'Tool Generating Qty Opname'],
    ]],
    ['title' => 'Logistic & Purchasing', 'image' => 'undraw_heavy_box_agqi.png', 'links' => [
        ['Report/Logistic/eta_purchase_order', 'ETA Purchase Order'],
        ['Report/Logistic/index_price_comparison_last_v_this_year', 'Item Price Comparison'],
        ['Report/Logistic/upload_tax_invoice', 'Upload Faktur Pajak'],
        ['ScriptTool/update_costcenter', 'Repair VIN Cost Center'],
    ]],
    ['title' => 'RND Guitar', 'image' => 'undraw_researching_49yy.png', 'links' => [
        ['Report/RND/guitar_report', 'BOM Detail Report'],
    ]],
];
$isAdmin = $this->session->userdata('sys_sba_isAdm') == true;
if ($isAdmin || $this->session->userdata('sys_sba_NIK') == '09466' || $this->session->userdata('sys_sba_NIK') == '09460') {
    $categories[] = ['title' => 'HR Report', 'image' => 'undraw_team-page_q5am.png', 'links' => [
        ['ReportPemakaianCuti', 'Pemakaian Cuti Terkini'],
        ['InformasiKaryawan', 'Master Data Karyawan & Photo'],
    ]];
}
if ($isAdmin) {
    $categories[] = ['title' => 'MIS Panel', 'image' => 'Mis.png', 'restricted' => true, 'links' => [
        ['Report/MIS/ERP_AccessPermission', 'ERP Access Permission'],
        ['Report/MIS/PreviewListCbrApproval_lastStep', 'Preview List Approval'],
    ]];
}
?>
<section class="report-navigation w-100" aria-labelledby="report-navigation-title">
    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 id="report-navigation-title" class="fs-3 fw-bold mb-1">Reports &amp; Tools</h1>
            <p class="text-muted mb-0">Pilih laporan atau alat yang ingin digunakan.</p>
        </div>
        <a href="<?= base_url() ?>" class="btn btn-sm btn-light-danger flex-shrink-0">
            <i class="far fa-arrow-alt-circle-left" aria-hidden="true"></i> Back
        </a>
    </div>
    <div class="report-navigation-grid">
        <?php foreach ($categories as $index => $category): ?>
            <section class="card card-bordered report-navigation-card" aria-labelledby="report-category-<?= $index ?>">
                <div class="card-header">
                    <img src="<?= base_url('assets/media/illustrations/' . $category['image']) ?>" alt="" width="56" height="56" loading="lazy">
                    <h2 id="report-category-<?= $index ?>" class="card-title fs-5 fw-bold m-0"><?= html_escape($category['title']) ?></h2>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled m-0">
                        <?php foreach ($category['links'] as $link): ?>
                            <li>
                                <a href="<?= base_url($link[0]) ?>" class="report-navigation-link<?= !empty($category['restricted']) ? ' mis-panel-link' : '' ?>">
                                    <span><?= html_escape($link[1]) ?></span>
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
</section>
