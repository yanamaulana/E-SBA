<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Vpr_audit_excel
{
    public const MIME_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    public static function filename(string $start, string $end): string
    {
        return 'Audit-VPR-' . $start . '_' . $end . '.xlsx';
    }

    public function render(array $reports, string $start, string $end): string
    {
        $book = new Spreadsheet();
        $header = $book->getActiveSheet()->setTitle('Temuan VPR');
        $detail = $book->createSheet()->setTitle('Detail VPR');
        $headerRows = [];
        $detailRows = [];
        foreach ($reports as $report) {
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
        $period = 'Periode input ' . $start . ' s.d. ' . $end;
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
        ob_start();
        try {
            (new Xlsx($book))->save('php://output');
            return (string) ob_get_contents();
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
