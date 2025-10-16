<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <div class="card-body pt-0">
                <div class="py-5">
                    <div class="row">
                        <form action="#" method="post" id="filter-data">
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
                    </div>
                    <hr style="padding-top: 5px; color: black; background-color: black;" />
                    <div class="pb-5 table-responsive">
                        <table id="TableDataHistory" class="table-sm align-middle display compact table-rounded table-bordered border dataTable no-footer dt-inline">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-white fw-bolder text-uppercase">
                                    <th class="text-center text-white">#</th>
                                    <th class="text-center text-white">Doc Numb</th>
                                    <th class="text-center text-white">Submission</th>
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
                                    <th class="text-center text-white">Dept</th>
                                    <th class="text-center text-white">Created_By</th>
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
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; DIR G/P</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; PRESDIR</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. STAFF</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. MANAGER</th>
                                    <th class="text-center text-white"><i class="fas fa-edit fs-5 text-white"></i>&nbsp; FIN. DIRECTOR</th>

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