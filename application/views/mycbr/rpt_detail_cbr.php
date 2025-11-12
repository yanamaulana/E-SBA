<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?= base_url() ?>assets/E-SBA_assets/web-logo/favicon.ico" />
<style>
    /* Global Print Settings */
    @page {
        size: A4 landscape;
        /* Margin dikurangi agar lebih banyak ruang untuk konten */
        margin: 10mm 10mm 10mm 10mm;
        font-size: 10pt;
        font-family: 'Times New Roman', Times, serif;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm 10mm 10mm 10mm;
            font-size: 10pt !important;
            font-family: 'Times New Roman', Times, serif;
        }

        /* Memastikan tidak ada pemotongan pada elemen penting */
        .table-ttd,
        .table-loop {
            page-break-inside: avoid;
        }
    }

    /* Reset dan Body */
    html,
    body {
        background: #FFF;
        overflow: visible;
        font-family: 'Times New Roman', Times, serif;
        font-size: 10pt;
    }

    /* Global Styles */
    .text-center {
        text-align: center;
        vertical-align: middle !important;
    }

    td.sign {
        font-weight: bold;
    }


    .text-left {
        text-align: left;
        vertical-align: top;
    }

    .text-right {
        text-align: right;
        vertical-align: middle;
    }

    .font-weight-bold {
        font-weight: bold;
    }

    .row {
        margin: 0;
    }

    /* Table General */
    .table-ttd {
        border-collapse: collapse;
        width: 100%;
        /* MEMASTIKAN FIT DALAM A4 */
        font-size: 9pt !important;
    }

    .table-ttd tr td {
        border: 1px solid black;
        padding: 4px;
        font-size: 9pt !important;
        vertical-align: top;
    }

    .table-ttd thead tr td,
    #tr-footer {
        font-weight: bold;
    }

    /* Table Detail Loop */
    .table-loop {
        margin-top: 5px;
    }

    .table-loop tr td {
        padding: 5px !important;
    }

    /* Layout Kontainer untuk Jumlah (Flexbox) */
    .container {
        display: flex;
        justify-content: space-between;
    }

    /* Footer Timestamp */
    #print-footer {
        margin-top: 5px;
        font-size: 8pt;
        text-align: right;
    }
</style>

<head>
    <title>Report Cbr - <?= $CbrHeader->CBReq_No ?></title>
</head>
<?php
$i = 1;
function format_rupiah($angka)
{
    // Menggunakan pemisah ribuan dan desimal yang umum di Indonesia
    $rupiah = number_format($angka, 2, '.', ',');
    return $rupiah;
}
?>

