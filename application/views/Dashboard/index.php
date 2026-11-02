<?php
$monthNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$initialDivision = $dashboard_divisions[0] ?? '';
?>
<section class="division-dashboard" id="division-dashboard"
    data-endpoint="<?= base_url('Dashboard/general_data') ?>"
    data-year-endpoint="<?= base_url('Dashboard/general_year_data') ?>"
    data-year="<?= $dashboard_year ?>" data-current-month="<?= $dashboard_month ?>">
    <div class="dd-section-heading">
        <div><span class="dd-eyebrow">DIVISION OVERVIEW / <?= $dashboard_year ?></span>
            <h1>CBR tahun berjalan <span class="dd-division-name"><?= html_escape($initialDivision) ?></span></h1>
        </div>
        <span class="dd-year-caption">Januari &ndash; <?= $monthNames[$dashboard_month] ?></span>
    </div>
    <div class="dd-year-error" id="dd-year-error" role="alert" hidden>
        <span></span> <button type="button" id="dd-year-retry" class="btn btn-sm btn-light-danger">Coba lagi</button>
    </div>
    <div class="dd-status-grid" aria-label="Status CBR tahun berjalan" aria-busy="true" id="dd-year-cards">
        <?php foreach ([
            ['void', 'Void', 'fas fa-ban', 'Dokumen dibatalkan / ditutup', 'rose'],
            ['unsubmitted', 'Belum diajukan', 'far fa-paper-plane', 'Siap diajukan ke approval ESBA', 'amber'],
            ['approved', 'Sudah approve', 'fas fa-check-double', 'Dokumen sudah disahkan', 'teal'],
            ['in_progress', 'Proses approval', 'far fa-clock', 'Menunggu tahapan persetujuan', 'blue'],
        ] as [$key, $label, $icon, $note, $tone]): ?>
            <article class="dd-status-card dd-tone-<?= $tone ?>">
                <div class="dd-status-heading"><span><?= $label ?></span><i class="<?= $icon ?>" aria-hidden="true"></i></div>
                <div class="dd-status-value"><strong id="dd-year-<?= $key ?>">&mdash;</strong><span>CBR</span></div>
                <p><?= $note ?></p>
                <div class="dd-status-track" aria-hidden="true"><span id="dd-year-bar-<?= $key ?>"></span></div>
            </article>
        <?php endforeach; ?>
    </div>
    <div class="dd-year-footnote">
        <span>Total <strong id="dd-year-total">&mdash;</strong> CBR &middot; Semua mata uang &middot; Berdasarkan tanggal dokumen</span>
        <span class="dd-rejected-note">Ditolak / perlu revisi: <strong id="dd-year-rejected">&mdash;</strong> CBR</span>
    </div>

    <div class="dd-hero">
        <div><span class="dd-eyebrow">YOUR DIVISION, AT A GLANCE</span>
            <h2>Kenali ritme pengajuan divisi Anda.</h2>
            <p>Halo, <?= html_escape($this->session->userdata('sys_sba_nama') ?: 'rekan Samick') ?>. Telusuri tren bulanan dan posisi pembayaran CBR dalam satu tampilan.</p>
        </div>
        <div class="dd-hero-actions">
            <a class="btn btn-sm btn-primary" href="<?= base_url('MyCbr') ?>">Pengajuan saya <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i></a>
            <a class="dd-hero-link" href="<?= base_url('Report/Navigation') ?>">Reports &amp; Tools</a>
        </div>
    </div>

    <form id="dd-filters" class="dd-filter-card">
        <div class="dd-filter-heading"><strong>Periode analisis / <?= $dashboard_year ?></strong>
            <div class="dd-range-shortcuts" aria-label="Pilih periode cepat">
                <button type="button" data-range="year" class="is-active" aria-pressed="true">Tahun berjalan</button>
                <button type="button" data-range="month" aria-pressed="false">Bulan ini</button>
            </div>
        </div>
        <div class="dd-filter-grid">
            <div class="dd-field dd-field-division"><label for="dd-division">Divisi</label>
                <select class="form-select form-select-sm" id="dd-division" name="division">
                    <?php if (!$dashboard_divisions): ?><option value="">Divisi belum tersedia</option><?php endif; ?>
                    <?php foreach ($dashboard_divisions as $division): ?><option value="<?= html_escape($division) ?>"><?= html_escape($division) ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="dd-field"><label for="dd-from">Dari bulan</label><select class="form-select form-select-sm" id="dd-from" name="from_month">
                <?php for ($month = 1; $month <= $dashboard_month; $month++): ?><option value="<?= $month ?>"><?= $monthNames[$month] ?></option><?php endfor; ?>
            </select></div>
            <div class="dd-field"><label for="dd-to">Sampai bulan</label><select class="form-select form-select-sm" id="dd-to" name="to_month">
                <?php for ($month = 1; $month <= $dashboard_month; $month++): ?><option value="<?= $month ?>" <?= $month === $dashboard_month ? 'selected' : '' ?>><?= $monthNames[$month] ?></option><?php endfor; ?>
            </select></div>
            <div class="dd-field"><label for="dd-currency">Mata uang</label><select class="form-select form-select-sm" id="dd-currency" name="currency"><option value="">Otomatis</option></select></div>
            <button type="submit" class="btn btn-primary btn-sm" id="dd-apply"><i class="fas fa-filter" aria-hidden="true"></i> Terapkan</button>
        </div>
    </form>
    <div class="dd-error" id="dd-error" role="alert" hidden></div>
    <div id="dd-loading" class="dd-loading" role="status"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menyiapkan ringkasan divisi...</div>

    <div id="dd-results" hidden>
        <div class="dd-section-heading dd-analysis-heading"><h2>Aktivitas <span id="dd-period-label"></span></h2><span class="dd-updated" id="dd-updated"></span></div>
        <div id="dd-empty" class="dd-empty" role="status" hidden>Belum ada CBR yang masuk ESBA pada periode dan mata uang ini. Pilih periode atau mata uang lainnya.</div>
        <div class="dd-metric-grid">
            <article class="dd-metric dd-metric-primary"><span>Nilai pengajuan <b class="dd-currency-label"></b></span><strong id="dd-amount">&mdash;</strong><small>Nominal dokumen, bukan realisasi pembayaran</small></article>
            <article class="dd-metric"><span>CBR masuk ESBA</span><strong id="dd-count">&mdash;</strong><small id="dd-average"></small></article>
            <article class="dd-metric"><span>Belum lunas</span><strong id="dd-outstanding">&mdash;</strong><small id="dd-outstanding-note"></small></article>
            <article class="dd-metric"><span>Proporsi CBR lunas</span><strong id="dd-paid-rate">&mdash;</strong><small id="dd-paid-note"></small></article>
        </div>
        <div class="dd-chart-grid">
            <article class="dd-panel">
                <div class="dd-panel-heading"><div><h3>Tren bulanan</h3><p>Perjalanan CBR divisi dalam periode terpilih</p></div>
                    <div class="dd-chart-toggle" aria-label="Jenis grafik tren"><button type="button" data-metric="amount" class="is-active" aria-pressed="true">Nominal</button><button type="button" data-metric="count" aria-pressed="false">Jumlah CBR</button></div>
                </div>
                <div id="dd-trend-chart" class="dd-chart" role="img" aria-label="Grafik tren bulanan"></div>
                <div class="dd-chart-insight" id="dd-trend-insight"></div>
                <details class="dd-chart-table"><summary>Lihat angka per bulan</summary><div class="table-responsive"><table class="table table-sm"><caption class="visually-hidden">Rincian tren bulanan</caption><thead><tr><th>Bulan</th><th class="text-end">CBR</th><th class="text-end">Nilai pengajuan</th></tr></thead><tbody id="dd-monthly-rows"></tbody></table></div></details>
            </article>
            <article class="dd-panel">
                <div class="dd-panel-heading"><div><h3>Posisi pembayaran</h3><p>Berdasarkan jumlah dokumen CBR</p></div></div>
                <div id="dd-payment-chart" class="dd-chart dd-donut" role="img" aria-label="Grafik komposisi status pembayaran"></div>
                <ul class="dd-payment-legend" id="dd-payment-legend"></ul>
            </article>
        </div>
        <article class="dd-panel dd-recent-panel">
            <div class="dd-panel-heading"><div><h3>CBR terbaru</h3><p>8 dokumen terbaru pada periode dan mata uang terpilih</p></div><span class="dd-currency-chip dd-currency-label"></span></div>
            <div class="table-responsive"><table class="table align-middle dd-recent-table"><caption class="visually-hidden">CBR terbaru divisi</caption><thead><tr><th>Dokumen / tanggal</th><th>Keperluan</th><th>Pembayaran</th><th class="text-end">Nominal</th></tr></thead><tbody id="dd-recent-rows"></tbody></table></div>
        </article>
        <p class="dd-method-note">Analisis bulanan memakai CBR yang sudah masuk ESBA; VOID dan SPJ dikecualikan. Nilai tetap dipisahkan per mata uang. Kartu tahunan di atas mencakup CBR VOID serta dokumen yang masih siap diajukan. Divisi dokumen yang belum diajukan mengikuti profil pembuat saat ini.</p>
    </div>
</section>
