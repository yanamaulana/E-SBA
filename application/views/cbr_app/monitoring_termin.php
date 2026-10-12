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
                                            <option value="TaccCashBookReq_Header.Document_Date">Document Date</option>
                                            <option value="TAccCashBookReq_Header.Creation_DateTime">Creation Date</option>
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
                        <table id="TableDataHistory" class="table-sm align-middle display compact nowrap dt-nowrap table-rounded table-bordered border dataTable no-footer dt-inline">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-white fw-bolder text-uppercase">
                                    <th class="text-center text-white">#</th>
                                    <th class="text-center text-white">Doc Numb</th>
                                    <th class="text-center text-white">Type</th>
                                    <th class="text-center text-white">Date</th>
                                    <th class="text-center text-white">Curr</th>
                                    <th class="text-center text-white">Total Amount</th>
                                    <th class="text-center text-white">Ref No</th>
                                    <th class="text-white" style="min-width: 220px;">Description</th>
                                    <th class="text-center text-white">baseamount</th>
                                    <th class="text-center text-white">curr_rate</th>
                                    <th class="text-center text-white">Approval_Status</th>
                                    <th class="text-center text-white">Doc Status</th>
                                    <th class="text-center text-white">Payment Status</th>
                                    <th class="text-center text-white">Creation_DateTime</th>
                                    <th class="text-center text-white">Dept</th>
                                    <th class="text-center text-white">Created_By</th>
                                    <th class="text-center text-white">Last_Update</th>
                                    <th class="text-center text-white">Acc_ID</th>
                                    <th class="text-center text-white">Approved Date</th>
                                    <th class="text-center text-warning">Termin Submitted</th>
                                    <th class="text-center text-warning">Total Termin Amount</th>
                                    <th class="text-center text-danger">Remaining Amount</th>
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
