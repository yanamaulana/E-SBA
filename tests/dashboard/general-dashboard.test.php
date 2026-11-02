<?php
// Run: php tests/dashboard/general-dashboard.test.php (no database connection).
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__ . '/../../system/');
define('FCPATH', __DIR__ . '/../../');
class CI_Model { public $db, $load; public function __construct() {} }
class CI_Controller { public $input, $session, $output, $dash_help, $load, $data = []; }
function log_message($level, $message) {}
function base_url($path = '') { return 'http://example.test/ESBA/' . $path; }
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
require FCPATH . 'application/models/m_Dashboard.php';
require FCPATH . 'application/controllers/Dashboard.php';
class RowsStub {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function result_array() { return $this->rows; }
}
class DbStub {
    public $db_debug = true, $responses, $calls = [];
    public function __construct($responses) { $this->responses = $responses; }
    public function query($sql, $bindings) {
        $this->calls[] = [$sql, $bindings];
        $rows = array_shift($this->responses);
        return $rows === false ? false : new RowsStub($rows);
    }
}
$rows = [
    ['Month_Number'=>1,'Currency_ID'=>'IDR','Paid_Status'=>'NP','Document_Count'=>2,'Total_Amount'=>300],
    ['Month_Number'=>1,'Currency_ID'=>'IDR','Paid_Status'=>'FP','Document_Count'=>1,'Total_Amount'=>200],
    ['Month_Number'=>2,'Currency_ID'=>'IDR','Paid_Status'=>'HP','Document_Count'=>1,'Total_Amount'=>100],
    ['Month_Number'=>3,'Currency_ID'=>'IDR','Paid_Status'=>null,'Document_Count'=>1,'Total_Amount'=>50],
    ['Month_Number'=>3,'Currency_ID'=>'IDR','Paid_Status'=>'X','Document_Count'=>1,'Total_Amount'=>25],
    ['Month_Number'=>1,'Currency_ID'=>'USD','Paid_Status'=>'FP','Document_Count'=>8,'Total_Amount'=>9999],
];
$model = new m_Dashboard(); $model->db = new DbStub([$rows, []]);
$result = $model->get_general_dashboard('Logistic', 2026, 1, 2, 'IDR');
check($result['summary']['count'] === 4, 'Count must use selected months and currency');
check($result['summary']['amount'] === 600.0, 'Currencies must never be added together');
check($result['summary']['average'] === 150.0 && $result['summary']['paid_percentage'] === 25.0, 'Ratios are document-based');
check($result['payments'] === ['NP'=>2,'HP'=>1,'FP'=>1,'OTHER'=>0], 'Payment buckets must reconcile');
check($model->db->calls[0][1][2] === 'Logistic', 'Division must be bound in aggregate query');
check($model->db->calls[1][1] === ['2026-01-01','2026-03-01','Logistic','IDR'], 'Recent rows must obey the same period, division and currency');
check(strpos($model->db->calls[0][0], 'EXISTS') !== false, 'Header amounts must not multiply across approval rows');
$model->db = new DbStub([$rows, []]);
$result = $model->get_general_dashboard('Logistic', 2026, 3, 4, 'IDR');
check($result['monthly'][1] === ['month'=>4,'count'=>0,'amount'=>0.0], 'Empty months must be filled with zero');
check($result['payments']['NP'] === 1 && $result['payments']['OTHER'] === 1, 'Null and unknown payment states must be accounted for');
$model->db = new DbStub([[], []]);
$result = $model->get_general_dashboard('Empty', 2026, 1, 2);
check($result['summary']['average'] === 0.0 && $result['summary']['paid_percentage'] === 0.0, 'Empty data must not divide by zero');
$model->db = new DbStub([$rows]);
try { $model->get_general_dashboard('Logistic', 2026, 1, 2, 'EUR'); throw new RuntimeException('Invalid currency accepted'); }
catch (InvalidArgumentException $expected) {}
$model->db = new DbStub([false]);
try { $model->get_general_dashboard('Logistic', 2026, 1, 2); throw new LogicException('DB error was hidden'); }
catch (RuntimeException $expected) { check($model->db->db_debug === true, 'DB debug setting must be restored after failure'); }
class HrLoaderStub {
    public $hr;
    public function database($name, $return) { return $this->hr; }
}
$model->load = new HrLoaderStub();
$model->load->hr = new DbStub([[['Emp_No'=>'old-division'], ['Emp_No'=>'hr-user']]]);
$model->db = new DbStub([[
    ['User_ID'=>1,'User_Name'=>'old-division','NonHR_ID'=>1,'Division_Name'=>'Other division'],
    ['User_ID'=>2,'User_Name'=>'override','NonHR_ID'=>2,'Division_Name'=>'Logistic'],
    ['User_ID'=>3,'User_Name'=>'hr-user','NonHR_ID'=>null,'Division_Name'=>null],
], [['Status_Key'=>'void','Document_Count'=>1],['Status_Key'=>'unsubmitted','Document_Count'=>2],['Status_Key'=>'approved','Document_Count'=>3],['Status_Key'=>'in_progress','Document_Count'=>4],['Status_Key'=>'rejected','Document_Count'=>1]]]);
$annual = $model->get_general_year_status('Logistic', 2026);
check($annual['total'] === 11 && $annual['counts']['rejected'] === 1, 'Rejected documents must remain separate from in-progress');
check(strpos($model->db->calls[1][0], 'H.Created_By IN (2,3)') !== false, 'NonHR override must take precedence over HR for unsubmitted creators');
check($model->db->calls[1][1][2] === 'Logistic', 'Annual query division must be bound');
class SessionStub {
    public $data = ['sys_sba_department'=>'Logistic','sys_cbr_divs'=>'Logistic, Purchasing','sys_sba_username'=>'fixture'];
    public function userdata($key) { return $this->data[$key] ?? null; }
}
class InputStub { public $data = []; public function get($key) { return $this->data[$key] ?? null; } }
class OutputStub {
    public $status, $data;
    public function set_status_header($status) { $this->status=$status; return $this; }
    public function set_content_type(...$args) { return $this; }
    public function set_header(...$args) { return $this; }
    public function set_output($data) { $this->data=json_decode($data,true); return $this; }
}
class ModelStub {
    public $calls=[];
    public function get_general_dashboard(...$args) { $this->calls[]=$args; return ['ok'=>true]; }
    public function get_general_year_status(...$args) { $this->calls[]=$args; return ['ok'=>true]; }
    public function get_Count_Awaiting_Approvals($user) { return 1; }
    public function get_Count_After_Approvals($user,$status) { return 2; }
    public function get_Amount_Approved($user) { return ['IDR'=>100]; }
    public function get_Division_Summary(...$args) { return ['currencies'=>['IDR'],'divisions'=>[]]; }
}
class ViewLoaderStub { public $view; public function view($name,$data) { $this->view=$data['page_content']; } }
function controller($input) {
    $reflection = new ReflectionClass(Dashboard::class); $controller=$reflection->newInstanceWithoutConstructor();
    foreach (['year'=>'2026','month'=>'11'] as $key=>$value) { $property=$reflection->getProperty($key); $property->setAccessible(true); $property->setValue($controller,$value); }
    $controller->session=new SessionStub; $controller->input=new InputStub; $controller->input->data=$input;
    $controller->output=new OutputStub; $controller->dash_help=new ModelStub; $controller->load=new ViewLoaderStub;
    return $controller;
}
foreach ([['division'=>'Unauthorized'], ['division'=>['Logistic']]] as $input) {
    $c=controller($input); $c->general_data(); check($c->output->status === 403 && !$c->dash_help->calls, 'Unauthorized division must never reach the model');
    $c->general_year_data(); check($c->output->status === 403 && !$c->dash_help->calls, 'Annual endpoint must enforce the same scope');
}
foreach ([['from_month'=>0],['from_month'=>4,'to_month'=>3],['to_month'=>12],['from_month'=>['1']],['currency'=>['IDR']]] as $input) {
    $c=controller($input); $c->general_data(); check($c->output->status === 422 && !$c->dash_help->calls, 'Invalid filter must be rejected');
}
$c=controller(['division'=>'Purchasing','from_month'=>2,'to_month'=>4,'currency'=>'USD','year'=>1999]); $c->general_data();
check($c->dash_help->calls[0] === ['Purchasing',2026,2,4,'USD'], 'Only authorized divisions and the current year are allowed');
$c=controller([]); $c->index(); check($c->load->view === 'Dashboard/index', 'General users need the new dashboard');
check($c->data['dashboard_divisions'] === ['Logistic','Purchasing'], 'Division choices must derive from the session');
$c=controller([]); $c->session->data['sys_sba_jabatan']='President Director'; $c->index();
check($c->load->view === 'Dashboard/index_presdir' && $c->data['Awaiting_Approvals'] === 1, 'Presdir dashboard and metrics must remain available');
echo "PASS: metrics, currency isolation, month ranges, NonHR mapping, empty/error states, access control and Presdir routing.\n";
