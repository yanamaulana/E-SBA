<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><?= $page_title; ?></h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light-danger" onclick="window.history.back()">
                        <i class="fas fa-arrow-alt-circle-left"></i> Back
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="main-form" class="form-horizontal" enctype="multipart/form-data" action="javascript:void(0)">
                    <div class="form-group row">
                        <label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm">Step Approval Name :</label>
                        <div class="col-sm-5">
                            <div class="input-group">
                                <button class="input-group-text"><i class="fas fa-edit fs-4 text-dark"></i></button>
                                <div class="flex-grow-1">
                                    <select class="form-select rounded-0" data-control="select2" data-placeholder="Select an option" name="Approval" id="Approval">
                                        <option selected disabled>-Choose Step Approval-</option>
                                        <?php foreach ($Approvals as $li) : ?>
                                            <?= '<option value="' . $li->SysId . '">' . $li->Setting_Approval_Code . '</option>' ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <!-- <button class="input-group-text btn btn-danger" id="btn-search"><i class="fas fa-search"></i> User</button> -->
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6" style="border-right: solid #b1afafff 1px">
                            <!-- input select2 bisa multiple select value, data employee, yang akan di panggil dengan ajax -->
                            <div class="row">
                                <label for="colFormLabelSm" class="col-sm-12 col-form-label col-form-label-sm">Select Employee :</label>
                                <div class="col-sm-12">
                                    <select class="form-select rounded-0" data-control="select2" data-placeholder="Select Employee" name="Employee[]" id="Employee" data-allow-clear="true" multiple="multiple">
                                        <!-- Options will be populated via AJAX -->
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-5 mt-5">
                                    <button type="button" id="submit-main-data" class="btn btn-primary btn-sm me-2 mb-2 shadow-sm">
                                        <span class="svg-icon svg-icon-1 svg-icon-muted">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black" />
                                                <path opacity="0.3" d="M13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H13Z" fill="black" />
                                                <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H8L11.3 17.7C11.7 18.1 12.3 18.1 12.7 17.7L16 14.4H13Z" fill="black" />
                                            </svg>
                                        </span> &nbsp;&nbsp;<strong>Assign Approval</strong>&nbsp;&nbsp;
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table id="TableData" class="display compact nowrap table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5" style="width:100%">
                                    <thead style="background-color: #3B6D8C;">
                                        <tr class="text-start text-white fw-bolder text-uppercase">
                                            <th>No</th>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 fw-bold">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <a href="javascript:void(0)" onclick="window.history.back()" class="btn btn-danger float-end"><i class="far fa-times-circle"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>