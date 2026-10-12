<div class="row gx-5 gx-xl-10">

    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form action="" target="popup" id="FormReport" action="<?= base_url() ?>Report/Sales/Rpt_OstSO_v_Bom_v_StokMtrl" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <!-- <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Location :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-control="select2" required data-placeholder="Select an option" name="SelLocation" id="SelLocation">
                                        <option value="9">Finish Good (Owned Office)</option>
                                        <option value="1" selected="">Logistik (Owned Office)</option>
                                        <option value="7">Logistik Kayu (Owned Office)</option>
                                        <option value="8">Logistik Kayu Produksi (Owned Office)</option>
                                        <option value="6">Logistik Produksi (Owned Office)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Item Category Type :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-control="select2" data-placeholder="Select an option" required name="selCatType" id="selCatType">
                                        <option value="RM">Raw Material</option>
                                        <option value="FG">Finished Goods or Services</option>
                                        <option value="SP" selected="">Supplies</option>
                                        <option value="SF">Semi-Finished Goods</option>
                                        <option value="WIP">Working In Process</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Range Eta Date :</label>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="from" id="from" class="form-control text-center date-picker" placeholder="Start..." value="<?= date('Y-m-01') ?>">
                                        <span class="input-group-text btn-primary text-white">S/d</span>
                                        <input type="text" name="until" id="until" class="form-control text-center date-picker" placeholder="End..." value="<?= date('Y-m-t') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" class="btn btn-sm btn-light-danger float-start" id="Generate"><i class="fas fa-file"></i> Display Report E-TA PO</button>
                <a href="<?= base_url() ?>" class="btn btn-danger btn-sm float-end"><i class="far fa-arrow-alt-circle-left"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>