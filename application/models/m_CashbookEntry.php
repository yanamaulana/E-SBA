<?php
defined('BASEPATH') or exit('No direct script access allowed');

class m_CashbookEntry extends CI_Model
{
    // ESBA uses company 2, as do its existing CBR and cashbook queries.
    private $companyId = 2;
    private $fields = [
        'document' => 'H.JournalH_Code', 'description' => 'H.Memo',
        'date' => 'CAST(H.CashBookDate AS date)', 'memo' => 'H.Memo',
        'payee' => 'H.Payor_Payee', 'currency' => 'H.Currency_ID',
        'account' => 'A.Account_NameEn', 'amount' => 'H.Total_Amount',
    ];

    private function query($sql, $bindings = [])
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $result = $this->db->query($sql, $bindings);
            if ($result === false) {
                throw new RuntimeException('Cashbook database query failed.');
            }
            return $result;
        } finally {
            $this->db->db_debug = $debug;
        }
    }

    public function filter_options()
    {
        return [
            'accounts' => $this->query('SELECT A.Acc_ID, A.Account_Number, A.Account_NameEn AS Account_Name,
                A.AccountClass_ID FROM TAccChartAccount A
                INNER JOIN TAccChartCategory C ON A.AccChartCategory_ID = C.ChartCategory_ID
                WHERE A.Company_ID = ? AND A.AccountClass_ID IN (3,4,5)
                ORDER BY A.AccChartCategory_ID, A.Account_Number', [$this->companyId])->result_array(),
            'currencies' => $this->query('SELECT Currency_ID FROM TCurrency WHERE Status = 1 ORDER BY Currency_ID')->result_array(),
            'statuses' => $this->query("SELECT KEY_ID, STATUS_CODE, STATUS_REMARK FROM TERPDOCUMENTSTATUS
                WHERE FUNCTION_ID = 7879 AND KEY_ID IN ('DOC','APPROVAL') ORDER BY STATUS_ID")->result_array(),
        ];
    }

    private function scalar($value)
    {
        if (!is_scalar($value) && $value !== null) {
            throw new InvalidArgumentException('Format filter tidak valid.');
        }
        return trim((string) $value);
    }

    private function date_value($value)
    {
        $value = $this->scalar($value);
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Tanggal harus valid dengan format YYYY-MM-DD.');
        }
        return $value;
    }

    private function like_value($value)
    {
        return str_replace(['!', '%', '_', '['], ['!!', '!%', '!_', '!['], $value);
    }

    private function advanced_conditions($criteria, &$bindings)
    {
        if (!is_array($criteria) || count($criteria) > 5) {
            throw new InvalidArgumentException('Pencarian lanjutan maksimal 5 kondisi.');
        }
        $parts = [];
        foreach ($criteria as $rule) {
            if (!is_array($rule)) {
                throw new InvalidArgumentException('Kondisi pencarian tidak valid.');
            }
            $value = $this->scalar($rule['value'] ?? '');
            if ($value === '') { continue; }
            $field = $this->scalar($rule['field'] ?? '');
            $operator = $this->scalar($rule['operator'] ?? '');
            $join = $this->scalar($rule['join'] ?? 'AND');
            if (!isset($this->fields[$field]) || !in_array($join, ['AND', 'OR'], true)) {
                throw new InvalidArgumentException('Field atau penghubung pencarian tidak valid.');
            }
            $column = $this->fields[$field];
            $comparison = ['eq' => '=', 'ne' => '<>', 'gt' => '>', 'gte' => '>=', 'lt' => '<', 'lte' => '<='];
            if ($field === 'date') { $value = $this->date_value($value); }
            if ($field === 'amount' && isset($comparison[$operator]) && !is_numeric($value)) {
                throw new InvalidArgumentException('Nominal harus berupa angka.');
            }
            if (isset($comparison[$operator])) {
                $condition = "$column {$comparison[$operator]} ?";
                $bindings[] = $value;
            } elseif (in_array($operator, ['contains', 'starts', 'not_contains'], true) && $field !== 'date') {
                $column = $field === 'amount' ? 'CAST(H.Total_Amount AS varchar(60))' : $column;
                $like = $operator === 'not_contains' ? 'NOT LIKE' : 'LIKE';
                $condition = "$column $like ? ESCAPE '!'";
                if ($operator === 'not_contains') { $condition = "($condition OR $column IS NULL)"; }
                $bindings[] = ($operator === 'starts' ? '' : '%') . $this->like_value($value) . '%';
            } else {
                throw new InvalidArgumentException('Operator pencarian tidak valid.');
            }
            $parts[] = (count($parts) ? " $join " : '') . "($condition)";
        }
        // Keep OR conditions inside the company/type/date scope (same precedence as CF10).
        return $parts ? ' AND (' . implode('', $parts) . ')' : '';
    }

    public function export_headers(array $request)
    {
        // Reuse precisely the same filters/order while ignoring the browser page.
        $request['start'] = 0;
        $request['length'] = 100;
        do {
            $page = $this->datatable($request, false);
            foreach ($page['data'] as $header) { yield $header; }
            $request['start'] += 100;
        } while (count($page['data']) === 100);
    }

    public function datatable(array $request, $includeCounts = true)
    {
        $type = $this->scalar($request['entry_type'] ?? 'BD');
        if (!in_array($type, ['BR', 'CR', 'BD', 'CD'], true)) {
            throw new InvalidArgumentException('Tipe cashbook tidak valid.');
        }
        $from = $this->date_value($request['date_from'] ?? date('Y-m-01'));
        $to = $this->date_value($request['date_to'] ?? date('Y-m-t'));
        if ($from > $to) { throw new InvalidArgumentException('Tanggal akhir harus sama atau setelah tanggal awal.'); }
        $fromSql = ' FROM TAccCashBookHeader H
            INNER JOIN TAccChartAccount A ON H.Acc_ID = A.Acc_ID AND H.Company_ID = A.Company_ID
            LEFT JOIN TAccCashFlowType F ON H.CashFlow_Type = F.CashFlow_Code
            LEFT JOIN THRMMasterType M ON F.Header = M.Type_Code';
        // CF10 also excludes cash flow master types belonging to another company.
        $scope = ' WHERE H.Company_ID = ? AND (M.Company_ID = ? OR M.Company_ID IS NULL)';
        $scopeBindings = [$this->companyId, $this->companyId];
        $where = $scope . ' AND H.CashBookType = ? AND H.CashBookDate >= ? AND H.CashBookDate < ?';
        $bindings = array_merge($scopeBindings, [$type, $from, (new DateTime($to))->modify('+1 day')->format('Y-m-d')]);
        $posted = $this->scalar($request['posted'] ?? '');
        $void = $this->scalar($request['void'] ?? '');
        foreach (['H.ClearingFlag' => $posted, 'ISNULL(H.isVoid,0)' => $void] as $column => $value) {
            if ($value !== '') {
                if (!in_array($value, ['0', '1'], true)) { throw new InvalidArgumentException('Filter posted/void tidak valid.'); }
                $where .= " AND $column = ?";
                $bindings[] = (int) $value;
            }
        }
        $account = $this->scalar($request['account'] ?? '');
        if ($account !== '') { $where .= ' AND H.Acc_ID = ?'; $bindings[] = $account; }
        foreach (['document_status' => 'H.Status', 'approval_status' => 'H.Approval_Status'] as $key => $column) {
            $values = $request[$key] ?? [];
            if (!is_array($values) || count($values) > 20) { throw new InvalidArgumentException('Filter status tidak valid.'); }
            if ($values) {
                $where .= " AND $column IN (" . implode(',', array_fill(0, count($values), '?')) . ')';
                foreach ($values as $value) { $bindings[] = $this->scalar($value); }
            }
        }
        $where .= $this->advanced_conditions($request['criteria'] ?? [], $bindings);
        $search = $this->scalar($request['search']['value'] ?? '');
        if ($search !== '') {
            $parts = [];
            foreach (['H.JournalH_Code', 'H.Payor_Payee', 'A.Account_NameEn', 'H.Currency_ID', 'H.Memo',
                'F.CashFlow_Name', 'M.Type_NameEn', 'CONVERT(varchar(10), H.CashBookDate, 101)',
                'CONVERT(varchar(10), H.CashBookDate, 23)', 'CAST(H.Total_Amount AS varchar(60))'] as $column) {
                $parts[] = "$column LIKE ? ESCAPE '!'";
                $bindings[] = '%' . $this->like_value($search) . '%';
            }
            // Match a header by its details without multiplying or truncating its rows.
            $detailParts = [];
            foreach (['DA.Account_NameEn', 'CAST(D.Description AS varchar(max))', 'D.DebCred',
                'D.CBR_Document', 'D.Invoice_No', 'CAST(D.Amount AS varchar(60))'] as $column) {
                $detailParts[] = "$column LIKE ? ESCAPE '!'";
                $bindings[] = '%' . $this->like_value($search) . '%';
            }
            $parts[] = 'EXISTS (SELECT 1 FROM TAccCashBookDetail D
                LEFT JOIN TAccChartAccount DA ON DA.Acc_ID = D.Acc_ID AND DA.Company_ID = H.Company_ID
                WHERE D.JournalH_Code = H.JournalH_Code AND (' . implode(' OR ', $detailParts) . '))';
            $where .= ' AND (' . implode(' OR ', $parts) . ')';
        }
        $columns = [2 => 'H.JournalH_Code', 3 => 'H.Payor_Payee', 4 => 'H.CashBookDate',
            5 => 'A.Account_NameEn', 6 => 'H.Currency_ID', 7 => 'H.Total_Amount', 8 => 'H.Memo',
            9 => 'H.Status', 10 => 'H.Approval_Status', 11 => 'H.isVoid', 12 => 'M.Type_NameEn'];
        $orders = $request['order'] ?? [];
        if (!is_array($orders)) { throw new InvalidArgumentException('Urutan tabel tidak valid.'); }
        $orderBy = [];
        foreach (array_slice($orders, 0, 13) as $order) {
            if (!is_array($order)) { throw new InvalidArgumentException('Urutan tabel tidak valid.'); }
            $column = (int) $this->scalar($order['column'] ?? -1);
            if (isset($columns[$column])) {
                $direction = strtoupper($this->scalar($order['dir'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
                $orderBy[$columns[$column]] = $columns[$column] . ' ' . $direction;
            }
        }
        if (!$orderBy) { $orderBy[] = 'H.CashBookDate DESC'; }
        if (!isset($orderBy['H.JournalH_Code'])) { $orderBy[] = 'H.JournalH_Code DESC'; }
        $start = max(0, (int) $this->scalar($request['start'] ?? 0));
        $length = max(1, min(100, (int) $this->scalar($request['length'] ?? 15)));
        $total = $includeCounts ? $this->query('SELECT COUNT(*) AS total' . $fromSql . $scope, $scopeBindings)->row_array() : ['total'=>0];
        $filtered = $includeCounts ? $this->query('SELECT COUNT(*) AS total' . $fromSql . $where, $bindings)->row_array() : ['total'=>0];
        $rows = $this->query('SELECT H.JournalH_Code, H.CashBookDate, H.CashBookType, H.Currency_ID,
            H.Total_Amount, H.Memo, A.Account_NameEn AS Account_Name, H.ClearingBank,
            H.Payor_Payee, H.Status, H.Approval_Status, ISNULL(H.isVoid,0) AS IsVoid,
            ISNULL(H.CashFlow_Type,0) AS CashFlow_Type, F.CashFlow_Name, M.Type_NameEn' .
            $fromSql . $where . ' ORDER BY ' . implode(', ', array_unique($orderBy)) .
            " OFFSET $start ROWS FETCH NEXT $length ROWS ONLY", $bindings)->result_array();
        // Page headers first, then fetch every detail of those headers in one query.
        // Joining details before OFFSET would make 15 mean detail rows instead of documents.
        $cashbookDetails = [];
        if ($rows) {
            $headerCodes = array_values(array_unique(array_column($rows, 'JournalH_Code')));
            $detailRows = $this->query("SELECT D.JournalH_Code, D.CashBookD_ID,
                DA.Account_NameEn AS Account_Name, D.DebCred, D.Amount, D.Description,
                COALESCE(NULLIF(LTRIM(RTRIM(D.CBR_Document)), ''),
                    CASE WHEN D.Invoice_No LIKE 'CBR%' THEN D.Invoice_No ELSE '' END) AS CBR_No
                FROM TAccCashBookDetail D
                INNER JOIN TAccCashBookHeader H ON H.JournalH_Code = D.JournalH_Code
                LEFT JOIN TAccChartAccount DA ON DA.Acc_ID = D.Acc_ID AND DA.Company_ID = H.Company_ID
                WHERE H.Company_ID = ? AND D.JournalH_Code IN (" . implode(',', array_fill(0, count($headerCodes), '?')) . ')
                ORDER BY D.JournalH_Code, D.CashBookD_ID', array_merge([$this->companyId], $headerCodes))->result_array();
            foreach ($detailRows as $detail) { $cashbookDetails[$detail['JournalH_Code']][] = $detail; }
        }
        // Batch clearing-account lookups only for the visible page.
        $codes = [];
        foreach ($rows as $row) {
            if (!empty($row['ClearingBank'])) { $codes[] = $row['JournalH_Code']; $codes[] = $row['ClearingBank']; }
        }
        $clearing = [];
        if ($codes) {
            $codes = array_values(array_unique($codes));
            $details = $this->query('SELECT D.JournalH_Code, D.Acc_ID, A.Account_NameEn AS Account_Name
                FROM TAccJournalDetail D INNER JOIN TAccChartAccount A ON D.Acc_ID = A.Acc_ID
                WHERE A.Company_ID = ? AND D.JournalH_Code IN (' . implode(',', array_fill(0, count($codes), '?')) . ')
                ORDER BY D.Acc_ID', array_merge([$this->companyId], $codes))->result_array();
            foreach ($details as $detail) { $clearing[$detail['JournalH_Code']][] = $detail; }
        }
        foreach ($rows as &$row) {
            $row['Details'] = $cashbookDetails[$row['JournalH_Code']] ?? [];
            $row['Clearing_Account'] = '';
            if (!empty($row['ClearingBank'])) {
                $details = array_merge($clearing[$row['JournalH_Code']] ?? [], $clearing[$row['ClearingBank']] ?? []);
                usort($details, function ($a, $b) { return $a['Acc_ID'] <=> $b['Acc_ID']; });
                $previous = null;
                foreach ($details as $detail) {
                    if ($previous != $detail['Acc_ID'] && $detail['JournalH_Code'] !== $row['JournalH_Code']) {
                        $row['Clearing_Account'] = $detail['JournalH_Code'] . ' - ' . $detail['Account_Name'];
                        break;
                    }
                    $previous = $detail['Acc_ID'];
                }
            }
        }
        unset($row);
        return ['draw' => (int) $this->scalar($request['draw'] ?? 0), 'recordsTotal' => (int) $total['total'],
            'recordsFiltered' => (int) $filtered['total'], 'data' => $rows];
    }
}
