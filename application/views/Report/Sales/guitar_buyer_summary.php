<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<section class="gbs-section" aria-label="Summary per Buyer">
<style>
    #gbr-report .gbs-section { margin-top: 24px; }
    #gbr-report .gbs-section h2 { font-size: 19px; margin: 0 0 8px; }
    #gbr-report .gbs-section h3 { font-size: 14px; margin: 16px 0 5px; break-after: avoid; page-break-after: avoid; }
    #gbr-report .gbs-table { min-width: 850px; margin-bottom: 12px; table-layout: fixed; }
    #gbr-report .gbs-table.gbs-years-2 { min-width: 950px; }
    #gbr-report .gbs-table.gbs-years-3 { min-width: 1190px; font-size: 11px; }
    #gbr-report .gbs-table.gbs-years-3 th, #gbr-report .gbs-table.gbs-years-3 td { padding: 2px 3px; }
    #gbr-report .gbs-table .gbr-number { white-space: normal; overflow-wrap: anywhere; }
    #gbr-report .gbs-table .gbr-accounting { gap: 2px; }
    #gbr-report .gbs-table .gbr-accounting > span { min-width: 0; white-space: normal; overflow-wrap: anywhere; }
    #gbr-report .gbs-table .gbs-label { white-space: normal; overflow-wrap: anywhere; text-align: left; }
    #gbr-report .gbs-table.gbs-monthly .gbs-label { text-align: center; padding-left: 2px; padding-right: 2px; }
    #gbr-report .gbs-table .gbs-qty-total { font-weight: 700; }
    #gbr-report .gbs-table .gbs-na { text-align: center; color: #777; background: #f4f4f4; }
    #gbr-report .gbs-section .gbs-warning { color: #a50034; font-size: 12px; }
    @media print {
        #gbr-report .gbs-section { margin-top: 14px; }
        #gbr-report .gbs-section h2 { font-size: 14px; break-after: avoid; page-break-after: avoid; }
        #gbr-report .gbs-section h3 { font-size: 11px; margin: 10px 0 4px; }
        #gbr-report .gbs-table, #gbr-report .gbs-table.gbs-years-2,
        #gbr-report .gbs-table.gbs-years-3 { min-width: 0; margin-bottom: 7px; }
        #gbr-report .gbs-table.gbs-years-3 { font-size: 8px; }
        #gbr-report .gbs-table.gbs-years-3 th, #gbr-report .gbs-table.gbs-years-3 td { padding: 1px 2px; }
        #gbr-report .gbs-table .gbs-label { white-space: normal; }
        #gbr-report .gbs-table .gbr-accounting { gap: 1px; }
        #gbr-report .gbs-section .gbs-warning { font-size: 9px; }
    }
</style>
<h2>Summary per Buyer</h2>
<?php if (!empty($buyer_error)): ?>
    <p class="gbs-warning"><?= html_escape($buyer_error) ?></p>
