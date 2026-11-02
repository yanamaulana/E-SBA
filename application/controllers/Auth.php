<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    private $HR;
    private $mis = 'Management Information System';
    private $Tmst_User_NonHR = 'Tmst_User_NonHR';
    private $ERPQview_User_Employee = 'ERPQview_User_Employee';
    private $Taccess_Approval_Cbr   = 'Taccess_Approval_Cbr';
    private $HRQview_Employee_Detail = 'HRQviewEmployeeDetail';
    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_helper', 'help');
        $this->load->helper('cookie');
        $this->HR = $this->load->database('HR', TRUE);
    }

    public function index()
    {
        if ($this->session->userdata("sys_sba_username")) {
            return redirect("Dashboard");
        }
        $this->data['page_title'] = "Login";

        $this->load->view('Auth/index', $this->data);
    }


    public function post_login()
    {
        if (!$this->input->is_ajax_request()) {
            $response = [
                "code" => 404,
                "msg" => "The technical error occurred, Request denied!"
            ];
            return $this->help->Fn_resulting_response($response);
        }
        $login = $this->input->post(null, true);
        $login = (object) $login;
        $login->username = $login->u;
        $login->password = $login->p;

        if (empty($login->username) || empty($login->password)) {
            $response = [
                "code" => 404,
                "msg" => "Username & Password must be filled in!"
            ];
            return $this->help->Fn_resulting_response($response);
        }

        $user_query = $this->db->get_where($this->ERPQview_User_Employee, [
            'User_Name' => $login->username,
        ]);

        if ($user_query->num_rows() == 0) {
            return $this->help->Fn_resulting_response(["code" => 404, "msg" => "User not found !"]);
        }

        $user = $user_query->row_array();

        if ($user['User_Pass_Txt'] !== $login->password) {
            return $this->help->Fn_resulting_response(["code" => 505, "msg" => "Password not match !"]);
        }

        if ($user['is_Passive'] == 1) {
            return $this->help->Fn_resulting_response(["code" => 404, "msg" => "The user has been disabled!"]);
        }

        $sqlemployee = $this->HR->get_where($this->HRQview_Employee_Detail, ['Emp_No' => $login->username]);
        $SqlIsBod = $this->db->get_where($this->Tmst_User_NonHR, ['UserID' => $user['User_ID']]);

        $employee = null;
        $is_dir = 0;
        // Explicit ESBA profiles take precedence over the employee's HR profile.
        if ($SqlIsBod->num_rows() > 0) {
            $employee = $SqlIsBod->row_array();
            if ($employee['Division_Name'] == 'Board Of Directors') { // Use == for comparison
                $is_dir = 1;
            }
        } elseif ($sqlemployee->num_rows() > 0) {
            $employee = $sqlemployee->row_array();
        } else {
            return $this->help->Fn_resulting_response(["code" => 404, "msg" => "Your job position data was not found in the HR system. !"]);
        }

        $ValidatePayemntCheckPermission = $this->db->get_where('Tmst_User_Check_Payment_Permission', ['UserName' => $login->username]);
        $PayemntCheckPermission = $ValidatePayemntCheckPermission->num_rows() > 0;

        $is_admin = ($employee['Division_Name'] == $this->mis);

        $this->delete_cache();

        $Cbr_Depts = $this->get_arr_dept($employee['Division_Name'], $login->username);

        $jabatan = $employee['Pos_Name'];
        if ($user['User_Name'] == '30194') {
            $jabatan = 'Senior Manager';
        }

        $session_data = [
            'sys_sba_isDir'                  => $is_dir,
            'sys_sba_isAdm'                  => $is_admin,
            'sys_sba_isSalesPerson'          => $user['isSalesPerson'],
            'sys_cbr_divs'                   => $Cbr_Depts,
            'sys_sba_userid'                 => $user['User_ID'],
            'sys_sba_username'               => $user['User_Name'],
            'sys_sba_NIK'                    => $user['User_Name'],
            'sys_sba_nama'                   => $user['First_Name'],
            'sys_sba_jabatan'                => $jabatan,
            'sys_sba_email'                  => $user['Email_Address'],
            'sys_sba_department'             => $employee['Division_Name'],
            'sys_sba_PayemntCheckPermission' => $PayemntCheckPermission,
        ];

        $this->db->insert('EsbaUserLog', [
            'User_Name'  => $user['User_Name'],
            'Remote_IP'  => $this->input->ip_address(),
            'Log_Date'   => date('Y-m-d H:i:s'),
            'Log_Action' => 'Login'
        ]);

        $this->session->set_userdata($session_data);
        set_cookie(['name' => 'currencyid', 'value' => 'IDR', 'expire' => 86400 * 30, 'path' => '/', 'secure' => FALSE, 'httponly' => FALSE]);
        set_cookie(['name' => 'companyid', 'value' => '2', 'expire' => 86400 * 30, 'path' => '/', 'secure' => FALSE, 'httponly' => FALSE]);

        return $this->help->Fn_resulting_response([
            "code" => 200,
            "msg"  => "Successfully login into " . $this->config->item('init_app_name') . " !"
        ]);
    }

    // EsbaUserLog (
    //     UserLog_ID int IDENTITY(1,1) NOT NULL,
    //     User_Name int NOT NULL,
    //     Remote_IP varchar(100) COLLATE SQL_Latin1_General_CP1_CI_AS NULL,
    //     Log_Date datetime NULL,
    //     Log_Action varchar(50) COLLATE SQL_Latin1_General_CP1_CI_AS NULL
    // );

    private function get_arr_dept($div_personal, $username)
    {
        $depts = [];

        $sql_depts = $this->db->get_where($this->Taccess_Approval_Cbr, [
            'UserName' => $username
        ]);

        if ($sql_depts->num_rows() > 0) {
            foreach ($sql_depts->result() as $dpt) {
                $depts[] = $dpt->Additional_Div;
            }
        }
        $depts[] = $div_personal;
        $divisions = implode(",", $depts);
        return $divisions;
    }

    private function delete_cache()
    {
        $this->load->helper('file');

        delete_files(APPPATH . 'cache', true);
    }

    public function logout()
    {
        $this->db->insert('EsbaUserLog', [
            'User_Name' => $this->session->userdata('sys_sba_username'),
            'Remote_IP' => $this->input->ip_address(),
            'Log_Date' => date('Y-m-d H:i:s'),
            'Log_Action' => 'Logout'
        ]);

        delete_cookie('currencyid');
        $this->output->delete_cache();
        $array_items = array(
            'sys_sba_nama',
            'sys_sba_NIK',
            'sys_sba_username',
            'sys_sba_jabatan',
            'sys_sba_department',
            'sys_sba_email',
            'sys_sba_isDir',
            'sys_sba_isAdm',
            'sys_cbr_divs',
        );
        $this->session->unset_userdata($array_items);
        session_destroy();
        $this->session->set_flashdata('success', "Please log in again to access " . $this->config->item('app_name'));
        return redirect('Auth');
    }
}
