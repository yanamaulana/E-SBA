<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $this->config->item('init_app_name') ?> | <?= $page_title ?></title>
    <meta name="description" content="E Teacher Attendance" />
    <meta name="keywords" content="E Teacher Attendance" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta name="base_url" content="<?= base_url() ?>">
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="E-Teacher Attendance" />
    <meta property="og:url" content="<?= base_url() ?>" />
    <meta property="og:site_name" content="E-Teacher Attendance" />
    <link rel="canonical" href="<?= base_url() ?>" />
    <link rel="shortcut icon" href="<?= base_url() ?>assets/E-TA_assets/web-logo/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
</head>

<body id="kt_body" class="bg-body">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(<?= base_url() ?>assets/Metronic/dist/assets/media/illustrations/sketchy-1/14.png">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <div class="w-lg-600px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form">
                <?php $this->load->view('partials/csrf_field'); ?>
                        <div class="mb-10 text-center">
                            <h1 class="text-dark mb-3">Create an Account</h1>
                            <div class="text-gray-400 fw-bold fs-4">Already have an account?
                                <a href="<?= base_url('Auth') ?>" class="link-primary fw-bolder">Sign in here</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-10">
                            <div class="border-bottom border-gray-300 mw-50 w-100"></div>
                            <span class="fw-bold text-gray-400 fs-7 mx-2">Register</span>
                            <div class="border-bottom border-gray-300 mw-50 w-100"></div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Name :</label>
                                <input class="form-control form-control-lg form-control-solid" type="text" placeholder="Full Name..." name="Nama" id="Nama" autocomplete="off" required />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">ID Access Control :</label>
                                <input class="form-control form-control-lg form-control-solid" type="number" id="ID" name="ID" maxlength="3" placeholder="ID Access Control..." autocomplete="off" required />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Username :</label>
                                <input class="form-control form-control-lg form-control-solid" type="text" id="UserName" name="UserName" minlength="3" maxlength="12" placeholder="User name..." autocomplete="off" required />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Gender :</label>
                                <select id="Gender" name="Gender" required class="form-select form-select-solid form-control form-control-lg form-control-solid" data-control="select2" data-placeholder="Select an option">
                                    <option></option>
                                    <option value="LAKI-LAKI">LAKI-LAKI</option>
                                    <option value="PEREMPUAN">PEREMPUAN</option>
                                </select>
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">NO. KTP :</label>
                                <input class="form-control form-control-lg form-control-solid" type="text" id="KTP" name="KTP" maxlength="17" placeholder="KTP..." autocomplete="off" />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Place of Birth :</label>
                                <input class="form-control form-control-lg form-control-solid" type="text" id="Tempat_Lahir" name="Tempat_Lahir" placeholder="Place of Birth..." autocomplete="off" />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Date of Birth :</label>
                                <input class="form-control form-control-lg form-control-solid date-picker" value="1990-06-01" type="text" id="Tanggal_Lahir" name="Tanggal_Lahir" placeholder="Date of Birth..." autocomplete="off" />
                            </div>
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Telephone Number :</label>
                                <input class="form-control form-control-lg form-control-solid" type="number" id="Telpon" name="Telpon" placeholder="Telephone Number..." autocomplete="off" />
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bolder text-dark fs-6">Email :</label>
                            <input class="form-control form-control-lg form-control-solid" type="email" placeholder="Email..." id="Email" name="Email" autocomplete="off" />
                        </div>
                        <div class="row fv-row mb-7">
                            <div class="col-xl-12">
                                <label class="form-label fw-bolder text-dark fs-6">Martial Status :</label>
                                <select id="Status_Pernikahan" name="Status_Pernikahan" required class="form-select form-select-solid form-control form-control-lg form-control-solid" data-control="select2" data-placeholder="Select an option">
                                    <option></option>
                                    <option value="LAJANG">LAJANG</option>
                                    <option value="MENIKAH">MENIKAH</option>
                                    <option value="CERAI">CERAI</option>
                                </select>
                            </div>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bolder text-dark fs-6">Date Join :</label>
                            <input class="form-control form-control-lg form-control-solid" type="text" id="Tanggal_Join" name="Tanggal_Join" placeholder="Date Join..." autocomplete="off" />
                        </div>
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bolder text-dark fs-6">Address :</label>
                            <textarea class="form-control form-control-lg form-control-solid" id="Full_address" name="Full_address" placeholder="Address..."></textarea>
                        </div>
                        <div class="fv-row mb-7">
                            <label class="form-label fw-bolder text-dark fs-6">Office Position :</label>
                            <select required class="form-select form-select-solid form-control form-control-lg form-control-solid" data-control="select2" data-placeholder="Select an option" id="Fk_Jabatan" name="Fk_Jabatan" data-placeholder="Select an option">
                                <option></option>
                                <?php foreach ($jabatans as $jab) : ?>
                                    <option value="<?= $jab->SysId ?>"><?= $jab->Jabatan ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-10 fv-row" data-kt-password-meter="true">
                            <div class="mb-1">
                                <label class="form-label fw-bolder text-dark fs-6">Password</label>
                                <div class="position-relative mb-3">
                                    <input class="form-control form-control-lg form-control-solid" minlength="5" type="password" required placeholder="Password..." name="password" autocomplete="off" />
                                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                                        <i class="bi bi-eye-slash fs-2"></i>
                                        <i class="bi bi-eye fs-2 d-none"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="text-muted">Recomendation : Use 5 or more characters with a mix of letters &amp; numbers.</div>
                        </div>
                        <div class="fv-row mb-5">
                            <label class="form-label fw-bolder text-dark fs-6">Confirm Password</label>
                            <input class="form-control form-control-lg form-control-solid" minlength="5" type="password" required placeholder="Password..." name="confirm-password" id="confirm-password" autocomplete="off" />
                        </div>
                        <div class="text-center">
                            <button type="button" id="kt_sign_up_submit" class="btn btn-lg btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="d-flex flex-center flex-column-auto p-10">
                <div class="d-flex align-items-center fw-bold fs-6">
                    <a href="#" class="text-muted text-hover-primary px-2">About</a>
                    <a href="#" class="text-muted text-hover-primary px-2">Contact</a>
                    <a href="#" class="text-muted text-hover-primary px-2">Contact Us</a>
                </div>
                <!--end::Links-->
            </div>
            <!--end::Footer-->
        </div>
        <!--end::Authentication - Sign-up-->
    </div>
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.js"></script>
    <?php $this->load->view('partials/csrf_script'); ?>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/scripts.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/custom/authentication/sign-in/general.js"></script>
    <script src="<?= base_url() ?>assets/global-assets/jquery-validation/jquery.validate.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="<?= base_url() ?>assets/login-script/register.js"></script>
    <?php if ($this->session->flashdata('error')) : ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You must login first !',
                footer: '<a href="javascript:void(0)">Notification System</a>'
            });
        </script>
        <?php session_destroy() ?>
    <?php endif; ?>
</body>

</html>