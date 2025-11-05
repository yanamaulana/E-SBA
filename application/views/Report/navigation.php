<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <div class="card-body pt-0">
                <div class="py-5">
                    <div class="row g-5">
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-bordered shadow-sm">
                                <div class="card-body p-0">
                                    <div class="dropdown text-center">
                                        <button class="btn btn-lg dropdown-toggle text-dark" type="button" id="dropdownMenu1" data-bs-toggle="dropdown" aria-expanded="false">
                                            <strong>Sales Report</strong>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenu1">
                                            <li><a href="<?= base_url('Report/Sales/sales_order_report?SelCurr=IDR') ?>" class="dropdown-item">Sales Order Report</a></li>
                                            <li><a href="<?= base_url('Report/Sales/ostpo_rawmaterial') ?>" class="dropdown-item">Raw Material Outstanding PO</a></li>
                                            <li><a href="<?= base_url('Report/Sales/index_hpp') ?>" class="dropdown-item">Sales HPP Report</a></li>
                                        </ul>
                                    </div>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px card-rounded-bottom" alt="Image Illustration" src="<?= base_url() ?>assets/media/illustrations/undraw_Marketing_re_7f1g.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-bordered shadow-sm">
                                <div class="card-body p-0">
                                    <div class="dropdown text-center">
                                        <button class="btn btn-lg dropdown-toggle text-dark" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                            <strong>HR Report</strong>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenu2">
                                            <li><button class="dropdown-item" type="button">Action</button></li>
                                            <li><button class="dropdown-item" type="button">Another action</button></li>
                                            <li><button class="dropdown-item" type="button">Something else here</button></li>
                                        </ul>
                                    </div>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px card-rounded-bottom" alt="Image Illustration" src="<?= base_url() ?>assets/media/illustrations/undraw_conference_call_b0w6.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="card card-bordered shadow-sm">
                                <div class="card-body p-0">
                                    <div class="dropdown text-center">
                                        <button class="btn btn-lg dropdown-toggle text-dark" type="button" id="dropdownMenu3" data-bs-toggle="dropdown" aria-expanded="false">
                                            <strong>Logistic Report</strong>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="dropdownMenu3">
                                            <li><a href="<?= base_url('Report/Logistic/eta_purchase_order') ?>" class="dropdown-item">ETA Purchase Order</a></li>
                                            <li><a href="<?= base_url('Report/Logistic/index_price_comparison_last_v_this_year') ?>" class="dropdown-item">Item Price Comparison </a></li>
                                            <li><a href="<?= base_url('ScriptTool/update_costcenter') ?>" class="dropdown-item"> Repair VIN Cost Center</a></li>
                                        </ul>
                                    </div>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px card-rounded-bottom" alt="Image Illustration" src="<?= base_url() ?>assets/media/illustrations/undraw_heavy_box_agqi.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= base_url() ?>" class="btn btn-danger float-end"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>