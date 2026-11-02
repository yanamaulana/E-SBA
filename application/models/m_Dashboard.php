<?php
class m_Dashboard extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_general_dashboard($division, $year, $fromMonth, $toMonth, $currency = null)
    {
        $yearStart = sprintf('%04d-01-01', $year);
        $tomorrow = date('Y-m-d', strtotime('tomorrow'));
        $currencySql = "COALESCE(NULLIF(UPPER(LTRIM(RTRIM(H.Currency_ID))), ''), 'N/A')";
        // EXISTS avoids multiplying a header's amount by approval or payment-term rows.
        $where = "H.Type = 'D' AND H.Company_ID = 2
            AND ISNULL(H.isSPJ, 0) = 0 AND ISNULL(H.isClose, 0) = 0
            AND H.Approval_Status = 3 AND H.CBReq_Status = 3
            AND H.Document_Date >= ? AND H.Document_Date < ?
            AND EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A
                WHERE A.CBReq_No = H.CBReq_No AND A.UserDivision = ?)";
        $rows = $this->general_query("SELECT MONTH(H.Document_Date) AS Month_Number,
                $currencySql AS Currency_ID, H.Paid_Status,
                COUNT(*) AS Document_Count, SUM(ISNULL(H.Amount, 0)) AS Total_Amount
            FROM TAccCashBookReq_Header H WHERE $where
            GROUP BY MONTH(H.Document_Date), $currencySql, H.Paid_Status",
            [$yearStart, $tomorrow, $division]);

        $currencies = array_values(array_unique(array_column($rows, 'Currency_ID')));
        sort($currencies);
        if (!$currencies) {
            $currencies = ['IDR'];
        }
        if ($currency === null || $currency === '') {
            $currency = in_array('IDR', $currencies, true) ? 'IDR' : $currencies[0];
        }
        if (!in_array($currency, $currencies, true)) {
            throw new InvalidArgumentException('Mata uang tidak tersedia untuk divisi ini.');
        }

        $monthly = [];
        for ($month = $fromMonth; $month <= $toMonth; $month++) {
            $monthly[$month] = ['month' => $month, 'count' => 0, 'amount' => 0.0];
        }
        $summary = ['count' => 0, 'amount' => 0.0, 'average' => 0.0, 'paid_percentage' => 0.0];
        $payments = ['NP' => 0, 'HP' => 0, 'FP' => 0, 'OTHER' => 0];
        foreach ($rows as $row) {
            $month = (int) $row['Month_Number'];
            if ($row['Currency_ID'] !== $currency || !isset($monthly[$month])) {
                continue;
            }
            $count = (int) $row['Document_Count'];
            $amount = (float) $row['Total_Amount'];
            $monthly[$month]['count'] += $count;
            $monthly[$month]['amount'] += $amount;
            $summary['count'] += $count;
            $summary['amount'] += $amount;
            $payments[$this->general_payment_status($row['Paid_Status'])] += $count;
        }
        if ($summary['count'] > 0) {
            $summary['average'] = $summary['amount'] / $summary['count'];
            $summary['paid_percentage'] = round(100 * $payments['FP'] / $summary['count'], 1);
        }
        $periodStart = sprintf('%04d-%02d-01', $year, $fromMonth);
        $periodEnd = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $toMonth)))
            ->modify('+1 month')->format('Y-m-d');
        $periodEnd = min($periodEnd, $tomorrow);
        $recent = $this->general_query("SELECT TOP (8) H.CBReq_No,
                CONVERT(varchar(10), H.Document_Date, 23) AS Document_Date,
                H.Descript, H.Amount, H.Paid_Status
            FROM TAccCashBookReq_Header H WHERE $where AND $currencySql = ?
            ORDER BY H.Document_Date DESC, H.CBReq_No DESC",
            [$periodStart, $periodEnd, $division, $currency]);
        foreach ($recent as &$document) {
            $document['Amount'] = (float) $document['Amount'];
            $document['Paid_Status'] = $this->general_payment_status($document['Paid_Status']);
        }
        unset($document);

        return [
            'division' => $division, 'year' => $year,
            'from_month' => $fromMonth, 'to_month' => $toMonth,
            'currency' => $currency, 'currencies' => $currencies,
            'updated_at' => date('d M Y, H:i'),
            'summary' => $summary, 'payments' => $payments,
            'monthly' => array_values($monthly), 'recent' => $recent,
        ];
    }

    private function general_payment_status($value)
    {
        $status = strtoupper(trim((string) $value));
        if ($status === '') {
            return 'NP';
        }
        return in_array($status, ['NP', 'HP', 'FP'], true) ? $status : 'OTHER';
    }

    public function get_general_year_status($division, $year)
    {
        // Unsubmitted documents have no UserDivision yet. Resolve their creators
        // using the same NonHR-first division rule used by Auth.
        $hr = $this->load->database('HR', true);
        $employees = $this->general_query('SELECT Emp_No FROM HRQviewEmployeeDetail WHERE Division_Name = ?', [$division], $hr);
        $employeeNames = array_fill_keys(array_map('strval', array_column($employees, 'Emp_No')), true);
        $users = $this->general_query('SELECT U.User_ID, U.User_Name, N.UserID AS NonHR_ID, N.Division_Name
            FROM ERPQview_User_Employee U LEFT JOIN Tmst_User_NonHR N ON N.UserID = U.User_ID', []);
        $creatorIds = [];
        foreach ($users as $user) {
            $matches = $user['NonHR_ID'] !== null
                ? $user['Division_Name'] === $division
                : isset($employeeNames[(string) $user['User_Name']]);
            if ($matches && filter_var($user['User_ID'], FILTER_VALIDATE_INT) !== false) {
                $creatorIds[] = (int) $user['User_ID'];
            }
        }
        // Only validated integer IDs from the database enter this list (no request input).
        $creatorSql = $creatorIds ? 'H.Created_By IN (' . implode(',', array_unique($creatorIds)) . ')' : '1 = 0';
        $rejected = [];
        foreach (['Staff', 'Chief', 'AsstManager', 'Manager', 'SeniorManager', 'GeneralManager', 'Additional', 'Director', 'PresidentDirector', 'FinancePerson', 'FinanceDirector'] as $step) {
            $rejected[] = "(R.IsAppv$step = 1 AND R.Status_Appv$step = 2)";
        }
        $rejectionSql = implode(' OR ', $rejected);
        $rows = $this->general_query("SELECT S.Status_Key, COUNT(*) AS Document_Count FROM (
            SELECT CASE
                WHEN H.isClose = 1 THEN 'void'
                WHEN NOT EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A WHERE A.CBReq_No = H.CBReq_No) THEN 'unsubmitted'
                WHEN EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval R WHERE R.CBReq_No = H.CBReq_No AND ($rejectionSql)) THEN 'rejected'
                WHEN EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A WHERE A.CBReq_No = H.CBReq_No AND A.Legitimate = 1) THEN 'approved'
                ELSE 'in_progress' END AS Status_Key
            FROM TAccCashBookReq_Header H
            WHERE H.Type = 'D' AND H.Company_ID = 2 AND ISNULL(H.isSPJ, 0) = 0
                AND H.Document_Date >= ? AND H.Document_Date < ?
                AND (H.isClose = 1 OR (H.Approval_Status = 3 AND H.CBReq_Status = 3
                    AND (EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A WHERE A.CBReq_No = H.CBReq_No)
                         OR H.Paid_Status IN ('NP', 'HP'))))
                AND (EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A WHERE A.CBReq_No = H.CBReq_No AND A.UserDivision = ?)
                    OR (NOT EXISTS (SELECT 1 FROM Ttrx_Cbr_Approval A WHERE A.CBReq_No = H.CBReq_No) AND ($creatorSql)))
            ) S GROUP BY S.Status_Key", [sprintf('%04d-01-01', $year), date('Y-m-d', strtotime('tomorrow')), $division]);
        $counts = ['void' => 0, 'unsubmitted' => 0, 'approved' => 0, 'in_progress' => 0, 'rejected' => 0];
        foreach ($rows as $row) {
            $counts[$row['Status_Key']] = (int) $row['Document_Count'];
        }
        return ['division' => $division, 'year' => $year, 'counts' => $counts, 'total' => array_sum($counts)];
    }

    private function general_query($sql, $bindings, $connection = null)
    {
        $connection = $connection ?? $this->db;
        $debug = $connection->db_debug;
        $connection->db_debug = false;
        try {
            $query = $connection->query($sql, $bindings);
            if ($query === false) {
                throw new RuntimeException('Unable to read division CBR metrics.');
            }
            return $query->result_array();
        } finally {
            $connection->db_debug = $debug;
        }
    }

    public function get_Count_Awaiting_Approvals($username)
    {
        // Menggunakan Query Binding untuk keamanan dan JOIN ke tabel termin
        $sql = "SELECT COUNT(DISTINCT H.CBReq_No) as Awaiting_Approvals
                FROM Ttrx_Cbr_Approval_Termin TM
                JOIN TAccCashBookReq_Header H ON TM.CBReq_No = H.CBReq_No
                JOIN Ttrx_Cbr_Approval A ON TM.CBReq_No = A.CBReq_No
                WHERE H.Type = 'D'
                AND H.Company_ID = 2 
                AND (H.isSPJ IS NULL OR H.isSPJ = 0) 
                AND H.Approval_Status = 3 
                AND H.CBReq_Status = 3
                AND (H.isClose IS NULL OR H.isClose = 0)
                AND A.IsAppvPresidentDirector = 1
                -- Kondisi utama sekarang ada di tabel termin
                AND TM.Status_AppvPresdir = 0
                AND TM.AppvPresdir_By = ?
                -- Mempertahankan validasi rantai approval dari tabel lama
                AND (A.IsAppvStaff = 0          OR A.Status_AppvStaff = 1)
                AND (A.IsAppvChief = 0          OR A.Status_AppvChief = 1)
                AND (A.IsAppvAsstManager = 0    OR A.Status_AppvAsstManager = 1)
                AND (A.IsAppvManager = 0        OR A.Status_AppvManager = 1)
                AND (A.IsAppvSeniorManager = 0  OR A.Status_AppvSeniorManager = 1)
                AND (A.IsAppvGeneralManager = 0 OR A.Status_AppvGeneralManager = 1)
                AND (A.IsAppvAdditional = 0     OR A.Status_AppvAdditional = 1)
                AND (A.IsAppvDirector = 0       OR A.Status_AppvDirector = 1)
                AND (A.IsAppvFinancePerson = 0  OR A.Status_AppvFinancePerson = 1) 
                AND (A.IsAppvFinanceDirector = 0 OR A.Status_AppvFinanceDirector = 1)";

        $query = $this->db->query($sql, array($username));
        return $query->row()->Awaiting_Approvals;
    }

    public function get_Count_After_Approvals($username, $status)
    {
        $first_date_on_month = date('Y-m-01 00:00:00');
        $last_date_on_month = date('Y-m-t 23:59:59');

        $sql = "SELECT COUNT(DISTINCT H.CBReq_No) as After_Approvals
                FROM Ttrx_Cbr_Approval_Termin TM
                JOIN TAccCashBookReq_Header H ON TM.CBReq_No = H.CBReq_No
                JOIN Ttrx_Cbr_Approval A ON TM.CBReq_No = A.CBReq_No
                WHERE H.Type = 'D'
                AND H.Company_ID = 2 
                AND (H.isSPJ IS NULL OR H.isSPJ = 0) 
                AND H.Approval_Status = 3 
                AND H.CBReq_Status = 3
                AND (H.isClose IS NULL OR H.isClose = 0)
                AND A.IsAppvPresidentDirector = 1
                AND TM.Status_AppvPresdir = ?
                AND TM.AppvPresdir_At >= ?
                AND TM.AppvPresdir_At <= ?
                AND TM.AppvPresdir_By = ?
                AND (A.IsAppvStaff = 0          OR A.Status_AppvStaff = 1)
                AND (A.IsAppvChief = 0          OR A.Status_AppvChief = 1)
                AND (A.IsAppvAsstManager = 0    OR A.Status_AppvAsstManager = 1)
                AND (A.IsAppvManager = 0        OR A.Status_AppvManager = 1)
                AND (A.IsAppvSeniorManager = 0  OR A.Status_AppvSeniorManager = 1)
                AND (A.IsAppvGeneralManager = 0 OR A.Status_AppvGeneralManager = 1)
                AND (A.IsAppvAdditional = 0     OR A.Status_AppvAdditional = 1)
                AND (A.IsAppvDirector = 0       OR A.Status_AppvDirector = 1)
                AND (A.IsAppvFinancePerson = 0  OR A.Status_AppvFinancePerson = 1) 
                AND (A.IsAppvFinanceDirector = 0 OR A.Status_AppvFinanceDirector = 1)";

        $query = $this->db->query($sql, array(
            $status,
            $first_date_on_month,
            $last_date_on_month,
            $username
        ));

        return $query->row()->After_Approvals;
    }

    public function get_Amount_Approved($username)
    {
        $first_date_on_month = date('Y-m-01 00:00:00');
        $last_date_on_month = date('Y-m-t 23:59:59');
        $status = 1;

        $sql = "SELECT 
                    TM.Currency_ID, 
                    SUM(TM.Amount_Termin) as Total_Amount
                FROM Ttrx_Cbr_Approval_Termin TM
                JOIN TAccCashBookReq_Header H ON TM.CBReq_No = H.CBReq_No
                JOIN Ttrx_Cbr_Approval A ON TM.CBReq_No = A.CBReq_No
                WHERE H.Type = 'D'
                AND H.Company_ID = 2 
                AND (H.isSPJ IS NULL OR H.isSPJ = 0) 
                AND H.Approval_Status = 3 
                AND H.CBReq_Status = 3
                AND (H.isClose IS NULL OR H.isClose = 0)
                AND A.IsAppvPresidentDirector = 1
                AND TM.Status_AppvPresdir = ?
                AND TM.AppvPresdir_At >= ?
                AND TM.AppvPresdir_At <= ?
                AND TM.AppvPresdir_By = ?
                AND (A.IsAppvStaff = 0          OR A.Status_AppvStaff = 1)
                AND (A.IsAppvChief = 0          OR A.Status_AppvChief = 1)
                AND (A.IsAppvAsstManager = 0    OR A.Status_AppvAsstManager = 1)
                AND (A.IsAppvManager = 0        OR A.Status_AppvManager = 1)
                AND (A.IsAppvSeniorManager = 0  OR A.Status_AppvSeniorManager = 1)
                AND (A.IsAppvGeneralManager = 0 OR A.Status_AppvGeneralManager = 1)
                AND (A.IsAppvAdditional = 0     OR A.Status_AppvAdditional = 1)
                AND (A.IsAppvDirector = 0       OR A.Status_AppvDirector = 1)
                AND (A.IsAppvFinancePerson = 0  OR A.Status_AppvFinancePerson = 1) 
                AND (A.IsAppvFinanceDirector = 0 OR A.Status_AppvFinanceDirector = 1)
                GROUP BY TM.Currency_ID";

        $query = $this->db->query($sql, array(
            $status,
            $first_date_on_month,
            $last_date_on_month,
            $username
        ));

        $totals = [];

        foreach ($query->result() as $row) {
            $currency = strtoupper(trim($row->Currency_ID));
            if (!empty($currency)) {
                $totals[$currency] = (float) $row->Total_Amount;
            }
        }

        return $totals;
    }

    public function get_Division_Summary($status, $username, $year, $month)
    {
        // karena month = 5 bukan 05 kita harus ubah dahulu formatnya agar bisa dibandingkan dengan format bulan di database yang biasanya 2 digit
        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        $first_date = "$year-$month-01 00:00:00";
        $last_date = date("Y-m-t", strtotime($first_date)) . " 23:59:59";

        $sql_range =  "";
        if ($status == 1) {
            $sql_range =  "AND TM.AppvPresdir_At >= ?
                           AND TM.AppvPresdir_At <= ? ";
        }


        $sql = "SELECT 
                A.UserDivision, 
                TM.Currency_ID,
                COUNT(DISTINCT H.CBReq_No) AS Total_CBRs,
                SUM(TM.Amount_Termin) AS Total_Amount
            FROM Ttrx_Cbr_Approval_Termin TM
            JOIN TAccCashBookReq_Header H ON TM.CBReq_No = H.CBReq_No
            JOIN Ttrx_Cbr_Approval A ON TM.CBReq_No = A.CBReq_No
            WHERE H.Type = 'D'
            AND H.Company_ID = 2 
            AND (H.isSPJ IS NULL OR H.isSPJ = 0) 
            AND H.Approval_Status = 3 
            AND H.CBReq_Status = 3
            AND (H.isClose IS NULL OR H.isClose = 0)
            AND A.IsAppvPresidentDirector = 1
            AND TM.Status_AppvPresdir = ?
            AND TM.AppvPresdir_By = ?
            $sql_range
            AND (A.IsAppvStaff = 0          OR A.Status_AppvStaff = 1)
            AND (A.IsAppvChief = 0          OR A.Status_AppvChief = 1)
            AND (A.IsAppvAsstManager = 0    OR A.Status_AppvAsstManager = 1)
            AND (A.IsAppvManager = 0        OR A.Status_AppvManager = 1)
            AND (A.IsAppvSeniorManager = 0  OR A.Status_AppvSeniorManager = 1)
            AND (A.IsAppvGeneralManager = 0 OR A.Status_AppvGeneralManager = 1)
            AND (A.IsAppvAdditional = 0     OR A.Status_AppvAdditional = 1)
            AND (A.IsAppvDirector = 0       OR A.Status_AppvDirector = 1)
            AND (A.IsAppvFinancePerson = 0  OR A.Status_AppvFinancePerson = 1) 
            AND (A.IsAppvFinanceDirector = 0 OR A.Status_AppvFinanceDirector = 1)
            GROUP BY A.UserDivision, TM.Currency_ID";

        if ($status == 1) {
            $query = $this->db->query($sql, array($status, $username, $first_date, $last_date));
        } else {
            $query = $this->db->query($sql, array($status, $username));
        }

        // Pindahkan ke fungsi penyusunan array
        return $this->format_dynamic_table($query->result_array());
    }

    private function format_dynamic_table($raw_data)
    {
        $divisions = [];
        $currencies = []; // Array untuk menyimpan daftar mata uang apa saja yang muncul

        foreach ($raw_data as $row) {
            $div = $row['UserDivision'];
            $curr = strtoupper(trim($row['Currency_ID']));

            // Simpan mata uang ke daftar jika belum ada (Biar dinamis)
            if (!empty($curr) && !in_array($curr, $currencies)) {
                $currencies[] = $curr;
            }

            // Jika divisi belum ada di array, buat format dasarnya
            if (!isset($divisions[$div])) {
                $divisions[$div] = [
                    'Total_CBRs' => 0,
                    'Amounts' => []
                ];
            }

            // Tambahkan jumlah CBR ke total divisi tersebut
            $divisions[$div]['Total_CBRs'] += $row['Total_CBRs'];

            // Masukkan nominal ke mata uang masing-masing
            $divisions[$div]['Amounts'][$curr] = $row['Total_Amount'];
        }

        // Kembalikan dua data: Daftar Divisi & Daftar Mata Uang (untuk Header Kolom)
        return [
            'currencies' => $currencies,
            'divisions'  => $divisions
        ];
    }
}
