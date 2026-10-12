<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">

            <div class="card-header py-5">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link mr-5 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_4">
                                <h5 class="font-weight-bold" id="table-title-main">Pending Payment.</h5>
                            </a>
                        </li>
                        <li class="nav-item mr-5">
                            <a class="nav-link btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#kt_tab_pane_5">
                                <h5 class="font-weight-bold" id="table-title-history">History Payment</h5>
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
                                                <th class="text-center text-white">Dept</th>
                                                <th class="text-center text-white">Requester</th>
                                                <th class="text-center text-white">Acc_ID</th>
                                                <th class="text-center text-white">Approved Date</th>
                                                <th class="text-center text-white">-</th>
                                                <th class="text-center text-white">Payment Plan Date</th>
                                                <!-- <th class="text-center text-white"><i class="fas fa-cogs"></i></th> -->
                                            </tr>
                                        </thead>
                                        <tbody class="text-gray-600 fw-bold">
                                        </tbody>
                                    </table>
                                </form>
                            </div>

                            <div class="modal fade" tabindex="-1" id="modal-excel-action">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Mass Action By Upload Excel</h5>
                                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                                                <span class="svg-icon svg-icon-2x">
                                                    <i class="fas fa-times fs-4"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="modal-body">
                                            <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                                                <span class="svg-icon svg-icon-2hx svg-icon-warning me-4">
                                                    <i class="fas fa-info-circle fs-1 text-warning"></i>
                                                </span>
                                                <div class="d-flex flex-column">
                                                    <h4 class="mb-1">Format Template</h4>
                                                    <span>Silakan gunakan template Excel yang telah disediakan agar format sesuai dengan sistem.
                                                        <br />
                                                        <strong>Kolom wajib: CBR_NUMBER, ACTION, TERMIN_KE. ACTION: 1 = Paid, 2 = Tidak Jadi Bayar.</strong>
                                                        <br />
                                                        <a href="<?= base_url('assets/templates/Mass_Action_Temp.xlsx') ?>" class="fw-bolder fs-6 text-primary border-bottom border-primary" download>
                                                            <i class="fas fa-file-excel text-primary me-1"></i> Download Template .xlsx
                                                        </a>
                                                    </span>
                                                </div>
                                            </div>

                                            <form id="form-upload-excel" enctype="multipart/form-data">
                <?php $this->load->view('partials/csrf_field'); ?>
                                                <div class="mb-3">
                                                    <label for="file_excel" class="required form-label fw-bold">Pilih File Excel</label>
                                                    <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".xls,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" required>
                                                    <div class="text-muted fs-7 mt-2">Gunakan file <strong>.xls atau .xlsx</strong>. SysID termin tidak diperlukan dan tetap dikelola internal oleh sistem.</div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-danger" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-success" id="btn-process-excel">
                                                <i class="fas fa-upload me-1"></i> Upload & Process
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                                    <option value="TA.Rec_Created_At">Submission Date</option>
                                                    <option value="H.Document_Date" selected>Document Date</option>
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
                                            <th class="text-center text-white">
                                                <input class="form-check-input" type="checkbox" id="CheckAll_hst" value="checkall_hst">
                                            </th>
                                            <th class="text-center text-white">Doc Numb</th>
                                            <th class="text-center text-white">Termin</th>
                                            <th class="text-center text-white">Date</th>
                                            <th class="text-center text-white">Curr</th>
                                            <th class="text-center text-white">Amount</th>
                                            <th class="text-center text-white">Ref No</th>
                                            <th class="text-white" style="min-width: 220px;">Description</th>
                                            <th class="text-center text-white">Base Amount</th>
                                            <th class="text-center text-white">Approval Status</th>
                                            <th class="text-center text-white">Status</th>
                                            <th class="text-center text-white">Presdir Approval</th>
                                            <th class="text-center text-white">Paid Status</th>
                                            <!-- Kolom ini ditambahkan agar sesuai dengan JS index ke-13 -->
                                            <th class="text-center text-white">Payment Status Detail</th>
                                            <th class="text-center text-white">Action By</th>
                                            <th class="text-center text-white">Paid Status (ERP)</th>
                                            <th class="text-center text-white">Creation DateTime</th>
                                            <th class="text-center text-white">Created By</th>
                                            <th class="text-center text-white">Department</th>
                                            <th class="text-center text-white">Created By (Name)</th>
                                            <th class="text-center text-white">Last Update</th>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>
