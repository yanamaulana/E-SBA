<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Guitar Order Balance Summary</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 20px; font: 13px Arial, sans-serif; color: #181c32; background: #fff; }
        h1 { font-size: 24px; margin: 0 0 6px; }
        .title-line { border-top: 5px solid #ff4655; border-bottom: 4px solid #a50034; margin-bottom: 12px; }
        .report-meta { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px; line-height: 1.5; margin-bottom: 14px; }
        .toolbar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
        .toolbar button { font: inherit; font-weight: bold; padding: 8px 14px; border: 0; border-radius: 5px; cursor: pointer; background: #fff0f4; color: #c72550; }
        .toolbar button:hover { background: #ffe1e9; }
        #gbr-report .gbr-table { border-collapse: collapse; width: 100%; white-space: nowrap; font-size: 12px; }
        #gbr-report .gbr-table th, #gbr-report .gbr-table td { border: 1px solid #a1adbb; padding: 3px 5px; vertical-align: middle; }
        #gbr-report .gbr-table th { background: #dce6f1; color: #181c32; text-align: center; font-weight: 700; }
        #gbr-report .gbr-table .gbr-number { text-align: right; font-variant-numeric: tabular-nums; }
        #gbr-report .gbr-table .gbr-total td, #gbr-report .gbr-table .gbr-average td { background: #e2e8f0; font-weight: 700; }
        #gbr-report .gbr-table .gbr-amount-total { font-weight: 700; }
        #gbr-report .gbr-accounting { display: flex; align-items: baseline; justify-content: space-between; gap: 5px; min-width: 0; }
        #gbr-report .gbr-table .gbr-negative { color: #b00020; }
        .gbr-table-wrap { overflow-x: auto; }
        .text-muted { color: #555; line-height: 1.5; font-size: 11px; }
        @media print {
            @page { size: A4 landscape; margin: 7mm; }
            body { margin: 0; font-size: 10px; }
            h1 { font-size: 18px; margin-bottom: 4px; }
            .title-line { border-top-width: 3px; border-bottom-width: 2px; margin-bottom: 6px; }
            .report-meta { gap: 4px; margin-bottom: 7px; }
            .text-muted { font-size: 8px; line-height: 1.3; margin: 6px 0 0; }
            .toolbar { display: none !important; }
            .gbr-table-wrap { overflow: visible !important; }
            #gbr-report .gbr-table { font-size: 9px; table-layout: fixed; width: 100%; line-height: 1.2; }
            #gbr-report .gbr-table th, #gbr-report .gbr-table td { padding: 2px 3px; border-width: 0.5px; }
            #gbr-report .gbr-table th { white-space: normal; }
            #gbr-report .gbr-table td { white-space: nowrap; }
            #gbr-report .gbr-table .gbr-col-year { width: 4%; }
            #gbr-report .gbr-table .gbr-col-month { width: 5.4%; }
            #gbr-report .gbr-table .gbr-col-qty { width: 4.3%; }
            #gbr-report .gbr-table .gbr-col-amt { width: 7.2%; }
            #gbr-report .gbr-accounting { gap: 2px; min-width: 0; }
            #gbr-report .gbr-accounting > span { white-space: nowrap; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
            * { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <main id="gbr-report">
        <h1>Guitar Order Balance Summary</h1>
        <div class="title-line"></div>
        <div class="report-meta">
            <div><strong>PT. Samick Indonesia</strong><br>
                Production period: <?= sprintf('%02d/%04d – %02d/%04d', $from_month, $from_year, $to_month, $to_year) ?> · Currency: USD
            </div>
            <div>Generated: <?= html_escape($generated) ?></div>
        </div>
        <div class="toolbar">
            <form method="get"
                action="<?= html_escape(site_url('Report/GuitarBalanceReport')) ?>">

                <input type="hidden" name="action" value="xlsx">

                <input type="hidden" name="from_year"
                    value="<?= (int) $from_year ?>">

                <input type="hidden" name="from_month"
                    value="<?= (int) $from_month ?>">

                <input type="hidden" name="to_year"
                    value="<?= (int) $to_year ?>">

                <input type="hidden" name="to_month"
                    value="<?= (int) $to_month ?>">

                <button type="submit"
                        style="background:#217346; color:#fff;">
                    Export Excel
                </button>
            </form>
        </div>
        <div class="gbr-table-wrap">
            <table class="gbr-table" aria-label="Guitar Order Balance Summary">
                <colgroup>
                    <col class="gbr-col-year"><col class="gbr-col-month">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <col class="gbr-col-qty"><col class="gbr-col-amt">
                        <col class="gbr-col-qty"><col class="gbr-col-amt"><col class="gbr-col-amt">
                    <?php endfor; ?>
                </colgroup>
                <thead>
                    <tr>
                        <th rowspan="3">Year</th><th rowspan="3">Month</th>
                        <?php foreach (['Order', 'Balance', 'Export'] as $section): ?><th colspan="5"><?= $section ?></th><?php endforeach; ?>
                    </tr>
                    <tr><?php for ($i = 0; $i < 3; $i++): ?><th colspan="2">AG</th><th colspan="2">EG</th><th>Total</th><?php endfor; ?></tr>
                    <tr><?php for ($i = 0; $i < 3; $i++): ?><th>Qty</th><th>Amt</th><th>Qty</th><th>Amt</th><th>Total Amt</th><?php endfor; ?></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr class="gbr-<?= html_escape($row['kind']) ?>">
                            <td><?= (int) $row['year'] ?></td>
                            <td><?= html_escape($row['month']) ?></td>
                            <?php foreach ($row['values'] as $i => $v): ?>
                                <?php
                                $qty = in_array($i % 5, [0, 2], true);
                                $formatted = $qty && $row['kind'] !== 'average'
                                    ? rtrim(rtrim(number_format($v, 4, '.', ','), '0'), '.')
                                    : number_format($v, 0);
                                $display = $v == 0 ? '–' : $formatted;
                                ?>
                                <td class="gbr-number <?= $row['kind'] === 'month' && $i % 5 === 4 ? 'gbr-amount-total' : '' ?> <?= $v < 0 ? 'gbr-negative' : '' ?>"><?php if ($qty): ?><?= html_escape($display) ?><?php else: ?><span class="gbr-accounting"><span>$</span><span><?= html_escape($display) ?></span></span><?php endif; ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- <p class="text-muted fs-7 mt-4 mb-0">Production month/year, not shipment date. SG/GS POs excluded. Export quantities use approved non-void shipment notes. Amounts are proportional booked SO values, not invoice values (provisional). Negative balances retained; no shipment cutoff. Totals and averages cover selected months only. USD amounts and average quantities display as whole numbers; calculations retain their precision.</p> -->
    <?php include APPPATH . 'views/Report/Sales/guitar_buyer_summary.php'; ?>

    <?php include APPPATH . 'views/Report/Sales/guitar_production_orders.php'; ?>
    </main>
</body>
</html>
