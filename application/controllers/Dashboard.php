<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{
    private $Day;
    private $Date;
    private $year;
    private $month;
    private $layout = 'layout';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->Day = date("l");
        $this->Date = date("Y-m-d");
        $this->year = date("Y");
        $this->month = date("n");
        $this->load->model('m_helper', 'help');
        $this->load->model('m_Dashboard', 'dash_help');
    }

    public function index()
    {
        $this->data['page_title'] = "Dashboard";
        $username = $this->session->userdata('sys_sba_username');

        if ($this->session->userdata('sys_sba_jabatan') == 'President Director') {
            $this->data['page_content'] = "Dashboard/index_presdir";
            $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/Dashboard/index.js?v=' . time() . '""></script>';
            $this->data['Awaiting_Approvals'] = $this->dash_help->get_Count_Awaiting_Approvals($username);
            $this->data['Approved'] = $this->dash_help->get_Count_After_Approvals($username, 1);
            $this->data['Rejected'] = $this->dash_help->get_Count_After_Approvals($username, 2);
            $this->data['Amount_Approved'] = $this->dash_help->get_Amount_Approved($username);
            $table_data = $this->dash_help->get_Division_Summary(0, $username, $this->year, $this->month);
            $this->data['currencies'] = $table_data['currencies'];
            $this->data['divisions'] = $table_data['divisions'];
        } else {
            $this->data['page_content'] = "Dashboard/index";
            $this->data['script_page'] = '<script src="' . base_url('assets/Pages/Dashboard/general.js') . '?v=' . filemtime(FCPATH . 'assets/Pages/Dashboard/general.js') . '"></script>';
            $this->data['dashboard_divisions'] = $this->general_divisions();
            $this->data['dashboard_year'] = (int) $this->year;
            $this->data['dashboard_month'] = (int) $this->month;
        }

        $this->data['date'] = date('Y-m-d');
        $this->load->view($this->layout, $this->data);
    }

    private function general_divisions()
    {
        $divisions = array_merge(
            [(string) $this->session->userdata('sys_sba_department')],
            explode(',', (string) $this->session->userdata('sys_cbr_divs'))
        );
        return array_values(array_unique(array_filter(array_map('trim', $divisions), function ($division) {
            return $division !== '';
        })));
    }

    public function general_data()
    {
        $divisions = $this->general_divisions();
        $division = $this->input->get('division');
        if ($division === null || $division === '') {
            $division = $divisions[0] ?? '';
        }
        // The browser may only select divisions already granted by the login session.
        if (!is_string($division) || !in_array($division, $divisions, true)) {
            return $this->general_json(['message' => 'Divisi tidak tersedia untuk akun ini.'], 403);
        }
        $from = $this->input->get('from_month') ?? 1;
        $to = $this->input->get('to_month') ?? (int) $this->month;
        if (!is_scalar($from) || !is_scalar($to) ||
            filter_var($from, FILTER_VALIDATE_INT) === false ||
            filter_var($to, FILTER_VALIDATE_INT) === false ||
            (int) $from < 1 || (int) $to > (int) $this->month || (int) $from > (int) $to) {
            return $this->general_json(['message' => 'Pilih rentang Januari sampai bulan berjalan.'], 422);
        }
        $currency = $this->input->get('currency');
        if ($currency !== null && (!is_string($currency) || strlen($currency) > 12)) {
            return $this->general_json(['message' => 'Pilihan mata uang tidak valid.'], 422);
        }
        try {
            $data = $this->dash_help->get_general_dashboard($division, (int) $this->year, (int) $from, (int) $to, $currency);
            return $this->general_json($data);
        } catch (InvalidArgumentException $error) {
            return $this->general_json(['message' => $error->getMessage()], 422);
        } catch (RuntimeException $error) {
            log_message('error', 'General dashboard: ' . $error->getMessage());
            return $this->general_json(['message' => 'Data dashboard belum dapat dimuat. Silakan coba lagi.'], 500);
        }
    }

    public function general_year_data()
    {
        $divisions = $this->general_divisions();
        $division = $this->input->get('division') ?? ($divisions[0] ?? '');
        if (!is_string($division) || !in_array($division, $divisions, true)) {
            return $this->general_json(['message' => 'Divisi tidak tersedia untuk akun ini.'], 403);
        }
        try {
            return $this->general_json($this->dash_help->get_general_year_status($division, (int) $this->year));
        } catch (RuntimeException $error) {
            log_message('error', 'Annual division dashboard: ' . $error->getMessage());
            return $this->general_json(['message' => 'Ringkasan tahunan belum dapat dimuat. Silakan coba lagi.'], 500);
        }
    }

    private function general_json($data, $status = 200)
    {
        return $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_header('Cache-Control: no-store')
            ->set_output(json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE));
    }

    public function get_history_data()
    {
        $username = $this->session->userdata('sys_sba_username');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $table_data = $this->dash_help->get_Division_Summary(1, $username, $year, $month);
        $currencies = $table_data['currencies'];
        $divisions  = $table_data['divisions'];

        echo json_encode([
            'currencies' => $currencies,
            'divisions' => $divisions
        ]);
    }

    public function Access_log()
    {
        $this->data['page_title'] = "Access Log";
        $this->data['page_content'] = "Dashboard/Access_log";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/Dashboard/Access_log.js"></script>';
        $this->data['date'] = date('Y-m-d');

        $this->load->view($this->layout, $this->data);
    }

    public function DT_list_Access_Log()
    {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'UserLog_ID',
            1 => 'User_Name',
            2 => 'First_Name',
            3 => 'Remote_IP',
            4 => 'Log_Date',
            5 => 'Log_Action'
        );
        $order  = $columns[$requestData['order']['0']['column']];
        $dir    = $requestData['order']['0']['dir'];
        $from   = $this->input->post('from');
        $until  = $this->input->post('until');

        $sql = "SELECT Eslog.UserLog_ID, TUserPersonal.First_Name, Eslog.User_Name, Eslog.Remote_IP, Eslog.Log_Date, Eslog.Log_Action
            FROM EsbaUserLog Eslog 
            JOIN tuser on Eslog.User_Name = tuser.User_Name
            JOIN TUserPersonal on tuser.User_ID = TUserPersonal.User_ID
            WHERE Eslog.User_Name = '" . $this->session->userdata('sys_sba_username') . "'
            AND Eslog.Log_Date >= {ts '$from 00:00:00'}
            AND Eslog.Log_Date <= {ts '$until 23:59:59'} ";

        $totalData = $this->db->query($sql)->num_rows();
        if (!empty($requestData['search']['value'])) {
            $sql .= " AND (Remote_IP LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Log_Date LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Log_Action LIKE '%" . $requestData['search']['value'] . "%') ";
        }
        //----------------------------------------------------------------------------------
        $totalFiltered = $this->db->query($sql)->num_rows();
        $sql .= " ORDER BY $order $dir OFFSET " . $requestData['start'] . " ROWS FETCH NEXT " . $requestData['length'] . " ROWS ONLY ";
        $query = $this->db->query($sql);
        $data = array();
        foreach ($query->result_array() as $row) {
            $nestedData = array();
            $nestedData['UserLog_ID'] = $row['UserLog_ID'];
            $nestedData['User_Name'] = $row['User_Name'];
            $nestedData['First_Name'] = $row['First_Name'];
            $nestedData['Remote_IP'] = $row['Remote_IP'];
            $nestedData['Log_Date'] = $row['Log_Date'];
            $nestedData['Log_Action'] = $row['Log_Action'];

            $data[] = $nestedData;
        }
        //----------------------------------------------------------------------------------
        $json_data = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        //----------------------------------------------------------------------------------
        echo json_encode($json_data);
    }
}
