<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">Upload Excel Faktur Pajak</h3>
        <div class="card-toolbar">
            <a href="<?= base_url('Report/Logistic/template_faktur_pajak') ?>" class="btn btn-sm btn-light-success">
                <i class="fas fa-file-excel"></i> Download Template
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="formUploadFaktur" method="post" enctype="multipart/form-data">
                <?php $this->load->view('partials/csrf_field'); ?>
            <div class="mb-3">
                <label for="fileExcel" class="form-label">Pilih File Excel</label>
                <input class="form-control" type="file" id="fileExcel" name="file_excel" accept=".xls, .xlsx" required>
            </div>

            <div class="alert alert-primary d-flex align-items-center p-5">
                <i class="fas fa-info-circle fs-2hx text-primary me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-primary">Petunjuk</h4>
                    <span>Pastikan file Excel yang diunggah sesuai dengan format pada template yang telah disediakan.</span>
                    <span>Kolom yang wajib diisi adalah: `Invoice_Number`, `TaxDocNumber`, `TaxDate [YYYY-MM_DD]`.</span>
                </div>
            </div>
        </form>
    </div>
    <div class="card-footer">
        <button type="button" id="btnProcessUpload" class="btn btn-primary">
            <i class="fas fa-cogs"></i> Proses & Simpan
        </button>
        <a href="<?= base_url('Report/Navigation') ?>" class="btn btn-danger float-end"><i class="far fa-arrow-alt-circle-left"></i> Kembali</a>
    </div>
</div>

<!-- (Optional) Modal untuk menampilkan preview data sebelum disimpan -->
<div class="modal fade" tabindex="-1" id="modalPreview">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="previewContent">
            <!-- Konten preview akan dimuat di sini oleh JavaScript -->
        </div>
    </div>
</div>