<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MIS extends CI_Controller
{
    private $layout = 'layout';
    private $erp_access_user_group_ids = [27, 31, 34, 44, 46, 49, 66, 72, 87, 104, 111];
    private $erp_access_function_ids = ['ERSTD07854', 'ERSTD08128', 'ERSTD07148', 'ERSTD07142'];

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        // Load m_DataTable model
        $this->load->model('m_DataTable', 'M_Datatables');
    }

    public function ERP_AccessPermission()
    {
        $user_group_ids = implode(', ', $this->erp_access_user_group_ids);
        $function_ids = "'" . implode("', '", $this->erp_access_function_ids) . "'";

        $this->data['page_title'] = "ERP Access Permission";
        $this->data['page_content'] = "Report/MIS/erp_access_permission";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/MIS/Erp_AccessPermission.js?v=' . time() . '"></script>';

        // Mengambil data untuk filter dropdown
        $this->data['user_groups'] = $this->db->query("SELECT DISTINCT Tug.UserGroup_ID, Tug.UserGroup_Name 
            FROM TUserGroupFuncL Tgf 
            JOIN TUserGroup Tug ON Tug.UserGroup_ID = Tgf.UserGroup_ID
            WHERE Tgf.UserGroup_ID IN ({$user_group_ids})
            ORDER BY Tug.UserGroup_Name")->result();

        $this->data['functions'] = $this->db->query("SELECT DISTINCT Tgf.sf_ufunc_id, Tuf.SF_UFUNC_NAME_EN
            FROM TUserGroupFuncL Tgf
            JOIN TSF_USERFUNCTION Tuf ON Tgf.sf_ufunc_id = Tuf.SF_UFUNC_ID
            WHERE Tgf.sf_ufunc_id IN ({$function_ids})
            ORDER BY Tuf.SF_UFUNC_NAME_EN")->result();

        $this->data['employees'] = $this->db->query("
        SELECT THRMEmpPersonalData.User_ID, THRMEmpPersonalData.Emp_ID, THRMEmpPersonalData.First_Name
        FROM THRMEmpPersonalData, THRMCompany
        WHERE THRMEmpPersonalData.Company_ID = THRMCompany.Company_ID 
        AND THRMCompany.Company_ID = 2 
        AND THRMEmpPersonalData.Terminate_Date IS NULL
        ORDER BY THRMEmpPersonalData.First_Name ASC")->result();

        $this->load->view($this->layout, $this->data);
    }

    function PreviewListCbrApproval_lastStep()
    {
        $this->data['page_title'] = "Preview List Approval";
        $this->data['page_content'] = "Report/MIS/preview_list_cbr_approval_last_step";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/MIS/PreviewListCbrApproval_lastStep.js?v=' . time() . '"></script>
                                       <script src="' . base_url() . 'assets/Pages/cbr_app/history_approval_presdir.js?v=' . time() . '"></script>';

        $this->load->view($this->layout, $this->data);
    }

    // CRUD SECTION

    public function update_access_permission()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $id = $this->input->post('id');
        $access = $this->input->post('access'); // 'true' atau 'false'
        $employeeId = $this->input->post('employee_id');
        $durationMinutes = $this->input->post('duration_minutes') ?? '180';
        $durationLabels = ['180' => '3 jam', '360' => '6 jam', '120' => '2 jam', '60' => '1 jam', '30' => '0,5 jam'];

        if ($access === 'true' && (!is_string($durationMinutes) || !isset($durationLabels[$durationMinutes]))) {
            echo json_encode(['status' => 'error', 'message' => 'Durasi akses tidak valid.']);
            return;
        }

        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
            return;
        }

        // Memulai transaksi untuk memastikan integritas data
        $this->db->trans_start();

        // Ambil status akses saat ini sebelum diubah
        $current_access_row = $this->db->select('sf_ufunc_access')
            ->where('UserGroupFuncL_ID', $id)
            ->get('TUserGroupFuncL')
            ->row();
        $previous_access = $current_access_row ? strtolower(trim($current_access_row->sf_ufunc_access)) : 'read';

        $new_access_level = ($access === 'true') ? 'delete' : 'read';
        $new_access_text = ($access === 'true') ? 'Full Access (delete)' : 'View Only (read)';

        // Lakukan update pada tabel utama
        $data = ['sf_ufunc_access' => $new_access_level];
        $this->db->where('UserGroupFuncL_ID', $id);
        $this->db->update('TUserGroupFuncL', $data);

        $is_temporary = false;
        // LOGIKA: Hanya catat log jika hak akses dinaikkan dari 'read' ke 'delete'
        if ($access === 'true' && $previous_access === 'read') {
            $is_temporary = true;
            $changedAt = time();
            $log_data = [
                'UserGroupFuncL_ID' => $id,
                'UserAsk' => $employeeId,
                'Previous_Access'   => 'read',
                'New_Access'        => 'delete',
                'Changed_By'        => $this->session->userdata('sys_sba_userid'),
                'Changed_At'        => date('Y-m-d H:i:s', $changedAt),
                'Revert_At'         => date('Y-m-d H:i:s', $changedAt + (int) $durationMinutes * 60),
                'Is_Reverted'       => 0
            ];
            $this->db->insert('TblTemporaryAccessLog', $log_data);
        }

        // Selesaikan transaksi
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $message = ['status' => 'error', 'message' => 'Gagal memperbarui hak akses karena kesalahan database.'];
        } else {
            $this->db->trans_commit();
            $success_message = 'Hak akses diubah menjadi ' . $new_access_text . '.';
            if ($is_temporary) {
                $success_message = 'Hak akses sementara diubah menjadi ' . $new_access_text . '. Durasi akses: ' . $durationLabels[$durationMinutes] . '.';
            }
            $message = ['status' => 'success', 'message' => $success_message];
        }

        echo json_encode($message);
    }

    // DATATABLE SECTION

    public function DT_ERP_AccessPermission()
    {
        $usergroup_id = $this->input->post('usergroup_id');
        $function_id = $this->input->post('function_id');
        $user_group_ids = implode(', ', $this->erp_access_user_group_ids);
        $function_ids = "'" . implode("', '", $this->erp_access_function_ids) . "'";

        $where = null;
        $iswhere = "Tgf.UserGroup_ID in ({$user_group_ids})
            and Tgf.sf_ufunc_id in ({$function_ids})";

        // Menambahkan kondisi filter jika ada
        if ($usergroup_id != 'ALL' && !empty($usergroup_id)) {
            $where['Tgf.UserGroup_ID'] = $usergroup_id;
        }
        if ($function_id != 'ALL' && !empty($function_id)) {
            $where['Tgf.sf_ufunc_id'] = $function_id;
        }

        $query = "
            SELECT Tgf.UserGroupFuncL_ID, Tgf.UserGroup_ID, Tug.UserGroup_Name, Tgf.isLog, Tgf.sf_ufunc_id, Tuf.SF_UFUNC_NAME_EN, Tgf.sf_ufunc_access
            FROM TUserGroupFuncL Tgf 
            join TUserGroup Tug on Tug.UserGroup_ID = Tgf.UserGroup_ID
            join TSF_USERFUNCTION Tuf on Tgf.sf_ufunc_id = Tuf.SF_UFUNC_ID
        ";

        $cari = array('Tug.UserGroup_Name', 'Tuf.SF_UFUNC_NAME_EN');

        echo $this->M_Datatables->get_tables_query($query, $cari, $where, $iswhere);
    }

    private function GetPresdirData()
    {
        return $this->db->get_where('Tmst_User_NonHR', ['Pos_Name' => 'President Director'])->row();
    }

    public function DT_Preview_List_Approval()
    {
        $requestData = $_REQUEST;
        $columns = array(
            0  => 'H.CBReq_No',
            1  => 'H.CBReq_No',
            2  => 'TM.Termin_Ke',
            3  => 'H.Type',
            4  => 'TM.Amount_Type',
            5  => 'H.Document_Date',
            6  => 'TM.Payment_Plan_Date',
            7  => 'H.Payment_Plan_Date',
            8  => 'H.Currency_Id',
            9  => 'TM.Amount_Termin',
            10 => 'H.Document_Number',
            11 => 'H.Descript',
            12 => 'H.baseamount',
            13 => 'H.curr_rate',
            14 => 'H.Approval_Status',
            15 => 'TM.Status_AppvPresdir',
            16 => 'TA.Payment_Status',
            17 => 'H.Creation_DateTime',
            18 => 'H.Created_By',
            19 => 'TA.UserDivision',
            20 => 'U.First_Name',
            21 => 'H.Last_Update',
            22 => 'H.Acc_ID',
            23 => 'H.Approve_Date',
            24 => 'H.Payment_Plan_Date',
        );
        $columnIndex = isset($requestData['order'][0]['column']) ? (int) $requestData['order'][0]['column'] : 0;
        $order = isset($columns[$columnIndex]) ? $columns[$columnIndex] : 'H.CBReq_No';
        $dir = (isset($requestData['order'][0]['dir']) && strtolower($requestData['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';
        $username = $this->getPresdirData()->Emp_No; // Ambil User_ID dari data President Director

        $select = "SELECT DISTINCT 
            H.CBReq_No, 
            TM.SysID AS Termin_SysID,
            TM.Termin_Ke, 
            TM.Amount_Type,
            TM.Amount_Termin AS Amount,
            TM.Payment_Plan_Date AS Termin_Payment_Plan_Date,
            H.Payment_Plan_Date,
            H.Type, H.Document_Date, H.Document_Number, H.Acc_ID, H.Descript, 
            H.baseamount, H.curr_rate, H.Approval_Status, H.CBReq_Status, H.Paid_Status, 
            H.Creation_DateTime, H.Created_By, U.First_Name AS Created_By_Name, 
            H.Last_Update, H.Update_By, H.Currency_Id, H.Approve_Date, TA.UserDivision, 
            TM.Status_AppvPresdir AS Status_AppvPresidentDirector, 
            TA.Payment_Status, TA.Payment_Status_Time_Change, TA.Payment_Status_Change_By";

        $fromWhere = "
            FROM TAccCashBookReq_Header H
            INNER JOIN TUserGroupL GL ON H.Created_By = GL.User_ID
            INNER JOIN TUserPersonal U ON H.Created_By = U.User_ID
            INNER JOIN Ttrx_Cbr_Approval TA ON H.CBReq_No = TA.CBReq_No
            INNER JOIN Ttrx_Cbr_Approval_Termin TM ON H.CBReq_No = TM.CBReq_No
            WHERE H.Type = 'D'
                AND H.Company_ID = 2 
                AND ISNULL(H.isSPJ,0) = 0
                AND H.Approval_Status = 3
                AND H.CBReq_Status = 3
                AND (H.isClose IS NULL OR H.isClose = 0)
                AND TA.IsAppvPresidentDirector = 1
                AND TM.Status_AppvPresdir = 0 
                AND TM.AppvPresdir_By = $username
                AND ((TA.IsAppvStaff = 0)          OR (TA.IsAppvStaff = 1 AND TA.Status_AppvStaff = 1))
                AND ((TA.IsAppvChief = 0)          OR (TA.IsAppvChief = 1 AND TA.Status_AppvChief = 1))
                AND ((TA.IsAppvAsstManager = 0)    OR (TA.IsAppvAsstManager = 1 AND TA.Status_AppvAsstManager = 1))
                AND ((TA.IsAppvManager = 0)        OR (TA.IsAppvManager = 1 AND TA.Status_AppvManager = 1))
                AND ((TA.IsAppvSeniorManager = 0)  OR (TA.IsAppvSeniorManager = 1 AND TA.Status_AppvSeniorManager = 1))
                AND ((TA.IsAppvGeneralManager = 0) OR (TA.IsAppvGeneralManager = 1 AND TA.Status_AppvGeneralManager = 1))
                AND ((TA.IsAppvAdditional = 0)     OR (TA.IsAppvAdditional = 1 AND TA.Status_AppvAdditional = 1))
                AND ((TA.IsAppvDirector = 0)       OR (TA.IsAppvDirector = 1 AND TA.Status_AppvDirector = 1))
                AND ((TA.IsAppvFinancePerson = 0)  OR (TA.IsAppvFinancePerson = 1 AND TA.Status_AppvFinancePerson = 1)) 
                AND ((TA.IsAppvFinanceDirector = 0) OR (TA.IsAppvFinanceDirector = 1 AND TA.Status_AppvFinanceDirector = 1))";

        // Search filter (LIKE escape via '!')
        $searchSql = '';
        if (!empty($requestData['search']['value'])) {
            $searchValue = $this->db->escape_like_str($requestData['search']['value']);
            $searchSql = " AND (
                H.CBReq_No LIKE '%$searchValue%' ESCAPE '!'
                OR H.Payment_Plan_Date LIKE '%$searchValue%' ESCAPE '!'
                OR TM.Amount_Type LIKE '%$searchValue%' ESCAPE '!'
                OR TM.Payment_Plan_Date LIKE '%$searchValue%' ESCAPE '!'
                OR U.First_Name LIKE '%$searchValue%' ESCAPE '!'
                OR H.Document_Number LIKE '%$searchValue%' ESCAPE '!'
                OR H.Currency_Id LIKE '%$searchValue%' ESCAPE '!'
                OR H.Descript LIKE '%$searchValue%' ESCAPE '!'
                OR TA.UserDivision LIKE '%$searchValue%' ESCAPE '!'
            )";
        }

        // Count via wrapped DISTINCT subquery (no full-row hydration like num_rows())
        $totalData     = (int) $this->db->query("SELECT COUNT(*) AS total FROM ($select $fromWhere) AS sub")->row()->total;
        $totalFiltered = (int) $this->db->query("SELECT COUNT(*) AS total FROM ($select $fromWhere $searchSql) AS sub")->row()->total;

        $start  = isset($requestData['start']) ? (int) $requestData['start'] : 0;
        $length = isset($requestData['length']) ? (int) $requestData['length'] : 10;

        $dataSql = "$select $fromWhere $searchSql ORDER BY $order $dir OFFSET $start ROWS FETCH NEXT $length ROWS ONLY";
        $query = $this->db->query($dataSql);
        $data = array();
        foreach ($query->result_array() as $row) {
            $nestedData = array();
            $nestedData['Termin_SysID'] = $row['Termin_SysID'];
            $nestedData['CBReq_No'] = $row['CBReq_No'];
            $nestedData['Type'] = $row['Type'];
            $nestedData['Termin_Ke'] = $row['Termin_Ke'];
            $nestedData['Document_Date'] = $row['Document_Date'];
            $nestedData['Acc_ID'] = $row['Acc_ID'];
            $nestedData['Descript'] = $row['Descript'];
            $nestedData['Document_Number'] = $row['Document_Number'];
            $nestedData['Amount_Type'] = $row['Amount_Type'];
            $nestedData['Amount'] = $row['Amount']; // Amount sudah berasal dari TM.Amount_Termin
            $nestedData['Termin_Payment_Plan_Date'] = $row['Termin_Payment_Plan_Date'];
            $nestedData['baseamount'] = $row['baseamount'];
            $nestedData['curr_rate'] = $row['curr_rate'];
            $nestedData['Approval_Status'] = $row['Approval_Status'];
            $nestedData['CBReq_Status'] = $row['CBReq_Status'];
            $nestedData['Paid_Status'] = $row['Paid_Status'];
            $nestedData['Creation_DateTime'] = $row['Creation_DateTime'];
            $nestedData['Created_By'] = $row['Created_By'];
            $nestedData['First_Name'] = $row['Created_By_Name'];
            $nestedData['Last_Update'] = $row['Last_Update'];
            $nestedData['Update_By'] = $row['Update_By'];
            $nestedData['Currency_Id'] = $row['Currency_Id'];
            $nestedData['Approve_Date'] = $row['Approve_Date'];
            $nestedData['UserDivision'] = $row['UserDivision'];
            $nestedData['Status_AppvPresidentDirector'] = $row['Status_AppvPresidentDirector'];
            $nestedData['Payment_Status'] = $row['Payment_Status'];
            // $nestedData['Termin_Payment_Plan_Date'] = $row['Termin_Payment_Plan_Date'];
            $nestedData['Payment_Plan_Date'] = $row['Payment_Plan_Date'];

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
