<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vpr_audit extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
        if (!$this->session->userdata('sys_sba_isAdm')) {
            show_error('Laporan ini hanya untuk administrator.', 403);
        }
        $this->load->model('Vpr_audit_model');
    }

    public function index()
    {
        $zone = new DateTimeZone('Asia/Jakarta');
        $today = new DateTimeImmutable('today', $zone);
        $start = $this->input->get('start') ?? $today->modify('first day of this month')->format('Y-m-d');
        $end = $this->input->get('end') ?? $today->format('Y-m-d');
        $format = $this->input->get('format') ?? 'screen';
        if (!in_array($format, ['screen', 'pdf', 'excel'], true)) {
            show_404();
        }

        $from = $this->parseDate($start, $zone);
        $until = $this->parseDate($end, $zone);
        $error = !$from || !$until || $from > $until
            ? 'Isi tanggal dalam format YYYY-MM-DD. Tanggal mulai harus sama atau lebih awal dari tanggal akhir.'
            : '';
        if ($error !== '' && $format !== 'screen') {
            show_error($error, 400);
        }
        try {
            $report = $error === ''
                ? $this->Vpr_audit_model->report($from, $until)
                : ['reports' => [], 'userCount' => 0, 'itemCount' => 0];
        } catch (Throwable $e) {
            log_message('error', 'VPR audit web report: ' . $e->getMessage());
            show_error('Data audit Vendor Pricing Request tidak dapat dibaca.', 500);
        }

        $data = [
            'page_title' => 'Audit Akses Vendor Pricing Request',
            'page_content' => 'Report/MIS/vpr_audit',
            'script_page' => '',
            'start' => is_string($start) ? $start : '',
            'end' => is_string($end) ? $end : '',
            'error' => $error,
            'print' => $format === 'pdf',
        ] + $report;
        if ($format === 'excel') {
            $this->load->library('Vpr_audit_excel');
            $filename = Vpr_audit_excel::filename($data['start'], $data['end']);
            $this->output->set_content_type(Vpr_audit_excel::MIME_TYPE)
                ->set_header('Content-Disposition: attachment; filename="' . $filename . '"')
                ->set_output($this->vpr_audit_excel->render($data['reports'], $data['start'], $data['end']));
            return;
        }
        if ($data['print']) {
            $this->load->view('Report/MIS/vpr_audit', $data);
            return;
        }
        $this->load->view('layout', $data);
    }

    private function parseDate($value, DateTimeZone $zone)
    {
        if (!is_string($value) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $value)
            || $value < '1753-01-01' || $value >= '9999-12-31') {
            return null;
        }
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value, $zone);
        return $date && $date->format('Y-m-d') === $value ? $date : null;
    }
}
