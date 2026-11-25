<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vpr_audit_model extends CI_Model
{
    private const FUNCTION_ID = 'ERSTD08128';
    public const NO_ACCESS_FINDING = 'Tidak ada izin tercatat sebelum input';

    public function report(DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        $rows = $this->exceptions($start, $end);
        $reports = [];
        $users = [];
        $itemCount = 0;
        while ($row = $rows->unbuffered_row('array')) {
            $number = $row['VPR_NUMBER'];
            if (!isset($reports[$number])) {
                $reports[$number] = ['header' => $row, 'items' => []];
            }
            if ($row['ITEM_CODE'] !== null) {
                $reports[$number]['items'][] = $row;
                $itemCount++;
            }
            if ((int) $row['USER_ADD'] > 0) {
                $users[$row['USER_ADD']] = true;
            }
        }
        $rows->free_result();
        return ['reports' => $reports, 'userCount' => count($users), 'itemCount' => $itemCount];
    }

    private function exceptions(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        $sql = "
            WITH activities AS (
                SELECT H.VPR_NUMBER, H.VPR_DATE, H.VPR_NOTES, H.ACCOUNT_ID, H.VPR_STATUS,
                    H.LOCATION_ID, H.APPROVAL_STATUS, H.USER_ADD, H.DATE_ADD
                FROM dbo.TaccPriceVendor_Header H
                WHERE H.DATE_ADD >= ? AND H.DATE_ADD < ?
            )
            SELECT H.*, U.First_Name AS Input_Name, A.Account_Name AS Vendor_Name,
                GrantLog.Changed_At AS Access_Start, GrantLog.Access_End,
                CASE
                    WHEN H.USER_ADD IS NULL OR H.USER_ADD <= 0 THEN 'User pencatat tidak tersedia'
                    WHEN GrantLog.Log_ID IS NULL THEN ?
                    ELSE 'Input di luar rentang izin'
                END AS Finding,
                D.ITEM_CODE, D.ITEM_TYPE, D.Dimension_ID, D.currency_id, D.PRICE, D.EFFECTIVE_DATE
            FROM activities H
            OUTER APPLY (
                SELECT TOP (1) L.Log_ID, L.Changed_At, E.Access_End
                FROM dbo.TblTemporaryAccessLog L
                INNER JOIN dbo.TUserGroupFuncL G ON G.UserGroupFuncL_ID = L.UserGroupFuncL_ID
                CROSS APPLY (VALUES (
                    CASE WHEN L.Reverted_At < L.Revert_At THEN L.Reverted_At ELSE L.Revert_At END
                )) E(Access_End)
                WHERE G.sf_ufunc_id = ? AND L.New_Access = 'delete'
                    AND H.USER_ADD > 0 AND LTRIM(RTRIM(L.UserAsk)) = CONVERT(varchar(11), H.USER_ADD)
                    AND L.Changed_At <= H.DATE_ADD
                ORDER BY CASE WHEN H.DATE_ADD < E.Access_End THEN 0 ELSE 1 END,
                    L.Changed_At DESC, L.Log_ID DESC
            ) GrantLog
            LEFT JOIN dbo.TUserPersonal U ON U.User_ID = H.USER_ADD
            LEFT JOIN dbo.TAccount A ON A.Account_ID = H.ACCOUNT_ID
            LEFT JOIN dbo.TaccPriceVendor_Detail D ON D.VPR_NUMBER = H.VPR_NUMBER
            WHERE GrantLog.Access_End IS NULL OR H.DATE_ADD >= GrantLog.Access_End
            ORDER BY H.DATE_ADD DESC, H.VPR_NUMBER, D.ITEM_CODE, D.Dimension_ID";

        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $query = $this->db->query($sql, [
                $start->format('Ymd'), $end->modify('+1 day')->format('Ymd'), self::NO_ACCESS_FINDING, self::FUNCTION_ID,
            ]);
        } finally {
            $this->db->db_debug = $debug;
        }
        if ($query === false) {
            throw new RuntimeException('Data audit Vendor Pricing Request tidak dapat dibaca.');
        }
        return $query;
    }
}
