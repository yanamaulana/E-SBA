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

        $this->data['dir_data'] = $this->db->get_where('Tmst_User_NonHR', ['is_active' => 1])->result();

        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/setting/step_approval_cbr.js"></script>';
        // $this->data['approvals'] = $this->db->get_where($this->TmstTrxSettingSteppApprovalCbr, ['UserName_User' => $this->session->userdata('sys_sba_username')]);

        $this->load->view($this->layout, $this->data);
    }

    public function ValidatePerson()
    {
        $NIK = $this->input->post('person');
        $is_acc = $this->input->post('is_acc');
        $is_dir = $this->input->post('is_dir');
        $pos_must = $this->input->post('pos_must');

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
            if ($pos_must == '') {
                // tidak melakukan apa-apa jika $pos_must kosong, dan lanjut ke validasi berikutnya
            } else if ($employee['Pos_Name'] != $pos_must) {
                return $this->help->Fn_resulting_response([
                    'code' => 404,
                    'msg'  => "Position must be {$pos_must}, but the entered NIK/username has position as {$employee['Pos_Name']}",
                    'valid' => 0
                ]);
            }
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
        // "SELECT SysId, Setting_Approval_Code, Staff, Staff_Person, Chief, Chief_Person, AsstManager, AsstManager_Person, Manager, Manager_Person, SeniorManager, SeniorManager_Person, GeneralManager, GeneralManager_Person, Director, Director_Person, FinanceManager, FinanceManager_Person, PresidentDirector, PresidentDirector_Person, FinanceDirector, FinanceDirector_Person, LastUpdated_at, Doc_Legitimate_Pos_On FROM TmstTrxSettingSteppApprovalCbr;"

        $Setting_Approval_Code = $this->input->post('Setting_Approval_Code');

        $Chief = $this->input->post('Chief');
        $Chief_Person = $this->input->post('Chief_person');
        $Chief_Valid = $this->input->post('Chief_valid');
        // =============================================
        $AsstManager = $this->input->post('AsstManager');
        $AsstManager_Person = $this->input->post('AsstManager_person');
        $AsstManager_Valid = $this->input->post('AsstManager_valid');
        // =============================================
        $Manager = $this->input->post('Manager');
        $Manager_Person = $this->input->post('Manager_person');
        $Manager_Valid = $this->input->post('Manager_valid');
        // =============================================
        $SeniorManager = $this->input->post('SeniorManager');
        $SeniorManager_Person = $this->input->post('SeniorManager_person');
        $SeniorManager_Valid = $this->input->post('SeniorManager_valid');
        // =============================================
        $GeneralManager = $this->input->post('GeneralManager');
        $GeneralManager_Person = $this->input->post('GeneralManager_person');
        $GeneralManager_Valid = $this->input->post('GeneralManager_valid');
        // =============================================
        $Additional = $this->input->post('Additional');
        $Additional_Person = $this->input->post('Additional_person');
        $Additional_Valid = $this->input->post('Additional_valid');
        // =============================================
        $Director = $this->input->post('Director');
        $Director_Person = $this->input->post('Director_person');
        $Director_Valid = $this->input->post('Director_valid');
        // =============================================
        $PresidentDirector = $this->input->post('PresidentDirector');
        $PresidentDirector_Person = $this->input->post('PresidentDirector_person');
        $PresidentDirector_Valid = $this->input->post('PresidentDirector_valid');
        // =============================================
        $FinanceDirector = $this->input->post('FinanceDirector');
        $FinanceDirector_Person = $this->input->post('FinanceDirector_person');
        $FinanceDirector_Valid = $this->input->post('PresidentDirector_valid');


        $errors = [];
        // Fungsi bantu untuk validasi
        $this->validate_approval_to_insert($Chief, $Chief_Valid, "Chief", $errors);
        $this->validate_approval_to_insert($AsstManager, $AsstManager_Valid, "Asst Manager", $errors);
        $this->validate_approval_to_insert($Manager, $Manager_Valid, "Manager", $errors);
        $this->validate_approval_to_insert($SeniorManager, $SeniorManager_Valid, "Senior Manager", $errors);
        $this->validate_approval_to_insert($GeneralManager, $GeneralManager_Valid, "General Manager", $errors);
        $this->validate_approval_to_insert($Director, $Director_Valid, "Director", $errors);
        $this->validate_approval_to_insert($PresidentDirector, $PresidentDirector_Valid, "President Director", $errors);
        $this->validate_approval_to_insert($FinanceDirector, $FinanceDirector_Valid, "Finance Director", $errors);

        if (!empty($errors)) {
            // Jika ada error validasi
            $response = [
                "code" => 400,
                "msg" => "Validasi data gagal. Silakan periksa detailnya.",
                "details" => $errors
            ];
            // Kirim response error
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($response));
        }

        $Doc_Legitimate_Pos_On = null;
        $error_message = null;

        // 1. Kasus $FinanceDirector = 0 & $PresidentDirector = 1
        if ($FinanceDirector == 0 && $PresidentDirector == 1) {
            $Doc_Legitimate_Pos_On = 'PresidentDirector';

            // 2. Kasus Sebaliknya ($FinanceDirector = 1 & $PresidentDirector = 0)
        } elseif ($FinanceDirector == 1 && $PresidentDirector == 0) {
            $Doc_Legitimate_Pos_On = 'FinanceDirector';

            // 3. Kasus Sama-sama 1 (Mengutamakan FinanceDirector)
        } elseif ($FinanceDirector == 1 && $PresidentDirector == 1) {
            $Doc_Legitimate_Pos_On = 'FinanceDirector';

            // 4. Kasus Keduanya 0
        } elseif ($FinanceDirector == 0 && $PresidentDirector == 0) {
            $error_message = "Persetujuan Akhir (President Director atau Finance Director) harus dipilih salah satu atau keduanya.";

            // Kasus tidak terduga (jika ada nilai selain 0 atau 1)
        } else {
            $error_message = "Nilai Director tidak dipilih.";
        }

        // --- Penanganan Error ---

        if ($error_message !== null) {
            // Kembalikan pesan error sesuai format JSON AJAX CodeIgniter
            $response = [
                "code" => 400, // Bad Request
                "msg" => "Validasi Gagal.",
                "details" => [$error_message]
            ];

            // Menggunakan output CodeIgniter untuk mengirim respons JSON
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($response));
        }

        // Jika lolos, $Doc_Legitimate_Pos_On sudah siap untuk dimasukkan ke database
        // Contoh: $data_insert['Doc_Legitimate_Pos_On'] = $Doc_Legitimate_Pos_On;


        // Asumsi $Doc_Legitimate_Pos_On sudah dihitung dan didefinisikan

        $data_insert = array(
            'Setting_Approval_Code' => $this->input->post('Setting_Approval_Code'), // Tetap dari post, karena tidak ada variabel lokal untuk ini
            'LastUpdated_at' => date('Y-m-d H:i:s'),
            'LastUpdated_by' => $this->session->userdata('sys_sba_username'),

            // Kolom Chief
            'Chief' => $Chief,
            'Chief_Person' => $Chief_Person,

            // Kolom AsstManager
            'AsstManager' => $AsstManager,
            'AsstManager_Person' => $AsstManager_Person,

            // Kolom Manager
            'Manager' => $Manager,
            'Manager_Person' => $Manager_Person,

            // Kolom SeniorManager
            'SeniorManager' => $SeniorManager,
            'SeniorManager_Person' => $SeniorManager_Person,

            // Kolom GeneralManager
            'GeneralManager' => $GeneralManager,
            'GeneralManager_Person' => $GeneralManager_Person,

            // Kolom Additional (Saya menyertakan ini karena ada di daftar variable Anda)
            'Additional' => $Additional,
            'Additional_Person' => $Additional_Person,

            // Kolom Director
            'Director' => $Director,
            'Director_Person' => $Director_Person,

            // Kolom PresidentDirector
            'PresidentDirector' => $PresidentDirector,
            'PresidentDirector_Person' => $PresidentDirector_Person,

            // Kolom FinanceDirector
            'FinanceDirector' => $FinanceDirector,
            'FinanceDirector_Person' => $FinanceDirector_Person,

            // Kolom yang nilainya dihitung berdasarkan logika Anda
            'Doc_Legitimate_Pos_On' => $Doc_Legitimate_Pos_On,
        );

        $this->db->trans_start();

        $this->db->insert($this->TmstTrxSettingSteppApprovalCbr, $data_insert);

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

    private function validate_approval_to_insert($jabatan_flag, $jabatan_valid, $jabatan_name, &$errors)
    {
        // Cek jika flag jabatan diaktifkan (misalnya nilai radio button adalah '1')
        if ($jabatan_flag == 1 || $jabatan_flag == '1') {
            // Maka, validasi person-nya HARUS 1
            if ($jabatan_valid != 1 || $jabatan_valid != '1') {
                $errors[] = "Persetujuan **{$jabatan_name}** diaktifkan, namun validasi PERSON belum dilakukan atau tidak sesuai dengan kolom jabatan !.";
            }
        }
    }


    // ============================================== DATATABLE SECTION

    public function DT_List_Template()
    {
        $query  = "SELECT * from TmstTrxSettingSteppApprovalCbr";
        $search = array(
            'Setting_Approval_Code',
            'Staff',
            'Staff_Person',
            'Chief',
            'Chief_Person',
            'AsstManager',
            'AsstManager_Person',
            'Manager',
            'Manager_Person',
            'SeniorManager',
            'SeniorManager_Person',
            'GeneralManager',
            'GeneralManager_Person',
            'Additional',
            'Additional_Person',
            'Director',
            'Director_Person',
            'PresidentDirector',
            'PresidentDirector_Person',
            'FinanceDirector',
            'FinanceDirector_Person',
            'LastUpdated_at',
            'Doc_Legitimate_Pos_On'
        );
        $where  = null;
        $isWhere = null;

        header('Content-Type: application/json');
        echo $this->M_Datatables->get_tables_query($query, $search, $where, $isWhere);
    }
}
