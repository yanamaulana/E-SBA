<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $page_title ?></title>
    <meta name="base_url" content="<?= base_url() ?>">
    <meta name="description" content="E-Samick Support System" />
    <meta name="keywords" content="E-Samick Support System" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta charset="utf-8" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="E-Samick Support System" />
    <meta property="og:url" content="<?= base_url() ?>" />
    <meta property="og:site_name" content="E-Samick Support System" />
    <link rel="canonical" href="<?= base_url() ?>" />
    <link rel="shortcut icon" href="<?= base_url() ?>assets/E-SBA_assets/web-logo/favicon.ico" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/E-SBA_assets/font/main-font.css">
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/Metronic/dist/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/global-assets/custom-table.css" rel="stylesheet" type="text/css" />
    <script src="<?= base_url() ?>assets/global-assets/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/global-assets/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"></script>
</head>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed">
    <!-- data-kt-aside-minimize="on" -->
    <div class="d-flex flex-column flex-root">
        <div class="page d-flex flex-row flex-column-fluid">
            <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
                <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                    <a href="<?= base_url() ?>">
                        <img alt="Logo" src="<?= base_url() ?>assets/E-SBA_assets/logo-app/logo_samick_transparent.png" class="h-35px logo" />
                    </a>
                    <div id="kt_aside_toggle" class="btn btn-icon w-auto px-0 btn-active-color-primary aside-toggle" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="aside-minimize">
                        <span class="svg-icon svg-icon-1 rotate-180">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="black"></path>
                                <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="black"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <?php
                $AccDept = 'Accounting';
                $Menu = $this->uri->segment(1);
                $is_dir = $this->session->userdata('sys_sba_isDir');

                $is_admin = $this->session->userdata('sys_sba_isAdm');
                $sess_dept = $this->session->userdata('sys_sba_department');
                $sess_jabatan = $this->session->userdata('sys_sba_jabatan');
                $Menu = $this->uri->segment(1);
                $SubMenu = $this->uri->segment(2);
                ?>
                <div class="aside-menu flex-column-fluid">
                    <div class="hover-scroll-overlay-y my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="0">
                        <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
                            <div class="menu-item">
                                <div class="menu-content pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1">Main Menu</span>
                                </div>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link <?= ($Menu == 'Dashboard') ? 'active' : null ?>" href="<?= base_url('Dashboard') ?>">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon | path: assets/media/icons/duotune/graphs/gra010.svg-->
                                        <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M13.0021 10.9128V3.01281C13.0021 2.41281 13.5021 1.91281 14.1021 2.01281C16.1021 2.21281 17.9021 3.11284 19.3021 4.61284C20.7021 6.01284 21.6021 7.91285 21.9021 9.81285C22.0021 10.4129 21.5021 10.9128 20.9021 10.9128H13.0021Z" fill="black" />
                                                <path opacity="0.3" d="M11.0021 13.7128V4.91283C11.0021 4.31283 10.5021 3.81283 9.90208 3.91283C5.40208 4.51283 1.90209 8.41284 2.00209 13.1128C2.10209 18.0128 6.40208 22.0128 11.3021 21.9128C13.1021 21.8128 14.7021 21.3128 16.0021 20.4128C16.5021 20.1128 16.6021 19.3128 16.1021 18.9128L11.0021 13.7128Z" fill="black" />
                                                <path opacity="0.3" d="M21.9021 14.0128C21.7021 15.6128 21.1021 17.1128 20.1021 18.4128C19.7021 18.9128 19.0021 18.9128 18.6021 18.5128L13.0021 12.9128H20.9021C21.5021 12.9128 22.0021 13.4128 21.9021 14.0128Z" fill="black" />
                                            </svg></span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="menu-title">Dashboard</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <div class="menu-content pt-4 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1 fw-bold">Cash Book Requisition</span>
                                </div>
                            </div>
                            <div class="menu-item" data-bs-toggle="tooltip" title="List My Cash Book Requisition">
                                <a class="menu-link <?= ($Menu == 'MyCbr') ? 'active' : null ?>" href="<?= base_url('MyCbr') ?>">
                                    <span class="menu-icon">
                                        <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z" fill="black" />
                                                <path d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z" fill="black" />
                                            </svg>
                                        </span>
                                    </span>
                                    <span class="menu-title ml-2">Cashbook Request Submission</span>
                                </a>
                            </div>

                            <?php if ($is_admin == true || $sess_jabatan == 'Asst Manager') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="Asst. Manager Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppAsstManager') ? 'active' : null ?>" href="<?= base_url('CbrAppAsstManager') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M22 12C22 13.8 21.5 15.5 20.7 17L14.9 7H20.7C21.5 8.5 22 10.2 22 12ZM3.3 7L6.2 12L12 2C8.3 2 5.1 4 3.3 7ZM3.3 17C5 20 8.3 22 12 22L14.9 17H3.3Z" fill="black" />
                                                    <path d="M20.7 7H9.2L12.1 2C15.7 2 18.9 4 20.7 7ZM3.3 7C2.4 8.5 2 10.2 2 12C2 13.8 2.5 15.5 3.3 17H9.10001L3.3 7ZM17.8 12L12 22C15.7 22 18.9 20 20.7 17L17.8 12Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">Asst.Manager Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if ($is_admin == true || $sess_jabatan == 'Manager') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="Asst. Manager Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppManager') ? 'active' : null ?>" href="<?= base_url('CbrAppManager') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M14.1 15.013C14.6 16.313 14.5 17.813 13.7 19.113C12.3 21.513 9.29999 22.313 6.89999 20.913C5.29999 20.013 4.39999 18.313 4.39999 16.613C5.09999 17.013 5.99999 17.313 6.89999 17.313C8.39999 17.313 9.69998 16.613 10.7 15.613C11.1 15.713 11.5 15.813 11.9 15.813C12.7 15.813 13.5 15.513 14.1 15.013ZM8.5 12.913C8.5 12.713 8.39999 12.513 8.39999 12.313C8.39999 11.213 8.89998 10.213 9.69998 9.613C9.19998 8.313 9.30001 6.813 10.1 5.513C10.6 4.713 11.2 4.11299 11.9 3.71299C10.4 2.81299 8.49999 2.71299 6.89999 3.71299C4.49999 5.11299 3.70001 8.113 5.10001 10.513C5.80001 11.813 7.1 12.613 8.5 12.913ZM16.9 7.313C15.4 7.313 14.1 8.013 13.1 9.013C14.3 9.413 15.1 10.513 15.3 11.713C16.7 12.013 17.9 12.813 18.7 14.113C19.2 14.913 19.3 15.713 19.3 16.613C20.8 15.713 21.8 14.113 21.8 12.313C21.9 9.513 19.7 7.313 16.9 7.313Z" fill="black" />
                                                    <path d="M9.69998 9.61307C9.19998 8.31307 9.30001 6.81306 10.1 5.51306C11.5 3.11306 14.5 2.31306 16.9 3.71306C18.5 4.61306 19.4 6.31306 19.4 8.01306C18.7 7.61306 17.8 7.31306 16.9 7.31306C15.4 7.31306 14.1 8.01306 13.1 9.01306C12.7 8.91306 12.3 8.81306 11.9 8.81306C11.1 8.81306 10.3 9.11307 9.69998 9.61307ZM8.5 12.9131C7.1 12.6131 5.90001 11.8131 5.10001 10.5131C4.60001 9.71306 4.5 8.91306 4.5 8.01306C3 8.91306 2 10.5131 2 12.3131C2 15.1131 4.2 17.3131 7 17.3131C8.5 17.3131 9.79999 16.6131 10.8 15.6131C9.49999 15.1131 8.7 14.1131 8.5 12.9131ZM18.7 14.1131C17.9 12.8131 16.7 12.0131 15.3 11.7131C15.3 11.9131 15.4 12.1131 15.4 12.3131C15.4 13.4131 14.9 14.4131 14.1 15.0131C14.6 16.3131 14.5 17.8131 13.7 19.1131C13.2 19.9131 12.6 20.5131 11.9 20.9131C13.4 21.8131 15.3 21.9131 16.9 20.9131C19.3 19.6131 20.1 16.5131 18.7 14.1131Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">Manager Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if ($is_admin == true || $sess_jabatan == 'Senior Manager') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="Senior Manager Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppSeniorManager') ? 'active' : null ?>" href="<?= base_url('CbrAppSeniorManager') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M22 8H8L12 4H19C19.6 4 20.2 4.39999 20.5 4.89999L22 8ZM3.5 19.1C3.8 19.7 4.4 20 5 20H12L16 16H2L3.5 19.1ZM19.1 20.5C19.7 20.2 20 19.6 20 19V12L16 8V22L19.1 20.5ZM4.9 3.5C4.3 3.8 4 4.4 4 5V12L8 16V2L4.9 3.5Z" fill="black" />
                                                    <path d="M22 8L20 12L16 8H22ZM8 16L4 12L2 16H8ZM16 16L12 20L16 22V16ZM8 8L12 4L8 2V8Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">Senior&nbsp;Manager&nbsp;Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if ($is_admin == true || $sess_jabatan == 'General Manager') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="General Manager Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppGeneralManager') ? 'active' : null ?>" href="<?= base_url('CbrAppGeneralManager') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M8 2V16H2V8.60001C2 8.20001 2.2 7.8 2.5 7.5L8 2ZM16 8V22L21.5 16.5C21.8 16.2 22 15.8 22 15.4V8H16Z" fill="black" />
                                                    <path d="M22 8H8V2H15.4C15.8 2 16.2 2.2 16.5 2.5L22 8ZM2 16L7.5 21.5C7.8 21.8 8.20001 22 8.60001 22H16V16H2Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">GM Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>



                            <?php if ($is_admin == true || $sess_jabatan == 'Director') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="Director Production Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppDirector') ? 'active' : null ?>" href="<?= base_url('CbrAppDirector') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M2.10001 10C3.00001 5.6 6.69998 2.3 11.2 2L8.79999 4.39999L11.1 7C9.60001 7.3 8.30001 8.19999 7.60001 9.59999L4.5 12.4L2.10001 10ZM19.3 11.5L16.4 14C15.7 15.5 14.4 16.6 12.7 16.9L15 19.5L12.6 21.9C17.1 21.6 20.8 18.2 21.7 13.9L19.3 11.5Z" fill="black" />
                                                    <path d="M13.8 2.09998C18.2 2.99998 21.5 6.69998 21.8 11.2L19.4 8.79997L16.8 11C16.5 9.39998 15.5 8.09998 14 7.39998L11.4 4.39998L13.8 2.09998ZM12.3 19.4L9.69998 16.4C8.29998 15.7 7.3 14.4 7 12.8L4.39999 15.1L2 12.7C2.3 17.2 5.7 20.9 10 21.8L12.3 19.4Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">Director Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($is_admin == true || $sess_jabatan == 'President Director') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="President Director Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppPresidentDirector') ? 'active' : null ?>" href="<?= base_url('CbrAppPresidentDirector') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M2 11.7127L10 14.1127L22 11.7127L14 9.31274L2 11.7127Z" fill="black" />
                                                    <path opacity="0.3" d="M20.9 7.91274L2 11.7127V6.81275C2 6.11275 2.50001 5.61274 3.10001 5.51274L20.6 2.01274C21.3 1.91274 22 2.41273 22 3.11273V6.61273C22 7.21273 21.5 7.81274 20.9 7.91274ZM22 16.6127V11.7127L3.10001 15.5127C2.50001 15.6127 2 16.2127 2 16.8127V20.3127C2 21.0127 2.69999 21.6128 3.39999 21.4128L20.9 17.9128C21.5 17.8128 22 17.2127 22 16.6127Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">President Director Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($is_admin == true || $sess_jabatan == 'Finance Director') : ?>
                                <div class="menu-item" data-bs-toggle="tooltip" title="Finance Director Approval : Cash Book Requisition">
                                    <a class="menu-link <?= ($Menu == 'CbrAppFinanceDirector') ? 'active' : null ?>" href="<?= base_url('CbrAppFinanceDirector') ?>">
                                        <span class="menu-icon">
                                            <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path opacity="0.3" d="M11.425 7.325C12.925 5.825 15.225 5.825 16.725 7.325C18.225 8.825 18.225 11.125 16.725 12.625C15.225 14.125 12.925 14.125 11.425 12.625C9.92501 11.225 9.92501 8.825 11.425 7.325ZM8.42501 4.325C5.32501 7.425 5.32501 12.525 8.42501 15.625C11.525 18.725 16.625 18.725 19.725 15.625C22.825 12.525 22.825 7.425 19.725 4.325C16.525 1.225 11.525 1.225 8.42501 4.325Z" fill="black" />
                                                    <path d="M11.325 17.525C10.025 18.025 8.425 17.725 7.325 16.725C5.825 15.225 5.825 12.925 7.325 11.425C8.825 9.92498 11.125 9.92498 12.625 11.425C13.225 12.025 13.625 12.925 13.725 13.725C14.825 13.825 15.925 13.525 16.725 12.625C17.125 12.225 17.425 11.825 17.525 11.325C17.125 10.225 16.525 9.22498 15.625 8.42498C12.525 5.32498 7.425 5.32498 4.325 8.42498C1.225 11.525 1.225 16.625 4.325 19.725C7.425 22.825 12.525 22.825 15.625 19.725C16.325 19.025 16.925 18.225 17.225 17.325C15.425 18.125 13.225 18.225 11.325 17.525Z" fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="menu-title ml-2">&nbsp;Finance&nbsp;Director&nbsp;Approval</span>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="menu-item" data-bs-toggle="tooltip" title="Monitoring Cbr">
                                <a class="menu-link <?= ($Menu == 'CbrMonitoring') ? 'active' : null ?>" href="<?= base_url('CbrMonitoring') ?>">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon | path: assets/media/icons/duotune/graphs/gra010.svg-->
                                        <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                            <i class="fas fa-receipt fs-3"></i>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="menu-title">My Cashbook Requisition</span>
                                </a>
                            </div>

                            <div class="menu-item" data-bs-toggle="tooltip" title="Monitoring Cbr">
                                <a class="menu-link <?= ($Menu == 'MonitoringCbr') ? 'active' : null ?>" href="<?= base_url('MonitoringCbr') ?>">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon | path: assets/media/icons/duotune/graphs/gra010.svg-->
                                        <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                            <i class="fas fa-tv fs-3"></i>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="menu-title">Cash Book Requisition</span>
                                </a>
                            </div>

                            <div class="menu-item">
                                <div class="menu-content pt-4 pb-2">
                                    <span class="menu-section text-muted text-uppercase fs-8 ls-1 fw-bold">SETTINGS</span>
                                </div>
                            </div>
                            <div data-kt-menu-trigger="click" class="menu-item menu-accordion <?= ($Menu == 'Set_StepApprovalCbr' || $Menu == 'Approval_Assignment') ? 'hover show' : null; ?>">
                                <span class="menu-link" data-bs-toggle="tooltip" title="Cash Book Requisition Approval Settings">
                                    <span class="menu-icon">
                                        <!--begin::Svg Icon | path: assets/media/icons/duotune/graphs/gra010.svg-->
                                        <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                            <i class="fas fa-cogs fs-3"></i>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </span>
                                    <span class="menu-title">SETTING</span>
                                    <span class="menu-arrow"></span>
                                </span>
                                <div class="menu-sub menu-sub-accordion kt-hidden-height=117">
                                    <div class="menu-item" data-bs-toggle="tooltip" title="Manage Settings Step Approval : CBR">
                                        <a class="menu-link <?= ($Menu == 'Set_StepApprovalCbr') ? 'active' : null ?>" href="<?= base_url('Set_StepApprovalCbr') ?>">
                                            <span class="menu-icon">
                                                <span class="svg-icon svg-icon-2">
                                                    <!--begin::Svg Icon | path: assets/media/icons/duotune/coding/cod001.svg-->
                                                    <span class="svg-icon svg-icon-muted svg-icon-2qx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="black" />
                                                            <path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="black" />
                                                        </svg></span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </span>
                                            <span class="menu-title">Step Approval CBR</span>
                                        </a>
                                    </div>
                                    <div class="menu-item" data-bs-toggle="tooltip" title="User Setting Step Approval Assignment">
                                        <a class="menu-link <?= ($Menu == 'Approval_Assignment') ? 'active' : null ?>" href="<?= base_url('Approval_Assignment') ?>">
                                            <span class="menu-icon">
                                                <span class="svg-icon svg-icon-2">
                                                    <!--begin::Svg Icon | path: assets/media/icons/duotune/communication/com006.svg-->
                                                    <span class="svg-icon svg-icon-muted svg-icon-2qx">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                            <path opacity="0.3" d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM12 7C10.3 7 9 8.3 9 10C9 11.7 10.3 13 12 13C13.7 13 15 11.7 15 10C15 8.3 13.7 7 12 7Z" fill="black" />
                                                            <path d="M12 22C14.6 22 17 21 18.7 19.4C17.9 16.9 15.2 15 12 15C8.8 15 6.09999 16.9 5.29999 19.4C6.99999 21 9.4 22 12 22Z" fill="black" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </span>
                                            <span class="menu-title">Step Approval Assignment</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <div id="kt_header" class="header align-items-stretch">
                    <div class="container-fluid d-flex align-items-stretch justify-content-between">
                        <div class="d-flex align-items-center d-lg-none ms-n3 me-1" title="Show aside menu">
                            <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_aside_mobile_toggle">
                                <span class="svg-icon svg-icon-2qx mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z" fill="black" />
                                        <path opacity="0.3" d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z" fill="black" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                            <a href="<?= base_url('Dashboard') ?>" class="d-lg-none">
                                <img alt="Logo" src="<?= base_url() ?>assets/E-SBA_assets/logo-app/logo-mobile.png" class="h-30px" />
                            </a>
                        </div>
                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                            <div class="d-flex align-items-center" id="kt_header_nav">
                                <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_header_nav'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1"><?= $this->config->item('init_app_name') ?></h1>
                                    <span class="h-20px border-gray-200 border-start mx-4"></span>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="<?= base_url('Dashboard') ?>" class="text-muted text-hover-primary">Menu</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-200 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-dark"><?= $page_title ?></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-stretch flex-shrink-0">
                                <div class="d-flex align-items-stretch flex-shrink-0">
                                    <div class="d-flex align-items-center ms-1 ms-lg-3">
                                        <div class="btn btn-icon btn-active-light-primary position-relative w-30px h-30px w-md-40px h-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                            <span class="svg-icon svg-icon-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                    <path d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z" fill="black" />
                                                    <path d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z" fill="black" />
                                                    <path opacity="0.3" d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z" fill="black" />
                                                    <path opacity="0.3" d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z" fill="black" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                                            <div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('<?= base_url() ?>assets/Metronic/dist/assets/media/misc/pattern-1.jpg')">
                                                <h3 class="text-white fw-bold px-9 mt-10 mb-6">Notifications</h3>
                                                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-bold px-9">
                                                    <li class="nav-item">
                                                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="kt_topbar_notifications_3" role="tabpanel">
                                                    <div class="scroll-y mh-325px my-5 px-8">
                                                        <div class="d-flex flex-stack py-4">
                                                            <div class="d-flex align-items-center me-2">
                                                                <span class="w-70px badge badge-light-success me-4">200 OK</span>
                                                                <a href="#" class="text-gray-800 text-hover-primary fw-bold">New order</a>
                                                            </div>
                                                            <span class="badge badge-light fs-8">Just now</span>
                                                        </div>
                                                    </div>
                                                    <div class="py-3 text-center border-top">
                                                        <a href="#" class="btn btn-color-gray-600 btn-active-color-primary">View All
                                                            <span class="svg-icon svg-icon-5">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                    <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="black" />
                                                                    <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="black" />
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                        <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                            <img src="<?= base_url() ?>assets/E-SBA_assets/employee-image/male.png" alt="user" />
                                        </div>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <div class="menu-content d-flex align-items-center px-3">
                                                    <div class="symbol symbol-50px me-5">
                                                        <img alt="Logo" src="<?= base_url() ?>assets/E-SBA_assets/employee-image/male.png" />
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div class="fw-bolder d-flex align-items-center fs-5"><?= $this->session->userdata('sys_sba_nama') ?></div>
                                                        <a href="#" class="fw-bold text-muted text-hover-primary fs-7"><?= $this->session->userdata('sys_sba_jabatan') ?> <?= $this->session->userdata('sys_sba_department') ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="menu-item px-5">
                                                <div class="menu-content px-5">
                                                    <label class="form-check form-switch form-check-custom form-check-solid pulse pulse-success" for="kt_user_menu_dark_mode_toggle">
                                                        Email : <span class="form-check-label text-gray-600 fs-7"><?= $this->session->userdata('sys_sba_email') ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="separator my-2"></div>
                                            <div class="menu-item px-5">
                                                <a href="<?= base_url('Auth/logout') ?>" class="menu-link px-5">Sign Out</a>
                                            </div>
                                            <!-- <div class="separator my-2"></div> -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-main flex-column flex-row-fluid py-4" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content  flex-column-fluid ">
                            <div id="kt_app_content_container" class="app-container  container-fluid ">
                                <?php if ($this->session->flashdata('success')) { ?>
                                    <div class="alert alert-dismissible bg-success d-flex flex-column flex-sm-row w-100 p-5 mb-5">
                                        <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="black"></path>
                                                <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                                            <h4 class="mb-2 text-light">Success!</h4>
                                            <span><?php echo $this->session->flashdata('success'); ?></span>
                                        </div>
                                        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                            <i class="fas fa-times fs-2x"></i>
                                        </button>
                                    </div>
                                <?php } else if ($this->session->flashdata('error')) { ?>
                                    <div class="alert alert-dismissible bg-danger d-flex flex-column flex-sm-row w-100 p-5 mb-5">
                                        <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="black"></path>
                                                <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                                            <h4 class="mb-2 text-light">Error!</h4>
                                            <span><?php echo $this->session->flashdata('error'); ?></span>
                                        </div>
                                        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                            <i class="fas fa-times fs-2x"></i>
                                        </button>
                                    </div>
                                <?php } else if ($this->session->flashdata('warning')) { ?>
                                    <div class="alert alert-dismissible bg-warning d-flex flex-column flex-sm-row w-100 p-5 mb-5">
                                        <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="black"></path>
                                                <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                                            <h4 class="mb-2 text-light">Warning!</h4>
                                            <span><?php echo $this->session->flashdata('warning'); ?></span>
                                        </div>
                                        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                            <i class="fas fa-times fs-2x"></i>
                                        </button>
                                    </div>
                                <?php } else if ($this->session->flashdata('info')) { ?>
                                    <div class="alert alert-dismissible bg-info d-flex flex-column flex-sm-row w-100 p-5 mb-5">
                                        <span class="svg-icon svg-icon-2hx svg-icon-light me-4 mb-5 mb-sm-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path opacity="0.3" d="M12 22C13.6569 22 15 20.6569 15 19C15 17.3431 13.6569 16 12 16C10.3431 16 9 17.3431 9 19C9 20.6569 10.3431 22 12 22Z" fill="black"></path>
                                                <path d="M19 15V18C19 18.6 18.6 19 18 19H6C5.4 19 5 18.6 5 18V15C6.1 15 7 14.1 7 13V10C7 7.6 8.7 5.6 11 5.1V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V5.1C15.3 5.6 17 7.6 17 10V13C17 14.1 17.9 15 19 15ZM11 10C11 9.4 11.4 9 12 9C12.6 9 13 8.6 13 8C13 7.4 12.6 7 12 7C10.3 7 9 8.3 9 10C9 10.6 9.4 11 10 11C10.6 11 11 10.6 11 10Z" fill="black"></path>
                                            </svg>
                                        </span>
                                        <div class="d-flex flex-column text-light pe-0 pe-sm-10">
                                            <h4 class="mb-2 text-light">Info!</h4>
                                            <span><?php echo $this->session->flashdata('info'); ?></span>
                                        </div>
                                        <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                            <i class="fas fa-times fs-2x"></i>
                                        </button>
                                    </div>
                                <?php } ?>
                                <?php $this->load->view($page_content) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-bold me-1">2023©</span>
                            <a href="https://yanamaulana.github.io" target="_blank" class="text-gray-800 text-hover-primary">Developer</a>
                        </div>
                        <ul class="menu menu-gray-600 menu-hover-primary fw-bold order-1">
                            <li class="menu-item">
                                <a href="#" class="menu-link px-2">About</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="menu-link px-2">Support</a>
                            </li>
                            <li class="menu-item">
                                <a href="<?= $this->config->item('website') ?>" target="_blank" class="menu-link px-2">Company Profile</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
        <span class="svg-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
                <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
            </svg>
        </span>
    </div>

    <script>
        var hostUrl = "<?= base_url() ?>assets/Metronic/dist/assets/";
    </script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/js/scripts.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="<?= base_url() ?>assets/Metronic/dist/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="<?= base_url() ?>assets/global-assets/jquery-validation/jquery.validate.js"></script>
    <script src="<?= base_url() ?>assets/global-assets/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <?= $script_page ?>
    <script>
        window.renderApprovalStatus = function(data, status) {
            if (data == 0) {
                return `<span data-bs-toggle="tooltip" title="No approval needed." class="badge bg-secondary"><i class="fas fa-ban text-dark"></i></span>`;
            }

            // Perbaikan: Cek status hanya sekali
            if (data == 1) {
                if (status == null || status === undefined || status === '') {} else if (status == 0) {
                    return `<span data-bs-toggle="tooltip" title="Approval In Progress" class="badge bg-warning"><i class="bi bi-hourglass-split text-dark"></i></span>`;
                } else if (status == 1) {
                    return `<span data-bs-toggle="tooltip" title="approved" class="badge bg-success"><i class="fas fa-check text-white"></i></span>`;
                } else {
                    return `<span  title="reject" class="badge bg-danger"><i class="fas fa-exclamation-triangle text-dark"></i></span>`;
                }
            }

            return ''; // Default
        };

        window.renderApprovalStatusWithName = function(Has_Submitted_Approval, flag, status) {
            const tooltipAttr = 'data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark"';
            const baseClass = 'badge btn-icon';

            if (Has_Submitted_Approval == 0) {
                return `<span ${tooltipAttr} title="Approval not yet submitted" class="${baseClass} bg-info"><i class="fas fa-question"></i></span>`;
            }

            if (flag == 0) {
                return `<span ${tooltipAttr} title="No approval needed." class="${baseClass} bg-secondary"><i class="fas fa-ban text-dark"></i></span>`;
            }

            if (flag == 1) {
                if (status == 0) {
                    return `<span ${tooltipAttr} title="Approval in progress" class="${baseClass} bg-warning text-dark"><i class="bi bi-hourglass-split text-dark"></i></span>`;
                } else if (status == 1) {
                    // 3. Rejected
                    return `<span ${tooltipAttr} title="Approved" class="${baseClass} bg-success"><i class="fas fa-check text-white"></i></span>`;
                } else if (status == 2) {
                    // 4. In Progress (Status NULL/Belum disubmit)
                    return `<span ${tooltipAttr} title="Rejected" class="${baseClass} bg-danger"><i class="fas fa-times text-white"></i></span>`;
                }
            }

        }
    </script>
</body>

</html>