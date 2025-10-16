<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CbrMonitoring extends CI_Controller
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
        $this->data['page_title'] = "Monitoring Cash Book Requisition";
        $this->data['page_content'] = "cbr_app/monitoring";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/cbr_app/monitoring.js"></script>';

        $this->load->view($this->layout, $this->data);
    }

    public function DT_Monitoring()
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

        $sql = "Select distinct TAccCashBookReq_Header.CBReq_No, Type, Document_Date, Document_Number, TAccCashBookReq_Header.Acc_ID, Descript, Amount, baseamount, curr_rate, Approval_Status, CBReq_Status, Paid_Status, Creation_DateTime, Created_By, First_Name AS Created_By_Name, Last_Update, Update_By, TAccCashBookReq_Header.Currency_Id, TAccCashBookReq_Header.Approve_Date,
        CASE WHEN Ttrx_Cbr_Approval.CBReq_No IS NOT NULL AND Ttrx_Cbr_Approval.CBReq_No != '' THEN 1 ELSE 0 END AS Has_Submitted_Approval,
         Ttrx_Cbr_Approval.IsAppvStaff, Ttrx_Cbr_Approval.Status_AppvStaff, Ttrx_Cbr_Approval.AppvStaff_By, Ttrx_Cbr_Approval.AppvStaff_Name, Ttrx_Cbr_Approval.AppvStaff_At, Ttrx_Cbr_Approval.IsAppvChief, Ttrx_Cbr_Approval.Status_AppvChief, Ttrx_Cbr_Approval.AppvChief_By, Ttrx_Cbr_Approval.AppvChief_Name, Ttrx_Cbr_Approval.AppvChief_At, Ttrx_Cbr_Approval.IsAppvAsstManager, Ttrx_Cbr_Approval.Status_AppvAsstManager, Ttrx_Cbr_Approval.AppvAsstManager_By, Ttrx_Cbr_Approval.AppvAsstManager_Name, Ttrx_Cbr_Approval.AppvAsstManager_At, Ttrx_Cbr_Approval.IsAppvManager, Ttrx_Cbr_Approval.Status_AppvManager, Ttrx_Cbr_Approval.AppvManager_By, Ttrx_Cbr_Approval.AppvManager_Name, Ttrx_Cbr_Approval.AppvManager_At, Ttrx_Cbr_Approval.IsAppvSeniorManager, Ttrx_Cbr_Approval.Status_AppvSeniorManager, Ttrx_Cbr_Approval.AppvSeniorManager_By, Ttrx_Cbr_Approval.AppvSeniorManager_Name, Ttrx_Cbr_Approval.AppvSeniorManager_At, Ttrx_Cbr_Approval.IsAppvGeneralManager, Ttrx_Cbr_Approval.Status_AppvGeneralManager, Ttrx_Cbr_Approval.AppvGeneralManager_By, Ttrx_Cbr_Approval.AppvGeneralManager_Name, Ttrx_Cbr_Approval.AppvGeneralManager_At, Ttrx_Cbr_Approval.IsAppvDirector, Ttrx_Cbr_Approval.Status_AppvDirector, Ttrx_Cbr_Approval.AppvDirector_By, Ttrx_Cbr_Approval.AppvDirector_Name, Ttrx_Cbr_Approval.AppvDirector_At, Ttrx_Cbr_Approval.IsAppvPresidentDirector, Ttrx_Cbr_Approval.Status_AppvPresidentDirector, Ttrx_Cbr_Approval.AppvPresidentDirector_By, Ttrx_Cbr_Approval.AppvPresidentDirector_Name, Ttrx_Cbr_Approval.AppvPresidentDirector_At, Ttrx_Cbr_Approval.IsAppvFinanceStaff, Ttrx_Cbr_Approval.Status_AppvFinanceStaff, Ttrx_Cbr_Approval.AppvFinanceStaff_By, Ttrx_Cbr_Approval.AppvFinanceStaff_Name, Ttrx_Cbr_Approval.AppvFinanceStaff_At, Ttrx_Cbr_Approval.IsAppvFinanceManager, Ttrx_Cbr_Approval.Status_AppvFinanceManager, Ttrx_Cbr_Approval.AppvFinanceManager_By, Ttrx_Cbr_Approval.AppvFinanceManager_Name, Ttrx_Cbr_Approval.AppvFinanceManager_At, Ttrx_Cbr_Approval.IsAppvFinanceDirector, Ttrx_Cbr_Approval.Status_AppvFinanceDirector, Ttrx_Cbr_Approval.AppvFinanceDirector_By, Ttrx_Cbr_Approval.AppvFinanceDirector_Name, Ttrx_Cbr_Approval.AppvFinanceDirector_At, Ttrx_Cbr_Approval.UserName_User, Ttrx_Cbr_Approval.Rec_Created_At, Ttrx_Cbr_Approval.UserDivision
        FROM TAccCashBookReq_Header
        INNER JOIN TUserGroupL ON TAccCashBookReq_Header.Created_By = TUserGroupL.User_ID
        INNER JOIN TUserPersonal ON TAccCashBookReq_Header.Created_By = TUserPersonal.User_ID
        LEFT OUTER JOIN Ttrx_Cbr_Approval ON TAccCashBookReq_Header.CBReq_No = Ttrx_Cbr_Approval.CBReq_No
        WHERE TAccCashBookReq_Header.Type='D'
        And TAccCashBookReq_Header.Document_Date >= {d '$from'}
        And TAccCashBookReq_Header.Document_Date <= {d '$until'}
        AND TAccCashBookReq_Header.Company_ID = 2 
        AND isNull(isSPJ,0) = 0
        AND Approval_Status  = 3
        AND CBReq_Status = 3 
        AND Created_By = '" . $this->session->userdata('sys_sba_userid') . "' ";
        // -- AND Ttrx_Cbr_Approval.CBReq_No IS NOT NULL

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
        // var_dump($sql);
        // die;
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
            $nestedData['IsAppvStaff'] = $row['IsAppvStaff'];
            $nestedData['Status_AppvStaff'] = $row['Status_AppvStaff'];
            $nestedData['AppvStaff_By'] = $row['AppvStaff_By'];
            $nestedData['AppvStaff_Name'] = $row['AppvStaff_Name'] ?? '';
            $nestedData['AppvStaff_At'] = $row['AppvStaff_At'];
            $nestedData['IsAppvChief'] = $row['IsAppvChief'];
            $nestedData['Status_AppvChief'] = $row['Status_AppvChief'];
            $nestedData['AppvChief_By'] = $row['AppvChief_By'];
            $nestedData['AppvChief_Name'] = $row['AppvChief_Name'] ?? '';
            $nestedData['AppvChief_At'] = $row['AppvChief_At'];
            $nestedData['IsAppvAsstManager'] = $row['IsAppvAsstManager'];
            $nestedData['Status_AppvAsstManager'] = $row['Status_AppvAsstManager'];
            $nestedData['AppvAsstManager_By'] = $row['AppvAsstManager_By'];
            $nestedData['AppvAsstManager_Name'] = $row['AppvAsstManager_Name'] ?? '';
            $nestedData['AppvAsstManager_At'] = $row['AppvAsstManager_At'];
            $nestedData['IsAppvManager'] = $row['IsAppvManager'];
            $nestedData['Status_AppvManager'] = $row['Status_AppvManager'];
            $nestedData['AppvManager_By'] = $row['AppvManager_By'];
            $nestedData['AppvManager_Name'] = $row['AppvManager_Name'] ?? '';
            $nestedData['AppvManager_At'] = $row['AppvManager_At'];
            $nestedData['IsAppvSeniorManager'] = $row['IsAppvSeniorManager'];
            $nestedData['Status_AppvSeniorManager'] = $row['Status_AppvSeniorManager'];
            $nestedData['AppvSeniorManager_By'] = $row['AppvSeniorManager_By'];
            $nestedData['AppvSeniorManager_Name'] = $row['AppvSeniorManager_Name'] ?? '';
            $nestedData['AppvSeniorManager_At'] = $row['AppvSeniorManager_At'];
            $nestedData['IsAppvGeneralManager'] = $row['IsAppvGeneralManager'];
            $nestedData['Status_AppvGeneralManager'] = $row['Status_AppvGeneralManager'];
            $nestedData['AppvGeneralManager_By'] = $row['AppvGeneralManager_By'];
            $nestedData['AppvGeneralManager_Name'] = $row['AppvGeneralManager_Name'] ?? '';
            $nestedData['AppvGeneralManager_At'] = $row['AppvGeneralManager_At'];
            $nestedData['IsAppvDirector'] = $row['IsAppvDirector'];
            $nestedData['Status_AppvDirector'] = $row['Status_AppvDirector'];
            $nestedData['AppvDirector_By'] = $row['AppvDirector_By'];
            $nestedData['AppvDirector_Name'] = $row['AppvDirector_Name'] ?? '';
            $nestedData['AppvDirector_At'] = $row['AppvDirector_At'];
            $nestedData['IsAppvPresidentDirector'] = $row['IsAppvPresidentDirector'];
            $nestedData['Status_AppvPresidentDirector'] = $row['Status_AppvPresidentDirector'];
            $nestedData['AppvPresidentDirector_By'] = $row['AppvPresidentDirector_By'];
            $nestedData['AppvPresidentDirector_Name'] = $row['AppvPresidentDirector_Name'] ?? '';
            $nestedData['AppvPresidentDirector_At'] = $row['AppvPresidentDirector_At'];
            $nestedData['IsAppvFinanceStaff'] = $row['IsAppvFinanceStaff'];
            $nestedData['Status_AppvFinanceStaff'] = $row['Status_AppvFinanceStaff'];
            $nestedData['AppvFinanceStaff_By'] = $row['AppvFinanceStaff_By'];
            $nestedData['AppvFinanceStaff_Name'] = $row['AppvFinanceStaff_Name'] ?? '';
            $nestedData['AppvFinanceStaff_At'] = $row['AppvFinanceStaff_At'];
            $nestedData['IsAppvFinanceManager'] = $row['IsAppvFinanceManager'];
            $nestedData['Status_AppvFinanceManager'] = $row['Status_AppvFinanceManager'];
            $nestedData['AppvFinanceManager_By'] = $row['AppvFinanceManager_By'];
            $nestedData['AppvFinanceManager_Name'] = $row['AppvFinanceManager_Name'] ?? '';
            $nestedData['AppvFinanceManager_At'] = $row['AppvFinanceManager_At'];
            $nestedData['IsAppvFinanceDirector'] = $row['IsAppvFinanceDirector'];
            $nestedData['Status_AppvFinanceDirector'] = $row['Status_AppvFinanceDirector'];
            $nestedData['AppvFinanceDirector_By'] = $row['AppvFinanceDirector_By'];
            $nestedData['AppvFinanceDirector_Name'] = $row['AppvFinanceDirector_Name'] ?? '';
            $nestedData['AppvFinanceDirector_At'] = $row['AppvFinanceDirector_At'];
            $nestedData['UserName_User'] = $row['UserName_User'];
            $nestedData['Rec_Created_At'] = $row['Rec_Created_At'];
            $nestedData['UserDivision'] =  $row['UserDivision'] ?? $this->session->userdata('sys_sba_department'); // 
            $nestedData['Has_Submitted_Approval'] = $row['Has_Submitted_Approval'];

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
