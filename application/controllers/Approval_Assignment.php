<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval_Assignment extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';
    private $TmstTrxSettingSteppApprovalCbr = 'TmstTrxSettingSteppApprovalCbr';
    private $Ttrx_Assignment_Approval_User = 'Ttrx_Assignment_Approval_User';
    private $Tmst_User_NonHR = 'Tmst_User_NonHR';
    private $QviewSettingStepApproval = 'QviewSettingStepApproval';
    private $ERPQview_User_Employee = 'ERPQview_User_Employee';
    private $QviewTrx_Assignment_Approval_User = 'QviewTrx_Assignment_Approval_User';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->Date = date("Y-m-d");
        $this->DateTime = date("Y-m-d H:i:s");
        $this->load->model('m_helper', 'help');
        $this->load->model('m_DataTable', 'M_Datatables');
    }

    public function index()
    {
        $this->data['page_title'] = "User Step Approval Assignment";
        $this->data['page_content'] = "setting/approval_assignment";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/setting/approval_assignment.js?v=' . time() . '""></script>';

        $this->data['Approvals'] = $this->db->get($this->QviewSettingStepApproval)->result();

        $this->load->view($this->layout, $this->data);
    }

    public function user_approval_assignment()
    {
        $this->data['page_title'] = $this->session->userdata('sys_sba_nama') . ": Step Approval Assignment";
        $this->data['page_content'] = "setting/user_approval_assignment";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/setting/user_approval_assignment.js?v=' . time() . '""></script>';

        $this->data['Approvals'] = $this->db->get($this->QviewSettingStepApproval)->result();

        $this->data['mystep'] = $this->db->get_where($this->Ttrx_Assignment_Approval_User, ['UserName_Employee' => $this->session->userdata('sys_sba_username')])->row();


        $this->load->view($this->layout, $this->data);
    }


    public function store()
    {
        $Approval = $this->input->post('Approval');
        $Array_Nik_Employee = $this->input->post('Employee');

        // Cek apakah data sudah ada di database dengan looping array user nik employee
        $msg = '';
        $i = 0;
        foreach ($Array_Nik_Employee as $Employee) {
            $this->db->where('UserName_Employee', $Employee);
            $query = $this->db->get($this->QviewTrx_Assignment_Approval_User);
            if ($query->num_rows() > 0) {
                $i++;
                $Current_Assigment = $query->row();
                if ($i == 1) {
                    $msg .= 'The following employee(s) already assigned to approval step: ';
                    $msg .= $Current_Assigment->First_Name . ' => ' . $Current_Assigment->Setting_Approval_Code . ', ';
                } else {
                    $msg .= $Current_Assigment->First_Name . ' => ' . $Current_Assigment->Setting_Approval_Code . ', ';
                }
            }
        }
        if ($msg != '') {
            $response = [
                'code' => 422,
                'msg'  => $msg . ' . you need to remove them first before reassigning.',
            ];
            header('Content-Type: application/json');
            echo json_encode($response);
            return; // Hentikan eksekusi jika ada data yang sudah ada
        }


        $this->db->trans_start();
        // column tabel Ttrx_Assignment_Approval_User SysId, UserName_Employee, SysId_Approval, Created_at, Created_by;
        foreach ($Array_Nik_Employee as $Employee) {
            $this->db->insert($this->Ttrx_Assignment_Approval_User, [
                'UserName_Employee' => $Employee,
                'SysId_Approval' => $Approval,
                'Created_at' => $this->DateTime,
                'Created_by' => $this->session->userdata('sys_sba_username'),
            ]);
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
                'msg' => "The approval step status has been successfully updated!",
            ]);
        }
    }


    public function store_user()
    {
        $Approval = $this->input->post('Approval');
        $Nik_Employee = $this->input->post('Employee');

        //cek jika data sudah ada
        $Current_Assigment = $this->db->get_where($this->Ttrx_Assignment_Approval_User, [
            'UserName_Employee' => $Nik_Employee,
        ])->row();

        if ($Current_Assigment->SysId_Approval == $Approval) {
            return $this->help->Fn_resulting_response([
                'code' => 422,
                'msg'  => 'You are already assigned to this approval step.',
            ]);
        }

        $this->db->trans_start();

        $this->db->where('UserName_Employee', $Nik_Employee)->delete($this->Ttrx_Assignment_Approval_User);

        $this->db->insert($this->Ttrx_Assignment_Approval_User, [
            'UserName_Employee' => $Nik_Employee,
            'SysId_Approval' => $Approval,
            'Created_at' => $this->DateTime,
            'Created_by' => $this->session->userdata('sys_sba_username'),
        ]);

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
                'msg' => "The approval step status has been successfully updated!",
            ]);
        }
    }

    public function delete()
    {
        $Employee = $this->input->post('Employee');

        $this->db->trans_start();

        $this->db->where('UserName_Employee', $Employee)->delete($this->Ttrx_Assignment_Approval_User);

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
                'msg' => "The employee has been successfully removed from this approval step!",
            ]);
        }
    }

    public function Select2_User_Employee()
    {
        $search = $this->input->get('search');
        // buatkan query builder apabila kondisi jika tidak ada param search tambahkan dan User_Status = 1
        if (!empty($search)) {
            $this->db->select('First_Name, User_Name')
                ->from($this->ERPQview_User_Employee)
                ->group_start()
                ->where('User_Status', 1)
                ->like('First_Name', $search)
                ->or_like('User_Name', $search)
                ->group_end()
                ->limit(10);
        } else {
            $this->db->select('First_Name, User_Name')
                ->where('User_Status', 1)
                ->from($this->ERPQview_User_Employee)
                ->limit(10);
        }

        $query = $this->db->get();
        $options = [];
        foreach ($query->result() as $row) {
            $options[] = [
                'id' => $row->User_Name,
                'text' => $row->First_Name . ' (' . $row->User_Name . ')'
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($options);
    }


    // ============================================== DATATABLE SECTION
    public function DT_List_Approval_Assignment()
    {
        $query  = "SELECT * from $this->QviewTrx_Assignment_Approval_User";
        $search = ['UserName_Employee', 'First_Name'];
        $where  = ['SysId_Approval' => $this->input->post('Approval')];
        $isWhere = null;

        header('Content-Type: application/json');
        echo $this->M_Datatables->get_tables_query($query, $search, $where, $isWhere);
    }

    public function DT_List_Approval_Assignment_user()
    {
        $query  = "SELECT * from $this->QviewTrx_Assignment_Approval_User";
        $search = ['UserName_Employee', 'First_Name'];
        $where  = [
            'SysId_Approval' => $this->input->post('Approval'),
            'UserName_Employee' => $this->session->userdata('sys_sba_username')
        ];
        $isWhere = null;

        header('Content-Type: application/json');
        echo $this->M_Datatables->get_tables_query($query, $search, $where, $isWhere);
    }


    public function DT_Preview_Step_Approval()
    {
        $query  = "SELECT * from $this->QviewSettingStepApproval";
        $search = array('SysId', 'Setting_Approval_Code');
        $where  = ['SysId' => $this->input->post('Approval')];
        $isWhere = null;

        header('Content-Type: application/json');
        echo $this->M_Datatables->get_tables_query($query, $search, $where, $isWhere);
    }
}
