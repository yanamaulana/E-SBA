<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Approval_Assignment extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';
    private $TmstTrxSettingSteppApprovalCbr = 'TmstTrxSettingSteppApprovalCbr';
    private $HRQview_Employee_Detail = 'HRQviewEmployeeDetail';
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
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/setting/approval_assignment.js"></script>';

        $this->data['Approvals'] = $this->db->get($this->QviewSettingStepApproval)->result();

        $this->load->view($this->layout, $this->data);
    }

    // pembuatan funcction untuk select2 employee dengan ajax, table ERPQview_User_Employee : First_Name, User_Name
    // $('#Employee').select2({
    //     theme: 'bootstrap-4',
    //     placeholder: 'Select User',
    //     ajax: {
    //         url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/Select2_User_Employee", // Ganti dengan URL endpoint Anda   
    //         dataType: 'json',
    //         delay: 800, // Delay untuk menunggu user berhenti mengetik
    //         data: function (params) {
    //             return {
    //                 search: params.term // Term yang diketik user
    //             };
    //         },
    //         processResults: function (data) {
    //             return {
    //                 results: data.map(function (item) {
    //                     return {
    //                         id: item.UserName_Employee, // Gunakan UserName_Employee sebagai id
    //                         text: item.First_Name + ' (' + item.UserName_Employee + ')' // Tampilkan First_Name dan UserName_Employee
    //                     };
    //                 })
    //             };
    //         }
    //     }
    // });
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
}
