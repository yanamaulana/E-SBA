<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cashbook_attachment_files
{
    private $root;
    private $months = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
    private $types = ['pdf'=>'application/pdf', 'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg',
        'png'=>'image/png', 'gif'=>'image/gif', 'webp'=>'image/webp'];

    public function __construct($params = [])
    {
        $this->root = $params['root'] ?? APPPATH . 'uploads/cashbook';
    }

    public function limits()
    {
        $phpLimit = $this->ini_bytes(ini_get('upload_max_filesize'));
        return ['max_files'=>min(10, max(1, (int) ini_get('max_file_uploads'))),
            'max_file_bytes'=>min(10 * 1024 * 1024, $phpLimit ?: PHP_INT_MAX),
            'max_total_bytes'=>min(50 * 1024 * 1024, $this->ini_bytes(ini_get('post_max_size')) ?: PHP_INT_MAX)];
    }

    private function ini_bytes($value)
    {
        $value = trim($value);
        $unit = strtolower(substr($value, -1));
        return (int) ((float) $value * (['g'=>1073741824, 'm'=>1048576, 'k'=>1024][$unit] ?? 1));
    }

    public function folder(array $header)
    {
        $type = strtoupper(trim((string) ($header['CashBookType'] ?? '')));
        $value = substr((string) ($header['CashBookDate'] ?? ''), 0, 10);
        $date = DateTime::createFromFormat('!Y-m-d', $value);
        if (!in_array($type, ['BR','CR','BD','CD'], true) || !$date || $date->format('Y-m-d') !== $value) {
            throw new InvalidArgumentException('Tipe atau tanggal header cashbook tidak valid.');
        }
        return $date->format('Y') . '/' . $type . '/' . $this->months[(int) $date->format('n') - 1];
    }

    protected function is_uploaded($path) { return is_uploaded_file($path); }
    protected function move_uploaded($from, $to) { return move_uploaded_file($from, $to); }

    public function validate_batch($files, $notes)
    {
        $limits = $this->limits();
        if (!is_array($files) || !isset($files['name']) || !is_array($files['name']) ||
            !is_array($notes) || !count($files['name']) || count($files['name']) > $limits['max_files']) {
            throw new InvalidArgumentException('Pilih 1 sampai ' . $limits['max_files'] . ' file.');
        }
        $keys = array_keys($files['name']);
        foreach (['tmp_name','error','size'] as $key) {
            if (!isset($files[$key]) || !is_array($files[$key]) || array_keys($files[$key]) !== $keys) {
                throw new InvalidArgumentException('Data upload tidak lengkap.');
            }
        }
        if (array_keys($notes) !== $keys) { throw new InvalidArgumentException('Notes tidak sesuai dengan baris file.'); }
        $validated = [];
        $total = 0;
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        foreach ($keys as $index) {
            foreach (['name','tmp_name','error','size'] as $key) {
                if (!is_scalar($files[$key][$index])) { throw new InvalidArgumentException('Data file tidak valid.'); }
            }
            if ((int) $files['error'][$index] !== UPLOAD_ERR_OK) {
                throw new InvalidArgumentException('Salah satu file belum dipilih, terlalu besar, atau gagal diunggah.');
            }
            $tmp = $files['tmp_name'][$index];
            if (!$this->is_uploaded($tmp)) { throw new InvalidArgumentException('Sumber upload tidak valid.'); }
            $size = filesize($tmp);
            $total += $size;
            if ($size < 1 || $size > $limits['max_file_bytes'] || $total > $limits['max_total_bytes']) {
                throw new InvalidArgumentException('Ukuran file atau total upload melebihi batas.');
            }
            $name = basename(str_replace('\\', '/', $files['name'][$index]));
            $name = preg_replace('/[\x00-\x1F\x7F]/u', '', $name);
            if (!$name || mb_strlen($name, 'UTF-8') > 255) { throw new InvalidArgumentException('Nama file maksimal 255 karakter.'); }
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $mime = $finfo->file($tmp);
            if (!isset($this->types[$extension]) || $mime !== $this->types[$extension]) {
                throw new InvalidArgumentException('Hanya PDF, JPG, PNG, GIF, atau WEBP yang diperbolehkan; isi file harus sesuai ekstensinya.');
            }
            if ($extension === 'pdf') {
                if (file_get_contents($tmp, false, null, 0, 5) !== '%PDF-') {
                    throw new InvalidArgumentException('File PDF tidak valid.');
                }
            } elseif (@getimagesize($tmp) === false) {
                throw new InvalidArgumentException('File gambar tidak valid.');
            }
            if (!is_string($notes[$index]) || mb_strlen($notes[$index], 'UTF-8') > 2000) {
                throw new InvalidArgumentException('Notes maksimal 2000 karakter per file.');
            }
            $validated[] = ['tmp'=>$tmp, 'name'=>$name, 'extension'=>$extension, 'mime'=>$mime,
                'size'=>$size, 'notes'=>trim($notes[$index])];
        }
        return $validated;
    }

    public function store(array $header, array $file)
    {
        $folder = $this->folder($header);
        $directory = $this->root . '/' . $folder;
        if (!is_dir($directory) && !@mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new RuntimeException('Folder lampiran tidak dapat dibuat.');
        }
        $root = realpath($this->root);
        $resolved = realpath($directory);
        if (!$root || !$resolved || strpos($resolved . DIRECTORY_SEPARATOR, $root . DIRECTORY_SEPARATOR) !== 0) {
            throw new RuntimeException('Folder lampiran tidak valid.');
        }
        $relative = $folder . '/' . bin2hex(random_bytes(16)) . '.' . $file['extension'];
        if (!$this->move_uploaded($file['tmp'], $this->root . '/' . $relative)) {
            throw new RuntimeException('File lampiran tidak dapat disimpan.');
        }
        return $relative;
    }

    public function resolve($relative)
    {
        if (!is_string($relative) || !preg_match('~^\d{4}/(?:BR|CR|BD|CD)/(?:jan|feb|mar|apr|mei|jun|jul|agu|sep|okt|nov|des)/[a-f0-9]{32}\.(?:pdf|jpg|jpeg|png|gif|webp)$~D', $relative)) {
            throw new RuntimeException('Lokasi lampiran tidak valid.');
        }
        $root = realpath($this->root);
        $path = realpath($this->root . '/' . $relative);
        if (!$root || !$path || strpos($path, $root . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
            throw new RuntimeException('File lampiran tidak ditemukan.');
        }
        return $path;
    }

    public function remove_new($relative)
    {
        $path = $this->resolve($relative);
        if (!unlink($path)) { throw new RuntimeException('Pembersihan file upload gagal.'); }
    }
}
