<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form action="" target="popup" id="FormReport" action="<?= base_url() ?>Report/Sales/Rpt_OstSO_v_Bom_v_StokMtrl" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Customer :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-control="select2" data-placeholder="Select an option" name="customer" id="customer">
                                        <option value="ALL">-- ALL --</option>
                                        <?php foreach ($qAccount->result() as $qa) : ?>
                                            <option value="<?= $qa->Account_Id ?>"><?= $qa->Account_Name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Sales Type :</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input type="radio" class="form-check-input form-check-input-sm" name="sales_type" value="ALL" id="all" checked="">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio1">All</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input class="form-check-input form-check-input-sm" type="radio" name="sales_type" id="local" value="0">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio2">Local</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input class="form-check-input form-check-input-sm" type="radio" name="sales_type" id="export" value="1">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio3">Export</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">SO Date :</label>
                                <div class="col-sm-5">
                                    <div class="input-group">
                                        <input type="text" name="from" id="from" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-01') ?>">
                                        <span class="input-group-text btn btn-sm btn-primary" title="Date Range" data-toggle="tooltip"><i class="fas fa-calendar"></i> UNTIL</span>
                                        <input type="text" name="until" id="until" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-t') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Currency :</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input type="radio" class="form-check-input form-check-input-sm" name="rdocurrency" value="Doc" id="doc" checked="">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio1">Document</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input class="form-check-input form-check-input-sm" type="radio" name="rdocurrency" id="Base" value="base">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio2">Base Currency</label>
                                    </div>
                                    <div class="form-check form-check-inline form-check-sm">
                                        <input class="form-check-input form-check-input-sm" type="radio" name="rdocurrency" id="Rate" value="rate">
                                        <label class="form-check-label form-check-label-sm" for="inlineRadio3">Rate</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label"></label>
                                <div class="col-sm-4">
                                    <select class="form-select form-select-sm" data-control="select2" data-placeholder="Select an option" name="selCurrency" id="selCurrency">
                                        <?php foreach ($qCurrency->result() as $qc) : ?>
                                            <option value="<?= $qc->Currency_ID ?>" <?php if ($SelCurr == $qc->Currency_ID) : ?> selected <?php endif; ?>><?= $qc->currency_symbol ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <?php foreach ($qCurrency->result() as $qc) : ?>
                                <?php if ($SelCurr != $qc->Currency_ID) : ?>

                                    <?php $QRate = $this->db->query("SELECT currency_id_1, currency_id_2, scale
                                        FROM TCurrencyConverter
                                        WHERE currency_id_1 = '$qc->Currency_ID'
                                        AND currency_id_2 = '$SelCurr'
                                        AND Status = 1
                                        AND Company_ID = 2 ")->row();
                                    ?>

                                    <div class="row mt-1">
                                        <label for="Account" class="col-sm-2 col-form-label-sm col-form-label text-end"><?= $qc->currency_symbol ?> :</label>
                                        <div class="col-sm-3">
                                            <input class="form-control form-control-sm" name="Txt<?= $qc->currency_symbol ?>" id="Txt<?= $qc->currency_symbol ?>" <?php if (empty($QRate->scale)) : ?> value="0.0000" <?php else : ?> value="<?= number_format(floatval($QRate->scale), 4, ".", ","); ?>" <?php endif; ?>>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" value="pdf" class="btn btn-sm btn-light-danger" id="Pdf"><i class="fas fa-file-pdf"></i> Display Report</button>
                <!-- <button type="button" value="excel" class="btn btn-sm btn-light-success" id="Excel"><i class="fas fa-file-excel"></i> Export To Ms. Excel</button> -->
                <a href="<?= base_url() ?>" class="btn btn-danger btn-sm float-end"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>