<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $this->config->item('init_app_name') ?> | <?= $page_title ?></title>
    <meta name="description" content="<?= $this->config->item('app_name') ?>" />
    <meta name="keywords" content="<?= $this->config->item('app_name') ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#0d6efd">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta charset="utf-8" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?= $this->config->item('app_name') ?>" />
    <meta property="og:url" content="<?= base_url() ?>" />
    <meta property="og:site_name" content="<?= $this->config->item('app_name') ?>" />
    <link rel="canonical" href="<?= base_url() ?>" />
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="shortcut icon" href="<?= base_url() ?>assets/E-SBA_assets/web-logo/favicon.ico" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
</head>

<body id="kt_body" class="bg-body">
    <div class="d-flex flex-column flex-root">
        <div class="d-flex flex-column flex-column-fluid bgi-position-y-bottom position-x-center bgi-no-repeat bgi-size-contain bgi-attachment-fixed" style="background-image: url(<?= base_url() ?>assets/E-SBA_assets/logo-app/bg-login.png); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="d-flex flex-center flex-column flex-column-fluid p-10 pb-lg-20">
                <a href="<?= base_url() ?>Auth" class="mb-12">
                    <img alt="Logo" src="<?= base_url() ?>assets/E-SBA_assets/logo-app/logo_samick_transparent_normal.png" class="h-70px" />
                </a>
                <div class="w-lg-500px bg-body rounded shadow-sm p-10 p-lg-15 mx-auto">
                    <form id="form-login" method="post" action="<?= base_url('Auth/post_login') ?>" class="form w-100" novalidate="novalidate">
                        <div class="text-center mb-10">
                            <h2 class="text-dark mb-3">Login to <?= $this->config->item('app_name_short')   ?></h2>
                            <div class="text-gray-600 fw-bold fs-4">By <?= $this->config->item('company_name') ?></div>
                        </div>
                        <div class="fv-row mb-10">
                            <label class="form-label fs-6 fw-bolder text-dark">Username</label>
                            <input class="form-control form-control-lg form-control-solid" required type="text" name="username" id="username" placeholder="Username..." />
                        </div>
                        <div class="fv-row mb-10">
                            <div class="d-flex flex-stack mb-2">
                                <label class="form-label fw-bolder text-dark fs-6 mb-0">Password</label>
                                <span href="#" class="link-primary fs-6 fw-bolder">Forgot Password ? Call M.I.S</span>
                            </div>
                            <input class="form-control form-control-lg form-control-solid" required type="password" name="password" id="password" placeholder="Password..." />
                        </div>
                        <div class="text-center">
                            <button type="submit" id="btn--login" class="btn btn-lg btn-primary w-100 mb-5">
                                <span class="indicator-label">Sign-in</span>
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
            </div>
        </div>
    </div>
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/scripts.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/custom/authentication/sign-in/general.js"></script>
    <script src="<?= base_url() ?>assets/global-assets/jquery-validation/jquery.validate.js"></script>
    <script src="<?= base_url() ?>assets/login-script/index.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('<?= base_url('service-worker.js') ?>', {
                    scope: '<?= rtrim(base_url(), '/') ?>/'
                }).catch(function(error) {
                    console.error('ESBA service worker registration failed:', error);
                });
            });
        }
    </script>
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