<?php
// Run: php tests/cashbook/entry.test.php (no database required).
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__ . '/../../system/');
define('FCPATH', __DIR__ . '/../../');
class CI_Model { public $db; }
class CI_Controller {
    public $session, $load, $input, $output, $cashbook;
    public function __construct() { $this->session = $GLOBALS['session']; $this->load = $GLOBALS['loader']; }
}
function is_logged_in() { if (!$GLOBALS['session']->userdata('sys_sba_username')) throw new RuntimeException('login'); }
function show_error($message, $code) { throw new RuntimeException($message, $code); }
function log_message($level, $message) {}
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
require FCPATH . 'application/models/m_CashbookEntry.php';
require FCPATH . 'application/controllers/CashbookEntry.php';
class Rows {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function result_array() { return $this->rows; }
    public function row_array() { return $this->rows[0] ?? []; }
}
class Database {
    public $db_debug = true, $calls = [], $responses;
    public function __construct($responses = null) { $this->responses = $responses ?? [[['total'=>100]], [['total'=>1]], []]; }
    public function query($sql, $bindings) {
        $this->calls[] = [$sql, $bindings];
        $rows = array_shift($this->responses);
        return $rows === false ? false : new Rows($rows ?? []);
    }
}
$model = new m_CashbookEntry();
$model->db = new Database();
$request = ['entry_type'=>'BD', 'date_from'=>'2026-11-01', 'date_to'=>'2026-11-30',
    'posted'=>'0', 'void'=>'0', 'account'=>'123', 'document_status'=>['1','3'], 'approval_status'=>['3'],
    'start'=>15, 'length'=>30, 'draw'=>4,
    'order'=>[['column'=>2, 'dir'=>'asc']],
    'criteria'=>[
        ['field'=>'memo', 'operator'=>'contains', 'value'=>"O'Brien 100%_[", 'join'=>'AND'],
        ['field'=>'amount', 'operator'=>'gte', 'value'=>'100', 'join'=>'OR'],
        ['field'=>'date', 'operator'=>'eq', 'value'=>'2026-11-30', 'join'=>'AND'],
    ], 'search'=>['value'=>"%' OR 1=1 --"]];
$response = $model->datatable($request);
check($response['draw'] === 4 && $response['recordsTotal'] === 100 && $response['recordsFiltered'] === 1, 'DataTable counts/draw');
list($sql, $bindings) = $model->db->calls[2];
check(strpos($sql, 'OFFSET 15 ROWS FETCH NEXT 30 ROWS ONLY') !== false, 'Pagination');
check(substr_count(substr($sql, strpos($sql, ' ORDER BY ')), 'H.JournalH_Code') === 1, 'No duplicate document ordering');
check(strpos($sql, 'H.Company_ID = ? AND (M.Company_ID = ? OR M.Company_ID IS NULL)') !== false, 'Company scope');
check(array_slice($bindings, 0, 5) === [2,2,'BD','2026-11-01','2026-12-01'], 'Inclusive date-to must include timestamps on final day');
check(strpos($sql, "O'Brien") === false && strpos($sql, 'OR 1=1') === false, 'Search values must never enter SQL');
check(in_array("%O'Brien 100!%!_![%", $bindings, true), 'SQL Server wildcard escaping');
check(strpos($sql, "AND ((H.Memo LIKE ? ESCAPE '!') OR (H.Total_Amount >= ?) AND (CAST(H.CashBookDate AS date) = ?))") !== false, 'OR searches must stay within company/date scope');
check(strpos($sql, 'H.ClearingFlag = ?') !== false && strpos($sql, 'ISNULL(H.isVoid,0) = ?') !== false, 'Zero-valued filters must not be dropped');
check(strpos($sql, 'EXISTS (SELECT 1 FROM TAccCashBookDetail D') !== false, 'Detail searches must select headers without expanding pagination');

foreach ([
    ['entry_type'=>"BD' OR 1=1"], ['date_from'=>'2026-02-30'], ['date_from'=>'2026-12-01','date_to'=>'2026-11-01'],
    ['posted'=>'2'], ['void'=>['0']], ['approval_status'=>'3'],
    ['criteria'=>[['field'=>'H.Company_ID','operator'=>'eq','value'=>'1']]],
    ['criteria'=>[['field'=>'amount','operator'=>'gte','value'=>'oops']]],
    ['criteria'=>[['field'=>'memo','operator'=>'drop','value'=>'x']]],
    ['criteria'=>array_fill(0, 6, ['field'=>'memo','operator'=>'eq','value'=>'x'])],
] as $invalid) {
    $model->db = new Database();
    try { $model->datatable(array_replace($request, $invalid)); throw new LogicException('Invalid filter accepted'); }
    catch (InvalidArgumentException $expected) { check(!$model->db->calls, 'Reject invalid input before database queries'); }
}
$model->db = new Database();
$model->datatable(['start'=>-5,'length'=>9999,'order'=>[['column'=>999,'dir'=>'DESC; DROP TABLE x']]]);
$sql = $model->db->calls[2][0];
check(strpos($sql, 'OFFSET 0 ROWS FETCH NEXT 100 ROWS ONLY') !== false && strpos($sql, 'DROP') === false, 'Bound pagination and allowlisted sorting');
$model->db = new Database([false]);
try { $model->datatable([]); throw new LogicException('DB error hidden'); }
catch (RuntimeException $expected) { check($model->db->db_debug === true, 'Restore debug setting on database error'); }

