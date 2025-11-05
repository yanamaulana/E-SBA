<?php

if (function_exists('sqlsrv_configure')) {
    sqlsrv_configure('ClientBufferMaxKBSize', 1024000); // 1GB
}
ini_set('memory_limit', '1G');
set_time_limit(0);
ini_set('memory_limit', '1G');
ini_set('max_execution_time', 1800);
ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', 'off');
// ignore_user_abort(false);
set_time_limit(0);
if (ob_get_level()) ob_end_clean();

$CI = &get_instance();
$CI->load->database();
$CI->load->helper(['url']);

// === MODE EKSPOR EXCEL (via ?excel=1) ===
$excel = (int) $CI->input->get('excel');
$excelMode = ($excel === 1);

if ($excelMode) {
    $filename = 'CustomerTransactionReport[' . date('Ymd_His') . '].xls';
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    echo "\xEF\xBB\xBF"; // BOM UTF-8 supaya karakter aman di Excel
}

// Style number Excel
$nf2 = $excelMode ? "mso-number-format:'0.00';" : '';
$nf4 = $excelMode ? "mso-number-format:'0.0000';" : '';
$tx  = $excelMode ? "mso-number-format:'@';" : ''; // text (untuk kode yg leading zero)

// ===== CF: <cfoutput> scope is implicit in PHP output
// ===== Base URL (approximation of CF Application vars)
$base_url = rtrim($CI->config->item('base_url') ?: base_url(), '/');

// ====== Language map replacement for CF_DO_V25_MULTILANGUAGE
// (Fallback: key => key)
$DO_VAR = [];
$languageKeys = [
    'SalesReport',
    'Print',
    'Close',
    'CustomerTransactionReport',
    'PrintedOn',
    'SONumber',
    'TotalAmount',
    'TotalDiscount',
    'TotalInvoiced',
    'SalesOrder',
    'CustomerRFQ',
    'RFQCode',
    'RFQDate',
    'NoTransactionAvailable',
    'ItemCode',
    'ItemName',
    'Qty',
    'Qty2',
    'NoRecordFound',
    'SODate',
    'QtyDelivered',
    'UnitPrice',
    'Amount',
    'Discount',
    'Tax',
    'CustomerName',
    'CustomerAddress',
    'Quotation',
    'SNDate',
    'IncludedPPN',
    'ProformaInvoice',
    'PINumber',
    'PIDate',
    'QuotationNumber',
    'QuotationDate',
    'SalesInvoice',
    'SINumber',
    'SIDate',
    'SalesReturn',
    'SRNumber',
    'SRDate',
    'ShipmentNote',
    'SNNumber',
    'UnitType',
    'UnitType2',
    'mthJanuary',
    'mthFebruary',
    'mthMarch',
    'mthApril',
    'mthMay',
    'mthJune',
    'mthJuly',
    'mthAugust',
    'mthSeptember',
    'mthOctober',
    'mthNovember',
    'mthDecember',
    'AdditionalDisc',
    'SalesContract',
    'SalesContractNumber',
    'SalesContractDate',
    'QuotationDueDate',
    'SalesContractStartDate',
    'SalesContractEndDate',
    'DiscountValue',
    'category',
    'Dimension',
    'AccountName',
    'AccountCode',
    'Notes',
    'Debit',
    'Credit',
    'currency',
    'DirectSalesInvoice',
    'DocumentReference',
    'Type',
    'Color',
    'Brand',
    'InvoicePrintNo',
    'grandtotal',
    'ShippingInstructionNumber',
    'claimdeduction',
    'TotAmount1',
    'customername'
];
foreach ($languageKeys as $k) {
    $DO_VAR[$k] = $k;
}

// ===== Excel header handling (CF: URL.excel)
$excel = (int) $CI->input->get('excel');
if ($excel === 1) {
    $useHeader = 1; // mimic checkbrowser.cfm outcome (adjust if you have logic)
    if ($useHeader == 1) {
        header('Content-Disposition: attachment; filename=CustomerTransactionReport[' . date('dmY') . '].xls');
    } else {
        header('Content-Disposition: inline; filename=CustomerTransactionReport[' . date('dmY') . '].xls');
        header('Content-Type: application/vnd.ms-excel');
    }
}

