<?php
/* Preview partial only. Controller must provide $productionOrderRows (result_array())
 * on success, or $productionOrderError on failure. No database calls in this view.
 * Q018 supplies BuyerName for monthly buyer subtotals.
 */
$poEscape = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$poMonths = array(1=>'JAN', 2=>'FEB', 3=>'MAR', 4=>'APR', 5=>'MAY', 6=>'JUN', 7=>'JUL', 8=>'AUG', 9=>'SEP', 10=>'OCT', 11=>'NOV', 12=>'DEC');
$poColumns = array('Order Receiving Date', 'Prod Year', 'Expected Ship. Date', 'Prod Month', 'Customer', 'P/O', 'AG Order', 'AG Balance', 'EG Order', 'EG Balance', 'ETC', 'AG PO Amt', 'EG PO Amt', 'Total Amount', 'Remark');
$poQuantity = array('AG Order', 'AG Balance', 'EG Order', 'EG Balance');
$poMoney = array('AG PO Amt', 'EG PO Amt', 'Total Amount');
$poNumeric = array_merge($poQuantity, $poMoney);
$poFormat = static function ($value, $money = false) {
    $number = (float) $value;
    if ($number == 0) { return '-'; }
    $places = $money ? 2 : 0;
    if (abs($number - round($number, $places)) > 0.0000001) { $places = 4; }
    return ($money ? '$' : '') . number_format($number, $places, '.', ',');
};
// Display formatting only: keep original dates and production period for grouping.
$poDateFormat = static function ($value) {
    if ($value instanceof DateTimeInterface) { return $value->format('m/d'); }
    $value = trim((string) $value);
    if ($value === '') { return ''; }
    if (preg_match('~^(\d{4})[-/](\d{1,2})[-/](\d{1,2})(?:$|[ T])~', $value, $parts)
        && checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])) {
        return sprintf('%02d/%02d', (int) $parts[2], (int) $parts[3]);
    }
    return $value;
};
$poGroups = array();
$poGroupingError = false;
if (isset($productionOrderRows) && is_array($productionOrderRows)) {
    foreach ($productionOrderRows as $poRow) {
        // Do not silently fall back to different grouping rules if old SQL is installed.
        if (!isset($poRow['BuyerName']) || trim((string) $poRow['BuyerName']) === '') {
            $poGroupingError = true;
            break;
        }
        $poYear = (int) $poRow['Prod Year'];
        $poMonth = (int) $poRow['Prod Month'];
        $poBuyer = (string) $poRow['BuyerName'];
        $poGroups[$poYear][$poMonth][$poBuyer][] = $poRow;
    }
}
ksort($poGroups);
?>
<style>
.guitar-po-preview { margin-top: 28px; color: inherit; }
.guitar-po-preview .po-year-accordion { margin: 12px 0; border: 1px solid #a1adbb; border-radius: 5px; }
.guitar-po-preview .po-year-title { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; font-size: 16px; font-weight: bold; color: #181c32; background: #dce6f1; cursor: pointer; list-style: none; }
.guitar-po-preview .po-year-title::-webkit-details-marker { display: none; }
.guitar-po-preview .po-year-title::after { content: '+'; font-size: 20px; line-height: 1; }
.guitar-po-preview .po-year-accordion[open] > .po-year-title { border-bottom: 3px solid #9b252b; }
.guitar-po-preview .po-year-accordion[open] > .po-year-title::after { content: '−'; }
.guitar-po-preview .po-year-title:focus-visible { outline: 2px solid #245b93; outline-offset: -3px; }
.guitar-po-preview .po-scroll { overflow-x: auto; max-width: 100%; }
.guitar-po-preview table { border-collapse: collapse; width: 100%; min-width: 1500px; font-size: 12px; }
.guitar-po-preview th, .guitar-po-preview td { border: 1px solid #888; padding: 5px 7px; vertical-align: middle; }
.guitar-po-preview th { background: #d9e8f5; color: #172b3e; text-align: center; white-space: nowrap; }
.guitar-po-preview .po-num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
.guitar-po-preview .po-center { text-align: center; white-space: nowrap; }
.guitar-po-preview .po-customer { min-width: 220px; }
.guitar-po-preview .po-buyer-total td { background: #e2efd9; color: #172b3e; font-weight: bold; }
.guitar-po-preview .po-month-total td { background: #d9e8f5; color: #172b3e; font-weight: bold; }
.guitar-po-preview .po-message { padding: 12px; border: 1px solid #aaa; }
</style>
<section class="guitar-po-preview" aria-label="Guitar Production Orders">
<?php if (!empty($productionOrderError)): ?>
    <p class="po-message">Guitar Production Orders could not be loaded.</p>
<?php elseif (!isset($productionOrderRows) || !is_array($productionOrderRows)): ?>
    <p class="po-message">Guitar Production Orders data is not connected yet.</p>
<?php elseif ($poGroupingError): ?>
    <p class="po-message">Buyer grouping data is missing. Check that the updated GuitarProductionOrders.sql is installed.</p>
<?php elseif (!$poGroups): ?>
    <p class="po-message">No Guitar Production Orders for the selected period.</p>
<?php else: ?>
    <?php foreach ($poGroups as $poYear => $poYearMonths): ksort($poYearMonths); ?>
        <details class="po-year-accordion" name="guitar-production-year">
        <summary class="po-year-title"><?php echo $poEscape($poYear); ?> Guitar Production Orders</summary>
        <div class="po-scroll">
        <table>
            <thead>
                <tr>
                    <?php foreach (array_slice($poColumns, 0, 6) as $poColumn): ?>
                        <th scope="col" rowspan="2"><?php echo $poEscape($poColumn); ?></th>
                    <?php endforeach; ?>
                    <th scope="colgroup" colspan="2">AG</th>
                    <th scope="colgroup" colspan="2">EG</th>
                    <th scope="col" rowspan="2">ETC</th>
                    <th scope="col" rowspan="2">AG<br>PO Amt</th>
                    <th scope="col" rowspan="2">EG<br>PO Amt</th>
                    <th scope="col" rowspan="2">Total<br>Amount</th>
                    <th scope="col" rowspan="2">Remark</th>
                </tr>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Balance</th>
                    <th scope="col">Order</th>
                    <th scope="col">Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($poYearMonths as $poMonth => $poBuyers): ?>
                <?php $poTotals = array_fill_keys($poNumeric, 0.0); ?>
                <?php foreach ($poBuyers as $poBuyer => $poRows): ?>
                    <?php $poBuyerTotals = array_fill_keys($poNumeric, 0.0); ?>
                    <?php foreach ($poRows as $poRow): ?>
                        <tr>
                        <?php foreach ($poColumns as $poColumn): ?>
                            <?php
                            $poValue = isset($poRow[$poColumn]) ? $poRow[$poColumn] : '';
                            $poClass = '';
                            if (in_array($poColumn, $poNumeric, true)) {
                                // Both totals count each original order exactly once.
                                $poTotals[$poColumn] += (float) $poValue;
                                $poBuyerTotals[$poColumn] += (float) $poValue;
                                $poValue = $poFormat($poValue, in_array($poColumn, $poMoney, true));
                                $poClass = 'po-num';
                            } elseif ($poColumn === 'Customer') {
                                $poClass = 'po-customer';
                            } elseif (in_array($poColumn, array('Order Receiving Date', 'Prod Year', 'Expected Ship. Date', 'Prod Month'), true)) {
                                $poClass = 'po-center';
                                if (in_array($poColumn, array('Order Receiving Date', 'Expected Ship. Date'), true)) {
                                    $poValue = $poDateFormat($poValue);
                                } elseif ($poColumn === 'Prod Year' && $poValue !== '') {
                                    $poValue = sprintf('%02d', (int) $poValue % 100);
                                } elseif ($poColumn === 'Prod Month' && $poValue !== '') {
                                    $poValue = sprintf('%02d', (int) $poValue);
                                }
                            }
                            ?>
                            <td class="<?php echo $poClass; ?>"><?php echo $poEscape($poValue); ?></td>
                        <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="po-buyer-total">
                        <td colspan="6">SUB-TOTAL <?php echo $poEscape($poBuyer); ?></td>
                        <?php foreach (array_slice($poColumns, 6) as $poColumn): ?>
                            <?php if (isset($poBuyerTotals[$poColumn])): ?>
                                <td class="po-num"><?php echo $poEscape($poFormat($poBuyerTotals[$poColumn], in_array($poColumn, $poMoney, true))); ?></td>
                            <?php else: ?><td></td><?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                <tr class="po-month-total">
                    <td colspan="6">TOTAL <?php echo $poEscape(isset($poMonths[$poMonth]) ? $poMonths[$poMonth] : $poMonth); ?></td>
                    <?php foreach (array_slice($poColumns, 6) as $poColumn): ?>
                        <?php if (isset($poTotals[$poColumn])): ?>
                            <td class="po-num"><?php echo $poEscape($poFormat($poTotals[$poColumn], in_array($poColumn, $poMoney, true))); ?></td>
                        <?php else: ?><td></td><?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        </details>
    <?php endforeach; ?>
<?php endif; ?>
</section>