$model->db = new Database([[['total'=>1]], [['total'=>1]], [
    ['JournalH_Code'=>'BD1','ClearingBank'=>'CLR1'],
], [], [
    ['JournalH_Code'=>'BD1','Acc_ID'=>1,'Account_Name'=>'Original'],
    ['JournalH_Code'=>'CLR1','Acc_ID'=>1,'Account_Name'=>'Shared'],
    ['JournalH_Code'=>'CLR1','Acc_ID'=>2,'Account_Name'=>'Cleared account'],
]]);
$response = $model->datatable([]);
check($response['data'][0]['Clearing_Account'] === 'CLR1 - Cleared account', 'CF10 clearing-account display');
check(count($model->db->calls) === 5, 'One batched detail query and one clearing query, not queries per row');

$headers = [];
for ($i = 1; $i <= 15; $i++) { $headers[] = ['JournalH_Code'=>'BD' . $i, 'ClearingBank'=>null]; }
$details = [];
// Even a document with more details than the page size must remain complete.
for ($i = 1; $i <= 22; $i++) {
    $details[] = ['JournalH_Code'=>'BD1', 'CashBookD_ID'=>$i, 'Account_Name'=>'Trade Accounts Payable',
        'DebCred'=>'D', 'Amount'=>'100.0000', 'Description'=>'Payment ' . $i, 'CBR_No'=>'CBR' . $i];
}
$details[] = ['JournalH_Code'=>'BD15', 'CashBookD_ID'=>23, 'Account_Name'=>null,
    'DebCred'=>'K', 'Amount'=>'200.0000', 'Description'=>null, 'CBR_No'=>''];
$model->db = new Database([[['total'=>50]], [['total'=>30]], $headers, $details]);
$response = $model->datatable(['length'=>15, 'start'=>15]);
check(count($response['data']) === 15 && $response['recordsFiltered'] === 30, 'Page and count must refer to headers');
check(count($response['data'][0]['Details']) === 22, 'Never truncate details at the header page length');
check($response['data'][1]['Details'] === [], 'Headers without details must remain visible');
check($response['data'][14]['Details'][0]['DebCred'] === 'K', 'Keep details associated with the correct header');
check($response['data'][0]['Details'][0]['Account_Name'] === 'Trade Accounts Payable', 'Use English account name');
list($detailSql, $detailBindings) = $model->db->calls[3];
check(strpos($detailSql, 'LEFT JOIN TAccChartAccount DA') !== false && strpos($detailSql, 'DA.Company_ID = H.Company_ID') !== false,
    'Account join must be company-scoped and retain details with missing account metadata');
check(strpos($detailSql, 'OFFSET') === false && $detailBindings === array_merge([2], array_column($headers, 'JournalH_Code')),
    'Detail query must fetch all details only for the paged headers');
check(strpos($detailSql, "CASE WHEN D.Invoice_No LIKE 'CBR%'") !== false, 'Legacy Invoice_No is the CBR fallback');
check(count($model->db->calls) === 4, 'Batch detail fetch must not cause an N+1 query');

class ExportPages extends m_CashbookEntry {
    public $requests = [];
    public function datatable(array $request, $includeCounts = true) {
        $this->requests[] = [$request, $includeCounts];
        $count = min(100, 205 - $request['start']);
        $rows = [];
        for ($i=0; $i<$count; $i++) { $rows[] = ['JournalH_Code'=>'BD' . ($request['start'] + $i), 'Details'=>[['Amount'=>1]]]; }
        return ['data'=>$rows];
    }
}
$export = new ExportPages();
$exportFilters = ['entry_type'=>'BD','start'=>15,'length'=>15,'search'=>['value'=>'needle'],
    'criteria'=>[['field'=>'amount','operator'=>'gt','value'=>'500']], 'order'=>[['column'=>2,'dir'=>'asc']]];
$all = iterator_to_array($export->export_headers($exportFilters));
check(count($all) === 205 && $all[0]['JournalH_Code'] === 'BD0' && $all[204]['JournalH_Code'] === 'BD204', 'Export must cover all headers, not the active page');
check(count($export->requests) === 3, 'Export retrieves bounded batches');
foreach ($export->requests as $call) {
    check($call[0]['search'] === $exportFilters['search'] && $call[0]['criteria'] === $exportFilters['criteria'] && $call[0]['order'] === $exportFilters['order'], 'Export must preserve filters, search and order across batches');
    check($call[0]['length'] === 100 && $call[1] === false, 'No repeated count scans during export');
}

class Session {
    public $data = [];
    public function userdata($key) { return $this->data[$key] ?? null; }
}
class Loader { public $loads = 0; public function model($name, $alias) { $this->loads++; } }
$GLOBALS['session'] = new Session(); $GLOBALS['loader'] = new Loader();
foreach ([['sys_sba_username'=>'test','sys_sba_department'=>'Accounting'], ['sys_sba_username'=>'test','sys_sba_isAdm'=>true]] as $allowed) {
    $GLOBALS['session']->data = $allowed; new CashbookEntry();
}
$before = $GLOBALS['loader']->loads;
$GLOBALS['session']->data = ['sys_sba_username'=>'test','sys_sba_department'=>'Sales'];
try { new CashbookEntry(); throw new LogicException('Unauthorized role accepted'); }
catch (RuntimeException $expected) { check($expected->getCode() === 403, 'Unauthorized users must get 403'); }
check($GLOBALS['loader']->loads === $before, 'No model/data access for unauthorized users');
$GLOBALS['session']->data = [];
try { new CashbookEntry(); throw new LogicException('Anonymous user accepted'); }
catch (RuntimeException $expected) { check($expected->getMessage() === 'login', 'Anonymous access requires login'); }
echo "Cashbook query, validation, pagination, clearing account, and access tests passed.\n";
