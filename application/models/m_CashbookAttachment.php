<?php
defined('BASEPATH') or exit('No direct script access allowed');

class m_CashbookAttachment extends CI_Model
{
    private $companyId = 2;

    private function query($sql, $bindings = [])
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $result = $this->db->query($sql, $bindings);
            if ($result === false) { throw new RuntimeException('Cashbook attachment database query failed.'); }
            return $result;
        } finally { $this->db->db_debug = $debug; }
    }

    public function header($code, $lock = false)
    {
        if (!is_string($code) || trim($code) === '' || strlen($code) > 50) {
            throw new InvalidArgumentException('Nomor header cashbook tidak valid.');
        }
        $header = $this->query('SELECT Company_ID, JournalH_Code, CashBookType, CashBookDate
            FROM TAccCashBookHeader' . ($lock ? ' WITH (UPDLOCK, HOLDLOCK)' : '') . '
            WHERE Company_ID = ? AND JournalH_Code = ?', [$this->companyId, $code])->row_array();
        if (!$header) { throw new OutOfBoundsException('Header cashbook tidak ditemukan.'); }
        $header['CashBookType'] = strtoupper(trim($header['CashBookType']));
        return $header;
    }

    public function listing($code)
    {
        $this->header($code);
        return $this->query('SELECT Attachment_ID, Original_Name, Mime_Type, File_Size, Notes, Created_By, Created_At
            FROM dbo.Ttrx_Cashbook_Attachment WHERE Company_ID = ? AND JournalH_Code = ?
            ORDER BY Attachment_ID DESC', [$this->companyId, $code])->result_array();
    }

    public function find($id)
    {
        if (!is_string($id) || !ctype_digit($id)) { throw new InvalidArgumentException('ID lampiran tidak valid.'); }
        $file = $this->query('SELECT A.* FROM dbo.Ttrx_Cashbook_Attachment A
            INNER JOIN TAccCashBookHeader H ON H.Company_ID = A.Company_ID AND H.JournalH_Code = A.JournalH_Code
            WHERE A.Company_ID = ? AND A.Attachment_ID = ?', [$this->companyId, $id])->row_array();
        if (!$file) { throw new OutOfBoundsException('Lampiran tidak ditemukan.'); }
        return $file;
    }

    public function save($code, $files, $notes, $username, $storage)
    {
        $validated = $storage->validate_batch($files, $notes);
        $paths = [];
        if (!$this->db->trans_begin()) { throw new RuntimeException('Transaksi lampiran tidak dapat dimulai.'); }
        try {
            // Re-read the authoritative date/type and keep them stable during upload.
            $header = $this->header($code, true);
            foreach ($validated as $file) {
                $path = $storage->store($header, $file);
                $paths[] = $path;
                $this->query('INSERT INTO dbo.Ttrx_Cashbook_Attachment
                    (Company_ID, JournalH_Code, CashBookType, Header_Date, Original_Name, Relative_Path,
                     Mime_Type, File_Size, Notes, Created_By)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                    [$this->companyId, $header['JournalH_Code'], $header['CashBookType'], substr($header['CashBookDate'], 0, 10),
                        $file['name'], $path, $file['mime'], $file['size'], $file['notes'], $username]);
            }
            if (!$this->db->trans_status() || !$this->db->trans_commit()) {
                throw new RuntimeException('Lampiran gagal disimpan.');
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            foreach ($paths as $path) {
                try { $storage->remove_new($path); }
                catch (Throwable $cleanup) { log_message('error', $cleanup->getMessage()); }
            }
            throw $e;
        }
        return count($validated);
    }
}
