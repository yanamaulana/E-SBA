<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__ . '/../../system/');
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../application/libraries/Cashbook_excel.php';
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
$header = ['JournalH_Code'=>'BDJ-CONTOH-001','Payor_Payee'=>'PT Contoh','CashBookDate'=>'2026-08-10 00:00:00',
    'Account_Name'=>'Bank Account','Clearing_Account'=>'','Currency_ID'=>'IDR','Total_Amount'=>'248400000.0000',
    'Memo'=>'Hidden memo','Status'=>3,'Approval_Status'=>3,'IsVoid'=>0,'CashFlow_Type'=>0,'Type_NameEn'=>null,'CashFlow_Name'=>null,
    'Details'=>[]];
for ($i=1;$i<=3;$i++) { $header['Details'][] = ['Account_Name'=>'Trade Accounts Payable','DebCred'=>'D','Amount'=>'82800000.0000',
    'Description'=>'=HYPERLINK("https://example.test")','CBR_No'=>'CBR-CONTOH-' . $i]; }
$void = array_replace($header,['JournalH_Code'=>'BDJ-VOID','IsVoid'=>1,'Details'=>[]]);
$statuses = [['KEY_ID'=>'DOC','STATUS_CODE'=>'3','STATUS_REMARK'=>'Closed'], ['KEY_ID'=>'APPROVAL','STATUS_CODE'=>'3','STATUS_REMARK'=>'eHRMApproved']];
$filters = ['entry_type'=>'BD','date_from'=>'2026-08-01','date_to'=>'2026-08-31'];
$builder = new Cashbook_excel();
$book = $builder->build([$header,$void],$filters,$statuses);
$temp = tempnam(sys_get_temp_dir(),'esba_excel_test_');
try {
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book);
    $writer->setPreCalculateFormulas(false); $writer->save($temp); $book->disconnectWorksheets();
    $read = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp);
    $sheet = $read->getActiveSheet();
    check($sheet->getHighestDataRow() === 7, 'Three detail rows plus a header without details');
    check(in_array('B4:B6',$sheet->getMergeCells(),true), 'Header cells must span all details');
    check($sheet->getCell('G4')->getValue() == 248400000 && $sheet->getCell('G5')->getValue() === null, 'Header amount must not repeat per detail');
    check($sheet->getCell('D4')->getFormattedValue() === '2026-08-10', 'Date format YYYY-MM-DD');
    check($sheet->getCell('K4')->getValue() === 'Trade Accounts Payable' && $sheet->getCell('L4')->getValue() === 'D', 'Account English and DebCred columns');
    check($sheet->getCell('M4')->getDataType() === 'n' && $sheet->getCell('M4')->getValue() == 82800000, 'Detail amounts must remain numeric');
    check($sheet->getCell('N4')->getDataType() === 's', 'User-entered formulas must export as text');
    check($sheet->getCell('O6')->getValue() === 'CBR-CONTOH-3', 'All references retained');
    check($sheet->getCell('I4')->getValue() === 'Approved' && $sheet->getCell('H4')->getValue() === 'Closed', 'Readable statuses');
    check($sheet->getCell('B7')->getValue() === 'BDJ-VOID' && $sheet->getStyle('A7')->getFill()->getStartColor()->getRGB() === 'FCD9E2', 'Void highlight retained');
    $read->disconnectWorksheets();
    $subset = $builder->build([$header], $filters + ['visible_columns'=>[2,7,13,14,15]],$statuses);
    check($subset->getActiveSheet()->getHighestDataColumn() === 'E', 'Export only selected visible columns');
    check($subset->getActiveSheet()->getCell('A4')->getValue() === 'BDJ-CONTOH-001','Column order follows selection');
    $subset->disconnectWorksheets();
    $empty = $builder->build([],$filters,$statuses);
    check($empty->getActiveSheet()->getCell('A4')->getValue() === 'Tidak ada data sesuai filter.', 'Empty export readable');
    $empty->disconnectWorksheets();
    try { $builder->build([], $filters+['visible_columns'=>[0]], $statuses); throw new LogicException('Hidden checkbox exported'); }
    catch (InvalidArgumentException $expected) {}
    echo "Excel tests passed: real XLSX round-trip, grouped headers/details, numeric amounts, dates, formula safety, visibility and void styles.\n";
} finally { if (is_file($temp)) unlink($temp); }
