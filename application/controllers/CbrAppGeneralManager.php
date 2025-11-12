<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CbrAppGeneralManager extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';
    private $Ttrx_Cbr_Approval = 'Ttrx_Cbr_Approval';

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
        $this->data['page_title'] = "General Manager Approval-Cash Book Requisition";
        $this->data['page_content'] = "cbr_app/approval";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/cbr_app/generalmanager.js?v=' . time() . '"></script>
                                       <script src="' . base_url() . 'assets/Pages/cbr_app/history_approval.js?v=' . time() . '"></script>';

        $this->load->view($this->layout, $this->data);
    }

    public function approve_submission()
    {
        $Cbrs = $this->input->post('CBReq_No');

        $this->db->trans_start();
        foreach ($Cbrs as $CBReq_No) {

            $RowApproval = $this->db->get_where($this->Ttrx_Cbr_Approval, ['CBReq_No' => $CBReq_No])->row();

            if ($RowApproval->AppvGeneralManager_By == $this->session->userdata('sys_sba_username')) {
                $this->db->where('CBReq_No', $CBReq_No)->update($this->Ttrx_Cbr_Approval, [
                    'Status_AppvGeneralManager' => 1,
                    'AppvGeneralManager_Name' => $this->session->userdata('sys_sba_nama'),
                    // 'AppvGeneralManager_By' => $this->session->userdata('sys_sba_username'),
                    'AppvGeneralManager_At' => $this->DateTime,
                ]);
            }

            if ($RowApproval->AppvAdditional_By == $this->session->userdata('sys_sba_username')) {
                $this->db->where('CBReq_No', $CBReq_No)->update($this->Ttrx_Cbr_Approval, [
                    'Status_AppvAdditional' => 1,
                    'AppvAdditional_Name' => $this->session->userdata('sys_sba_nama'),
                    // 'AppvAdditional_By' => $this->session->userdata('sys_sba_username'),
                    'AppvAdditional_At' => $this->DateTime,
                ]);
            }
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
                'msg' => 'Cash Book Requisition successfully approved !',
            ]);
        }
    }

    public function reject_submission()
    {
        $Cbrs = $this->input->post('CBReq_No');
        $rejection_reason = $this->input->post('rejection_reason');

        $this->db->trans_start();
        foreach ($Cbrs as $CBReq_No) {

            $RowApproval = $this->db->get_where($this->Ttrx_Cbr_Approval, ['CBReq_No' => $CBReq_No])->row();

            if ($RowApproval->AppvGeneralManager_By == $this->session->userdata('sys_sba_username')) {
                $this->db->where('CBReq_No', $CBReq_No)->update($this->Ttrx_Cbr_Approval, [
                    'Status_AppvGeneralManager' => 2,
                    'AppvGeneralManager_Name' => $this->session->userdata('sys_sba_nama'),
                    'AppvGeneralManager_By' => $this->session->userdata('sys_sba_username'),
                    'AppvGeneralManager_At' => $this->DateTime,
                ]);
            }

            if ($RowApproval->AppvAdditional_By == $this->session->userdata('sys_sba_username')) {
                $this->db->where('CBReq_No', $CBReq_No)->update($this->Ttrx_Cbr_Approval, [
                    'Status_AppvAdditional' => 2,
                    'AppvAdditional_Name' => $this->session->userdata('sys_sba_nama'),
                    'AppvAdditional_By' => $this->session->userdata('sys_sba_username'),
                    'AppvAdditional_At' => $this->DateTime,
                ]);
            }

            $this->help->record_history_approval($CBReq_No, $rejection_reason);
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
                'msg' => 'Cash Book Requisition successfully Rejected !',
            ]);
        }
    }

    // ========================================== DATATABLE 

    public function DT_List_To_Approve()
    {
        $requestData = $_REQUEST;
        $columns = array(
            0 => 'TAccCashBookReq_Header.CBReq_No',
            1 => 'TAccCashBookReq_Header.CBReq_No',
            2 => 'Type',
            3 => 'Document_Date',
            4 => 'TAccCashBookReq_Header.Currency_Id',
            5 => 'Amount',
            6 => 'Document_Number',
            7 => 'Descript',
            8 => 'baseamount',
            9 => 'curr_rate',
            10 => 'Approval_Status',
            11 => 'CBReq_Status',
            12 => 'Paid_Status',
            13 => 'Creation_DateTime',
            14 => 'Created_By',
            15 => 'First_Name',
            16 => 'Last_Update',
            17 => 'Update_By',
            18 => 'TAccCashBookReq_Header.Acc_ID ',
            19 => 'TAccCashBookReq_Header.Approve_Date',

        );
        $order  = $columns[$requestData['order']['0']['column']];
        $dir    = $requestData['order']['0']['dir'];
        $from   = $this->input->post('from');
        $until  = $this->input->post('until');
        $username = $this->session->userdata('sys_sba_username');

        $sql = "SELECT  distinct TAccCashBookReq_Header.CBReq_No, Type, Document_Date, Document_Number, TAccCashBookReq_Header.Acc_ID, Descript, Amount, baseamount, curr_rate, Approval_Status, CBReq_Status, Paid_Status, Creation_DateTime, Created_By, First_Name AS Created_By_Name, Last_Update, Update_By, TAccCashBookReq_Header.Currency_Id, TAccCashBookReq_Header.Approve_Date
        FROM TAccCashBookReq_Header
        INNER JOIN TUserGroupL ON TAccCashBookReq_Header.Created_By = TUserGroupL.User_ID
        INNER JOIN TUserPersonal ON TAccCashBookReq_Header.Created_By = TUserPersonal.User_ID
        LEFT OUTER JOIN Ttrx_Cbr_Approval ON TAccCashBookReq_Header.CBReq_No = Ttrx_Cbr_Approval.CBReq_No
        WHERE TAccCashBookReq_Header.Type='D'
        AND TAccCashBookReq_Header.Company_ID = 2 
        AND isNull(isSPJ,0) = 0
        AND Approval_Status  = 3
        AND CBReq_Status = 3
        AND Ttrx_Cbr_Approval.CBReq_No IS NOT NULL
        AND (
                Ttrx_Cbr_Approval.AppvGeneralManager_By = '$username' AND IsAppvGeneralManager = 1 AND Status_AppvGeneralManager = 0
                AND ((IsAppvStaff = 0)          or (IsAppvStaff = 1 and Status_AppvStaff = 1))
                AND ((IsAppvChief = 0)          or (IsAppvChief = 1 and Status_AppvChief = 1))
                AND ((IsAppvAsstManager = 0)    or (IsAppvAsstManager = 1 and Status_AppvAsstManager = 1))
                AND ((IsAppvManager = 0)        or (IsAppvManager = 1 and Status_AppvManager = 1))
                AND ((IsAppvSeniorManager) = 0  or (IsAppvSeniorManager = 1 and Status_AppvSeniorManager = 1)) 
            )
            OR 
            (
                AppvAdditional_By = '$username' AND IsAppvAdditional = 1 AND Status_AppvAdditional = 0 
                AND ((IsAppvStaff = 0)         or (IsAppvStaff = 1 and Status_AppvStaff = 1))
                AND ((IsAppvChief = 0)         or (IsAppvChief = 1 and Status_AppvChief = 1))
                AND ((IsAppvAsstManager = 0)   or (IsAppvAsstManager = 1 and Status_AppvAsstManager = 1))
                AND ((IsAppvManager) = 0       or (IsAppvManager = 1 and Status_AppvManager = 1))
                AND ((IsAppvSeniorManager) = 0 or (IsAppvSeniorManager = 1 and Status_AppvSeniorManager = 1))
            )
            ";

        $totalData = $this->db->query($sql)->num_rows();
        if (!empty($requestData['search']['value'])) {
            $sql .= " AND (TAccCashBookReq_Header.CBReq_No LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR First_Name LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Document_Number LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Document_Date LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR TAccCashBookReq_Header.Currency_Id LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Descript LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR CBReq_Status LIKE '%" . $requestData['search']['value'] . "%' ";
            $sql .= " OR Amount LIKE '%" . $requestData['search']['value'] . "%') ";
        }
        //----------------------------------------------------------------------------------
        $totalFiltered = $this->db->query($sql)->num_rows();
        $sql .= " ORDER BY $order $dir OFFSET " . $requestData['start'] . " ROWS FETCH NEXT " . $requestData['length'] . " ROWS ONLY ";
        $query = $this->db->query($sql);
        $data = array();
        foreach ($query->result_array() as $row) {
            $nestedData = array();
            $nestedData['CBReq_No'] = $row['CBReq_No'];
            $nestedData['Type'] = $row['Type'];
            $nestedData['Document_Date'] = $row['Document_Date'];
            $nestedData['Acc_ID'] = $row['Acc_ID'];
            $nestedData['Descript'] = $row['Descript'];
            $nestedData['Document_Number'] = $row['Document_Number'];
            $nestedData['Amount'] = $row['Amount'];
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
