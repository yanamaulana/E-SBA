<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GuitarBalanceReport extends CI_Controller
{
    private $guitarbalanceworkbook;

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
        // Standalone application library; no CI_ class prefix is required.
        $libraryPath = APPPATH . 'libraries/GuitarBalanceWorkbook.php';
        if (!is_file($libraryPath) || !is_readable($libraryPath)) {
            show_error('GuitarBalanceWorkbook.php is missing or unreadable in application/libraries.', 500);
            return;
        }
        require_once $libraryPath;
        if (!class_exists('GuitarBalanceWorkbook', false)) {
            show_error('GuitarBalanceWorkbook.php must define class GuitarBalanceWorkbook.', 500);
            return;
        }
        $this->guitarbalanceworkbook = new GuitarBalanceWorkbook();
    }

    public function index()
    {
        $this->output->set_header('Cache-Control: private, no-store');
        $action = $this->input->get('action');
        if ($action === null) {
            $this->renderReport([
                'from_year' => (int) date('Y'), 'from_month' => 1,
                'to_year' => (int) date('Y'), 'to_month' => (int) date('n'),
                'rows' => null, 'error' => null, 'generated' => null,
                'zipAvailable' => class_exists('ZipArchive')
            ]);
            return;
        }
        if (!in_array($action, ['preview', 'xlsx', 'csv'], true)) {
            show_error('Invalid report action.', 400);
            return;
        }
        $filters = [];
        foreach (['from_year', 'from_month', 'to_year', 'to_month'] as $key) {
            $value = $this->input->get($key);
            if (!is_string($value) || !preg_match('/^[0-9]{1,4}$/D', $value)) {
                show_error('Please select a valid month and year.', 400); return;
            }
            $filters[$key] = (int) $value;
        }
        extract($filters, EXTR_SKIP);
        $count = ($to_year - $from_year) * 12 + $to_month - $from_month + 1;
        if ($from_year < 2000 || $to_year > 2100 || $from_year > 2100 || $to_year < 2000 ||
            $from_month < 1 || $from_month > 12 || $to_month < 1 || $to_month > 12 ||
            $count < 1 || $count > 120) {
            show_error('Select a chronological period of 1 to 120 months, between 2000 and 2100.', 400); return;
        }
        if ($action === 'xlsx' && !class_exists('ZipArchive')) {
            show_error('PHP ZipArchive is unavailable. Enable the PHP zip extension in Herd.', 503); return;
        }
        $from = sprintf('%04d%02d01', $from_year, $from_month);
        $to = sprintf('%04d%02d01', $to_year, $to_month);
        // Preserve the user's current local SQL filename.
        $sql = file_get_contents(FCPATH . 'Database/GuitarBalanceReport.sql');
        if ($sql === false) { show_error('Report SQL file is missing.', 500); return; }
        $query = $this->db->query($sql, [$from, $to]);
        if ($query === false) { show_error('Unable to generate the report.', 500); return; }
        $months = $query->result_array();
        if (count($months) !== $count) { show_error('Unexpected number of report months.', 500); return; }
        $rows = $this->guitarbalanceworkbook->makeRows($months);
        $generated = date('Y-m-d H:i:s');
        $period = sprintf('%04d-%02d to %04d-%02d', $from_year, $from_month, $to_year, $to_month);
        $filename = 'Guitar_Order_Balance_' . substr($from,0,6) . '_' . substr($to,0,6);
        // Legacy CSV remains unchanged (main report only).
        if ($action === 'csv') {
            $bytes = $this->guitarbalanceworkbook->csv($rows, $period, $generated);
            $this->output->enable_profiler(false);
            $this->output->set_content_type('text/csv', 'utf-8');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            $this->output->set_output($bytes);
            return;
        }

        // Both preview and XLSX now use the same buyer query and selected period.
        $buyerRows = [];
        $buyerError = null;
        $buyerSqlPath = FCPATH . 'Database/GuitarBuyerSummary.sql';
        $buyerSql = is_readable($buyerSqlPath) ? file_get_contents($buyerSqlPath) : false;
        if ($buyerSql === false) {
            $buyerError = 'Buyer summary SQL file is missing or unreadable.';
        } else {
            $dbDebug = $this->db->db_debug;
            $this->db->db_debug = false;
            try {
                $buyerQuery = $this->db->query($buyerSql, [$from, $to]);
                if ($buyerQuery === false) {
                    $buyerError = 'Unable to generate the buyer summary.';
                } else {
                    $buyerRows = $buyerQuery->result_array();
                }
            } catch (\Throwable $e) {
                $buyerError = 'Unable to generate the buyer summary.';
            } finally {
                $this->db->db_debug = $dbDebug;
            }
        }

        // Load Guitar Production Orders for both browser preview and Excel export.
        if ($action === 'xlsx' && $buyerError !== null) { show_error($buyerError, 500); return; }
        $productionOrderRows = [];
        $productionOrderError = null;

        $productionOrderSqlPath = FCPATH . 'Database/GuitarProductionOrders.sql';

        $productionOrderSql = is_readable($productionOrderSqlPath)
            ? file_get_contents($productionOrderSqlPath)
            : false;

        if ($productionOrderSql === false) {
            $productionOrderError = 'Production orders SQL file is missing or unreadable.';
        } else {
            $dbDebug = $this->db->db_debug;
            $this->db->db_debug = false;

            try {
                $productionOrderQuery = $this->db->query(
                    $productionOrderSql,
                    [$from, $to]
                );

                if ($productionOrderQuery === false) {
                    $productionOrderError = 'Unable to generate production orders.';
                } else {
                    $productionOrderRows = $productionOrderQuery->result_array();
                }
            } catch (\Throwable $e) {
                $productionOrderError = 'Unable to generate production orders.';

                log_message(
                    'error',
                    'Guitar production orders failed: ' . $e->getMessage()
                );
            } finally {
                $this->db->db_debug = $dbDebug;
            }
        }

        if ($action === 'xlsx') {
            if ($productionOrderError !== null) { show_error($productionOrderError, 500); return; }
            $excelPath = APPPATH . 'libraries/GuitarBalanceExcel.php';
            if (!is_readable($excelPath)) {
                show_error('GuitarBalanceExcel.php is missing or unreadable in application/libraries.', 500); return;
            }
            try {
                require_once $excelPath;
                $excel = new GuitarBalanceExcel();
                $bytes = $excel->xlsx($months, $buyerRows, $filters, $period, $generated, $productionOrderRows);
            } catch (\Throwable $e) {
                log_message('error', 'Guitar balance XLSX export failed: ' . $e->getMessage());
                // Only show known, non-sensitive reconciliation messages to users.
                $message = 'Unable to generate the complete Excel workbook. Check the report data and application log.';
                if ($e instanceof RuntimeException && (
                    strpos($e->getMessage(), 'Buyer Order totals do not match the main report for: ') === 0 ||
                    strpos($e->getMessage(), 'Production Order totals do not match the main report for: ') === 0
                )) {
                    $message = html_escape($e->getMessage());
                }
                show_error($message, 500); return;
            }
            $this->output->enable_profiler(false);
            $this->output->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '');
            $this->output->set_header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
            $this->output->set_output($bytes);
            return;
        }

        // Preview remains a standalone document opened in a separate browser tab.
        $this->load->view('Report/Sales/guitar_balance_preview', $filters + [
            'rows' => $rows,
            'generated' => $generated,
            'buyer_rows' => $buyerRows,
            'buyer_error' => $buyerError,
            'balance_months' => $months,
            'productionOrderRows' => $productionOrderRows,
            'productionOrderError' => $productionOrderError
        ]);
    }

    private function renderReport(array $data)
    {
        $data['page_title'] = 'Guitar Order Balance Summary';
        $data['page_content'] = 'Report/Sales/guitar_balance_report';
        $data['script_page'] = '';
        $this->load->view('layout', $data);
    }
}