<?php else: ?>
<?php
// Work with selected production months only. Up to three years per table, with compact column widths.
$zero = [0.0, 0.0, 0.0, 0.0];
$fields = ['AG_OrderQty', 'AG_OrderAmt', 'EG_OrderQty', 'EG_OrderAmt'];
$selected = [];
$monthTotals = [];
$years = [];
for ($y = $from_year, $m = $from_month; $y < $to_year || ($y === $to_year && $m <= $to_month);) {
    $key = sprintf('%04d-%02d', $y, $m);
    $selected[$y][$m] = $key;
    $monthTotals[$key] = $zero;
    $years[$y] = $y;
    if (++$m > 12) { $m = 1; ++$y; }
}
$yearChunks = array_chunk(array_values($years), 3);
$sum = static function (array $a, array $b): array {
    return [$a[0]+$b[0], $a[1]+$b[1], $a[2]+$b[2], $a[3]+$b[3]];
};
$buyers = [];
$annualTotals = array_fill_keys(array_values($years), $zero);
foreach ($buyer_rows as $r) {
    $y = (int) $r['Production_year'];
    $m = (int) $r['Production_month'];
    if (!isset($selected[$y][$m])) { continue; }
    $name = (string) $r['BuyerName'];
    // Prefix prevents PHP from converting a numeric buyer name into an integer key.
    $id = 'buyer:' . $name;
    $v = [];
    foreach ($fields as $field) { $v[] = (float) $r[$field]; }
    if (!isset($buyers[$id])) {
        $buyers[$id] = ['name' => $name, 'months' => [], 'years' => [], 'total' => $zero];
    }
    $buyers[$id]['months'][$y][$m] = $sum($buyers[$id]['months'][$y][$m] ?? $zero, $v);
    $buyers[$id]['years'][$y] = $sum($buyers[$id]['years'][$y] ?? $zero, $v);
    $buyers[$id]['total'] = $sum($buyers[$id]['total'], $v);
    $annualTotals[$y] = $sum($annualTotals[$y], $v);
    $key = $selected[$y][$m];
    $monthTotals[$key] = $sum($monthTotals[$key], $v);
}
// Match the Excel export: test each calendar month across the entire selected period.
// Check individual quantity and amount values, not the net annual total.
foreach ($buyers as $id => $buyer) {
    $activeMonths = [];
    foreach ($buyer['months'] as $y => $monthly) {
        foreach ($monthly as $m => $v) {
            if (!isset($selected[$y][$m])) { continue; }
            foreach ($v as $value) {
                if ($value != 0) {
                    $activeMonths[$m] = true;
                    break;
                }
            }
        }
    }
    if (!$activeMonths) {
        unset($buyers[$id]);
    } else {
        $buyers[$id]['activeMonths'] = $activeMonths;
    }
}
// Most guitars ordered over the selected period first; alphabetical on ties.
uasort($buyers, static function (array $a, array $b): int {
    $qa = $a['total'][0] + $a['total'][2];
    $qb = $b['total'][0] + $b['total'][2];
    return ($qb <=> $qa) ?: (strcasecmp($a['name'], $b['name']) ?: strcmp($a['name'], $b['name']));
});
$mismatches = [];
foreach ($balance_months as $r) {
    $key = sprintf('%04d-%02d', (int) $r['Production_year'], (int) $r['Production_month']);
    foreach ($fields as $i => $field) {
        if (!isset($monthTotals[$key]) || abs($monthTotals[$key][$i] - (float) $r[$field]) > 0.0001) {
            $mismatches[$key] = $key;
        }
    }
}
$renderHeader = static function (array $ys, string $label): void {
    $yearCount = count($ys);
    // Monthly labels are short; reserve more width for order figures.
    $labelWidths = $label === 'Month' ? [1=>8, 2=>6, 3=>5] : [1=>28, 2=>20, 3=>15];
    $labelWidth = $labelWidths[$yearCount];
    $pairWidth = (100 - $labelWidth) / ($yearCount * 3);
    $qtyWidth = $pairWidth * 0.40;
    $amtWidth = $pairWidth * 0.60;
    ?>
    <colgroup>
        <col style="width:<?= $labelWidth ?>%">
        <?php foreach ($ys as $y): ?>
            <?php for ($i=0; $i<3; ++$i): ?>
                <col style="width:<?= number_format($qtyWidth, 6, '.', '') ?>%">
                <col style="width:<?= number_format($amtWidth, 6, '.', '') ?>%">
            <?php endfor; ?>
        <?php endforeach; ?>
    </colgroup>
    <thead>
        <tr><th rowspan="3"><?= html_escape($label) ?></th>
            <?php foreach ($ys as $y): ?><th colspan="6"><?= (int) $y ?></th><?php endforeach; ?>
        </tr>
        <tr><?php foreach ($ys as $y): ?><th colspan="2">AG</th><th colspan="2">EG</th><th colspan="2">Total</th><?php endforeach; ?></tr>
        <tr><?php foreach ($ys as $y): ?><th>Qty</th><th>Amt</th><th>Qty</th><th>Amt</th><th>Qty</th><th>Amt</th><?php endforeach; ?></tr>
    </thead>
<?php };
$renderValues = static function (?array $v): void {
    if ($v === null) { ?> <td colspan="6" class="gbs-na">N/A</td> <?php return; }
    $v = [$v[0], $v[1], $v[2], $v[3], $v[0]+$v[2], $v[1]+$v[3]];
    foreach ($v as $i => $n):
        $qty = $i % 2 === 0;
        $display = $n == 0 ? '–' : ($qty ? rtrim(rtrim(number_format($n, 4, '.', ','), '0'), '.') : number_format($n, 0));
        $totalClass = $i === 4 ? 'gbs-qty-total' : ($i === 5 ? 'gbr-amount-total' : '');
        ?>
        <td class="gbr-number <?= $totalClass ?> <?= $n < 0 ? 'gbr-negative' : '' ?>"><?php if ($qty): ?><?= html_escape($display) ?><?php else: ?><span class="gbr-accounting"><span>$</span><span><?= html_escape($display) ?></span></span><?php endif; ?></td>
    <?php endforeach;
};
?>
<!-- <p class="text-muted">Order quantities and booked SO amounts (USD), grouped by buyer and production period. Unclassified customer names are retained. Years are displayed in groups of up to three; totals cover selected months only.</p> -->
<?php if ($mismatches): ?>
    <p class="gbs-warning">Validation warning: buyer Order totals differ from the main report in <?= html_escape(implode(', ', $mismatches)) ?>. Check account joins and data changes between queries before relying on these figures.</p>
