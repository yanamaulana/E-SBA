<?php
// Run: php tests/cashbook/attachments.test.php. Uses temporary files and a fake database only.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', __DIR__ . '/../../system/');
define('APPPATH', __DIR__ . '/../../application/');
class CI_Model { public $db; }
function log_message($level, $message) {}
function check($condition, $message) { if (!$condition) throw new RuntimeException($message); }
require APPPATH . 'libraries/Cashbook_attachment_files.php';
require APPPATH . 'models/m_CashbookAttachment.php';
class TestStorage extends Cashbook_attachment_files {
    protected function is_uploaded($path) { return is_file($path); }
    protected function move_uploaded($from, $to) { return copy($from, $to); }
}
class TestRows {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function row_array() { return $this->rows[0] ?? []; }
    public function result_array() { return $this->rows; }
}
class TestDb {
    public $db_debug = true, $calls = [], $committed = false, $rolledBack = false, $failInsert = 0;
    private $insertCount = 0;
    public $header = ['Company_ID'=>2, 'JournalH_Code'=>'BDJ-test', 'CashBookType'=>'bd', 'CashBookDate'=>'2026-08-10 12:30:00'];
    public function trans_begin() { return true; }
    public function trans_status() { return true; }
    public function trans_commit() { $this->committed = true; return true; }
    public function trans_rollback() { $this->rolledBack = true; return true; }
    public function query($sql, $bindings) {
        $this->calls[] = [$sql, $bindings];
        if (strpos($sql, 'INSERT INTO') !== false) { return ++$this->insertCount !== $this->failInsert; }
        return new TestRows($this->header ? [$this->header] : []);
    }
}
function rejects($callback, $message) {
    try { $callback(); } catch (InvalidArgumentException $expected) { return; }
    throw new RuntimeException($message);
}
function filesIn($directory) {
    if (!is_dir($directory)) return [];
    $result = [];
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->isFile()) $result[] = $file->getPathname();
    }
    sort($result); return $result;
}
$temp = sys_get_temp_dir() . '/esba-cashbook-test-' . bin2hex(random_bytes(6));
mkdir($temp);
try {
    $png = $temp . '/sample.png'; $pdf = $temp . '/sample.pdf'; $bad = $temp . '/fake.pdf';
    file_put_contents($png, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    file_put_contents($pdf, "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF\n");
    file_put_contents($bad, '<?php echo "not a pdf";');
    $storage = new TestStorage(['root'=>$temp . '/uploads']);
    $months = ['jan','feb','mar','apr','mei','jun','jul','agu','sep','okt','nov','des'];
    foreach (['BR','CR','BD','CD'] as $type) {
        foreach ($months as $i=>$month) {
            check($storage->folder(['CashBookType'=>$type, 'CashBookDate'=>sprintf('2024-%02d-15', $i+1)]) === '2024/' . $type . '/' . $month,
                'Folders must derive from header type/date, including past years');
        }
    }
    rejects(function () use ($storage) { $storage->folder(['CashBookType'=>'../BD', 'CashBookDate'=>'2026-01-01']); }, 'Unsafe type accepted');
    check($storage->folder(['CashBookType'=>' bd ', 'CashBookDate'=>'2026-08-10']) === '2026/BD/agu', 'Normalize legacy lower-case types from the header');
    rejects(function () use ($storage) { $storage->folder(['CashBookType'=>'BD', 'CashBookDate'=>'2026-02-30']); }, 'Invalid date accepted');
    $files = ['name'=>['invoice.pdf','photo.png'], 'tmp_name'=>[$pdf,$png], 'error'=>[0,0], 'size'=>[filesize($pdf),filesize($png)]];
    $notes = ['Invoice note', 'Photo note'];
    $batch = $storage->validate_batch($files,$notes);
    check($batch[0]['notes'] === 'Invoice note' && $batch[1]['mime'] === 'image/png', 'File/notes pairing and real MIME detection');
    $strict = new Cashbook_attachment_files(['root'=>$temp . '/uploads']);
    rejects(function () use ($strict,$files,$notes) { $strict->validate_batch($files,$notes); }, 'Non-HTTP upload accepted');
    $wrong = $files; $wrong['tmp_name'][0] = $bad;
    rejects(function () use ($storage,$wrong,$notes) { $storage->validate_batch($wrong,$notes); }, 'Disguised executable accepted');
    $wrong = $files; $wrong['name'][1] = 'photo.svg';
    rejects(function () use ($storage,$wrong,$notes) { $storage->validate_batch($wrong,$notes); }, 'Unsupported image accepted');
    $wrong = $files; $wrong['error'][1] = UPLOAD_ERR_PARTIAL;
    rejects(function () use ($storage,$wrong,$notes) { $storage->validate_batch($wrong,$notes); }, 'Partial batch accepted');
    rejects(function () use ($storage,$files) { $storage->validate_batch($files,['only one note']); }, 'Misaligned notes accepted');
    rejects(function () use ($storage,$files) { $storage->validate_batch($files,[str_repeat('a',2001),'']); }, 'Oversized notes accepted');
    $many = []; foreach ($files as $key=>$values) $many[$key] = array_fill(0,11,$values[0]);
    rejects(function () use ($storage,$many) { $storage->validate_batch($many,array_fill(0,11,'')); }, 'Too many files accepted');
    try { $storage->resolve('../../config/database.php'); throw new LogicException('Traversal accepted'); }
    catch (RuntimeException $expected) {}

    $model = new m_CashbookAttachment(); $model->db = new TestDb();
    check($model->save('BDJ-test',$files,$notes,'accounting-user',$storage) === 2, 'Batch count');
    check($model->db->committed && !$model->db->rolledBack, 'Successful batch must commit');
    check(strpos($model->db->calls[0][0], 'UPDLOCK, HOLDLOCK') !== false, 'Header date/type must stay stable during save');
    check($model->db->calls[0][1] === [2,'BDJ-test'], 'Header must be company-scoped');
    $firstFiles = filesIn($temp . '/uploads'); check(count($firstFiles) === 2, 'Both files saved');
    foreach (array_slice($model->db->calls,1) as $i=>$call) {
        check(strpos($call[1][5], '2026/BD/agu/') === 0, 'Path must use header date/type');
        check($call[1][2] === 'BD', 'Normalize type snapshot before insertion');
        check($call[1][8] === $notes[$i] && $call[1][9] === 'accounting-user', 'Notes/creator stored for each file');
        check(is_file($storage->resolve($call[1][5])), 'Stored paths must resolve');
    }
    $model->db = new TestDb();
    $model->save('BDJ-test',$files,$notes,'accounting-user',$storage);
    $savedFiles = filesIn($temp . '/uploads'); check(count($savedFiles) === 4, 'Existing folder and identical original names must not overwrite prior files');
    $model->db = new TestDb(); $model->db->failInsert = 2;
    try { $model->save('BDJ-test',$files,$notes,'accounting-user',$storage); throw new LogicException('Failed insert committed'); }
    catch (RuntimeException $expected) {}
    check($model->db->rolledBack && !$model->db->committed, 'Failed batch must roll back metadata');
    check(filesIn($temp . '/uploads') === $savedFiles, 'Failed batch must remove only its new files');
    check($model->db->db_debug === true, 'Database debug setting restored');
    $model->db = new TestDb(); $model->db->header = [];
    try { $model->save('missing',$files,$notes,'accounting-user',$storage); throw new LogicException('Missing header accepted'); }
    catch (OutOfBoundsException $expected) {}
    check(filesIn($temp . '/uploads') === $savedFiles, 'Unknown header must not save files');
    echo "Attachment tests passed: folder dates/types, PDF/images, notes, validation, unique files, transactions, rollback cleanup and scoped headers.\n";
} finally {
    // Remove only the randomly named temporary test directory created above.
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($temp, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
        if ($item->isDir()) rmdir($item->getPathname()); else unlink($item->getPathname());
    }
    rmdir($temp);
}