// ===== Styles
$borderStyle = 'border-right:1px solid #CCCCCC; border-top:1px solid #CCCCCC; border-bottom:1px solid #CCCCCC; border-left:1px solid #CCCCCC;';

// ===== CF Variables / Defaults
$cbotype = 'FG';
$rdoView = 'all';

// Inputs translated from CF references
$selAccount = $CI->input->get('selAccount', true); // list of Account_ID (comma sep) when rdoView != all
$vauthAccountFilter = $CI->input->get('vauthaccountfilter', true) ?: $CI->input->get('REQUEST.vauthaccountfilter', true);
$companyId = 2;

$datefrom = '2025-01-01';
$dateto   = '2025-10-31';

$selcurrency = $CI->input->get('selcurrency', true);
$rdlocalimp  = $CI->input->get('rdlocalimp', true); // 0/1/2 as in CF
$rdo_invnumber = (int) $CI->input->get('rdo_invnumber');
$sel_invoiceprintno = $CI->input->get('sel_invoiceprintno', true);
$seldocument = strtolower($CI->input->get('seldocument', true) ?: 'salesinvoice');

// ===== Query: qcurrency (kept for parity, though not strictly used here)
$qcurrency = $CI->db->query('SELECT * FROM tcurrency');

// ===== Build qGetAccount with CF-like conditions
$where = [];
$params = [];
// cbotype mapping
if ($cbotype === 'FG') {
    $where[] = '(Cust_FG = 1)';
} elseif ($cbotype === 'RM') {
    $where[] = '(Cust_RM = 1)';
} elseif ($cbotype === 'AST') {
    $where[] = '(Cust_Ast = 1)';
} elseif ($cbotype === 'SP') {
    $where[] = '(Cust_SP = 1)';
} elseif ($cbotype === 'SF') {
    $where[] = '(Cust_SF = 1)';
} elseif ($cbotype === 'WIP') {
    $where[] = '(Cust_WIP = 1)';
} elseif ($cbotype === 'ALL') {
    $where[] = '(Cust_FG = 1 OR Cust_RM = 1 OR Cust_AST = 1 OR Cust_SP = 1 OR Cust_SF = 1 OR Cust_WIP = 1)';
}

if ($rdoView !== 'all' && $selAccount) {
    // Expect comma-separated list of IDs
    $ids = array_filter(array_map('trim', explode(',', $selAccount)), 'strlen');
    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where[] = "Account_ID IN ($placeholders)";
        $params = array_merge($params, $ids);
    }
}
if (!empty($vauthAccountFilter)) {
    $cats = array_filter(array_map('trim', explode(',', $vauthAccountFilter)), 'strlen');
    if (!empty($cats)) {
        $placeholders = implode(',', array_fill(0, count($cats), '?'));
        $where[] = "Category_ID IN ($placeholders)";
        $params = array_merge($params, $cats);
    }
}
if ($companyId) {
    $where[] = 'Company_ID = ?';
    $params[] = $companyId;
}
$where[] = 'TACCOUNT.Flag <> 1';

$sqlGetAccount = "SELECT DISTINCT Account_ID, Account_Name, Account_Address1, AccountTitle_Code
                  FROM TACCOUNT" .
    (count($where) ? (' WHERE ' . implode(' AND ', $where)) : '') .
    ' ORDER BY Account_Name';
$qGetAccount = $CI->db->query($sqlGetAccount, $params);

// ===== Render HTML head
?>
<html>

<head>
    <title></title>
</head>