<?php endif; ?>
<?php if (!$buyers): ?>
    <p class="text-muted">No guitar buyers found for the selected production period.</p>
<?php endif; ?>
<?php foreach ($yearChunks as $ys): ?>
    <h3>Annual Summary<?= count($yearChunks) > 1 ? ' – ' . html_escape(implode(' / ', $ys)) : '' ?></h3>
    <div class="gbr-table-wrap"><table class="gbr-table gbs-table gbs-years-<?= count($ys) ?>" aria-label="Annual Summary per Buyer">
        <?php $renderHeader($ys, 'Buyer'); ?>
        <tbody>
            <?php foreach ($buyers as $buyer): ?>
                <tr><td class="gbs-label"><?= html_escape($buyer['name']) ?></td>
                    <?php foreach ($ys as $y) { $renderValues($buyer['years'][$y] ?? $zero); } ?>
                </tr>
            <?php endforeach; ?>
            <tr class="gbr-total"><td class="gbs-label">Total</td><?php foreach ($ys as $y) { $renderValues($annualTotals[$y]); } ?></tr>
        </tbody>
    </table></div>
<?php endforeach; ?>
<?php if ($buyers): ?>
    <h2 style="margin-top:20px">Monthly Detail per Buyer</h2>
    <p class="text-muted">A dash indicates zero. N/A indicates a month outside the selected period.</p>
    <?php $monthNames = [1=>'JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC']; ?>
    <?php foreach ($buyers as $buyer): ?>
        <?php foreach ($yearChunks as $ys): ?>
            <h3><?= html_escape($buyer['name']) ?><?= count($yearChunks) > 1 ? ' – ' . html_escape(implode(' / ', $ys)) : '' ?></h3>
            <div class="gbr-table-wrap"><table class="gbr-table gbs-table gbs-monthly gbs-years-<?= count($ys) ?>" aria-label="<?= html_escape($buyer['name']) ?> monthly detail">
                <?php $renderHeader($ys, 'Month'); ?>
                <tbody>
                    <?php foreach ($monthNames as $m => $label): ?>
                        <?php if (!isset($buyer['activeMonths'][$m])) { continue; } ?>
                        <tr><td class="gbs-label"><?= html_escape($label) ?></td>
                            <?php foreach ($ys as $y) { $renderValues(isset($selected[$y][$m]) ? ($buyer['months'][$y][$m] ?? $zero) : null); } ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="gbr-total"><td class="gbs-label">Total</td><?php foreach ($ys as $y) { $renderValues($buyer['years'][$y] ?? $zero); } ?></tr>
                </tbody>
            </table></div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
</section>
