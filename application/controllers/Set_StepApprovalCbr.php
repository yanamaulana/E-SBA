<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Set_StepApprovalCbr extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';
    private $TmstTrxSettingSteppApprovalCbr = 'TmstTrxSettingSteppApprovalCbr';
    private $HRQview_Employee_Detail = 'HRQviewEmployeeDetail';
    private $Tmst_User_NonHR = 'Tmst_User_NonHR';
    private $ERPQview_User_Employee = 'ERPQview_User_Employee';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->HR = $this->load->database('HR', TRUE);
        $this->Date = date("Y-m-d");
        $this->DateTime = date("Y-m-d H:i:s");
        $this->load->model('m_helper', 'help');
        $this->load->model('m_DataTable', 'M_Datatables');
    }

    public function index()
    {
        $this->data['page_title'] = "Setting Approval Step Cash Book Requisition";
        $this->data['page_content'] = "setting/step_approval_cbr";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/setting/step_approval_cbr.js"></script>';
        // $this->data['approvals'] = $this->db->get_where($this->TmstTrxSettingSteppApprovalCbr, ['UserName_User' => $this->session->userdata('sys_sba_username')]);

        $this->load->view($this->layout, $this->data);
    }

    public function ValidatePerson()
    {
        $NIK = $this->input->post('person');
        $is_acc = $this->input->post('is_acc');
        $is_dir = $this->input->post('is_dir');

        $employeeParemeters = [];

        if ($is_acc == 1 && $is_dir == 0) {
            $employeeParemeters = [
                'Emp_No' => $NIK,
                'Division_Name' => 'Accounting'
            ];
        } elseif ($is_acc == 0 && $is_dir == 1) {
            $employeeParemeters = [
                'Emp_No' => $NIK,
                'Division_Name' => 'Board Of Directors'
            ];
        } elseif ($is_acc == 1 && $is_dir == 1) {
            $employeeParemeters = [
                'Emp_No' => $NIK,
                'Pos_Name' => 'Finance Director'
            ];
        } else {
            // Menangani semua kasus lainnya, termasuk $is_acc=0 dan $is_dir=0
            $employeeParemeters = [
                'Emp_No' => $NIK,
            ];
        }

        $name = "";
        $div = "";
        $position = "";

        $sqlemployee = $this->HR->get_where($this->HRQview_Employee_Detail, $employeeParemeters);
        $SqlIsBod = $this->db->get_where($this->Tmst_User_NonHR, $employeeParemeters);

        if ($sqlemployee->num_rows() > 0) {
            $employee = $sqlemployee->row_array();
        } elseif ($SqlIsBod->num_rows() > 0) {
            $employee = $SqlIsBod->row_array();
        } else {
            $response = [
                "code" => 404,
                "msg" => "this username/NIK not found on HR system !",
                'valid' => 0
            ];
            return $this->help->Fn_resulting_response($response);
        }

        $name = $employee['First_Name'];
        $div = $employee['Division_Name'];
        $position = $employee['Pos_Name'];


        if ($name != "" && $div != "" && $position != "") {
            return $this->help->Fn_resulting_response([
                'code' => 200,
                'msg'  => "Data found",
                'name' => $name,
                'div'  => $div,
                'position' => $position,
                'valid' => 1
            ]);
        } else {
            return $this->help->Fn_resulting_response([
                'code' => 404,
                'msg'  => "Data not found, Please enter a valid Sunfish ERP NIK/username",
                'valid' => 0
            ]);
        }
    }

    public function store()
    {
        $data = [
            'Staff' => $this->input->post('Staff'),
            'Chief' => $this->input->post('Chief'),
            'AsstManager' => $this->input->post('AsstManager'),
            'Manager' => $this->input->post('Manager'),
            'SeniorManager' => $this->input->post('SeniorManager'),
            'GeneralManager' => $this->input->post('GeneralManager'),
            'Director' => $this->input->post('Director'),
            'UserName_User' => $this->session->userdata('sys_sba_username'),
            'LastUpdated_at' => $this->DateTime
        ];
        $haveData = $this->db->get_where($this->TmstTrxSettingSteppApprovalCbr, ['UserName_User' => $this->session->userdata('sys_sba_username')]);
        $this->db->trans_start();

        if ($haveData->num_rows() == 0) {
            $this->db->insert($this->TmstTrxSettingSteppApprovalCbr, $data);
        } else {
            $this->db->where('UserName_User', $this->session->userdata('sys_sba_username'))->update($this->TmstTrxSettingSteppApprovalCbr, $data);
        }

        $error_msg = $this->db->error()["message"];
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->help->Fn_resulting_response([
                'code' => 505,
                'msg'  => $error_msg,
            ]);
        } else {
            $this->db->trans_commit();
            return $this->help->Fn_resulting_response([
                'code' => 200,
                'msg' => "The approval step has been successfully saved!",
            ]);
        }
    }
}