<body>

    <table border="0" cellpadding="3" cellspacing="1" width="100%">
        <?php if ($excel === 0): ?>
            <tr>
                <td class="formbody">
                    <div id="Layer1">
                        <a href="#" onclick="document.getElementById('Layer1').style.visibility='hidden'; window.print(); window.close(); return false;">
                            <img src="<?= $base_url ?>/images/print.gif" border="0" alt="" align="absmiddle">
                            <?= htmlspecialchars($DO_VAR['Print']) ?>
                        </a>
                        <a href="#" onclick="window.close(); return false;" class="button"> <?= htmlspecialchars($DO_VAR['Close']) ?> </a>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td><?= htmlspecialchars($companyId) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td class="formtitle" align="center"><?= htmlspecialchars($DO_VAR['CustomerTransactionReport']) ?></td>
        </tr>
        <tr>
            <td class="formtextreport" align="left"><?= htmlspecialchars($DO_VAR['PrintedOn']) ?> <?= date('m/d/Y') ?> <?= date('H:i:s') ?></td>
        </tr>
    </table>

    <?php
    // ===== Grand total holders & defaults
    $customerrfq_gtqty = 0;
    $quotation_gtamt = 0;
    $proformainvoice_gtamt = 0;
    $salescontract_gtamt = 0;
    $salesorder_gtqty_1 = 0;
    $salesorder_gtqty_2 = 0;
    $salesorder_gtamt   = 0;
    $shipmentnote_gtqty_1 = 0;
    $shipmentnote_gtqty_2 = 0;
    $salesreturn_gtqty = 0;

    $totqty = 0;
    $totAmt = ['IDR' => 0, 'EUR' => 0, 'USD' => 0];
    $totAmtD = ['IDR' => 0, 'EUR' => 0, 'USD' => 0];

    $selDocument = 'SalesInvoice'; // as in CF default

    // ===== Iterate accounts
    $acctIdx = 0; // ← tambahkan ini sebelum foreach
    foreach ($qGetAccount->result() as $acct) {
        $acctIdx++; // ← naikkan di awal iterasi
        $NoTransactionAvailable = 1;

        if ($selDocument === '' || strtolower($selDocument) === 'salesinvoice') {
            // === Build the big query (kept close to original SQL; parameters are bound)
            $filters = [];
            $binds = [];

            $filters[] = 'taccsi_header.account_id = ?';
            $binds[] = $acct->Account_ID;

            $filters[] = 'taccsi_header.invoice_date >= ?';
            $binds[] = $datefrom;
            $filters[] = 'taccsi_header.invoice_date <= ?';
            $binds[] = $dateto;

            $filters[] = 'ISNULL(taccsi_header.isvoid,0) = 0';

            if (!empty($selcurrency) && $selcurrency !== '0') {
                $filters[] = 'taccsi_header.currency_id = ?';
                $binds[] = $selcurrency;
            }
            if ($rdlocalimp !== null && $rdlocalimp !== '' && $rdlocalimp !== '2') {
                $filters[] = 'taccsi_header.ISEXPORT = ?';
                $binds[] = $rdlocalimp;
            }
            if ($rdo_invnumber === 1 && !empty($sel_invoiceprintno)) {
                $list = array_filter(array_map('trim', explode(',', $sel_invoiceprintno)), 'strlen');
                if (!empty($list)) {
                    $place = implode(',', array_fill(0, count($list), '?'));
                    $filters[] = "taccsi_header.invoiceprintnumber IN ($place)";
                    $binds = array_merge($binds, $list);
                }
            }

            // bind order: [account_id, date_from, date_to, (opsional) selcurrency, (opsional) rdlocalimp]
            $sqlSI = <<<SQL
                        -- SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED;

                        SELECT
                            taccsi_header.invoice_number,
                            taccsi_header.invoiceprintnumber,
                            taccsi_header.invoice_date,
                            taccsi_header.currency_id,
                            taccsi_header.invoice_id,
                            taccso_header.PO_NumCustomer,
                            taccsi_detail.item_code,
                            ISNULL(taccsi_detail.qty, 0)            AS qty,
                            ISNULL(taccsi_detail.unitprice, 0)      AS unitprice,
                            ISNULL(taccsi_detail.disc_percentage,0) AS disc_percentage,
                            ISNULL(taccsi_detail.disc_value, 0)     AS disc_value,
                            taccsi_detail.tax_code1,
                            taccsi_detail.tax_code2,
                            titem.item_name,
                            dimension_name,
                            ISNULL(taccsi_header.transactiondiscountrate, 0) AS transactiondiscountrate,
                            titem.customfield1 AS item_type,
                            titem.item_size    AS brand,
                            tgscolor.color_name,
                            taccsi_header.so_number,
                            CASE WHEN TAccSI_Header.Ori_Invoice_Amount = 0 THEN 0
                                ELSE dbo.fnc_calcsalesreport(
                                        TAccSI_Detail.qty,
                                        TAccSI_Detail.unitprice,
                                        TAccSI_Header.Ori_Invoice_Amount,
                                        TAccSI_Header.claimdeduction,
                                        1.000000000
                                )
                            END AS claimdeduction,
                            CASE WHEN TAccSI_Header.Ori_Base_Invoice_Amount = 0 THEN 0
                                ELSE dbo.fnc_calcsalesreport(
                                        TAccSI_Detail.qty,
                                        TAccSI_Detail.unitprice,
                                        TAccSI_Header.Ori_Invoice_Amount,
                                        TAccSI_Header.claimdeduction,
                                        TAccSI_Header.base_invoice_amount / TAccSI_Header.invoice_amount
                                )
                            END AS base_claimdeduction,
                            ISNULL(BOMDetail.BOM_TYPE, 0)            AS BOM_TYPE,
                            ISNULL(prod_cost_percentage, 0)          AS prod_cost_percentage,
                            ISNULL(loss_percentage, 0)               AS loss_percentage,
                            ISNULL(total_cost, 0)                    AS total_cost,
                            ISNULL(loss_cost, 0)                     AS loss_cost,
                            ISNULL(salary2, 0)                       AS salary2,
                            ISNULL(prod_cost, 0)                     AS prod_cost,
                            ISNULL(TScale.scale, 1)                  AS scale,
                            ISNULL(BOMDetail.TotalCOGS * TScale.scale, 0) AS TotalCOGS,
                            ISNULL(TBOM_Guitar.total_cost_rm,  0)    AS total_cost_rm,
                            ISNULL(TBOM_Guitar_2.total_cost_rm2, 0)  AS total_cost_rm2,
                            ISNULL(TBOM_Guitar_3.total_cost_rm_3, 0) AS total_cost_rm_3
                        FROM taccsi_header      WITH (NOLOCK)
                        INNER JOIN taccsi_detail WITH (NOLOCK)
                            ON taccsi_header.invoice_number = taccsi_detail.invoice_number
                        INNER JOIN titem        WITH (NOLOCK)
                            ON taccsi_detail.item_code = titem.item_code
                        INNER JOIN titemdimension WITH (NOLOCK)
                            ON titemdimension.dimension_id = taccsi_detail.dimension_id
                        LEFT JOIN taccso_header WITH (NOLOCK)
                            ON taccso_header.so_number = taccsi_detail.so_number
                        LEFT JOIN tgscolor      WITH (NOLOCK)
                            ON tgscolor.color_code = titem.item_color
                        LEFT JOIN (
                            SELECT 
                                bom_type,
                                item_code,
                                umr_salary,
                                currency_id,
                                loss_percentage,
                                salary,
                                prod_cost_percentage,
                                SUM(total_cost) AS TotalCost,
                                SUM(total_cost) AS total_cost,
                                ((SUM(total_cost) * (loss_percentage)) / 100) AS loss_cost,
                                salary AS salary2,
                                (((SUM(total_cost) + umr_salary) * prod_cost_percentage) / 100) AS prod_cost,
                                (SUM(total_cost)
                                + ((SUM(total_cost) * (loss_percentage)) / 100)
                                + salary
                                + (((SUM(total_cost) + umr_salary) * prod_cost_percentage) / 100)) AS TotalCOGS
                            FROM (
                                SELECT
                                    bom_type,
                                    bom_code,
                                    item_code,
                                    item_type,
                                    brand,
                                    umr_salary,
                                    currency_id,
                                    loss_percentage,
                                    salary,
                                    prod_cost_percentage,
                                    SUM(total_cost_converted) AS total_cost
                                FROM (
                                    SELECT
                                        bom_type,
                                        bom_code,
                                        item_code,
                                        item_type,
                                        brand,
                                        umr_salary,
                                        header_curr AS currency_id,
                                        global_loss AS loss_percentage,
                                        salary,
                                        prod_cost_percentage,
                                        CAST((total_cost / ISNULL(scale, 1)) AS money) AS total_cost_converted
                                    FROM (
                                        SELECT DISTINCT
                                            b.bom_type,
                                            b.bom_code,
                                            b.item_code,
                                            i.customfield1 AS item_type,
                                            i.item_size     AS brand,
                                            b.umr_salary,
                                            ISNULL(b.currency_id, 'idr') AS header_curr,
                                            ISNULL(b.loss_percentage, 0) AS global_loss,
                                            ISNULL(b.salary, 0)          AS salary,
                                            ISNULL(b.prod_cost_percentage, 0) AS prod_cost_percentage,
                                            d.rm_code,
                                            d.rm_qty,
                                            ISNULL(d.currency_id, 'idr') AS details_curr,
                                            ISNULL(d.is_accessories, 0)  AS is_accessories,
                                            (((d.cost * d.item_convertion) * d.rm_qty)
                                            + (((d.cost * d.item_convertion) * d.rm_qty) * d.comp_loss_percentage / 100)) AS total_cost,
                                            (SELECT TOP 1 scale
                                            FROM tcurrencyconverter WITH (NOLOCK)
                                            WHERE currency_id_1 = ISNULL(b.currency_id, 'idr')
                                            AND currency_id_2 = ISNULL(d.currency_id, 'idr')
                                            AND tcurrencyconverter.status = 1
                                            ORDER BY last_update DESC) AS scale,
                                            CASE WHEN d.is_expensive_parts = 1
                                                THEN d.loss_percentage
                                                ELSE 100
                                            END AS detail_loss_percent
                                        FROM TPPICITEMBOM_DETAIL d WITH (NOLOCK)
                                        INNER JOIN TPPICITEMBOM b      WITH (NOLOCK) ON b.bom_code = d.bom_code
                                        INNER JOIN (
                                            SELECT item_code, MAX(LAST_UPDATE) AS LAST_UPDATE
                                            FROM TPPICITEMBOM WITH (NOLOCK)
                                            GROUP BY item_code
                                        ) last_TPPICITEMBOM
                                            ON b.item_code = last_TPPICITEMBOM.item_code
                                        AND b.LAST_UPDATE = last_TPPICITEMBOM.LAST_UPDATE
                                        INNER JOIN titem i WITH (NOLOCK) ON i.item_code = b.item_code
                                        WHERE 1 = 1
                                    ) first_layer
                                    WHERE ISNULL(first_layer.total_cost, 0) <> 0
                                ) second_layer
                                GROUP BY
                                    bom_type, bom_code, item_code, item_type, brand,
                                    umr_salary, currency_id, loss_percentage, salary,
                                    prod_cost_percentage, total_cost_converted
                            ) T
                            GROUP BY
                                bom_type, bom_code, item_code, item_type, brand,
                                umr_salary, currency_id, loss_percentage, salary,
                                prod_cost_percentage
                        ) BOMDetail ON taccsi_detail.item_code = BOMDetail.item_code
                        LEFT JOIN (
                            SELECT
                                b.item_code,
                                SUM(((d.cost * d.item_convertion) * d.rm_qty)
                                    + (((d.cost * d.item_convertion) * d.rm_qty) * d.comp_loss_percentage / 100)) AS total_cost_rm
                            FROM TPPICITEMBOM_DETAIL d WITH (NOLOCK)
                            JOIN TPPICITEMBOM b WITH (NOLOCK) ON b.bom_code = d.bom_code
                            WHERE d.is_accessories = 1
                            GROUP BY b.item_code
                        ) TBOM_Guitar ON taccsi_detail.item_code = TBOM_Guitar.item_code
                        LEFT JOIN (
                            SELECT
                                b.item_code,
                                b.cost AS total_cost_rm2
                            FROM TPPICITEMBOM b WITH (NOLOCK)
                            JOIN (
                                SELECT item_code, MAX(LAST_UPDATE) AS LAST_UPDATE
                                FROM TPPICITEMBOM WITH (NOLOCK)
                                GROUP BY item_code
                            ) last_TPPICITEMBOM
                            ON b.item_code = last_TPPICITEMBOM.item_code
                            AND b.LAST_UPDATE = last_TPPICITEMBOM.LAST_UPDATE
                        ) TBOM_Guitar_2 ON taccsi_detail.item_code = TBOM_Guitar_2.item_code
                        LEFT JOIN (
                            SELECT
                                b.item_code,
                                SUM(((d.cost * d.item_convertion) * d.rm_qty)
                                    + (((d.cost * d.item_convertion) * d.rm_qty) * d.comp_loss_percentage / 100)) AS total_cost_rm_3
                            FROM TPPICITEMBOM_DETAIL d WITH (NOLOCK)
                            JOIN TPPICITEMBOM b WITH (NOLOCK) ON b.bom_code = d.bom_code
                            WHERE d.is_accessories <> 1
                            AND d.is_expensive_parts = 1
                            GROUP BY b.item_code
                        ) TBOM_Guitar_3 ON taccsi_detail.item_code = TBOM_Guitar_3.item_code
                        LEFT JOIN (
                            SELECT DISTINCT
                                x.currency_id_1 AS curr_to,
                                x.currency_id_2 AS curr_from,
                                (SELECT TOP 1 scale
                                FROM TCurrencyConverter WITH (NOLOCK)
                                WHERE currency_id_1 = x.currency_id_1
                                AND currency_id_2 = x.currency_id_2
                                ORDER BY last_update DESC) AS scale
                            FROM (
                                SELECT DISTINCT
                                    tc.currency_id_1,
                                    tc.currency_id_2
                                FROM tcurrency            c  WITH (NOLOCK)
                                INNER JOIN tcurrencyconverter tc WITH (NOLOCK)
                                    ON c.currency_id = tc.currency_id_1
                                WHERE c.status = 1
                            ) x
                        ) TScale ON TScale.curr_from = BOMDetail.currency_id
                                AND TScale.curr_to   = taccsi_header.currency_ID
                        WHERE taccsi_header.account_id = ?
                        AND taccsi_header.invoice_date >= ?
                        AND taccsi_header.invoice_date <= ?
                        AND ISNULL(taccsi_header.isvoid, 0) = 0
                        ORDER BY taccsi_header.invoice_number
                        -- OPTION (RECOMPILE);
                        SQL;

            $binds = [$acct->Account_ID, $datefrom, $dateto];
            $qSI = $CI->db->query($sqlSI, $binds);
            if ($qSI->num_rows() > 0) {
                $NoTransactionAvailable = 0;
            }

            if ($NoTransactionAvailable != 1) {
                if ($acctIdx > 1) { // ← ganti dari $qGetAccount->result_id->current_row
                    echo '<br><hr style="border-top:1px solid #CCCCCC;border-left:none;border-right:none;border-bottom:none"><br>';
                }
            }

            if ($qSI->num_rows() > 0) {
                // === Table header
                echo '<br><table border="0" cellpadding="3" cellspacing="1" width="100%">';
                echo '<tr class="formtextreport" style="border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; border-bottom:1px solid #000;">';
                echo '<td bgcolor="EFEFEF" style="' . $borderStyle . '; border-top:1px solid #CCC;" colspan="24" align="center"><b>' . $DO_VAR['SalesInvoice'] . '</b></td>';
                echo '</tr>';
                echo '<tr class="formtextreport" style="border-left:1px solid #000; border-right:1px solid #000; border-top:1px solid #000; border-bottom:1px solid #000;">';
                $hdrs = [
                    'No.',
                    'customername',
                    'SINumber',
                    'InvoicePrintNo',
                    'Customer PO Number',
                    'ShippingInstructionNumber',
                    'SIDate',
                    'ItemCode',
                    'ItemName',
                    'category',
                    'type',
                    'color',
                    'brand',
                    'Qty',
                    'UnitPrice',
                    '',
                    'DiscountValue',
                    'Discount',
                    'AdditionalDisc',
                    'Amount',
                    'Tax 1',
                    'Tax 2',
                    'claimdeduction',
                    'TotAmount1',
                    'Material',
                    'Payroll',
                    'Manufacture',
                    'Cost Of Goods'
                ];
                foreach ($hdrs as $i => $h) {
                    $label = isset($DO_VAR[$h]) ? $DO_VAR[$h] : $h;
                    echo '<td nowrap bgcolor="EFEFEF" style="' . $borderStyle . '; border-top:1px solid #CCC;">' . htmlspecialchars($label) . '</td>';
                }
                echo '</tr>';

                $Nomor = 1;
                $amt = 0;
                $cd = 0;
                $amtd = 0;
                $SubQty = 0;
                $subAmt = ['IDR' => 0, 'EUR' => 0, 'USD' => 0];
                $subAmtD = ['IDR' => 0, 'EUR' => 0, 'USD' => 0];

                $prevInvoice = null;
                foreach ($qSI->result() as $row) {
                    echo '<tr class="formtextreport" valign="top" style="' . $borderStyle . '">';

                    if ($prevInvoice === null || $prevInvoice !== $row->invoice_number) {
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . $Nomor . '.</td>';
                    } else {
                        echo '<td style="' . $borderStyle . '" class="formtextreport">&nbsp;</td>';
                    }

                    // customer name
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($acct->Account_Name ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->invoice_number ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->invoiceprintnumber ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->PO_NumCustomer ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->so_number ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . ($row->invoice_date ? date('d M Y', strtotime($row->invoice_date)) : '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->item_code ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->item_name ?: '&nbsp;') . '</td>';

                    // category name query (inline, as in CF)
                    $qCat = $CI->db->query("select itemcategory_name from titemcategory inner join titemcompany on titemcompany.itemcategory_id=titemcategory.itemcategory_id where item_code=?", [$row->item_code]);
                    $catName = ($qCat->num_rows() ? $qCat->row()->itemcategory_name : '');
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($catName !== '' ? htmlspecialchars($catName) : '&nbsp;') . '</td>';

                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->item_type ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->color_name ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->brand ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($row->qty) ? number_format($row->qty, 2) : '0.00') . '</td>';

                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->currency_id ?: '&nbsp;') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($row->unitprice) ? number_format($row->unitprice, 2) : '0.00') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($row->disc_value) ? number_format($row->disc_value, 2) : '0.00') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($row->disc_percentage) ? number_format($row->disc_percentage, 2) . '%' : '0.00') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($row->transactiondiscountrate) ? number_format($row->transactiondiscountrate, 2) . '%' : '0.00') . '</td>';

                    // amt / cd / amtd calculations
                    $qty  = (float) $row->qty;
                    $unit = (float) $row->unitprice;
                    $dval = (float) $row->disc_value;
                    $dper = (float) $row->disc_percentage;
                    $trdr = (float) $row->transactiondiscountrate;

                    $amt  = $qty * ($unit - $dval) * (1 - ($dper / 100));
                    $amt  = $amt - ($amt * ($trdr / 100));
                    $cd   = (float) $row->claimdeduction;
                    $amtd = $amt - $cd; // PrecisionEvaluate equivalent not needed in PHP

                    $SubQty += $qty;
                    $c = strtoupper($row->currency_id);
                    if (!isset($subAmt[$c]))  $subAmt[$c] = 0;
                    if (!isset($subAmtD[$c])) $subAmtD[$c] = 0;
                    if (!isset($totAmt[$c]))  $totAmt[$c] = 0;
                    if (!isset($totAmtD[$c])) $totAmtD[$c] = 0;

                    $subAmt[$c]  += $amt;
                    $subAmtD[$c] += $amtd;
                    $totqty      += $qty;
                    $totAmt[$c]  += $amt;
                    $totAmtD[$c] += $amtd;

                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($amt) ? number_format($amt, 4) : '0.00') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->tax_code1 === '0' ? '-' : ($row->invoice_id == 0 ? $DO_VAR['IncludedPPN'] : ($row->tax_code1 ?: '&nbsp;'))) . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport">' . ($row->tax_code2 === '0' ? '-' : ($row->tax_code2 ?: '&nbsp;')) . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($cd) ? number_format($cd, 4) : '0.00') . '</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($amtd) ? number_format($amtd, 4) : '0.00') . '</td>';

                    // COGS branch
                    $scale = (float)($row->scale ?: 1);
                    if ((int)$row->BOM_TYPE > 3 || (int)$row->BOM_TYPE === 0) {
                        $total_COGS = (float)$row->total_cost;
                        $total_lossCOGS = ($total_COGS * (float)$row->loss_percentage) / 100.0;
                        $total_prod_costCOGS = ($total_COGS * (float)$row->prod_cost_percentage) / 100.0;
                        $grandtotal = ($total_COGS + $total_lossCOGS + (float)$row->salary2 + $total_prod_costCOGS);

                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . number_format((($total_COGS + $total_lossCOGS) / $scale), 2) . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . number_format(((float)$row->salary2) / $scale, 2) . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($total_prod_costCOGS) ? number_format(($total_prod_costCOGS / $scale), 2) : '0.00') . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($grandtotal) ? number_format(($grandtotal / $scale), 2) : '0.00') . '</td>';
                    } else {
                        $total_acc = 0;
                        $total_costR = 0;
                        $total_idr = (float)$row->total_cost_rm2;
                        $total_costR += $total_idr;
                        $total_loss_amountR = 0;

                        $tcostidr = (float)$row->total_cost_rm_3;
                        $losspersen = 1 - ((float)$row->loss_percentage / 100.0);
                        $lossamount = $tcostidr * $losspersen;
                        $total_loss_amountR += $lossamount;

                        $cost_idr = (float)$row->total_cost_rm;
                        $total_acc += $cost_idr;
                        $rm_1_cost = $total_costR - $total_acc;
                        $rm_2_cost = $rm_1_cost - $total_loss_amountR;

                        $total_loss_amount = $rm_2_cost * ((float)$row->loss_percentage / 100.0);
                        $total_prod_cost = ($total_loss_amount + $rm_2_cost) * (((float)$row->prod_cost_percentage) / 100.0);
                        $total_cog = $total_prod_cost + $total_loss_amount + (float)$row->salary2 + $total_costR;

                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . number_format((($total_cog / $scale) - ($total_prod_cost / $scale) - (((float)$row->salary2) / $scale)), 2) . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . number_format(((float)$row->salary2) / $scale, 2) . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($total_prod_cost) ? number_format(($total_prod_cost / $scale), 2) : '0.00') . '</td>';
                        echo '<td nowrap style="' . $borderStyle . '" class="formtextreport" align="right">' . (is_numeric($total_cog) ? number_format(($total_cog / $scale), 2) : '0.00') . '</td>';
                    }

                    echo '</tr>';

                    if ($prevInvoice === null || $prevInvoice !== $row->invoice_number) {
                        $Nomor++;
                    }
                    $prevInvoice = $row->invoice_number;
                }

                // === Subtotal per currency (EUR, IDR, USD)
                $lstCurr = ['EUR', 'IDR', 'USD'];
                $cnt = 0;
                foreach ($lstCurr as $curr) {
                    $cnt++;
                    echo '<tr>';
                    echo '<td colspan="11">&nbsp;</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtext" align="right">' . ($cnt === 1 ? 'SUB' : '&nbsp;') . '</td>';
                    if ($cnt === 1) {
                        echo '<td nowrap style="' . $borderStyle . '" class="formtext" align="right"><b>' . (is_numeric($SubQty) ? number_format($SubQty, 2) : '0.00') . '</b></td>';
                    } else {
                        echo '<td>&nbsp;</td>';
                    }
                    echo '<td colspan="4">&nbsp;</td>';
                    echo '<td>' . $curr . '</td>';
                    $v1 = isset($subAmt[$curr]) ? $subAmt[$curr] : 0;
                    $v2 = isset($subAmtD[$curr]) ? $subAmtD[$curr] : 0;
                    echo '<td nowrap style="' . $borderStyle . '" class="formtext" align="right">' . number_format($v1, 4) . '</td>';
                    echo '<td colspan="3">&nbsp;</td>';
                    echo '<td nowrap style="' . $borderStyle . '" class="formtext" align="right">' . number_format($v2, 4) . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            }
        }
    }
    ?>
    <br>

    <?php if (!empty($seldocument) && $excel == 1): ?>
        <table width="100%" border="1">
            <tr>
                <?php if ($seldocument === 'salesinvoice'): ?>
                    <?php $lstCurr = ['EUR', 'IDR', 'USD'];
                    $cnt = 0;
                    foreach ($lstCurr as $curr): $cnt++; ?>
            <tr>
                <td colspan="11">&nbsp;</td>
                <td nowrap style="<?= $borderStyle ?>" class="formtext" align="right"><?= $cnt === 1 ? 'TOTAL' : '&nbsp;' ?></td>
                <?php if ($cnt === 1): ?>
                    <td nowrap style="<?= $borderStyle ?>" class="formtext" align="right"><b><?= is_numeric($totqty) ? number_format($totqty, 2) : '0.00' ?></b></td>
                <?php else: ?>
                    <td>&nbsp;</td>
                <?php endif; ?>
                <td colspan="4">&nbsp;</td>
                <td><?= $curr ?></td>
                <td nowrap style="<?= $borderStyle ?>" class="formtext" align="right"><?= number_format(isset($totAmt[$curr]) ? $totAmt[$curr] : 0, 4) ?></td>
                <td colspan="3">&nbsp;</td>
                <td nowrap style="<?= $borderStyle ?>" class="formtext" align="right"><?= number_format(isset($totAmtD[$curr]) ? $totAmtD[$curr] : 0, 4) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tr>
        </table>
    <?php endif; ?>

</body>

</html>

<script>
    window.resizeTo(screen.width, screen.height);
    window.moveTo(0, 0);
</script>