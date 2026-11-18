<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MonitoringCbr extends CI_Controller
{
    private $Date;
    private $DateTime;
    private $layout = 'layout';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        $this->Date = date("Y-m-d");
        $this->DateTime = date("Y-m-d H:i:s");
        $this->load->model('m_helper', 'help');
        $this->load->model('m_DataTable', 'M_Datatables');
    }

    private function bdj_attachment_error($error)
    {
        $status = $error instanceof InvalidArgumentException ? 400 : ($error instanceof OutOfBoundsException ? 404 : 500);
        if ($status === 500) { log_message('error', 'Monitoring BDJ attachment: ' . $error->getMessage()); }
        return $this->output->set_status_header($status)->set_content_type('application/json')
            ->set_output(json_encode(['error' => $status === 500 ? 'Lampiran BDJ gagal dimuat.' : $error->getMessage()]));
    }

    public function bdj_attachments()
    {
        $this->load->model('m_CashbookAttachment', 'bdjAttachments');
        try {
            $rows = $this->bdjAttachments->listing($this->input->get('journal', false));
            foreach ($rows as &$row) {
                $row['url'] = base_url('MonitoringCbr/bdj_attachment_file/' . $row['Attachment_ID']);
            }
            unset($row);
            return $this->output->set_content_type('application/json')->set_output(json_encode(['data' => $rows]));
        } catch (Throwable $e) { return $this->bdj_attachment_error($e); }
    }

    public function bdj_attachment_file($id = '')
    {
        $this->load->model('m_CashbookAttachment', 'bdjAttachments');
        $this->load->library('Cashbook_attachment_files');
        try {
            $file = $this->bdjAttachments->find((string) $id);
            $content = file_get_contents($this->cashbook_attachment_files->resolve($file['Relative_Path']));
            if ($content === false) { throw new RuntimeException('File tidak dapat dibaca.'); }
            return $this->output->set_content_type($file['Mime_Type'])
                ->set_header('X-Content-Type-Options: nosniff')->set_header('Cache-Control: private, no-store')
                ->set_header('Content-Disposition: inline; filename="attachment.' . pathinfo($file['Relative_Path'], PATHINFO_EXTENSION) .
                    '"; filename*=UTF-8\'\'' . rawurlencode($file['Original_Name']))->set_output($content);
        } catch (Throwable $e) { return $this->bdj_attachment_error($e); }
    }

    public function index()
    {
        $this->data['page_title'] = "CBR - Global Monitoring";
        $this->data['page_content'] = "cbr_app/monitoring_cbr"; // Assuming this is the view file
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Pages/cbr_app/monitoring_cbr.js?v=' . time() . '"></script>';

        $this->data['employees'] = $this->db->query("SELECT THRMEmpPersonalData.User_ID, THRMEmpPersonalData.Emp_ID, THRMEmpPersonalData.First_Name
        FROM THRMEmpPersonalData, THRMCompany
        WHERE THRMEmpPersonalData.Company_ID = THRMCompany.Company_ID 
        AND THRMCompany.Company_ID = 2 
        AND THRMEmpPersonalData.Terminate_Date IS  NULL
        Order By THRMEmpPersonalData.First_Name ASC")->result();

        $this->load->view($this->layout, $this->data);
    }

    private function get_base_query($filters)
    {
        $column_range = $filters['column_range'];
        $from = $filters['from'];
        $until = $filters['until'];
        $employee = $filters['employee'];

        $sql = "SELECT
                    H.CBReq_No,
                    CASE WHEN A.CBReq_No IS NOT NULL THEN 1 ELSE 0 END AS Has_Submitted_Approval,
                    H.Type, H.Document_Date, A.Rec_Created_At, H.Currency_Id, H.Amount, H.Document_Number, H.Descript,
                    H.baseamount, H.curr_rate, H.Approval_Status, H.isClose, A.Payment_Status, H.Creation_DateTime,
                    A.UserDivision, P.First_Name, H.Last_Update, H.Acc_ID, H.Approve_Date, A.Legitimate,
                    A.IsAppvStaff, A.Status_AppvStaff, A.AppvStaff_At,
                    A.IsAppvChief, A.Status_AppvChief, A.AppvChief_At,
                    A.IsAppvAsstManager, A.Status_AppvAsstManager, A.AppvAsstManager_At,
                    A.IsAppvManager, A.Status_AppvManager, A.AppvManager_At,
                    A.IsAppvSeniorManager, A.Status_AppvSeniorManager, A.AppvSeniorManager_At,
                    A.IsAppvGeneralManager, A.Status_AppvGeneralManager, A.AppvGeneralManager_At,
                    A.IsAppvAdditional, A.Status_AppvAdditional, A.AppvAdditional_At,
                    A.IsAppvFinancePerson, A.Status_AppvFinancePerson, A.AppvFinancePerson_At,
                    A.IsAppvDirector, A.Status_AppvDirector, A.AppvDirector_At,
                    A.IsAppvFinanceDirector, A.Status_AppvFinanceDirector, A.AppvFinanceDirector_At,
                    A.IsAppvPresidentDirector, A.Status_AppvPresidentDirector, A.AppvPresidentDirector_At, H.Payment_Plan_Date
                FROM TAccCashBookReq_Header H
                INNER JOIN TUserPersonal P ON H.Created_By = P.User_ID
                LEFT JOIN Ttrx_Cbr_Approval A ON H.CBReq_No = A.CBReq_No
                WHERE H.Type='D'
                AND H.Company_ID = 2
                AND isNull(H.isSPJ,0) = 0
                AND H.Approval_Status = 3
                AND H.CBReq_Status = 3
                AND $column_range >= {d '$from'}
                AND $column_range <= {d '$until'}";

        if (!empty($employee) && $employee !== 'ALL') {
            $sql .= " AND H.Created_By = '" . $this->db->escape_str($employee) . "'";
        }

        return $sql;
    }

    public function DT_Monitoring_global()
    {
        $requestData = $_REQUEST;
        $filters = [
            'from' => $this->input->post('from'),
            'until' => $this->input->post('until'),
            'column_range' => $this->input->post('column_range'),
            'employee' => $this->input->post('employee')
        ];

        $sql = $this->get_base_query($filters);

        $totalData = $this->db->query($sql)->num_rows();

        if (!empty($requestData['search']['value'])) {
            $searchValue = $this->db->escape_like_str($requestData['search']['value']);
            $sql .= " AND (H.CBReq_No LIKE '%$searchValue%' ESCAPE '!'
                      OR P.First_Name LIKE '%$searchValue%' ESCAPE '!'
                      OR A.UserDivision LIKE '%$searchValue%' ESCAPE '!'
                      OR H.Document_Number LIKE '%$searchValue%' ESCAPE '!'
                      OR H.Payment_Plan_Date LIKE '%$searchValue%' ESCAPE '!'
                      OR H.Descript LIKE '%$searchValue%' ESCAPE '!') ";
        }

        $totalFiltered = $this->db->query($sql)->num_rows();

        $columns = [
            1 => 'H.CBReq_No',
            2 => 'Has_Submitted_Approval',
            4 => 'H.Document_Date',
            5 => 'A.Rec_Created_At',
            6 => 'H.Currency_Id',
            7 => 'H.Amount',
            8 => 'H.Document_Number',
            9 => 'H.Descript',
            13 => 'H.isClose',
            14 => 'A.Payment_Status',
            16 => 'A.UserDivision',
            17 => 'P.First_Name'
        ];
        $order_column = $columns[$requestData['order']['0']['column']] ?? 'H.Document_Date';
        $dir = $requestData['order']['0']['dir'] ?? 'DESC';

        $sql .= " ORDER BY $order_column $dir OFFSET " . $requestData['start'] . " ROWS FETCH NEXT " . $requestData['length'] . " ROWS ONLY ";

        $query = $this->db->query($sql);
        echo json_encode([
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $query->result_array(),
        ]);
    }

    public function export_excel()
    {
        $filters = [
            'from' => $this->input->get('from'),
            'until' => $this->input->get('until'),
            'column_range' => $this->input->get('column_range'),
            'employee' => $this->input->get('employee')
        ];

        $sql = $this->get_base_query($filters) . " ORDER BY H.Document_Date DESC";
        $query = $this->db->query($sql);
        $results = $query->result_array();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CBR Global Monitoring');

        // Create the title
        $title = 'List CBR ' . $filters['from'] . ' - ' . $filters['until'] . ' downloaded at ' . date('Y-m-d H:i:s');
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:AD1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Set Headers starting from row 2
        $headers = [
            'CBR No',
            'Submitted',
            'Doc Date',
            'Submit Date',
            'Currency',
            'Amount',
            'Ref Number',
            'Description',
            'Status',
            'Payment Status',
            'Division',
            'Created By',
            'Asst. Manager',
            'Asst. Manager At',
            'Manager',
            'Manager At',
            'Senior Manager',
            'Senior Manager At',
            'General Manager',
            'General Manager At',
            'Additional',
            'Additional At',
            'Finance Person',
            'Finance Person At',
            'Director',
            'Director At',
            'Finance Director',
            'Finance Director At',
            'President Director',
            'President Director At'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '2', $header);
            $sheet->getStyle($col . '2')->getFont()->setBold(true);
            $col++;
        }

        // Data starts from row 3
        $rowNum = 3;
        $renderApprovalStatus = function ($has_submitted, $is_needed, $status) {
            if ($has_submitted == 0) return 'Not Submitted';
            if ($is_needed == 0) return 'N/A';
            if ($status == 0) return 'Pending';
            if ($status == 1) return 'Approved';
            if ($status == 2) return 'Rejected';
            return 'Pending';
        };

        $renderApprovalDate = function ($has_submitted, $is_needed, $status, $timestamp) {
            if ($has_submitted == 0 || $is_needed == 0 || $status == 0) {
                return '-';
            }
            return $timestamp ? substr($timestamp, 0, 10) : '-';
        };

        foreach ($results as $row) {
            $paymentStatus = 'Unknown';
            if ($row['Payment_Status'] == 0 || $row['Payment_Status'] == null) $paymentStatus = 'Not Paid';
            else if ($row['Payment_Status'] == 1) $paymentStatus = 'Fully Paid';
            else if ($row['Payment_Status'] == 2) $paymentStatus = 'Payment Rejected';
            else if ($row['Payment_Status'] == 3) $paymentStatus = 'Partially Paid';

            $sheet->setCellValue('A' . $rowNum, $row['CBReq_No']);
            $sheet->setCellValue('B' . $rowNum, $row['Has_Submitted_Approval'] == 1 ? 'Yes' : 'No');
            $sheet->setCellValue('C' . $rowNum, substr($row['Document_Date'], 0, 10));
            $sheet->setCellValue('D' . $rowNum, $row['Rec_Created_At'] ? substr($row['Rec_Created_At'], 0, 10) : '-');
            $sheet->setCellValue('E' . $rowNum, $row['Currency_Id']);
            $sheet->setCellValue('F' . $rowNum, $row['Amount']);
            $sheet->getStyle('F' . $rowNum)->getNumberFormat()->setFormatCode('#,##0.0000');
            $sheet->setCellValue('G' . $rowNum, $row['Document_Number']);
            $sheet->setCellValue('H' . $rowNum, $row['Descript']);
            $sheet->setCellValue('I' . $rowNum, $row['isClose'] == 1 ? 'VOID' : 'Open');
            $sheet->setCellValue('J' . $rowNum, $paymentStatus);
            $sheet->setCellValue('K' . $rowNum, $row['UserDivision']);
            $sheet->setCellValue('L' . $rowNum, $row['First_Name']);

            $sheet->setCellValue('M' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvAsstManager'], $row['Status_AppvAsstManager']));
            $sheet->setCellValue('N' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvAsstManager'], $row['Status_AppvAsstManager'], $row['AppvAsstManager_At']));
            $sheet->setCellValue('O' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvManager'], $row['Status_AppvManager']));
            $sheet->setCellValue('P' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvManager'], $row['Status_AppvManager'], $row['AppvManager_At']));
            $sheet->setCellValue('Q' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvSeniorManager'], $row['Status_AppvSeniorManager']));
            $sheet->setCellValue('R' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvSeniorManager'], $row['Status_AppvSeniorManager'], $row['AppvSeniorManager_At']));
            $sheet->setCellValue('S' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvGeneralManager'], $row['Status_AppvGeneralManager']));
            $sheet->setCellValue('T' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvGeneralManager'], $row['Status_AppvGeneralManager'], $row['AppvGeneralManager_At']));
            $sheet->setCellValue('U' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvAdditional'], $row['Status_AppvAdditional']));
            $sheet->setCellValue('V' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvAdditional'], $row['Status_AppvAdditional'], $row['AppvAdditional_At']));
            $sheet->setCellValue('W' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvFinancePerson'], $row['Status_AppvFinancePerson']));
            $sheet->setCellValue('X' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvFinancePerson'], $row['Status_AppvFinancePerson'], $row['AppvFinancePerson_At']));
            $sheet->setCellValue('Y' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvDirector'], $row['Status_AppvDirector']));
            $sheet->setCellValue('Z' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvDirector'], $row['Status_AppvDirector'], $row['AppvDirector_At']));
            $sheet->setCellValue('AA' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvFinanceDirector'], $row['Status_AppvFinanceDirector']));
            $sheet->setCellValue('AB' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvFinanceDirector'], $row['Status_AppvFinanceDirector'], $row['AppvFinanceDirector_At']));
            $sheet->setCellValue('AC' . $rowNum, $renderApprovalStatus($row['Has_Submitted_Approval'], $row['IsAppvPresidentDirector'], $row['Status_AppvPresidentDirector']));
            $sheet->setCellValue('AD' . $rowNum, $renderApprovalDate($row['Has_Submitted_Approval'], $row['IsAppvPresidentDirector'], $row['Status_AppvPresidentDirector'], $row['AppvPresidentDirector_At']));

            // Apply conditional coloring
            if ($row['Status_AppvPresidentDirector'] == 1) {
                $sheet->getStyle('A' . $rowNum . ':AD' . $rowNum)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFC6EFCE'); // Light Green
            }

            if ($row['isClose'] == 1) {
                $sheet->getStyle('A' . $rowNum . ':AD' . $rowNum)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFFC7CE'); // Light Red
            }

            $rowNum++;
        }

        foreach (range('A', 'AD') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'CBR_Global_Monitoring_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
