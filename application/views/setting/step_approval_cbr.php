<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><?= $page_title; ?></h3>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-light-danger" id="back-button">
                        <i class="fas fa-arrow-alt-circle-left"></i> Back
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="el-table">
                    <div class="table-responsive">
                        <table id="TableData" class="display compact nowrap table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-start text-white fw-bolder text-uppercase">
                                    <th class="text-center text-white">#</th>
                                    <th class="text-center text-white"><i class="fas fa-cogs"></i></th>
                                    <th class="text-center text-white">Approval Name</th>
                                    <th class="text-center text-white">Chief</th>
                                    <th class="text-center text-white">Chief <i class="fas fa-user"></th>
                                    <th class="text-center text-white">Asst.Manager</th>
                                    <th class="text-center text-white">Asst.Manager <i class="fas fa-user"></th>
                                    <th class="text-center text-white">Manager</th>
                                    <th class="text-center text-white">Manager <i class="fas fa-user"></th>
                                    <th class="text-center text-white">SR.Manager</th>
                                    <th class="text-center text-white">SR.Manager <i class="fas fa-user"></th>
                                    <th class="text-center text-white">GM</th>
                                    <th class="text-center text-white">GM <i class="fas fa-user"></i></th>
                                    <th class="text-center text-white">Additional</th>
                                    <th class="text-center text-white">Additional <i class="fas fa-user"></i></th>
                                    <th class="text-center text-white">Director</th>
                                    <th class="text-center text-white">Director <i class="fas fa-user"></i></th>
                                    <th class="text-center text-white">PresDir</th>
                                    <th class="text-center text-white">PresDir <i class="fas fa-user"></i></th>
                                    <th class="text-center text-white">FinDir</th>
                                    <th class="text-center text-white">FinDir <i class="fas fa-user"></i></th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                            </tbody>
                        </table>
                    </div>
                </div>
                <form id="main-form" class="form-horizontal" enctype="multipart/form-data" action="javascript:void(0)">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Template Approval Name :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="Setting_Approval_Code" id="Setting_Approval_Code" placeholder="Input Template Approval Name" aria-label="Recipient's username" required>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Chief :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Chief" id="Chief_Yes" value="1">
                                    <label class="form-check-label" for="Chief_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Chief" id="Chief_No" value="0" checked>
                                    <label class="form-check-label" for="Chief_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Chief PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Chief_person" id="Chief_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Chief">

                                <input type="hidden" name="Chief_valid" id="Chief_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Asst. Manager :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="AsstManager" id="AsstManager_Yes" value="1">
                                    <label class="form-check-label" for="AsstManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="AsstManager" id="AsstManager_No" value="0" checked>
                                    <label class="form-check-label" for="AsstManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Asst. Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="AsstManager_person" id="AsstManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Asst Manager">

                                <input type="hidden" name="AsstManager_valid" id="AsstManager_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Manager :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Manager" id="Manager_Yes" value="1">
                                    <label class="form-check-label" for="Manager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Manager" id="Manager_No" value="0" checked>
                                    <label class="form-check-label" for="Manager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Manager_person" id="Manager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Manager">

                                <input type="hidden" name="Manager_valid" id="Manager_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Senior Manager :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="SeniorManager" id="SeniorManager_Yes" value="1">
                                    <label class="form-check-label" for="SeniorManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="SeniorManager" id="SeniorManager_No" value="0" checked>
                                    <label class="form-check-label" for="SeniorManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Senior Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="SeniorManager_person" id="SeniorManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Senior Manager">

                                <input type="hidden" name="SeniorManager_valid" id="SeniorManager_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL General Manager :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="GeneralManager" id="GeneralManager_Yes" value="1">
                                    <label class="form-check-label" for="GeneralManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="GeneralManager" id="GeneralManager_No" value="0" checked>
                                    <label class="form-check-label" for="GeneralManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">General Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="GeneralManager_person" id="GeneralManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="General Manager">

                                <input type="hidden" name="GeneralManager_valid" id="GeneralManager_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">Additional APPROVAL :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Additional" id="Additional_Yes" value="1">
                                    <label class="form-check-label" for="Additional_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Additional" id="Additional_No" value="0" checked>
                                    <label class="form-check-label" for="Additional_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Additional PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Additional_person" id="Additional_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="">

                                <input type="hidden" name="Additional_valid" id="Additional_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Director :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Director" id="Director_Yes" value="1">
                                    <label class="form-check-label" for="Director_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Director" id="Director_No" value="0" checked>
                                    <label class="form-check-label" for="Director_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Director PERSON :</label>
                            <?php
                            $tooltip_content = '';
                            foreach ($dir_data as $dir) {
                                // Gunakan <br> untuk baris baru
                                $tooltip_content .= $dir->Emp_No . ' - ' . $dir->First_Name . ' (' . $dir->Pos_Name . ')' . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            }
                            ?>
                            <div class="input-group input-group-sm">
                                <div class="input-group-append">
                                    <button class="btn btn-light-info" type="button" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-delay-hide="3000" data-bs-placement="top"
                                        title="<?= $tooltip_content ?>"><i class="fas fa-list-alt"></i>List Director</button>
                                </div>
                                <input type="text" class="form-control nik" name="Director_person" id="Director_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="1" data-pos="Board Of Directors">

                                <input type="hidden" name="Director_valid" id="Director_valid" class="validation" value="0">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Validation</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL President Director :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PresidentDirector" id="PresidentDirector_Yes" value="1">
                                    <label class="form-check-label" for="PresidentDirector_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PresidentDirector" id="PresidentDirector_No" value="0" checked>
                                    <label class="form-check-label" for="PresidentDirector_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">President Director PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="PresidentDirector_person" id="PresidentDirector_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="1" data-pos="Board Of Directors" value="90108" readonly>

                                <input type="hidden" name="PresidentDirector_valid" id="PresidentDirector_valid" class="validation" value="1">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                        <p class="text-success"><b>Eric Kim (President Director)</b></p>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Finance Director :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="FinanceDirector" id="FinanceDirector_Yes" value="1">
                                    <label class="form-check-label" for="FinanceDirector_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="FinanceDirector" id="FinanceDirector_No" value="0" checked>
                                    <label class="form-check-label" for="FinanceDirector_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Finance Director PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="FinanceDirector_person" id="FinanceDirector_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="1" data-dir="1" data-pos="Board Of Directors" value="90112" readonly>

                                <input type="hidden" name="FinanceDirector_valid" id="FinanceDirector_valid" class="validation" value="1">

                                <div class="input-group-append">
                                    <button class="btn btn-danger validate-person" type="button"><i class="fas fa-user"></i>Person Validation</button>
                                </div>
                            </div>
                        </div>
                        <p class="text-success"><b>Ha Dong Hyun (Finance Director)</b></p>
                    </div>
                    <hr class="devider">



                </form>
            </div>
            <div class="card-footer">
                <a href="<?= base_url() ?>" class="btn btn-danger float-end"><i class="far fa-times-circle"></i> Cancel</a>
                <button type="button" id="submit-main-data" class="btn btn-primary me-2 mb-2 shadow-sm">
                    <span class="svg-icon svg-icon-1 svg-icon-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black" />
                            <path opacity="0.3" d="M13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H13Z" fill="black" />
                            <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H8L11.3 17.7C11.7 18.1 12.3 18.1 12.7 17.7L16 14.4H13Z" fill="black" />
                        </svg>
                    </span> &nbsp;&nbsp;<strong>Save</strong>&nbsp;&nbsp;
                </button>
            </div>
        </div>
    </div>
</div>