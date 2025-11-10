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

        // Data yang akan digunakan untuk INSERT/UPDATE
        $audit_data = [
            'SysId_Approval' => $Approval,
            'Created_at' => $this->DateTime,
            'Created_by' => $this->session->userdata('sys_sba_username'),
        ];

        $insert_count = 0;
        $update_count = 0;
        $msg_details = [];

        $this->db->trans_start();

        // Loop melalui setiap employee yang dipilih
        foreach ($Array_Nik_Employee as $Employee) {

            // 1. Cek apakah employee sudah memiliki assignment
            $Current_Assignment = $this->db->get_where($this->Ttrx_Assignment_Approval_User, [
                'UserName_Employee' => $Employee,
            ])->row();

            if ($Current_Assignment) {
                // A. UPDATE: Jika data sudah ada

                // Cek apakah assignmentnya sama (Jika sama, tidak perlu update)
                if ($Current_Assignment->SysId_Approval == $Approval) {
                    // Catat sebagai updated, tapi lewati query UPDATE
                    $update_count++;
                    $msg_details[] = $Employee . ' (skipped: same assignment)';
                    continue;
                }

                // Lakukan UPDATE
                $this->db->where('UserName_Employee', $Employee)
                    ->update($this->Ttrx_Assignment_Approval_User, $audit_data);

                $update_count++;
                $msg_details[] = $Employee . ' (updated)';
            } else {
                // B. INSERT: Jika data belum ada

                $insert_data = $audit_data;
                $insert_data['UserName_Employee'] = $Employee;

                $this->db->insert($this->Ttrx_Assignment_Approval_User, $insert_data);

                $insert_count++;
                $msg_details[] = $Employee . ' (inserted)';
            }
        }

        $error_msg = $this->db->error()["message"];
        $this->db->trans_complete();

        // Final Response Handling
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->help->Fn_resulting_response([
                'code' => 505,
                'msg'  => $error_msg,
            ]);
        } else {
            $final_msg = sprintf(
                "Assignment successful: %d records inserted, %d records updated. (%s)",
                $insert_count,
                $update_count,
                implode(', ', $msg_details)
            );

            $this->db->trans_commit();
            return $this->help->Fn_resulting_response([
                'code' => 200,
                'msg' => $final_msg,
            ]);
        }
    }


    public function store_user()
    {
        $Approval = $this->input->post('Approval');
        $Nik_Employee = $this->input->post('Employee');

        // Cek jika data sudah ada
        $Current_Assigment = $this->db->get_where($this->Ttrx_Assignment_Approval_User, [
            'UserName_Employee' => $Nik_Employee,
        ])->row();

        // 1. Cek Duplikasi (Jika sudah ada dan SysId_Approval sama)
        if ($Current_Assigment != NULL && $Current_Assigment->SysId_Approval == $Approval) {
            return $this->help->Fn_resulting_response([
                'code' => 422,
                'msg'  => 'You are already assigned to this approval step.',
            ]);
        }

        // Data yang akan digunakan untuk INSERT atau UPDATE
        $data_assignment = [
            'SysId_Approval' => $Approval,
            'Created_at' => $this->DateTime,
            'Created_by' => $this->session->userdata('sys_sba_username'),
        ];

        $this->db->trans_start();

        if ($Current_Assigment) {
            // 2. JIKA DATA SUDAH ADA (UPDATE)
            // Kita hanya update SysId_Approval dan timestamp, tidak perlu update UserName_Employee
            $update = $this->db->where('UserName_Employee', $Nik_Employee)
                ->update($this->Ttrx_Assignment_Approval_User, $data_assignment);

            $msg = "The approval step status has been successfully updated!";
        } else {
            // 3. JIKA DATA BELUM ADA (INSERT)
            $data_assignment['UserName_Employee'] = $Nik_Employee; // Tambahkan NIK hanya saat INSERT
            $insert = $this->db->insert($this->Ttrx_Assignment_Approval_User, $data_assignment);

            $msg = "The approval step status has been successfully created!";
        }

        $error_msg = $this->db->error()["message"];
        $this->db->trans_complete();

        // Perbaikan: Pastikan transaksi gagal jika status FALSE
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return $this->help->Fn_resulting_response([
                'code' => 505,
                'msg'  => $error_msg,
            ]);
        }

        // Transaksi berhasil
        $this->db->trans_commit();
        return $this->help->Fn_resulting_response([
            'code' => 200,
            'msg' => $msg,
        ]);
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
