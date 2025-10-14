<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="<?= base_url() ?>assets/E-SBA_assets/web-logo/favicon.ico" />
<style>
    @page {
        size: A4 landscape;
        margin: 20px 20px 20px 20px;
        font-size: 10pt !important;
        font-family: sans-serif;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 20px 20px 20px 20px;
            font-size: 10pt !important;
            font-family: sans-serif;
        }
    }

    html,
    body {
        /* height: 200mm; */
        width: 297mm;
        background: #FFF;
        overflow: visible;
    }

    .table-ttd {
        border-collapse: collapse;
        width: 295mm;
        margin-left: 1mm;
        font-size: 10pt !important;
        font-family: sans-serif;
    }

    h3 {
        font-family: sans-serif;
    }

    .table-ttd tr,
    .table-ttd tr td {
        border: 0.5px solid black;
        padding: 4px;
        padding: 4px;
        font-size: 10pt !important;
        font-family: sans-serif;
    }

    input,
    textarea,
    select {
        font-family: inherit;
    }

    .table-ttd tr,
    .table-ttd tr td {
        border: 1px solid black;
        padding: 3px;
        padding: 3px;
        font-size: 10pt !important;
    }

    /* tr {
        page-break-before: always;
        page-break-inside: avoid;
        font-size: 10pt !important;
    } */

    .tablee td,
    .tablee th {
        border-collapse: collapse;
        /* border: 0.5px solid black; */
        padding: 2.5px;
        font-size: 10pt !important;
        font-size: 10pt !important;
        font-family: sans-serif;
    }


    /* ul,
    li {
        list-style-type: none;
        font-size: 10pt !important;
    } */

    .table-ttd thead tr td,
    #tr-footer {
        font-weight: bold;
        font-family: sans-serif;
    }

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

    .rotate {
        /* FF3.5+ */
        -moz-transform: rotate(-90.0deg);
        /* Opera 10.5 */
        -o-transform: rotate(-90.0deg);
        /* Saf3.1+, Chrome */
        -webkit-transform: rotate(-90.0deg);
        /* IE6,IE7 */
        filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
        /* IE8 */
        -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
        /* Standard */
        transform: rotate(-90.0deg);
    }

    * {
        box-sizing: border-box;
    }

    .row {
        margin-left: -5px;
        margin-right: -5px;
    }

    .column {
        float: left;
        width: 50%;
        padding: 5px;
    }

    /* Clearfix (clear floats) */
    /* .row::after {
        content: "";
        clear: both;
        display: table;
    } */

    .tablee {
        border-collapse: collapse;
        border-spacing: 0;
        width: 148mm;
        /* border: 1px solid #ddd; */
    }

    .tablee th,
    .tablee td {
        padding: 2.5px;
    }
</style>

<head>
    <title><?= $vin ?></title>
</head>
<?php $i = 1; ?>

