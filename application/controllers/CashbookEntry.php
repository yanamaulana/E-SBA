<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CashbookEntry extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        // Match the Accounting Person Approval menu, including direct AJAX access.
        if ($this->session->userdata('sys_sba_isAdm') != true &&
            $this->session->userdata('sys_sba_department') !== 'Accounting') {
            show_error('Anda tidak memiliki akses ke List Of Cahbook Entry.', 403);
        }
        $this->load->model('m_CashbookEntry', 'cashbook');
    }

    public function index()
    {
        $data = $this->cashbook->filter_options();
        $this->load->library('Cashbook_attachment_files');
        $data['upload_limits'] = $this->cashbook_attachment_files->limits();
        $data['page_title'] = 'List Of Cahbook Entry';
        $data['page_content'] = 'cashbook_entry/index';
        $data['script_page'] = '<script src="' . base_url('assets/Pages/cashbook_entry/index.js') . '?v=' .
            filemtime(FCPATH . 'assets/Pages/cashbook_entry/index.js') . '"></script>';
        $this->load->view('layout', $data);
    }

    public function DT_List()
    {
        $request = $this->input->post(NULL, false) ?: [];
        try {
            $response = $this->cashbook->datatable($request);
        } catch (InvalidArgumentException $e) {
            $this->output->set_status_header(400);
            $response = ['error' => $e->getMessage()];
        } catch (RuntimeException $e) {
            log_message('error', 'Cashbook entry: ' . $e->getMessage());
            $this->output->set_status_header(500);
            $response = ['error' => 'Data cashbook tidak dapat dimuat. Silakan coba kembali.'];
        }
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    private function attachment_services()
    {
        $this->load->model('m_CashbookAttachment', 'attachments');
        $this->load->library('Cashbook_attachment_files');
    }

    public function export_excel()
    {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405)->set_header('Allow: POST')
                ->set_content_type('application/json')->set_output(json_encode(['error'=>'Gunakan tombol Export All.']));
        }
        $file = null;
        $book = null;
        try {
            $raw = $this->input->post('filters', false);
            $filters = is_string($raw) ? json_decode($raw, true) : null;
            if (!is_array($filters)) { throw new InvalidArgumentException('Filter export tidak valid.'); }
            $this->load->library('Cashbook_excel');
            $options = $this->cashbook->filter_options();
            set_time_limit(300);
            $headers = $this->cashbook->export_headers($filters);
            $headers->rewind(); // Validate filters before using them in workbook metadata.
            $book = $this->cashbook_excel->build($headers, $filters, $options['statuses']);
            $file = tempnam(sys_get_temp_dir(), 'esba_xlsx_');
            if ($file === false) { throw new RuntimeException('File export tidak dapat dibuat.'); }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book);
            $writer->setPreCalculateFormulas(false);
            $writer->save($file);
            $book->disconnectWorksheets();
            $content = file_get_contents($file);
            if ($content === false) { throw new RuntimeException('File export tidak dapat dibaca.'); }
            return $this->output->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->set_header('Content-Disposition: attachment; filename="Cashbook_Entry_' . date('Ymd_His') . '.xlsx"')
                ->set_header('Cache-Control: private, no-store')->set_header('X-Content-Type-Options: nosniff')->set_output($content);
        } catch (Throwable $e) {
            $status = $e instanceof InvalidArgumentException ? 400 : 500;
            if ($status === 500) { log_message('error', 'Cashbook export: ' . $e->getMessage()); }
            return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode([
                'error'=>$status === 400 ? $e->getMessage() : 'Export gagal. Coba kembali atau persempit rentang tanggal.'
            ]));
        } finally {
            if ($book) { $book->disconnectWorksheets(); }
            if ($file && is_file($file)) { unlink($file); }
        }
    }

    private function attachment_error($error)
    {
        $status = $error instanceof InvalidArgumentException ? 400 : ($error instanceof OutOfBoundsException ? 404 : 500);
        if ($status === 500) { log_message('error', 'Cashbook attachment: ' . $error->getMessage()); }
        return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode([
            'error'=>$status === 500 ? 'Lampiran gagal diproses. Silakan coba kembali.' : $error->getMessage()
        ]));
    }

    public function attachments()
    {
        $this->attachment_services();
        try {
            $rows = $this->attachments->listing($this->input->get('journal', false));
            foreach ($rows as &$row) {
                $row['url'] = base_url('CashbookEntry/attachment_file/' . $row['Attachment_ID']);
            }
            unset($row);
            return $this->output->set_content_type('application/json')->set_output(json_encode(['data'=>$rows]));
        } catch (Throwable $e) { return $this->attachment_error($e); }
    }

    public function upload_attachments()
    {
        if ($this->input->method() !== 'post') {
            return $this->output->set_status_header(405)->set_header('Allow: POST')
                ->set_content_type('application/json')->set_output(json_encode(['error'=>'Gunakan POST untuk upload.']));
        }
        $this->attachment_services();
        try {
            $count = $this->attachments->save($this->input->post('journal', false), $_FILES['files'] ?? [],
                $this->input->post('notes', false), $this->session->userdata('sys_sba_username'), $this->cashbook_attachment_files);
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'message'=>$count . ' lampiran berhasil disimpan.', 'count'=>$count
            ]));
        } catch (Throwable $e) { return $this->attachment_error($e); }
    }

    public function attachment_file($id = '')
    {
        $this->attachment_services();
        try {
            $file = $this->attachments->find((string) $id);
            $path = $this->cashbook_attachment_files->resolve($file['Relative_Path']);
            $content = file_get_contents($path);
            if ($content === false) { throw new RuntimeException('File tidak dapat dibaca.'); }
            return $this->output->set_content_type($file['Mime_Type'])
                ->set_header('X-Content-Type-Options: nosniff')
                ->set_header('Cache-Control: private, no-store')
                ->set_header('Content-Disposition: inline; filename="attachment.' . pathinfo($path, PATHINFO_EXTENSION) .
                    '"; filename*=UTF-8\'\'' . rawurlencode($file['Original_Name']))
                ->set_output($content);
        } catch (Throwable $e) { return $this->attachment_error($e); }
    }
}
