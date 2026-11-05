<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Cashbook_excel
{
    public function build($headers, array $filters, array $statuses)
    {
        $types = ['BD'=>'Bank Payment','BR'=>'Bank Receipt','CD'=>'Cash Payment','CR'=>'Cash Receipt'];
        $type = $filters['entry_type'] ?? 'BD';
        if (!is_string($type) || !isset($types[$type])) { throw new InvalidArgumentException('Tipe cashbook tidak valid.'); }
        $payee = in_array($type, ['BD','CD'], true) ? 'Payee' : 'Payer';
        $columns = [1=>'No.', 2=>'Document No.', 3=>$payee, 4=>'Date', 5=>'Account', 6=>'Currency',
            7=>$types[$type] ?? 'Amount', 8=>'Memo', 9=>'Status', 10=>'Approval Status', 11=>'Void',
            12=>'Cash Flow Type', 13=>'Account (Detail)', 14=>'Deb/Cred', 15=>'Amount Detail', 16=>'Detail Desc', 17=>'Reff No.'];
        $selected = $filters['visible_columns'] ?? [1,2,3,4,5,6,7,9,10,11,13,14,15,16,17];
        if (!is_array($selected) || !$selected || count($selected) > count($columns)) {
            throw new InvalidArgumentException('Pilihan kolom export tidak valid.');
        }
        $indexes = [];
        foreach ($selected as $index) {
            if (!is_scalar($index) || !ctype_digit((string) $index) || !isset($columns[(int) $index])) {
                throw new InvalidArgumentException('Kolom export tidak dikenali.');
            }
            $indexes[(int) $index] = (int) $index;
        }
        $indexes = array_values($indexes);
        $labels = ['DOC'=>[], 'APPROVAL'=>[]];
        foreach ($statuses as $status) {
            $remark = preg_replace('/^eHRM/i', '', trim((string) $status['STATUS_REMARK']));
            $labels[$status['KEY_ID']][(string) $status['STATUS_CODE']] = preg_replace('/([a-z])([A-Z])/', '$1 $2', $remark);
        }
        $book = new Spreadsheet();
        $book->getProperties()->setCreator('ESBA')->setTitle('List Of Cashbook Entry');
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Cashbook Entry');
        $book->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $lastColumn = Coordinate::stringFromColumnIndex(count($indexes));
        $sheet->setCellValue('A1', 'List Of Cashbook Entry');
        $sheet->setCellValueExplicit('A2', ($types[$type] ?? '') . ' | ' . ($filters['date_from'] ?? date('Y-m-01')) .
            ' - ' . ($filters['date_to'] ?? date('Y-m-t')), DataType::TYPE_STRING);
        if (count($indexes) > 1) { $sheet->mergeCells('A1:' . $lastColumn . '1'); $sheet->mergeCells('A2:' . $lastColumn . '2'); }
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15);
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(22);
        foreach ($indexes as $position=>$index) {
            $column = Coordinate::stringFromColumnIndex($position + 1);
            $sheet->setCellValue($column . '3', $columns[$index]);
            $width = [1=>6,2=>25,3=>30,4=>14,5=>34,6=>11,7=>20,8=>42,9=>14,10=>19,11=>9,12=>28,13=>32,14=>11,15=>20,16=>42,17=>25][$index];
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->getStyle('A3:' . $lastColumn . '3')->applyFromArray([
            'font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'3B6D8C']],
            'alignment'=>['vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(30);
        $number = 0;
        $line = 4;
        try {
            foreach ($headers as $header) {
                $number++;
                $details = $header['Details'] ?: [[]];
                $end = $line + count($details) - 1;
                if ($end > 1048576) { throw new RuntimeException('Hasil melebihi batas baris Excel. Persempit rentang tanggal.'); }
                $values = [1=>$number, 2=>$header['JournalH_Code'], 3=>$header['Payor_Payee'],
                    4=>substr((string) $header['CashBookDate'],0,10),
                    5=>$header['Account_Name'] . (!empty($header['Clearing_Account']) ? ' (' . $header['Clearing_Account'] . ')' : ''),
                    6=>$header['Currency_ID'], 7=>$header['Total_Amount'], 8=>$header['Memo'],
                    9=>$labels['DOC'][(string) $header['Status']] ?? (string) $header['Status'],
                    10=>$labels['APPROVAL'][(string) $header['Approval_Status']] ?? (string) $header['Approval_Status'],
                    11=>(int) $header['IsVoid'] === 1 ? 'Yes' : 'No',
                    12=>empty($header['CashFlow_Type']) ? 'None' : implode(' / ',array_filter([$header['Type_NameEn'],$header['CashFlow_Name']]))];
                foreach ($indexes as $position=>$index) {
                    $column = Coordinate::stringFromColumnIndex($position + 1);
                    if ($index <= 12) {
                        $this->cell($sheet, $column . $line, $values[$index] ?? '', $index);
                        if ($end > $line) { $sheet->mergeCells($column . $line . ':' . $column . $end); }
                    } else {
                        $key = [13=>'Account_Name',14=>'DebCred',15=>'Amount',16=>'Description',17=>'CBR_No'][$index];
                        foreach ($details as $offset=>$detail) { $this->cell($sheet, $column . ($line + $offset), $detail[$key] ?? '', $index); }
                    }
                }
                $sheet->getStyle('A' . $line . ':' . $lastColumn . $end)->applyFromArray([
                    'alignment'=>['vertical'=>Alignment::VERTICAL_CENTER,'wrapText'=>true],
                    'borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'D7E1E8']]],
                ]);
                if ((int) $header['IsVoid'] === 1) {
                    $sheet->getStyle('A' . $line . ':' . $lastColumn . $end)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FCD9E2');
                }
                $line = $end + 1;
            }
            if ($number === 0) { $sheet->setCellValue('A4', 'Tidak ada data sesuai filter.'); }
            $sheet->freezePane('C4');
            $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0)->setRowsToRepeatAtTopByStartAndEnd(1,3);
            return $book;
        } catch (Throwable $e) {
            $book->disconnectWorksheets();
            throw $e;
        }
    }

    private function cell($sheet, $coordinate, $value, $index)
    {
        if ($value !== null && $value !== '' && in_array($index, [1,7,15], true)) {
            $sheet->setCellValueExplicit($coordinate, (float) $value, DataType::TYPE_NUMERIC);
            if ($index !== 1) { $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('#,##0.00########'); }
        } elseif ($index === 4 && $value) {
            $sheet->setCellValueExplicit($coordinate, Date::PHPToExcel(new DateTime($value)), DataType::TYPE_NUMERIC);
            $sheet->getStyle($coordinate)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        } else {
            // Document numbers, notes and descriptions are text, never spreadsheet formulas.
            $sheet->setCellValueExplicit($coordinate, (string) ($value ?? ''), DataType::TYPE_STRING);
        }
    }
}
