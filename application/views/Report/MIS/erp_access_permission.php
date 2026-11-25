<div class="card">
    <div class="card-header align-items-center">
        <h3 class="card-title">Manajemen Hak Akses ERP</h3>
        <?php if ($this->session->userdata('sys_sba_isAdm')): ?>
            <div class="card-toolbar">
                <a class="btn btn-sm btn-light-primary" href="<?= site_url('Report/Vpr_audit') ?>">Audit Akses VPR</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="filter_usergroup" class="form-label">Grup Pengguna</label>
                <select id="filter_usergroup" class="form-select form-select-sm select2-filter">
                    <option value="ALL">Semua Grup</option>
                    <?php foreach ($user_groups as $group) : ?>
                        <option value="<?= $group->UserGroup_ID ?>"><?= $group->UserGroup_Name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_function" class="form-label">Fungsi / Menu</label>
                <select id="filter_function" class="form-select form-select-sm select2-filter">
                    <option value="ALL">Semua Fungsi</option>
                    <?php foreach ($functions as $func) : ?>
                        <option value="<?= $func->sf_ufunc_id ?>"><?= $func->SF_UFUNC_NAME_EN ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 align-self-end">
                <button id="btn_filter" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </div>

        <table id="table_erp_access" class="display compact table-bordered table-striped table-hover table-sm align-middle gy-5 gs-5" style="width:100%">
            <thead>
                <tr style="background-color: #3B6D8C;">
                    <th>ID</th>
                    <th>Grup Pengguna</th>
                    <th>Nama Fungsi</th>
                    <th>Punya Akses</th>
                    <th>Aktivitas Log</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<!-- Letakkan di bagian mana saja di dalam view, bisa sebelum memanggil file .js utama -->
<script>
    const employees = <?= json_encode($employees); ?>;
</script>