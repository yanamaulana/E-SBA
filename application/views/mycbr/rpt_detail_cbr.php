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
        vertical-align: middle;
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
                    <td class="text-center" style="border-right: solid black 5px; width: 15%;">AMOUNT</td>
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
                        <td style="border-right: solid black 5px;" class="text-right">
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
                        <td style="border-right: solid black 5px;">&nbsp;</td>
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
                    <td style="border-right: solid black 5px;">
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
                    <td class="text-center" style="width: 7%;">CREATOR</td>
                    <td class="text-center" style="width: 7%;">SUBMITTER</td>
                    <td class="text-center" style="width: 7%;">ASST.MANAGER</td>
                    <td class="text-center" style="width: 7%;">MANAGER</td>
                    <td class="text-center" style="width: 7%;">SENIOR MANAGER</td>
                    <td class="text-center" style="width: 7%;">GENERAL MANAGER</td>
                    <td class="text-center" style="width: 7%;">ADDITIONAL</td>

                    <td class="text-center" rowspan="3" style="width: 1%;"></td>

                    <td class="text-center" style="width: 7%;">DIRECTOR</td>
                    <td class="text-center" style="width: 7%;">FINANCE DIRECTOR</td>
                    <td class="text-center" style="width: 7%;">PRESIDENT DIRECTOR</td>

                    <td class="text-center" rowspan="3" style="width: 1%;"></td>

                    <td class="text-center" style="width: 7%;">STATUS APPROVAL</td>
                </tr>
                <tr style="height: 85px;">
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center" rowspan="2"></td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="print-footer">
        Generated Time: <?= date('Y-m-d H:i:s') ?>
    </div>
</body>

</html>