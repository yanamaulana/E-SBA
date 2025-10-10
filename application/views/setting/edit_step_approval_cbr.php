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
                    <input type="hidden" name="SysId" id="SysId" value="<?= $setting->SysId ?>">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Template Approval Name :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" name="Setting_Approval_Code" id="Setting_Approval_Code" placeholder="Input Template Approval Name" aria-label="Recipient's username" required value="<?= $setting->Setting_Approval_Code ?>">
                            </div>
                        </div>
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Chief :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Chief" id="Chief_Yes" value="1" <?= ($setting->Chief == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Chief_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Chief" id="Chief_No" value="0" <?= ($setting->Chief == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Chief_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Chief PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Chief_person" id="Chief_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Chief" value="<?= $setting->Chief_Person ?>">

                                <input type="hidden" name="Chief_valid" id="Chief_valid" class="validation" value="<?= $setting->Chief ?>">

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
                                    <input class="form-check-input" type="radio" name="AsstManager" id="AsstManager_Yes" value="1" <?= ($setting->AsstManager == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="AsstManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="AsstManager" id="AsstManager_No" value="0" checked <?= ($setting->AsstManager == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="AsstManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Asst. Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="AsstManager_person" id="AsstManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Asst Manager" value="<?= $setting->AsstManager_Person ?>">

                                <input type="hidden" name="AsstManager_valid" id="AsstManager_valid" class="validation" value="<?= $setting->AsstManager ?>">

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
                                    <input class="form-check-input" type="radio" name="Manager" id="Manager_Yes" value="1" <?= ($setting->Manager == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Manager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Manager" id="Manager_No" value="0" <?= ($setting->Manager == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Manager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Manager_person" id="Manager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Manager" value="<?= $setting->Manager_Person ?>">

                                <input type="hidden" name="Manager_valid" id="Manager_valid" class="validation" value="<?= $setting->Manager ?>">

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
                                    <input class="form-check-input" type="radio" name="SeniorManager" id="SeniorManager_Yes" value="1" <?= ($setting->SeniorManager == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="SeniorManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="SeniorManager" id="SeniorManager_No" value="0" <?= ($setting->SeniorManager == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="SeniorManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Senior Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="SeniorManager_person" id="SeniorManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="Senior Manager" value="<?= $setting->SeniorManager_Person ?>">

                                <input type="hidden" name="SeniorManager_valid" id="SeniorManager_valid" class="validation" value="<?= $setting->SeniorManager ?>">

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
                                    <input class="form-check-input" type="radio" name="GeneralManager" id="GeneralManager_Yes" value="1" <?= ($setting->GeneralManager == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="GeneralManager_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="GeneralManager" id="GeneralManager_No" value="0" <?= ($setting->GeneralManager == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="GeneralManager_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">General Manager PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="GeneralManager_person" id="GeneralManager_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="General Manager" value="<?= $setting->GeneralManager_Person ?>">

                                <input type="hidden" name="GeneralManager_valid" id="GeneralManager_valid" class="validation" value="<?= $setting->GeneralManager ?>">

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
                                    <input class="form-check-input" type="radio" name="Additional" id="Additional_Yes" value="1" <?= ($setting->Additional == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Additional_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Additional" id="Additional_No" value="0" <?= ($setting->Additional == 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Additional_No">NO</label>
                                </div>

                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label pr-5">Additional PERSON :</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control nik" name="Additional_person" id="Additional_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="0" data-pos="" value="<?= $setting->Additional_Person ?>">

                                <input type="hidden" name="Additional_valid" id="Additional_valid" class="validation" value="<?= $setting->Additional ?>">

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
                                    <input class="form-check-input" type="radio" name="Director" id="Director_Yes" value="1" <?= ($setting->Director == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="Director_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="Director" id="Director_No" value="0" <?= ($setting->Director == 0) ? 'checked' : '' ?>>
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
                                <input type="text" class="form-control nik" name="Director_person" id="Director_person" placeholder="NIK/Username ERP Sunfish" aria-label="Recipient's username" aria-describedby="button-addon2" data-fin="0" data-dir="1" data-pos="Board Of Directors" value="<?= $setting->Director_Person ?>">

                                <input type="hidden" name="Director_valid" id="Director_valid" class="validation" value="<?= $setting->Director ?>">

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
                                    <input class="form-check-input" type="radio" name="PresidentDirector" id="PresidentDirector_Yes" value="1" <?= ($setting->PresidentDirector == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="PresidentDirector_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="PresidentDirector" id="PresidentDirector_No" value="0" <?= ($setting->PresidentDirector == 0) ? 'checked' : '' ?>>
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
                    </div>
                    <hr class="devider">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label pr-5">APPROVAL Finance Director :</label>
                            <div class="fv-row pt-5">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="FinanceDirector" id="FinanceDirector_Yes" value="1" <?= ($setting->FinanceDirector == 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="FinanceDirector_Yes">YES</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="FinanceDirector" id="FinanceDirector_No" value="0" <?= ($setting->FinanceDirector == 0) ? 'checked' : '' ?>>
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
                    </div>
                    <hr class="devider">



                </form>
            </div>
            <div class="card-footer">
                <a href="javascript:void(0)" onclick="window.history.back()" class="btn btn-danger float-end"><i class="far fa-times-circle"></i> Cancel</a>
                <button type="button" id="submit-main-data" class="btn btn-primary me-2 mb-2 shadow-sm">
                    <span class="svg-icon svg-icon-1 svg-icon-muted">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z" fill="black" />
                            <path opacity="0.3" d="M13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H13Z" fill="black" />
                            <path d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM13 14.4V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V14.4H8L11.3 17.7C11.7 18.1 12.3 18.1 12.7 17.7L16 14.4H13Z" fill="black" />
                        </svg>
                    </span> &nbsp;&nbsp;<strong>Update</strong>&nbsp;&nbsp;
                </button>
            </div>
        </div>
    </div>
</div>