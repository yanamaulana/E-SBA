<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">

            <div class="card-header py-5">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link mr-5 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_4">
                                <h5 class="font-weight-bold" id="table-title-main">Requires Accounting Approval.</h5>
                            </a>
                        </li>
                        <li class="nav-item mr-5">
                            <a class="nav-link btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_5">
                                <h5 class="font-weight-bold" id="table-title-history">Monitoring & History Accounting Approval.</h5>
                            </a>
                        </li>
                        <li class="nav-item mr-5">
                            <a class="nav-link btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_mon_termin">
                                <h5 class="font-weight-bold" id="table-title-history">Monitoring Termin.</h5>
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
                                                <th class="text-center text-white">Type</th>
                                                <th class="text-center text-white">Date</th>
                                                <th class="text-center text-white">Curr</th>
                                                <th class="text-center text-white">Amount</th>
                                                <th class="text-center text-white">Ref No</th>
                                                <th class="text-center text-white">Description</th>
                                                <th class="text-center text-white">baseamount</th>
                                                <th class="text-center text-white">curr_rate</th>
                                                <th class="text-center text-white">Approval_Status</th>
                                                <th class="text-center text-white">Status</th>
                                                <th class="text-center text-white">Paid Status</th>
                                                <th class="text-center text-white">Creation_DateTime</th>
                                                <th class="text-center text-white">Created_By</th>
                                                <th class="text-center text-white">Department</th>
                                                <th class="text-center text-white">Created By</th>
                                                <th class="text-center text-white">Last_Update</th>
                                                <th class="text-center text-white">Acc_ID</th>
                                                <th class="text-center text-white">Approved Date</th>
                                                <th class="text-center text-white">Sign Plan <i class="fas fa-calendar-alt text-white"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                            <div class="row">
                                <form action="#" method="post" id="filter-data">
                <?php $this->load->view('partials/csrf_field'); ?>
                                    <div class="row">
                                        <div class="col-xl-2 py-2 col-md-2">
                                            <div class="input-group">
                                                <select name="column_range" id="column_range" class="form-control form-control-sm text-center readonly">
                                                    <option value="TaccCashBookReq_Header.Document_Date" selected>Document Date</option>
                                                    <option value="Ttrx_Cbr_Approval.Rec_Created_At">Submission Date</option>
                                                </select>
                                            </div>
                                        </div>
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
                            </div>
                            <hr style="padding-top: 5px; color: black; background-color: black;" />
                            <div class="pb-5 table-responsive">
                                <table id="TableDataHistory" class="table-sm align-middle display compact table-rounded table-striped table-bordered border dataTable no-footer dt-inline">
                                    <thead style="background-color: #3B6D8C;">
                                        <tr class="text-white fw-bolder text-uppercase">
                                            <th class="text-center text-white">#</th>
                                            <th class="text-center text-white">Doc Numb</th>
                                            <th class="text-center text-white">Type</th>
                                            <th class="text-center text-white">Date</th>
                                            <th class="text-center text-white">Curr</th>
                                            <th class="text-center text-white">Amount</th>
                                            <th class="text-center text-white">Ref No</th>
                                            <th class="text-white" style="min-width: 220px;">Description</th>
                                            <th class="text-center text-white">baseamount</th>
                                            <th class="text-center text-white">curr_rate</th>
                                            <th class="text-center text-white">Approval_Status</th>
                                            <th class="text-center text-white">Status</th>
                                            <th class="text-center text-white">Paid Status</th>
                                            <th class="text-center text-white">Creation_DateTime</th>
                                            <th class="text-center text-white">Created_By</th>
                                            <th class="text-center text-white">Department</th>
                                            <th class="text-center text-white">Created By</th>
                                            <th class="text-center text-white">Last_Update</th>
                                            <th class="text-center text-white">Acc_ID</th>
                                            <th class="text-center text-white">Approved Date</th>
                                            <!-- APPROVAL SECTION -->
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; STAFF</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; CHIEF</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ASST.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; SR.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; G.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ADDITIONAL</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ACCOUNTING</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; DIRECTOR</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. DIRECTOR</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; PRESDIR</th>

                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="kt_tab_pane_mon_termin" role="tabpanel">
                            <form action="#" method="post" id="filter-data">
                <?php $this->load->view('partials/csrf_field'); ?>
                                <div class="row bg-light mb-5 py-5 px-3 rounded">
                                    <div class="py-2 col-md-2">
                                        <div class="input-group">
                                            <select name="column_range" id="column_range_termin" class="form-control form-control-sm text-center readonly">
                                                <option value="H.Document_Date" selected>Document Date</option>
                                                <!-- <option value="TA.AppvPresidentDirector_At" selected>Approved Date</option> -->
                                                <option value="TA.Rec_Created_At">Submission Date</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="py-2 col-md-4">
                                        <div class="input-group">
                                            <input type="text" name="from_termin" id="from_termin" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-01') ?>">
                                            <span class="input-group-text btn btn-sm btn-primary" title="Date Range" data-toggle="tooltip"><i class="fas fa-calendar"></i> UNTIL</span>
                                            <input type="text" name="until_termin" id="until_termin" class="form-control form-control-sm  date-picker text-center readonly" value="<?= date('Y-m-t') ?>">
                                            <button type="button" id="do--filter_termin" class="btn btn-danger btn-sm text-white">&nbsp;<i class="fas fa-search fs-4 me-2"></i> Search</button>
                                        </div>
                                    </div>
                                    <div class="py-2 col-md-6" id="data-summary-container">
                                        <!-- data summary akan di tampilkan di sini-->

                                    </div>
                                </div>
                            </form>

                            <hr style="padding-top: 5px; color: black; background-color: black;" />
                            <div class="pb-5 table-responsive">
                                <table id="TableDataTermin" class="table-sm align-middle display compact table-rounded table-striped table-bordered border dataTable no-footer dt-inline">
                                    <thead style="background-color: #3B6D8C;">
                                        <tr class="text-white fw-bolder text-uppercase">
                                            <th class="text-center text-white">#</th>
                                            <th class="text-center text-white">Doc Numb</th>
                                            <th class="text-center text-white">Termin</th>
                                            <th class="text-center text-white">Amount Type</th>
                                            <th class="text-center text-white">Date</th>
                                            <th class="text-center text-white">Schedule Acc</th>
                                            <th class="text-center text-white">Sign Schedule</th>
                                            <th class="text-center text-white">Curr</th>
                                            <th class="text-center text-white">Amount</th>
                                            <th class="text-white" style="min-width: 220px;">Description</th>
                                            <th class="text-center text-white">Status</th>
                                            <th class="text-center text-white">Approval Status</th>
                                            <th class="text-center text-white">Paid Status</th>
                                            <th class="text-center text-white">Department</th>
                                            <th class="text-center text-white">Created By</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ASST.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; SR.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; G.MANAGER</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ADDITIONAL</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ACCOUNTING</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; DIRECTOR</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. DIRECTOR</th>
                                            <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; PRESDIR</th>
                                            <th class="text-center text-white">Transfer Date</th>

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
<div class="modal fade" id="modal_set_termin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-4">
                <h5 class="modal-title fs-4">Atur Pembayaran Termin - <span id="txt_modal_cbr_no" class="text-primary"></span></h5>
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x fs-1"></i>
                </div>
            </div>
            <form id="form_termin" method="POST" action="<?= base_url('CbrAppAccounting/save_termin') ?>">
                <?php $this->load->view('partials/csrf_field'); ?>
                <div class="modal-body pt-4">
                    <div id="msg_termin_complete" class="d-none alert alert-success text-center">
                        <i class="fas fa-check-circle text-success fs-2x"></i>
                        <h4 class="text-success mt-2">Termin Pembayaran Selesai!</h4>
                        <p class="mb-0">Seluruh nominal CBR telah disetujui secara penuh.</p>
                    </div>
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-3 mb-4">
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <span class="text-gray-800 fw-bold fs-6 me-4">Total CBR: <span id="txt_modal_total_amount" class="text-dark">0</span></span>
                                <span class="text-gray-700 fs-6">Sisa Limit: <span id="txt_modal_remaining_amount" class="text-danger fw-bold">0</span></span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success text-dark" id="btn_add_termin_row" disabled>
                            <i class="fas fa-plus me-1"></i>Tambah Baris
                        </button>
                    </div>
                    <input type="hidden" name="cbreq_no" id="inp_modal_cbreq_no">
                    <input type="hidden" id="inp_modal_total_amount_raw">

                    <div class="table-responsive">
                        <table class="table table-bordered table-row-gray-300 align-middle gs-2 gy-2">
                            <thead>
                                <tr class="fw-bold text-muted fs-7 text-uppercase bg-light">
                                    <th class="w-50px text-center ps-2">No</th>
                                    <th class="w-120px text-center">Status</th>
                                    <th class="min-w-150px">Nominal Pembayaran</th>
                                    <th class="min-w-150px">Rencana Tgl Bayar</th>
                                    <th class="min-w-150px">Amount Type</th>
                                    <th class="w-50px text-center pe-2"><i class="fas fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="termin_table_body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="btn_save_termin">Simpan Termin</button>
                </div>
            </form>
        </div>
    </div>
</div>