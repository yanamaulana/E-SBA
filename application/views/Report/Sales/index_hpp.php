<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form action="" target="popup" id="FormReport" action="<?= base_url() ?>Report/Sales/Rpt_OstSO_v_Bom_v_StokMtrl" method="post">
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-6 col-form-label-sm col-form-label">Script ini akan langsung mendownload penjualan dengan hpp nya, anda hanya perlu memilih range tanggal document</label>
                            </div>
                            <div class="row mb-6 mt-5">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Document Date :</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="from" id="from" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-01-01') ?>">
                                        <span class="input-group-text btn btn-sm btn-primary" title="Date Range" data-toggle="tooltip"><i class="fas fa-calendar"></i> UNTIL</span>
                                        <input type="text" name="until" id="until" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-t') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" value="pdf" class="btn btn-danger btn-report"><i class="fas fa-file-pdf"></i> Display Report</button>
                <button type="button" value="excel" class="btn btn-success btn-report"><i class="fas fa-file-excel"></i> Export Excel</button>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>