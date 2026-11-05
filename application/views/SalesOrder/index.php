<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">

            <div class="card-header py-5">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link mr-5 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_4">
                                <h5 class="font-weight-bold" id="table-title-main">Sales Order.</h5>
                            </a>
                        </li>
                        <!-- <li class="nav-item mr-5">
                            <a class="nav-link btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_5">
                                <h5 class="font-weight-bold" id="table-title-history">History Payment</h5>
                            </a>
                        </li> -->
                    </ul>
                </div>
                <div class="card-toolbar">
                    <a href="<?= base_url('Dashboard') ?>" type="button" class="btn btn-sm btn-light-danger"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="py-5">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade active show" id="kt_tab_pane_4" role="tabpanel">
                            <div class="pb-5 table-responsive">
                                <form action="#" id="form-submission" method="post">
                                    <?php $this->load->view('partials/csrf_field'); ?>
                                    <div class="row">
                                        <div class="col-xl-4 py-2 col-md-6">
                                            <div class="input-group">
                                                <input type="text" name="from" id="from" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-01') ?>">
                                                <span class="input-group-text btn btn-sm btn-primary" title="Date Range" data-toggle="tooltip"><i class="fas fa-calendar"></i> UNTIL</span>
                                                <input type="text" name="until" id="until" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-t') ?>">
                                            </div>
                                        </div>
                                        <div class="col-xl-3 py-2 col-md-6">
                                            <div class="input-group">
                                                <button type="button" id="do--filter" class="btn btn-danger btn-sm text-white">&nbsp;<i class="fas fa-search fs-4 me-2"></i> Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <hr />
                                <table id="TableData" class="display compact table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5">
                                    <thead style="background-color: #3B6D8C;">
                                        <tr class="text-start text-white fw-bolder text-uppercase">
                                            <th class="text-center text-white">
                                                <div class="custom-checkbox" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="Select ALL">
                                                    <input class="form-check-input" type="checkbox" id="CheckAll" value="checkall" onclick="check_uncheck_checkbox(this.checked);">
                                                    <label for="CheckAll" class="custom-control-label"></label>
                                                </div>
                                            </th>
                                            <th class="text-center text-white">SO Number</th>
                                            <th class="text-center text-white">Customer</th>
                                            <th class="text-center text-white">Cust PO No</th>
                                            <th class="text-center text-white">SO Date</th>
                                            <th class="text-center text-white">Doc Status</th>
                                            <th class="text-center text-white">SO Status</th>
                                            <th class="text-center text-white">Approval</th>
                                            <th class="text-center text-white">Invoiced</th>
                                            <th class="text-center text-white">Is Active</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>