<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $reports = [];
        $users = [];
        $itemCount = 0;
        if ($error === '') {
            $rows = $this->Vpr_audit_model->exceptions($from, $until);
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
        } elseif ($format !== 'screen') {
            show_error($error, 400);
        }

        $data = [
            'page_title' => 'Audit Akses Vendor Pricing Request',
            'page_content' => 'Report/MIS/vpr_audit',
            'script_page' => '',
            'start' => is_string($start) ? $start : '',
            'end' => is_string($end) ? $end : '',
            'error' => $error,
            'reports' => $reports,
            'userCount' => count($users),
            'itemCount' => $itemCount,
            'print' => $format === 'pdf',
        ];
        if ($format === 'excel') {
            $this->exportExcel($data);
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

    private function exportExcel(array $data)
    {
        $book = new Spreadsheet();
        $header = $book->getActiveSheet()->setTitle('Temuan VPR');
        $detail = $book->createSheet()->setTitle('Detail VPR');
        $headerRows = [];
        $detailRows = [];
        foreach ($data['reports'] as $report) {
            $h = $report['header'];
            $headerRows[] = [
                $h['VPR_NUMBER'], $h['DATE_ADD'], $h['USER_ADD'], $h['Input_Name'], $h['Finding'],
                $h['Access_Start'], $h['Access_End'], $h['VPR_DATE'], $h['ACCOUNT_ID'], $h['Vendor_Name'],
                $h['VPR_STATUS'], $h['APPROVAL_STATUS'], $h['LOCATION_ID'], $h['VPR_NOTES'], count($report['items']),
            ];
            foreach ($report['items'] as $d) {
                $detailRows[] = [
                    $h['VPR_NUMBER'], $h['DATE_ADD'], $h['USER_ADD'], $h['Input_Name'], $h['Vendor_Name'],
                    $h['Finding'], $d['ITEM_CODE'], $d['ITEM_TYPE'], $d['Dimension_ID'], $d['currency_id'],
                    $d['PRICE'] === null ? null : (float) $d['PRICE'], $d['EFFECTIVE_DATE'],
                ];
            }
        }
        $period = 'Periode input ' . $data['start'] . ' s.d. ' . $data['end'];
        $this->writeSheet($header, $period, [
            'No. VPR', 'Waktu input', 'User ID', 'Nama input', 'Temuan', 'Mulai izin terakhir', 'Akhir izin terakhir',
            'Tanggal VPR', 'ID vendor', 'Vendor', 'Status VPR', 'Status approval', 'Lokasi', 'Catatan', 'Jumlah item',
        ], $headerRows, 4);
        $this->writeSheet($detail, $period, [
            'No. VPR', 'Waktu input', 'User ID', 'Nama input', 'Vendor', 'Temuan',
            'Kode item', 'Tipe item', 'Dimensi', 'Mata uang', 'Harga', 'Tanggal efektif',
        ], $detailRows, 5);
        if ($detailRows) {
            $detail->getStyle('K4:K' . (count($detailRows) + 3))->getNumberFormat()->setFormatCode('#,##0.00##');
        }
        $this->output->set_content_type('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->output->set_header('Content-Disposition: attachment; filename="Audit-VPR-' . $data['start'] . '_' . $data['end'] . '.xlsx"');
        ob_start();
        try {
            (new Xlsx($book))->save('php://output');
            $this->output->set_output(ob_get_contents());
        } finally {
            ob_end_clean();
            $book->disconnectWorksheets();
        }
    }

    private function writeSheet($sheet, $period, array $columns, array $rows, int $findingColumn)
    {
        $lastColumn = Coordinate::stringFromColumnIndex(count($columns));
        $sheet->setCellValue('A1', $period);
        foreach ($columns as $index => $label) {
            $column = Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($column . '3', $label);
            $sheet->getColumnDimension($column)->setWidth(max(16, strlen($label) + 5));
        }
        foreach ($rows as $index => $row) {
            foreach ($row as $offset => $value) {
                if ($value === null) {
                    continue;
                }
                $cell = Coordinate::stringFromColumnIndex($offset + 1) . ($index + 4);
                if (is_int($value) || is_float($value)) {
                    $sheet->setCellValue($cell, $value);
                } else {
                    $sheet->setCellValueExplicit($cell, (string) $value, DataType::TYPE_STRING);
                }
                if ($offset === $findingColumn && $value === Vpr_audit_model::NO_ACCESS_FINDING) {
                    $style = $sheet->getStyle($cell);
                    $style->getFill()->setFillType('solid')->getStartColor()->setARGB('FFB42318');
                    $style->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
                }
            }
        }
        $sheet->getStyle('A3:' . $lastColumn . '3')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A3:' . $lastColumn . '3')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF24536D');
        $sheet->setAutoFilter('A3:' . $lastColumn . (count($rows) + 3));
        $sheet->freezePane('A4');
    }
}
