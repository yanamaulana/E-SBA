<div class="row gx-5 gx-xl-10">

    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form action="" target="popup" id="FormReport" action="<?= base_url() ?>Report/Sales/Rpt_OstSO_v_Bom_v_StokMtrl" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Location :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-control="select2" required onchange="Initialize_select2_bin(this.value)" data-placeholder="Select an option" name="SelLocation" id="SelLocation">
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
                                    <select class="form-select form-select-sm" data-control="select2" data-placeholder="Select an option" required name="selCatType" id="selCatType" onchange="Initialize_select2_category(this.value)">
                                        <option value="RM">Raw Material</option>
                                        <option value="FG">Finished Goods or Services</option>
                                        <option value="SP" selected="">Supplies</option>
                                        <option value="SF">Semi-Finished Goods</option>
                                        <option value="WIP">Working In Process</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Category :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-placeholder="Select Item Category Type" required name="Category" id="Category">
                                        <option value="ALL" selected>ALL Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Gudang/Bin :</label>
                                <div class="col-sm-5">
                                    <select class="form-select form-select-sm" data-placeholder="Select Bin" required name="Gudang" id="Gudang">
                                        <option value="1" selected="">Gudang Logistik Umum</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Date Period :</label>
                                <div class="col-sm-5">
                                    <input class="form-control form-control-sm date-picker" required name="DatePeriod" id="DatePeriod" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="row mb-6">
                                <label for="Account" class="col-sm-2 col-form-label-sm col-form-label">Item Code :</label>
                                <div class="col-sm-5">
                                    <input class="form-control form-control-sm" required name="item_code" id="item_code" placeholder="For spesific item code....">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" class="btn btn-sm btn-success" id="Generate"><i class="fas fa-sign-in-alt"></i> Generate Qty Stock Opname</button>
                <a href="<?= base_url() ?>" class="btn btn-danger btn-sm float-end"><i class="far fa-arrow-alt-circle-left"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>