<body style="border: solid black 1px;">
    <div class="row">
        <h3 class="text-center">Preview Detail Purchase Invoice <?= $vin ?></h3>
    </div>
    <hr />
    <div class="row">
        <div class="column">
            <table class="tablee">
                <tbody>
                    <tr>
                        <td class="text-left">Paid to</td>
                        <td class="text-left">:</td>
                        <!-- {{-- Menggunakan operator ternary untuk mengecek $qheader --}} -->
                        <td class="text-left font-weight-bold">
                            <?= ($qheader ?? null) ? $qheader->accounttitle_code . '. ' . $qheader->account_name : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Vendor Invoice Number</td>
                        <td class="text-left">:</td>
                        <!-- {{-- Menggunakan Null Coalescing Operator (??) pada properti --}} -->
                        <td class="text-left">
                            <?= $qheader->VenInvoice_Number ?? '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Purchase Order Number</td>
                        <td class="text-left">:</td>
                        <td class="text-left font-weight-bold">
                            <?php
                            // Cek apakah $list_po ada dan berupa array/object
                            if (!empty($list_po) && is_array($list_po) || is_object($list_po)) :
                                foreach ($list_po as $li) : ?>
                                    <?= $li . '<br>' ?>
                            <?php endforeach;
                            else :
                                echo '-';
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Date of Goods Received</td>
                        <td class="text-left">:</td>
                        <td class="text-left ">
                            <?php
                            // Cek apakah $list_rr ada dan berupa array/object
                            if (!empty($list_rr) && is_array($list_rr) || is_object($list_rr)) :
                                foreach ($list_rr as $li) :
                                    $rr_date = $li->RR_date ?? date('d M Y'); // Handle jika RR_date null
                                    $formatted_date = date('d M Y', strtotime($rr_date)); ?>
                                    <?= '<b>' . ($li->rr_number ?? '-') . '</b> | ' . $formatted_date . '<br>' ?>
                            <?php endforeach;
                            else :
                                echo '-';
                            endif;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Amount</td>
                        <td class="text-left">:</td>
                        <td class="text-left">
                            <b><?= $qheader->Currency_ID ?? '-' ?></b>
                            <?= number_format($qheader->Invoice_Amount ?? 0, 4, '.', ',') ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Descriptions</td>
                        <td class="text-left">:</td>
                        <td class="text-left">
                            <?= $qheader->Invoice_Notes ?? '-' ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="column">
            <table class="tablee">
                <tbody>
                    <tr>
                        <td class="text-left">Date of Invoice Received</td>
                        <td class="text-left">:</td>
                        <td class="text-left">
                            <!-- {{-- Cek $qheader->Invoice_Date. Jika NULL, gunakan '-' --}} -->
                            <?= ($qheader->Invoice_Date ?? null) ? date('d M Y', strtotime($qheader->Invoice_Date)) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Due Date</td>
                        <td class="text-left">:</td>
                        <td class="text-left">
                            <!-- {{-- Cek $qheader->Due_date. Jika NULL, gunakan '-' --}} -->
                            <?= ($qheader->Due_date ?? null) ? date('d M Y', strtotime($qheader->Due_date)) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">Vendor SO Number</td>
                        <td class="text-left">:</td>
                        <td class="text-left">
                            <!-- {{-- Cek apakah $qheader ada dan isDirect != 1 --}} -->
                            <?php if (($qheader->isDirect ?? 1) != 1 && !empty($qget_so_numb)) : ?>
                                <!-- {{-- Hapus margin-left negatif di ul atau gunakan tag div jika margin bermasalah --}} -->
                                <ul style="margin-top:0; padding-left:20px;">
                                    <?php foreach ($qget_so_numb as $li) : ?>
                                        <li style="font-weight: bold;"><?= $li->rr_number ?? '-' ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                -N/A-
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <?php $i = 1; ?>
        <?php foreach ($list_rr as $rr) : ?>
            <?php $grandtotal = 0; ?>
            <?php if ($qheader != NULL && $qheader->itemcategorytype == 'AST') : ?>
                <?php $qget_rrdetails = $this->db->query("SELECT taccrr_item.item_code, 
							TAccAssetInventory.Asset_Desc as item_name,							
							taccrr_item.item_codealias, 
							TAccAssetInventory.model as item_type, 
							taccrr_item.item_namealias,
							isnull(taccvi_detail.qty,taccrr_item.qty) as qty,
							isnull(taccvi_detail.secondary_qty,taccrr_item.secondary_qty) as secondary_qty,
							TAccRR_Header.Ref_Number, 
							taccrr_header.currency_id, 
							taccrr_header.rr_type, 
							taccrr_header.isdirect, 
							'' as unit_name,
							taccrr_item.RR_Number
					        from taccrr_item
                                inner join taccrr_header on taccrr_header.rr_number = taccrr_item.rr_number
                                inner join TAccAssetInventory on TAccAssetInventory.AssetTemp_Code = taccrr_item.item_code
                                left join taccvi_detail on taccrr_item.rr_number=taccvi_detail.ref_number
                                    and taccvi_detail.item_code = taccrr_item.item_code
                                    and taccvi_detail.Invoice_Number = '$vin'
                            where taccrr_item.rr_number = '$rr->rr_number'
                            and taccrr_item.qty > 0
                            order by TAccRR_Header.Ref_Number, taccrr_item.RR_Number"); ?>
            <?php elseif ($qheader == NULL || $qheader->itemcategorytype != 'AST'): ?>
                <?php $qget_rrdetails = $this->db->query("select	taccrr_item.item_code, 
							titem.item_name, 
							taccrr_item.item_codealias, 
							titem.customfield1 as item_type, 
							taccrr_item.item_namealias,
							isnull(taccvi_detail.qty,taccrr_item.qty) as qty,
							isnull(taccvi_detail.secondary_qty,taccrr_item.secondary_qty) as secondary_qty,
							TAccRR_Header.Ref_Number, 
							taccrr_header.currency_id, 
							taccrr_header.rr_type, 
							taccrr_header.isdirect, 
							taccunittype.unit_name,
							taccrr_item.RR_Number
					from taccrr_item
						inner join taccrr_header on taccrr_header.rr_number = taccrr_item.rr_number
						inner join titem on titem.item_code = taccrr_item.item_code
						inner join taccunittype on taccunittype.unit_type_id = titem.unit_type_id
						left join taccvi_detail on taccrr_item.rr_number=taccvi_detail.ref_number
							and taccvi_detail.item_code = taccrr_item.item_code
							and taccvi_detail.Invoice_Number = '$vin'
					where taccrr_item.rr_number = '$rr->rr_number'
					and taccrr_item.qty > 0
					order by TAccRR_Header.Ref_Number, taccrr_item.RR_Number"); ?>
            <?php endif; ?>
            <table class="table-ttd" style="<?php if ($i == 1) : ?> margin-top: 150px; margin-bottom: 7px;<?php else : ?> margin-bottom: 7px; <?php endif; ?> margin-left: 2mm;">
                <?php if ($i == 1) : ?>
                    <tr style="border-left: none; border-right: none;">
                        <td colspan="8" style="border-left: none; border-right: none;">&nbsp;</td>
                    </tr>
                <?php endif; ?>
                <tr align="center" class="cols">
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-left:1px solid black;border-top:1px solid black;"><b>No.</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Item Code</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Item Description</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Model</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Unit</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Qty</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Price</b></td>
                    <td style="border-bottom:1px solid black; border-right:1px solid black;border-top:1px solid black;"><b>Total Amount</b></td>
                </tr>
                <tr>
                    <td style="border:1px solid black;border-top:none" colspan="8"><b><?= $rr->rr_number ?></b></td>
                </tr>
                <?php $last_po = ''; ?>
                <?php foreach ($qget_rrdetails->result() as $rr_dtl) : ?>
                    <tr>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;border-left:1px solid black;" align="center">
                            <?= $i; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;border-left:1px solid black;" align="center">
                            <?= $rr_dtl->item_code; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;border-left:1px solid black;" align="center">
                            <?= $rr_dtl->item_name; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;">
                            <?= $rr_dtl->item_type; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;">
                            <?= $rr_dtl->unit_name; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;" align="center">
                            <?php if (!empty($rr_dtl->qty)) : ?>
                                <?= number_format($rr_dtl->qty, 2, '.', ','); ?>
                            <?php else : ?>
                                0,00
                            <?php endif; ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;" align="right">
                            <?php
                            $totalamount = 0;
                            if (!empty($rr_dtl->rr_type)) {
                                if ($rr_dtl->rr_type === "RR_SRT") {
                                    $qgetprice =  $this->db->query("SELECT taccso_header.currency_id, 
                                              COALESCE(taccso_detail.unitprice, 0) as unit_price, 
                                              COALESCE(taccso_detail.disc_percentage, 0) as disc_percentage, 
                                              COALESCE(taccso_detail.disc_value, 0) as disc_value
                                              FROM taccrr_header
                                              INNER JOIN taccrr_item ON taccrr_item.rr_number = taccrr_header.rr_number
                                              INNER JOIN taccsr_header ON taccsr_header.sr_number = taccrr_header.ref_number
                                              INNER JOIN taccso_header ON taccso_header.so_number = taccsr_header.so_number
                                              INNER JOIN taccso_detail ON taccso_detail.so_number = taccso_header.so_number
                                              AND taccso_detail.item_code = taccrr_item.item_code
                                              WHERE taccrr_header.rr_number = '$rr->rr_number'
                                              AND taccrr_item.item_code = '$rr_dtl->item_code'");

                                    if ($qgetprice->num_rows() > 0) {
                                        $row = $qgetprice->row_array();
                                        $currency_id_rr = $row['currency_id'];
                                        $unit_price = $row['unit_price'];
                                        $disc_percentage = $row['disc_percentage'];
                                        $disc_value = $row['disc_value'];
                                        echo $currency_id_rr . ' ' . number_format($unit_price, 2, '.', ',');
                                        // Inisialisasi totalamount untuk memastikan variabel selalu terdefinisi
                                        $totalamount = 0;

                                        // Ambil status flag SecondaryUoMPricing, default ke 0 jika $qheader NULL
                                        $isSecondaryPricing = $qheader->SecondaryUoMPricing ?? 0;

                                        if ($isSecondaryPricing == 1) {
                                            // Logika jika SecondaryUoMPricing == 1
                                            $totalamount = (
                                                (floatval($unit_price) - floatval($disc_value)) -
                                                (floatval($disc_percentage) / 100 * (floatval($unit_price) - floatval($disc_value)))
                                            ) * floatval($tmpQty);
                                        } else {
                                            // Logika jika SecondaryUoMPricing == 0 atau $qheader NULL
                                            $totalamount = (
                                                (floatval($unit_price) - floatval($disc_value)) -
                                                (floatval($disc_percentage) / 100 * (floatval($unit_price) - floatval($disc_value)))
                                            ) * floatval($rr_dtl->$qty);
                                        }

                                        $grandtotal += $totalamount;
                                    }
                                } elseif ($rr_dtl->rr_type == "RR_PUR") {
                                    $qgetprice = $this->db->query("SELECT taccvi_header.currency_id, 
                                              COALESCE(taccvi_detail.unitprice, 0) as unit_price,
                                              COALESCE(taccvi_detail.disc_percentage, 0) as disc_percentage,
                                              0 as disc_value
                                              FROM taccvi_detail
                                              INNER JOIN taccvi_header ON taccvi_detail.invoice_number=taccvi_header.invoice_number
                                              INNER JOIN taccrr_item ON taccrr_item.rr_number=taccvi_detail.ref_number
                                              AND taccrr_item.item_code = taccvi_detail.item_code
                                              WHERE taccrr_item.rr_number = '$rr->rr_number'
                                              AND taccrr_item.item_code = '$rr_dtl->item_code'
                                              AND taccvi_detail.Invoice_Number = '$vin'");
                                    if ($qgetprice->num_rows() > 0) {
                                        $row = $qgetprice->row_array();
                                        $currency_id_rr = $row['currency_id'];
                                        $unit_price = $row['unit_price'];
                                        $disc_percentage = $row['disc_percentage'];
                                        $disc_value = $row['disc_value'];
                                        echo $currency_id_rr . ' ' . number_format($unit_price, 2, '.', ',');
                                        // Inisialisasi totalamount untuk memastikan variabel selalu terdefinisi
                                        $totalamount = 0;

                                        // Ambil status flag SecondaryUoMPricing, default ke 0 jika $qheader NULL
                                        $isSecondaryPricing = $qheader->SecondaryUoMPricing ?? 0;

                                        if ($isSecondaryPricing == 1) {
                                            // Logika jika SecondaryUoMPricing == 1
                                            $totalamount = (
                                                (floatval($unit_price) - floatval($disc_value)) -
                                                (floatval($disc_percentage) / 100 * (floatval($unit_price) - floatval($disc_value)))
                                            ) * floatval($tmpQty);
                                        } else {
                                            // Logika jika SecondaryUoMPricing == 0 atau $qheader NULL
                                            $totalamount = ((floatval($unit_price) - floatval($disc_value)) - (floatval($disc_percentage) / 100 * (floatval($unit_price) - floatval($disc_value)))) * floatval($rr_dtl->qty);
                                        }

                                        $grandtotal += $totalamount;
                                    }
                                } else {
                                    $totalamount = 0.00;
                                }
                            }
                            ?>
                        </td>
                        <td style="border-bottom:1px solid black; border-right:1px solid black;" align="right">
                            <?= number_format($totalamount, 4, '.', ','); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td style="border:1px solid black;border-top:none" colspan="7" align="right">
                        <b>Grand Total</b>
                    </td>
                    <td style="border:1px solid black;border-left:none;border-top:none" align="right">
                        <?= number_format($grandtotal, 4, '.', ','); ?>
                    </td>
                </tr>
            </table>
            <?php $i++; ?>
        <?php endforeach; ?>
        <!-- </div> -->
    </div>
    <?php
    $qCariJournal = $this->db->query("SELECT TaccJournalDetail.*, 
                                        TaccChartAccount.Account_nameen as acc_Name, Account_Number,
                                        TAccCostCenter.CostCenter_Code,TAccCostCenter.CostCenter_Name_en AS CostCenter_Name
                                    From 	TaccJournalDetail
                                        inner join TAccChartAccount on TaccJournalDetail.Acc_id = TAccChartAccount.acc_id
                                        left join TAccCostCenter on TAccCostCenter.CostCenter_ID = TAccJournalDetail.CostCenter
                                    Where 	JournalH_Code = '$vin'
                                    Order by Default_Acc");

    ?>
    <div class="row">
        <table class="tablee" style="margin-left: 2mm; width:295mm; margin-top:20px;">
            <tbody>
                <tr class="formtext" style="border-top: solid black 1px;">
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr class="formtext">
                    <td>
                        <strong>ACCOUNTING PURPHOSES ONLY</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <table class="tablee" style="margin-left: 2mm; width:295mm; margin-bottom:10px; margin-top: 10px;">
            <tbody>
                <tr>
                    <td align="center">Account</td>
                    <td align="center">D/C</td>
                    <td align="center" width="25%">Descriptions</td>
                    <?php if ($qheader != NULL && $qheader->isDirect == 1) : ?>
                        <td align="center">Notes</td>
                        <td align="center">Cost Center</td>
                    <?php endif; ?>
                    <td align="right">Amount</td>

                </tr>
                <?php if ($qCariJournal->num_rows() > 0) : ?>
                    <?php foreach ($qCariJournal->result() as $journal) : ?>
                        <?php $nilai = floatval($journal->JournalD_Debet) + floatval($journal->JournalD_Debet_tax) + floatval($journal->JournalD_Kredit) + floatval($journal->JournalD_Kredit_tax) ?>
                        <?php if ($nilai != 0) : ?>
                            <tr style="border-top: 1px solid #ddd;">
                                <td align="center"><?= $journal->Account_Number; ?></td>
                                <td align="center" width="15%">
                                    <?php if (floatval($journal->JournalD_Debet) == 0 && floatval($journal->JournalD_Debet_tax) == 0) : ?>K<?php else : ?>D<?php endif; ?>
                                </td>
                                <td align="center"><?= $journal->acc_Name; ?></td>
                                <?php if ($qheader != NULL && $qheader->isDirect == 1) : ?>
                                    <td align="center"><?= $journal->Notes; ?></td>
                                    <td align="center">
                                        <?php if (!empty($journal->CostCenter_Code)) : ?>[<?= $journal->CostCenter_Code ?>] <?= $journal->CostCenter_Name ?><?php else : ?>&nbsp;<?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td align="right"><?= $journal->Currency_id ?> <?= number_format($nilai, 4, '.', ','); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" align="center">...::: RECORD NOT FOUND :::...</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

<?php
function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}
?>

</html>