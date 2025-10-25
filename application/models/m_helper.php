<?php
class m_helper extends CI_Model
{
    private $tbl_CounterDocNumber = 'tsys_counterdocnumber';
    private $tmst_member_koperasi = 'tmst_member_koperasi';

    public function Fn_resulting_response($responses)
    {
        $response = json_encode($responses);
        echo $response;
    }

    public function format_idr($angka)
    {
        $hasil_rupiah = "Rp " . number_format($angka, 2, ',', '.');
        return $hasil_rupiah;
    }

    public function Gnrt_Identity_Number_PerYear($param, $trxName)
    {
        $rows = $this->db->get_where($this->tbl_CounterDocNumber, array(
            "TrxName" => $trxName,
            "TrxYear" => date('Y'),
            // "month" => date('m'),
        ));
        // TrxName, TrxMonth, TrxYear, TrxDate, CurrDocNumber
        $length = 3;
        if ($rows->num_rows() > 0) {
            $row = $rows->row();
            $newCount = intval($row->CurrDocNumber) + 1;

            $this->db->where('TrxName', $trxName);
            $this->db->where('TrxYear', date('Y'));
            // $this->db->where('TrxMonth', date('m'));
            $this->db->update($this->tbl_CounterDocNumber, [
                'CurrDocNumber' => $newCount,
            ]);

            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = date("y") . date("m") . $string;
        } else {
            $this->db->insert($this->tbl_CounterDocNumber, [
                "TrxName" => $trxName,
                "TrxYear" => date('Y'),
                // "TrxMonth" => date('m'),
                "CurrDocNumber" => 1,
            ]);
            $newCount = 1;
            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = date("y") . date("m") . $string;
        }

        return $param . '-' . $identity_number;
    }

    public function Gnrt_Identity_Number_PerMonth($trxName)
    {
        $rows = $this->db->get_where($this->tbl_CounterDocNumber, array(
            "TrxName" => $trxName,
            "TrxYear" => date('Y'),
            "TrxMonth" => intval(date('m')),
        ));
        // TrxName, TrxMonth, TrxYear, TrxDate, CurrDocNumber
        $length = 3;
        if ($rows->num_rows() > 0) {
            $row = $rows->row();
            $newCount = intval($row->CurrDocNumber) + 1;

            $this->db->where('TrxName', $trxName);
            $this->db->where('TrxYear', date('Y'));
            $this->db->where('TrxMonth', intval(date('m')));
            $this->db->update($this->tbl_CounterDocNumber, [
                'CurrDocNumber' => $newCount,
            ]);

            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = $string;
        } else {
            $this->db->insert($this->tbl_CounterDocNumber, [
                "TrxName" => $trxName,
                "TrxYear" => date('Y'),
                "TrxMonth" => intval(date('m')),
                "CurrDocNumber" => 1,
            ]);
            $newCount = 1;
            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = $string;
        }

        return $identity_number;
    }

    public function Counter_Payroll_Number($trxName)
    {
        $rows = $this->db->get_where($this->tbl_CounterDocNumber, array(
            "TrxName" => $trxName,
            "TrxYear" => date('Y'),
            // "month" => date('m'),
        ));
        // TrxName, TrxMonth, TrxYear, TrxDate, CurrDocNumber
        $length = 3;
        if ($rows->num_rows() > 0) {
            $row = $rows->row();
            $newCount = intval($row->CurrDocNumber) + 1;

            $this->db->where('TrxName', $trxName);
            $this->db->where('TrxYear', date('Y'));
            // $this->db->where('TrxMonth', date('m'));
            $this->db->update($this->tbl_CounterDocNumber, [
                'CurrDocNumber' => $newCount,
            ]);

            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = $string;
        } else {
            $this->db->insert($this->tbl_CounterDocNumber, [
                "TrxName" => $trxName,
                "TrxYear" => date('Y'),
                // "TrxMonth" => date('m'),
                "CurrDocNumber" => 1,
            ]);
            $newCount = 1;
            $string = substr(str_repeat(0, $length) . $newCount, -$length);
            $identity_number = $string;
        }

        return $identity_number;
    }

