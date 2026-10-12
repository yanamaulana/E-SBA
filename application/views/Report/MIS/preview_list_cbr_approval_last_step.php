<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">

            <div class="card-header py-5">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link mr-5 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_4">
                                <h5 class="font-weight-bold" id="table-title-main">List Approval President Director.</h5>
                            </a>
                        </li>
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
                                    <div id="summary-container" class="alert alert-secondary d-none mb-3">
                                        <div class="d-flex align-items-center">
                                            <span class="svg-icon svg-icon-2hx svg-icon-primary me-3">
                                                <i class="fas fa-wallet fs-2 text-primary"></i>
                                            </span>
                                            <div class="d-flex flex-column">
                                                <h5 class="mb-1">Selection Summary</h5>
                                                <div id="summary-text" class="fw-bold text-gray-800 fs-6">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="TableData" class="display compact table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5">
                                        <thead style="background-color: #3B6D8C;">
                                            <tr class="text-start text-white fw-bolder text-uppercase">
                                                <th class="text-center text-white">
                                                    <div class="custom-checkbox" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="Select ALL">
                                                        <input class="form-check-input" type="checkbox" id="CheckAll" value="checkall" onclick="check_uncheck_checkbox(this.checked);">
                                                        <label for="CheckAll" class="custom-control-label"></label>
                                                    </div>
                                                </th>
                                                <th class="text-center text-white">Doc Numb</th>
                                                <th class="text-center text-white">Termin</th>
                                                <th class="text-center text-white">Type</th>
                                                <th class="text-center text-white">Amount Type</th>
                                                <th class="text-center text-white">Doc Date</th>
                                                <th class="text-center text-white">ACC Plan <i class="fas fa-calendar-alt text-white"></i></th>
                                                <th class="text-center text-white">Sign Plan <i class="fas fa-calendar-alt text-white"></i></th>
                                                <th class="text-center text-white">Curr</th>
                                                <th class="text-center text-white">Amount</th>
                                                <th class="text-center text-white">Ref No</th>
                                                <th class="text-center text-white">Description</th>
                                                <th class="text-center text-white">baseamount</th>
                                                <th class="text-center text-white">curr_rate</th>
                                                <th class="text-center text-white">Approval_Status</th>
                                                <th class="text-center text-white">Approval Status</th>
                                                <th class="text-center text-white">Paid Status</th>
                                                <th class="text-center text-white">Creation_DateTime</th>
                                                <th class="text-center text-white">Created_By</th>
                                                <th class="text-center text-white">Department</th>
                                                <th class="text-center text-white">Created By</th>
                                                <th class="text-center text-white">Acc_ID</th>
                                                <th class="text-center text-white">Last_Update</th>
                                                <th class="text-center text-white">Approved Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>