<body>
    <div class="row">
        <table class="table-ttd" style="width: 100%;">
            <tr>
                <td style="width: 10%;">SLIP DATE</td>
                <td style="width: 20%;"><?= substr($CbrHeader->Document_Date, 0, 10) ?></td>
                <td rowspan="3" class="text-center" style="width: 20%;"><b style="font-size: 16pt;">PAYMENT RESOLUTION</b></td>
                <td style="width: 10%;">INPUT DEPT</td>
                <td style="width: 20%;"><?= $CbrHeader->Input_Dept ?></td>
            </tr>
            <tr>
                <td>SLIP NO</td>
                <td><?= $CbrHeader->CBReq_No ?></td>
                <td>INPUT NAME</td>
                <td><?= $CbrHeader->Input_Name ?></td>
            </tr>
            <tr>
                <td>PAGE</td>
                <td><?= "1 - 1" ?></td>
                <td>OUTPUT NAME</td>
                <td><?= $CbrHeader->Output_Name ?></td>
            </tr>
        </table>

        <table class="table-ttd table-loop" style="width: 100%;">
            <thead>
                <tr>
                    <td class="text-center" style="width: 3%;">NO</td>
                    <td class="text-center" style="width: 35%;">DESCRIPTION</td>
                    <td class="text-center" style="width: 25%;">ACCOUNT</td>
                    <td class="text-center" style="border-right: solid black 1px; width: 15%;">AMOUNT</td>
                    <td class="text-center" colspan="2" style="width: 22%;">PAYMENT INFO</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $Must = 9;
                $There = $CbrDetail->num_rows();
                $RestLine = $Must - $There;
                ?>
                <?php foreach ($CbrDetail->result() as $li) : ?>
                    <tr>
                        <td class="text-center"><?= $i ?></td>
                        <td><?= $li->Description ?></td>
                        <td><?= '[' . $li->Acc_ID . ']' . ' ' . $li->Account_Name ?></td>
                        <td style="border-right: solid black 1px;" class="text-right">
                            <div class="container" style="justify-content: space-between;">
                                <div style="text-align: left;"><?= $li->currency_id ?></div>
                                <div style="text-align: right;"><?= format_rupiah($li->Amount_Detail) ?></div>
                            </div>
                        </td>
                        <td style="border-right: none; width: 10%;">
                            <?php
                            $payment_label = '';
                            if ($i == 1) $payment_label = 'Date Line';
                            else if ($i == 2) $payment_label = 'Amount of Payment';
                            else if ($i == 3) $payment_label = '1st Payment';
                            else if ($i == 4) $payment_label = '2nd Payment';
                            else if ($i == 5) $payment_label = 'Discount';
                            else if ($i == 6) $payment_label = 'Amount of Deduction';
                            else if ($i == 7) $payment_label = 'Sum of Payment';
                            echo $payment_label;
                            ?>
                        </td>
                        <td style="border-left: none; width: 12%;">
                            <b>:</b>
                            <?php
                            if ($i == 1) {
                                echo substr($CbrHeader->duedate, 0, 10);
                            } else {
                                echo ' ';
                            }
                            ?>
                        </td>
                        <?php $i++; ?>
                    </tr>
                <?php endforeach; ?>
                <?php for ($loop = 1; $loop <= $RestLine; $loop++) : ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="border-right: solid black 1px;">&nbsp;</td>
                        <td style="border-right: none;">
                            <?php
                            $payment_label = '';
                            if ($i == 1) $payment_label = 'Date Line';
                            else if ($i == 2) $payment_label = 'Amount of Payment';
                            else if ($i == 3) $payment_label = '1st Payment';
                            else if ($i == 4) $payment_label = '2nd Payment';
                            else if ($i == 5) $payment_label = 'Discount';
                            else if ($i == 6) $payment_label = 'Amount of Deduction';
                            else if ($i == 7) $payment_label = 'Sum of Payment';
                            echo $payment_label;
                            ?>
                        </td>
                        <td style="border-left: none;">
                            <b>:</b>
                            <?php
                            if ($i == 1) {
                                echo substr($CbrHeader->duedate, 0, 10);
                            } else {
                                echo ' ';
                            }
                            ?>
                        </td>
                        <?php $i++; ?>
                    </tr>
                <?php endfor; ?>
                <tr style="font-weight: bold;">
                    <td colspan="2">TOTAL AMOUNT</td>
                    <td>TOTAL ==> <?= $CbrHeader->Currency_ID ?></td>
                    <td style="border-right: solid black 1px;">
                        <?php if ($CbrHeader->Currency_ID == 'IDR') : ?>
                            <div class="container" style="justify-content: space-between;">
                                <div style="text-align: left;"><?= $CbrHeader->Currency_ID ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->Amount) ?></div>
                            </div>
                        <?php else : ?>
                            <div class="container" style="justify-content: space-between;">
                                <div style="text-align: left;"><?= $CbrHeader->Currency_ID ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->Amount) ?></div>
                            </div>
                            <div class="container" style="justify-content: space-between; font-size: 8pt;">
                                <div style="text-align: left;"><?= 'IDR' ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->BaseAmount) ?></div>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td colspan="2">
                        <?php if ($CbrHeader->Currency_ID == 'IDR') : ?>
                            <div class="container" style="justify-content: space-between;">
                                <div style="text-align: left;"><?= $CbrHeader->Currency_ID ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->Amount) ?></div>
                            </div>
                        <?php else : ?>
                            <div class="container" style="justify-content: space-between;">
                                <div style="text-align: left;"><?= $CbrHeader->Currency_ID ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->Amount) ?></div>
                            </div>
                            <div class="container" style="justify-content: space-between; font-size: 8pt;">
                                <div style="text-align: left;"><?= 'IDR' ?></div>
                                <div style="text-align: right;"><?= format_rupiah($CbrHeader->BaseAmount) ?></div>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="width: 100%; border: solid black 1px; margin-top: 5px; margin-bottom: 5px;"></div>

        <table class="table-ttd" style="width: 100%;">
            <tbody>
                <tr>
                    <td rowspan="3" style="writing-mode: vertical-rl;letter-spacing: 0.4em; white-space: nowrap; text-orientation: upright; vertical-align: middle; text-align:center; width: 1.5%;"><strong>APPROVAL</strong></td>
                    <td class="text-center" style="width: 7%;">SUBMITTER</td>
                    <td class="text-center" style="width: 7%;"><?= 'CHIEF' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'ASST MANAGER' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'MANAGER' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'SENIOR MANAGER' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'GENERAL MANAGER' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'ADDITIONAL' ?></td>
                    <td class="text-center" style="width: 7%;"><?= 'ACCOUNTING' ?></td>

                    <td class="text-center" rowspan="3" style="width: 1%;"></td>

                    <td class="text-center" style="width: 7%;">
                        <?php if ($TrxApproval->Status_AppvDirector == 1 && $TrxApproval->AppvDirector_By == '90112'): ?>
                            FINANCE DIRECTOR
                        <?php elseif ($TrxApproval->Status_AppvDirector == 1 && $TrxApproval->AppvDirector_By != '90112'):  ?>
                            CHECKED
                        <?php else: ?>
                            <?= '' ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="width: 7%;">
                        <?= ($TrxApproval->Status_AppvFinanceDirector == 1) ? strtoupper('FINANCE DIRECTOR') : '' ?>
                    </td>
                    <td class="text-center" style="width: 7%;"><?= 'MANAGING DIRECTOR' ?></td>

                    <td class="text-center" rowspan="3" style="width: 1%;"></td>

                    <td class="text-center" style="width: 7%;">STATUS APPROVAL</td>
                </tr>
                <?php if ($TrxApproval): ?>
                    <tr style="height: 85px;">
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= strtoupper($CbrHeader->Request_Name) ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvChief == 1) ? strtoupper($TrxApproval->AppChief_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvAsstManager == 1) ? strtoupper($TrxApproval->AppvAsstManager_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvManager == 1) ? strtoupper($TrxApproval->AppvManager_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvSeniorManager == 1) ? strtoupper($TrxApproval->AppvSeniorManager_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvGeneralManager == 1) ? strtoupper($TrxApproval->AppvGeneralManager_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvAdditional == 1) ? strtoupper($TrxApproval->AppvAdditional_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvFinancePerson == 1) ? strtoupper($TrxApproval->AppvFinancePerson_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvDirector == 1) ? strtoupper($TrxApproval->AppvDirector_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvFinanceDirector == 1) ? strtoupper($TrxApproval->AppvFinanceDirector_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;"><?= ($TrxApproval->Status_AppvPresidentDirector == 1) ? strtoupper($TrxApproval->AppvPresidentDirector_Name) : '-' ?></td>
                        <td class="text-center sign" style="font-size: 10pt !important;" rowspan="2">
                            <?php
                            if ($CbrHeader->SysId_Step == NULL || $CbrHeader->SysId_Step == '') {
                                echo 'NOT SUBMITTED YET';
                            } elseif (
                                $TrxApproval->Status_AppvAsstManager == 2 or $TrxApproval->Status_AppvManager == 2 or $TrxApproval->Status_AppvSeniorManager == 2 or $TrxApproval->Status_AppvGeneralManager == 2 or $TrxApproval->Status_AppvAdditional == 2 or $TrxApproval->Status_AppvDirector == 2 or $TrxApproval->Status_AppvFinanceDirector == 2 or $TrxApproval->Status_AppvPresidentDirector == 2
                            ) {
                                echo 'REJECTED';
                            } elseif (($CbrHeader->SysId_Step != NULL || $CbrHeader->SysId_Step != '') && $TrxApproval->Legitimate == 0) {
                                echo 'APPROVAL IN PROGRESS';
                            } elseif (($CbrHeader->SysId_Step != NULL || $CbrHeader->SysId_Step != '') && $TrxApproval->Legitimate == 1) {
                                echo 'FINISH APPROVED';
                            }
                            ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr style="height: 85px;">
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;">-</td>
                        <td class="text-center sign" style="font-size: 12pt !important;" rowspan="2">NOT SUBMITTED YET</td>
                    </tr>
                <?php endif; ?>
                <?php
                /* SELECT SysID, CBReq_No, SysId_Step, IsAppvStaff, Status_AppvStaff, AppvStaff_By, AppvStaff_Name, AppvStaff_At, IsAppvChief, Status_AppvChief, AppvChief_By, AppvChief_Name, AppvChief_At,

                IsAppvAsstManager, Status_AppvAsstManager, AppvAsstManager_By, AppvAsstManager_Name, AppvAsstManager_At,
                IsAppvManager, Status_AppvManager, AppvManager_By, AppvManager_Name, AppvManager_At,
                IsAppvSeniorManager, Status_AppvSeniorManager, AppvSeniorManager_By, AppvSeniorManager_Name, AppvSeniorManager_At,
                IsAppvGeneralManager, Status_AppvGeneralManager, AppvGeneralManager_By, AppvGeneralManager_Name, AppvGeneralManager_At,
                IsAppvAdditional, Status_AppvAdditional, AppvAdditional_By, AppvAdditional_Name, AppvAdditional_At,
                IsAppvDirector, Status_AppvDirector, AppvDirector_By, AppvDirector_Name, AppvDirector_At,
                IsAppvPresidentDirector, Status_AppvPresidentDirector, AppvPresidentDirector_By, AppvPresidentDirector_Name, AppvPresidentDirector_At,
                IsAppvFinanceDirector, Status_AppvFinanceDirector, AppvFinanceDirector_By, AppvFinanceDirector_Name, AppvFinanceDirector_At,

                UserName_User, UserDivision, Rec_Created_At, IsAppvFinancePerson, Status_AppvFinancePerson, AppvFinancePerson_By, AppvFinancePerson_Name,
                AppvFinancePerson_At, Doc_Legitimate_Pos_On, Legitimate, Last_Submit_at
                FROM dbsai_erp_uat.dbo.Ttrx_Cbr_Approval; */
                ?>
                <?php if ($TrxApproval): ?>
                    <tr>
                        <td class="text-center"><small><?= formatTanggalLaporan($CbrHeader->Creation_DateTime) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvChief_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvAsstManager_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvManager_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvSeniorManager_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvGeneralManager_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvAdditional_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvFinancePerson_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvDirector_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvFinanceDirector_At) ?></small></td>
                        <td class="text-center"><small><?= formatTanggalLaporan($TrxApproval->AppvPresidentDirector_At) ?></small></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                        <td class="text-center"><?= '....' ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="print-footer">
        Generated Time: <?= date('Y-m-d H:i:s') ?>
    </div>
