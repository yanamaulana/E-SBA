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
                        <table id="TableData" class="display compact nowrap table-bordered table-sm align-middle gy-5 gs-5">
                            <thead style="background-color: #3B6D8C;">
                                <tr class="text-start text-white fw-bolder text-uppercase">
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
<div>
    <div class="modal fade" tabindex="-1" id="modal-add-permission">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Add New Permission</h5>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-2x"></span>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    <form id="main-form" class="form-horizontal" action="javascript:void(0)">
                <?php $this->load->view('partials/csrf_field'); ?>
                        <div class="row">
                            <label for="username" class="col-sm-12 col-form-label col-form-label">Input Valid NIK/User Name :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" required placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2" id="username" name="username">
                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"><i class="fas fa-user fs-1 text-dark"></i></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="submit-main-data">Save Permission</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>