    public function terbilangRupiah($nilai)
    {
        $nilai = abs($nilai);
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " " . $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilangRupiah($nilai - 10) . " belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilangRupiah($nilai / 10) . " puluh" . $this->terbilangRupiah($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " seratus" . $this->terbilangRupiah($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilangRupiah($nilai / 100) . " ratus" . $this->terbilangRupiah($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " seribu" . $this->terbilangRupiah($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilangRupiah($nilai / 1000) . "ribu" . $this->terbilangRupiah($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilangRupiah($nilai / 1000000) . " juta" . $this->terbilangRupiah($nilai % 1000000);
        } else if ($nilai < 1000000000000) {
            $temp = $this->terbilangRupiah($nilai / 1000000000) . " milyar" . $this->terbilangRupiah(fmod($nilai, 1000000000));
        } else if ($nilai < 1000000000000000) {
            $temp = $this->terbilangRupiah($nilai / 1000000000000) . " triliun" . $this->terbilangRupiah(fmod($nilai, 1000000000000));
        }
        return $temp;
    }

    public function konversiHari($englishDay)
    {
        $englishDays = array(
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu'
        );

        if (isset($englishDays[$englishDay])) {
            return $englishDays[$englishDay];
        } else {
            return 'Hari tidak valid';
        }
    }

    public function Fn_Get_Nominal_Simpanan_SukaRela($nik)
    {
        $Is_Member = $this->db->get_where($this->tmst_member_koperasi, ['ID_Access' => $nik]);
        if ($Is_Member->num_rows() > 0) {
            $Row_Member = $Is_Member->row();
            $nominal = $Row_Member->Deposito_Perbulan;
        } else {
            $nominal = 0;
        }

        return floatval($nominal);
    }

    public function Fn_Get_Nominal_Iuran_Bpjstk($nik)
    {
        $SqlBpjstk = $this->db->get_where('qview_mst_iuran_bpjskt', ['ID_Access' => $nik]);
        if ($SqlBpjstk->num_rows() > 0) {
            $RowBpjstk = $SqlBpjstk->row();
            return [
                'nominal' => floatval($RowBpjstk->Nominal),
                'kode' => $RowBpjstk->Kode_Potongan
            ];
        } else {
            return [
                'nominal' => 0,
                'kode' => 'BPJS_NOL'
            ];
        }
    }

    function translateMonth($englishMonth)
    {
        $months = array(
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        );

        return $months[$englishMonth];
    }

    function generate_sql_spesific_history_approval($username, $from, $until)
    {
        return "Select distinct TAccCashBookReq_Header.CBReq_No, Type, Document_Date, Document_Number, TAccCashBookReq_Header.Acc_ID, Descript, Amount, baseamount, curr_rate,
        Approval_Status, CBReq_Status, Paid_Status, Creation_DateTime, Created_By, First_Name AS Created_By_Name, Last_Update, Update_By, TAccCashBookReq_Header.Currency_Id,
        TAccCashBookReq_Header.Approve_Date, Ttrx_Cbr_Approval.IsAppvStaff, Ttrx_Cbr_Approval.Status_AppvStaff, Ttrx_Cbr_Approval.AppvStaff_By, Ttrx_Cbr_Approval.AppvStaff_Name,
        Ttrx_Cbr_Approval.AppvStaff_At, Ttrx_Cbr_Approval.IsAppvChief, Ttrx_Cbr_Approval.Status_AppvChief, Ttrx_Cbr_Approval.AppvChief_By, Ttrx_Cbr_Approval.AppvChief_Name,
        Ttrx_Cbr_Approval.AppvChief_At, Ttrx_Cbr_Approval.IsAppvAsstManager, Ttrx_Cbr_Approval.Status_AppvAsstManager, Ttrx_Cbr_Approval.AppvAsstManager_By,
        Ttrx_Cbr_Approval.AppvAsstManager_Name, Ttrx_Cbr_Approval.AppvAsstManager_At, Ttrx_Cbr_Approval.IsAppvManager, Ttrx_Cbr_Approval.Status_AppvManager, Ttrx_Cbr_Approval.AppvManager_By,
        Ttrx_Cbr_Approval.AppvManager_Name, Ttrx_Cbr_Approval.AppvManager_At, Ttrx_Cbr_Approval.IsAppvSeniorManager, Ttrx_Cbr_Approval.Status_AppvSeniorManager,
        Ttrx_Cbr_Approval.AppvSeniorManager_By, Ttrx_Cbr_Approval.AppvSeniorManager_Name, Ttrx_Cbr_Approval.AppvSeniorManager_At, Ttrx_Cbr_Approval.IsAppvGeneralManager,
        Ttrx_Cbr_Approval.Status_AppvGeneralManager, Ttrx_Cbr_Approval.AppvGeneralManager_By, Ttrx_Cbr_Approval.AppvGeneralManager_Name, Ttrx_Cbr_Approval.AppvGeneralManager_At,
        Ttrx_Cbr_Approval.IsAppvAdditional,Ttrx_Cbr_Approval.Status_AppvAdditional,Ttrx_Cbr_Approval.AppvAdditional_By,Ttrx_Cbr_Approval.AppvAdditional_Name,Ttrx_Cbr_Approval.AppvAdditional_At,
        Ttrx_Cbr_Approval.IsAppvDirector, Ttrx_Cbr_Approval.Status_AppvDirector, Ttrx_Cbr_Approval.AppvDirector_By, Ttrx_Cbr_Approval.AppvDirector_Name, Ttrx_Cbr_Approval.AppvDirector_At,
        Ttrx_Cbr_Approval.IsAppvPresidentDirector, Ttrx_Cbr_Approval.Status_AppvPresidentDirector, Ttrx_Cbr_Approval.AppvPresidentDirector_By, Ttrx_Cbr_Approval.AppvPresidentDirector_Name,
        Ttrx_Cbr_Approval.AppvPresidentDirector_At, Ttrx_Cbr_Approval.IsAppvFinanceDirector, Ttrx_Cbr_Approval.Status_AppvFinanceDirector, Ttrx_Cbr_Approval.AppvFinanceDirector_By,
        Ttrx_Cbr_Approval.AppvFinanceDirector_Name, Ttrx_Cbr_Approval.AppvFinanceDirector_At, Ttrx_Cbr_Approval.UserName_User, Ttrx_Cbr_Approval.Rec_Created_At,
        Ttrx_Cbr_Approval.UserDivision
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
        AND Ttrx_Cbr_Approval.CBReq_No IS NOT NULL
        AND (
                (IsAppvFinanceDirector = 1 and Status_AppvFinanceDirector <> 0 and AppvFinanceDirector_By = '$username') OR
                (IsAppvPresidentDirector = 1 and Status_AppvPresidentDirector <> 0 and AppvPresidentDirector_By = '$username') OR
                (IsAppvDirector = 1 and Status_AppvDirector <> 0 and AppvDirector_By = '$username') OR
                (IsAppvAdditional = 1 and Status_AppvAdditional <> 0 and AppvAdditional_By = '$username') OR
                (IsAppvGeneralManager = 1 and Status_AppvGeneralManager <> 0 and AppvGeneralManager_By = '$username') OR
                (IsAppvSeniorManager = 1 and Status_AppvSeniorManager <> 0 and AppvSeniorManager_By = '$username') OR
                (IsAppvManager = 1 and Status_AppvManager <> 0 and AppvManager_By = '$username') OR
                (IsAppvAsstManager = 1 and Status_AppvAsstManager <> 0 and AppvAsstManager_By = '$username')
            ) ";
        // ORDER BY TAccCashBookReq_Header.Document_Date DESC,TAccCashBookReq_Header.CBReq_No DESC 
    }

    function record_history_approval($CBReq_No)
    {
        // 1. Hitung SubmissionCount dari tabel History (Logika ini sudah benar)
        $count = $this->db
            ->where('CBReq_No', $CBReq_No)
            ->from('Thst_Trx_Cbr_Approval')
            ->count_all_results();

        $CountTry = $count + 1;

        // 2. Query INSERT INTO ... SELECT dengan Query Binding
        $sql = "INSERT INTO Thst_Trx_Cbr_Approval (
        SubmissionCount, SysID, CBReq_No, SysId_Step, IsAppvStaff, Status_AppvStaff, AppvStaff_By, AppvStaff_Name, AppvStaff_At, 
        IsAppvChief, Status_AppvChief, AppvChief_By, AppvChief_Name, AppvChief_At, IsAppvAsstManager, 
        Status_AppvAsstManager, AppvAsstManager_By, AppvAsstManager_Name, AppvAsstManager_At, IsAppvManager, 
        Status_AppvManager, AppvManager_By, AppvManager_Name, AppvManager_At, IsAppvSeniorManager, 
        Status_AppvSeniorManager, AppvSeniorManager_By, AppvSeniorManager_Name, AppvSeniorManager_At, 
        IsAppvGeneralManager, Status_AppvGeneralManager, AppvGeneralManager_By, AppvGeneralManager_Name, 
        AppvGeneralManager_At, IsAppvAdditional, Status_AppvAdditional, AppvAdditional_By, AppvAdditional_Name, 
        AppvAdditional_At, IsAppvDirector, Status_AppvDirector, AppvDirector_By, AppvDirector_Name, AppvDirector_At, 
        IsAppvPresidentDirector, Status_AppvPresidentDirector, AppvPresidentDirector_By, AppvPresidentDirector_Name, 
        AppvPresidentDirector_At, IsAppvFinanceDirector, Status_AppvFinanceDirector, AppvFinanceDirector_By, 
        AppvFinanceDirector_Name, AppvFinanceDirector_At, UserName_User, UserDivision, Rec_Created_At, 
        IsAppvFinancePerson, Status_AppvFinancePerson, AppvFinancePerson_By, AppvFinancePerson_Name, 
        AppvFinancePerson_At, Legitimate, Doc_Legitimate_Pos_On
    )
    SELECT
        ? AS SubmissionCount, T1.SysID, T1.CBReq_No, T1.SysId_Step, T1.IsAppvStaff, T1.Status_AppvStaff, T1.AppvStaff_By, T1.AppvStaff_Name, T1.AppvStaff_At, 
        T1.IsAppvChief, T1.Status_AppvChief, T1.AppvChief_By, T1.AppvChief_Name, T1.AppvChief_At, T1.IsAppvAsstManager, 
        T1.Status_AppvAsstManager, T1.AppvAsstManager_By, T1.AppvAsstManager_Name, T1.AppvAsstManager_At, T1.IsAppvManager, 
        T1.Status_AppvManager, T1.AppvManager_By, T1.AppvManager_Name, T1.AppvManager_At, T1.IsAppvSeniorManager, 
        T1.Status_AppvSeniorManager, T1.AppvSeniorManager_By, T1.AppvSeniorManager_Name, T1.AppvSeniorManager_At, 
        T1.IsAppvGeneralManager, T1.Status_AppvGeneralManager, T1.AppvGeneralManager_By, T1.AppvGeneralManager_Name, 
        T1.AppvGeneralManager_At, T1.IsAppvAdditional, T1.Status_AppvAdditional, T1.AppvAdditional_By, T1.AppvAdditional_Name, 
        T1.AppvAdditional_At, T1.IsAppvDirector, T1.Status_AppvDirector, T1.AppvDirector_By, T1.AppvDirector_Name, T1.AppvDirector_At, 
        T1.IsAppvPresidentDirector, T1.Status_AppvPresidentDirector, T1.AppvPresidentDirector_By, T1.AppvPresidentDirector_Name, 
        T1.AppvPresidentDirector_At, T1.IsAppvFinanceDirector, T1.Status_AppvFinanceDirector, T1.AppvFinanceDirector_By, 
        T1.AppvFinanceDirector_Name, T1.AppvFinanceDirector_At, T1.UserName_User, T1.UserDivision, T1.Rec_Created_At, 
        T1.IsAppvFinancePerson, T1.Status_AppvFinancePerson, T1.AppvFinancePerson_By, T1.AppvFinancePerson_Name, 
        T1.AppvFinancePerson_At, T1.Legitimate, T1.Doc_Legitimate_Pos_On
    FROM 
        dbo.Ttrx_Cbr_Approval AS T1
    WHERE T1.CBReq_No = ?
    ";

        // var_dump($sql);
        // die;

        // ✅ Buat array binding
        $bindings = [(int)$CountTry, $CBReq_No];

        // ✅ Eksekusi query dengan binding
        $result = $this->db->query($sql, $bindings);

        return $result;
    }
}