</body>

</html>

<?php

function formatTanggalLaporan($dateTimeString)
{
    // Cek apakah input valid.
    if (empty($dateTimeString)) {
        return '';
    }

    // 1. Buat objek DateTime dari string input.
    // Kita gunakan str_replace untuk menghapus milidetik karena format PHP Date tidak menanganinya secara langsung
    // dan bisa menyebabkan error pada beberapa versi PHP.
    $dateTimeString = preg_replace('/\.[0-9]{3}$/', '', $dateTimeString);

    try {
        $date = new DateTime($dateTimeString);
    } catch (Exception $e) {
        // Jika parsing gagal, kembalikan string kosong atau pesan error.
        return 'Format Tidak Valid';
    }

    // 2. Format objek DateTime ke output yang diinginkan.
    // Y: Tahun empat digit
    // M: Singkatan bulan (Jan-Des, bahasa Inggris)
    // d: Hari dua digit
    // H: Jam 24-jam dua digit
    // i: Menit dua digit
    $formatOutput = 'd M Y H:i';

    return $date->format($formatOutput);
}

// --- Contoh Penggunaan ---
$input = '2025-10-27 13:12:42.000';
$output = formatTanggalLaporan($input);

// Hasil: 2025 Oct 27 13:12
// echo $output;

?>