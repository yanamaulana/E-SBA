<div class="row gx-5 gx-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush overflow-hidden h-xl-100">
            <div class="card-header py-5">
                <div class="card-toolbar">
                    <ul class="nav nav-tabs fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link mr-5 active btn btn-flex btn-active-light-primary" data-bs-toggle="tab" href="#tab-1">
                                <h5 class="font-weight-bold" id="table-title-main"> Upload Profile Picture</h5>
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
                        <div class="tab-pane fade active show" id="tab-1" role="tabpanel">
                            <form action="javascript:void(0)" id="form-attachment">
                <?php $this->load->view('partials/csrf_field'); ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="exampleFormControlInput1" class="form-label">Upload Profile Picture</label>
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" id="fp" name="fp" placeholder="upload photo" accept="image/jpg" required>
                                        </div>
                                        <div class="text-dark text-sm mt-3">
                                            <small>
                                                <span class="text-danger">*</span> pastikan foto yang anda upload berextension jpg, max ukuran 5mb dan file name adalah NIK karyawan.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" id="submit--data"><i class="fas fa-upload"></i> Save Picture</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="location"></div>