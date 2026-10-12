<div class="row gx-5 gx-xl-10">

    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <form id="main-form" method="post">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="card-body pt-0">
                    <div class="py-5">
                        <div class="row g-5 mt-5">
                            <div class="mb-5 el-validation">
                                <label class="form-label">Purchase Invoice Number :</label>
                                <input type="text" class="form-control form-control-sm w-50" required name="vin" id="vin" placeholder="Purchase Invoice Number....">
                                <small class="text-sm text-primary" style="font-style: italic;">lakukan update serentak dengan cara menyambung dengan koma : VIN250950-01,VIN250950-05,VIN250950-09</small>
                            </div>
                            <div class="mb-5 el-validation">
                                <label class="form-label">Cost Center :</label>
                                <select class="form-select form-select-sm w-50" data-control="select2" data-placeholder="Select an option" required name="CostCenter" id="CostCenter">
                                    <option selected disabled>-PILIH-</option>
                                    <?php foreach ($CostCenterList as $cc) : ?>
                                        <option value="<?= $cc->COSTCENTER_ID ?>"><?= $cc->CostCenter_Name_En . ' - ' . $cc->COSTCENTER_CODE ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer" style="border-top:solid #EFF2F5 2px;">
                <button type="button" class="btn btn-sm btn-primary" id="submit-main-form"><i class="fas fa-download"></i> Update Cost Center</button>
                <a href="<?= base_url() ?>" class="btn btn-danger btn-sm float-end"><i class="far fa-arrow-alt-circle-left"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>