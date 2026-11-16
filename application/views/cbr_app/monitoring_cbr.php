<style>
    .cbr-monitoring-table {
        font-family: Tahoma, "MS Sans Serif", Arial, sans-serif;
        font-size: 11px;
    }

    .cbr-monitoring-table table.dataTable {
        margin: 0 !important;
        border-spacing: 0;
        font-family: inherit;
        font-size: 11px;
        line-height: 1.2;
    }

    .cbr-monitoring-table table.dataTable>thead>tr>th,
    .cbr-monitoring-table table.dataTable>tbody>tr>td {
        padding: 2px 4px !important;
        vertical-align: middle;
    }

    .cbr-monitoring-table table.dataTable>tbody>tr>td.details-control {
        padding-left: 25px !important;
        background-position: 4px center;
    }

    .cbr-monitoring-table table.dataTable>thead>tr>th {
        font-weight: 700 !important;
    }

    .cbr-monitoring-table table.dataTable>tbody {
        font-weight: 400 !important;
    }

    .cbr-monitoring-table table.dataTable>thead>tr>th i,
    .cbr-monitoring-table table.dataTable>tbody>tr>td>h5 {
        margin: 0 !important;
        font-size: 11px !important;
        line-height: 1.2;
    }

    .cbr-monitoring-table table.dataTable>tbody>tr>td>.badge {
        padding: 1px 3px !important;
        font-family: inherit;
        font-size: 10px !important;
        line-height: 1.2;
        min-width: 0;
        min-height: 0;
        width: auto;
        height: auto;
    }

    .cbr-monitoring-table .cbr-child-panel {
        position: sticky;
        left: 4px;
        width: var(--cbr-detail-width, 100%);
    }

    .cbr-monitoring-table .cbr-child-scroll {
        overflow-x: auto;
        overscroll-behavior-x: contain;
    }

    .cbr-monitoring-table .cbr-child-content {
        min-width: 1100px;
    }

    .cbr-monitoring-table .cbr-child-content>.row {
        margin-left: 0;
        margin-right: 0;
    }

    .cbr-monitoring-table .payment-plan-date {
        font-size: 75%;
    }

    .cbr-monitoring-table .approval-datetime {
        display: inline-block;
        font-size: 75%;
        line-height: 1.2;
        text-align: center;
        white-space: nowrap;
    }

    .cbr-monitoring-table .dataTables_wrapper>.row {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
    }

    .cbr-monitoring-table .dataTables_filter input,
    .cbr-monitoring-table .dataTables_length select,
    .cbr-monitoring-table .dt-buttons>.btn,
    .cbr-monitoring-table .pagination .page-link {
        padding: 3px 6px !important;
        min-height: 0;
        font-family: inherit;
        font-size: 11px;
    }
</style>
<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <div class="card-body pt-0">
                <div class="py-5">
                    <div class="row">
                        <form action="#" method="post" id="filter-data">
                            <?php $this->load->view('partials/csrf_field'); ?>
                            <div class="row">
                                <div class="col-xl-2 py-2 col-md-6">
                                    <div class="input-group">
                                        <select type="text" name="employee" id="employee" class="form-control form-control-sm  text-center" data-control="select2">
                                            <option value="ALL" selected>-- ALL --</option>
                                            <?php foreach ($employees as $li) : ?>
                                                <option value="<?= $li->User_ID ?>"><?= $li->First_Name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-2 py-2 col-md-2">
                                    <div class="input-group">
                                        <select name="column_range" id="column_range" class="form-control form-control-sm text-center readonly">
                                            <option value="H.Document_Date">Document Date</option>
                                            <option value="A.Rec_Created_At">Submission Date</option>
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
                    <div class="pb-1 table-responsive cbr-monitoring-table">
                        <table id="TableDataHistory" class="table-sm align-middle display compact table-rounded table-bordered border table-striped dataTable">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-white fw-bolder text-uppercase">
                                    <th class="text-center text-white">#</th>
                                    <th class="text-center text-white">Doc Numb</th>
                                    <th class="text-center text-white"><i class="fas fa-share fs-5 text-white"></i></th>
                                    <th class="text-center text-white">Type</th>
                                    <th class="text-center text-white">Date</th>
                                    <th class="text-center text-white">Submit <br><i class="far fa-calendar-alt text-white"></i></th>
                                    <th class="text-center text-white">Curr</th>
                                    <th class="text-center text-white">Amount</th>
                                    <th class="text-center text-white">Ref No</th>
                                    <th class="text-white" style="min-width: 220px;">Description</th>
                                    <th class="text-center text-white">Base Amount</th>
                                    <th class="text-center text-white">Rate</th>
                                    <th class="text-center text-white">Approval Status</th>
                                    <th class="text-center text-white">Doc <br>Status</th>
                                    <th class="text-center text-white">Payment<br> Status</th>
                                    <th class="text-center text-white">Creation Time</th>
                                    <th class="text-center text-white">Dept</th>
                                    <th class="text-center text-white">Created<br>By</th>
                                    <th class="text-center text-white">Last Update</th>
                                    <th class="text-center text-white">Acc ID</th>
                                    <th class="text-center text-white">Approved Date</th>
                                    <!-- APPROVAL SECTION -->
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; STAFF</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; CHIEF</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ASST.MANAGER</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; MANAGER</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; SR.MANAGER</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; G.MANAGER</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; ADDITIONAL</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; <br>ACCOUNTING</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; <br>DIRECTOR</th>
                                    <!-- <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. STAFF</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. MANAGER</th> -->
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN.<br> DIRECTOR</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; <br>PRESDIR</th>
                                    <th class="text-center text-white">Payment <br>Plan <br>Date</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= base_url() ?>" class="btn btn-danger float-end"><i class="far fa-arrow-alt-circle-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>