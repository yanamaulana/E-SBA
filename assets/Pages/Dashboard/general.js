(function ($) {
    'use strict';
    var root = document.getElementById('division-dashboard');
    if (!root || !$) return;
    var form = document.getElementById('dd-filters');
    var fields = { division: $('#dd-division'), from: $('#dd-from'), to: $('#dd-to'), currency: $('#dd-currency') };
    var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var number = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
    var money = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    var compact = new Intl.NumberFormat('id-ID', { notation: 'compact', maximumFractionDigits: 1 });
    var dateFormat = new Intl.DateTimeFormat('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    var paymentMeta = {
        NP: { label: 'Belum dibayar', color: '#e6a53c', tone: 'amber' },
        HP: { label: 'Dibayar sebagian', color: '#5278ee', tone: 'blue' },
        FP: { label: 'Lunas', color: '#18a68b', tone: 'teal' },
        OTHER: { label: 'Status lainnya', color: '#a1a9bb', tone: 'gray' }
    };
    var activeData = null, metric = 'amount', trendChart = null, paymentChart = null;
    var request = null, annualRequest = null, annualDivision = null;
    function text(id, value) { document.getElementById(id).textContent = value; }
    function show(id, visible) { document.getElementById(id).hidden = !visible; }
    function node(tag, className, value) {
        var element = document.createElement(tag);
        if (className) element.className = className;
        if (value !== undefined) element.textContent = value;
        return element;
    }
    function message(xhr, fallback) { return xhr.responseJSON && xhr.responseJSON.message || fallback; }
    function setBusy(busy) {
        form.querySelectorAll('select, button').forEach(function (element) { element.disabled = busy; });
        root.setAttribute('aria-busy', String(busy));
        show('dd-loading', busy);
        text('dd-apply', busy ? 'Memuat...' : 'Terapkan');
    }
    function loadAnnual(division) {
        if (annualRequest) annualRequest.abort();
        annualDivision = division;
        ['void', 'unsubmitted', 'approved', 'in_progress', 'rejected', 'total'].forEach(function (key) { text('dd-year-' + key, '\u2014'); });
        root.querySelectorAll('.dd-status-track span').forEach(function (bar) { bar.style.width = '0%'; });
        root.querySelectorAll('.dd-division-name').forEach(function (label) { label.textContent = division; });
        document.getElementById('dd-year-cards').setAttribute('aria-busy', 'true');
        show('dd-year-error', false);
        annualRequest = $.ajax({ url: root.dataset.yearEndpoint, data: { division: division }, dataType: 'json', timeout: 30000 })
            .done(function (data) {
                Object.keys(data.counts).forEach(function (key) {
                    text('dd-year-' + key, number.format(data.counts[key]));
                    var bar = document.getElementById('dd-year-bar-' + key);
                    if (bar) bar.style.width = (data.total ? data.counts[key] / data.total * 100 : 0) + '%';
                });
                text('dd-year-total', number.format(data.total));
            }).fail(function (xhr, status) {
                if (status === 'abort') return;
                document.querySelector('#dd-year-error span').textContent = message(xhr, 'Ringkasan tahunan belum tersedia. Coba muat ulang.');
                show('dd-year-error', true);
            }).always(function () { document.getElementById('dd-year-cards').setAttribute('aria-busy', 'false'); });
    }
    function renderMonthlyTable(data) {
        var body = document.getElementById('dd-monthly-rows'); body.replaceChildren();
        data.monthly.forEach(function (month) {
            var row = node('tr');
            row.append(node('td', '', monthNames[month.month - 1]), node('td', 'text-end', number.format(month.count)), node('td', 'text-end', money.format(month.amount)));
            body.append(row);
        });
    }
    function renderTrend() {
        if (!activeData) return;
        var data = activeData;
        var amount = metric === 'amount';
        var peak = data.monthly.reduce(function (best, current) { return !best || current[metric] > best[metric] ? current : best; }, null);
        text('dd-trend-insight', data.summary.count && peak
            ? 'Puncak ' + (amount ? 'nilai pengajuan' : 'jumlah CBR') + ' pada ' + monthNames[peak.month - 1] + ': ' + (amount ? data.currency + ' ' + money.format(peak.amount) : number.format(peak.count) + ' CBR') + '.'
            : 'Belum ada aktivitas pada rentang ini.');
        var options = {
            chart: { type: 'area', height: 280, fontFamily: 'inherit', toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: !window.matchMedia('(prefers-reduced-motion: reduce)').matches } },
            series: [{ name: amount ? 'Nilai pengajuan ' + data.currency : 'Jumlah CBR', data: data.monthly.map(function (month) { return month[metric]; }) }],
            colors: ['#5278ee'], stroke: { curve: 'smooth', width: 3 }, markers: { size: 3, hover: { size: 5 } },
            fill: { type: 'gradient', gradient: { shadeIntensity: 0, opacityFrom: 0.28, opacityTo: 0.02 } },
            dataLabels: { enabled: false }, grid: { borderColor: '#edf1f7', strokeDashArray: 4 },
            xaxis: { categories: data.monthly.map(function (month) { return monthNames[month.month - 1]; }), axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { formatter: function (value) { return amount ? compact.format(value) : number.format(value); } } },
            tooltip: { y: { formatter: function (value) { return amount ? data.currency + ' ' + money.format(value) : number.format(value) + ' CBR'; } } },
            noData: { text: 'Belum ada data' }
        };
        if (!window.ApexCharts) {
            text('dd-trend-chart', 'Grafik belum tersedia. Angka bulanan dapat dilihat pada tabel di bawah.');
            root.querySelector('.dd-chart-table').open = true;
            return;
        }
        if (trendChart) trendChart.updateOptions(options);
        else { trendChart = new ApexCharts(document.getElementById('dd-trend-chart'), options); trendChart.render(); }
    }
    function renderPayments(data) {
        var keys = ['NP', 'HP', 'FP'];
        if (data.payments.OTHER) keys.push('OTHER');
        var legend = document.getElementById('dd-payment-legend'); legend.replaceChildren();
        keys.forEach(function (key) {
            var item = node('li');
            var label = node('span'); label.append(node('i', 'dd-legend-dot dd-dot-' + paymentMeta[key].tone), document.createTextNode(paymentMeta[key].label));
            item.append(label, node('strong', '', number.format(data.payments[key]) + ' CBR')); legend.append(item);
        });
        var chartElement = document.getElementById('dd-payment-chart');
        if (!data.summary.count || !window.ApexCharts) {
            if (paymentChart) { paymentChart.destroy(); paymentChart = null; }
            chartElement.textContent = data.summary.count ? 'Rincian pembayaran tersedia di bawah.' : 'Belum ada dokumen';
            return;
        }
        var options = {
            chart: { type: 'donut', height: 250, fontFamily: 'inherit', animations: { enabled: !window.matchMedia('(prefers-reduced-motion: reduce)').matches } },
            series: keys.map(function (key) { return data.payments[key]; }),
            labels: keys.map(function (key) { return paymentMeta[key].label; }),
            colors: keys.map(function (key) { return paymentMeta[key].color; }),
            legend: { show: false }, dataLabels: { enabled: false }, stroke: { width: 3, colors: ['#fff'] },
            plotOptions: { pie: { donut: { size: '74%', labels: { show: true, value: { fontSize: '28px', fontWeight: 700, formatter: function (value) { return number.format(value); } }, total: { show: true, showAlways: true, label: 'Total CBR', formatter: function () { return number.format(data.summary.count); } } } } } },
            tooltip: { y: { formatter: function (value) { return number.format(value) + ' CBR'; } } }
        };
        if (paymentChart) paymentChart.updateOptions(options);
        else { chartElement.replaceChildren(); paymentChart = new ApexCharts(chartElement, options); paymentChart.render(); }
    }
    function renderRecent(data) {
        var body = document.getElementById('dd-recent-rows'); body.replaceChildren();
        if (!data.recent.length) {
            var empty = node('td', 'dd-table-empty', 'Tidak ada dokumen pada periode ini.'); empty.colSpan = 4;
            var emptyRow = node('tr'); emptyRow.append(empty); body.append(emptyRow); return;
        }
        data.recent.forEach(function (documentData) {
            var row = node('tr');
            var docCell = node('td'); docCell.dataset.label = 'Dokumen';
            docCell.append(node('strong', 'dd-document-number', documentData.CBReq_No), node('small', 'dd-document-date', dateFormat.format(new Date(documentData.Document_Date + 'T00:00:00'))));
            var description = node('td', 'dd-document-description', documentData.Descript || '\u2014'); description.dataset.label = 'Keperluan';
            var status = paymentMeta[documentData.Paid_Status] || paymentMeta.OTHER;
            var statusCell = node('td'); statusCell.dataset.label = 'Pembayaran'; statusCell.append(node('span', 'dd-payment-badge dd-badge-' + status.tone, status.label));
            var amount = node('td', 'text-end dd-document-amount', money.format(documentData.Amount)); amount.dataset.label = data.currency;
            row.append(docCell, description, statusCell, amount); body.append(row);
        });
    }
    function render(data) {
        activeData = data;
        fields.currency.empty(); data.currencies.forEach(function (currency) { fields.currency.append(new Option(currency, currency)); }); fields.currency.val(data.currency);
        root.querySelectorAll('.dd-currency-label').forEach(function (element) { element.textContent = data.currency; });
        text('dd-period-label', monthNames[data.from_month - 1] + (data.from_month === data.to_month ? '' : ' \u2013 ' + monthNames[data.to_month - 1]) + ' ' + data.year + ' / ' + data.division);
        text('dd-updated', 'Diperbarui ' + data.updated_at);
        text('dd-amount', compact.format(data.summary.amount)); document.getElementById('dd-amount').title = data.currency + ' ' + money.format(data.summary.amount);
        text('dd-count', number.format(data.summary.count)); text('dd-average', 'Rata-rata ' + data.currency + ' ' + compact.format(data.summary.average) + ' / CBR');
        text('dd-outstanding', number.format(data.payments.NP + data.payments.HP));
        text('dd-outstanding-note', number.format(data.payments.NP) + ' belum dibayar / ' + number.format(data.payments.HP) + ' sebagian');
        text('dd-paid-rate', new Intl.NumberFormat('id-ID', { maximumFractionDigits: 1 }).format(data.summary.paid_percentage) + '%');
        text('dd-paid-note', number.format(data.payments.FP) + ' dari ' + number.format(data.summary.count) + ' CBR lunas');
        show('dd-results', true); show('dd-empty', !data.summary.count);
        renderMonthlyTable(data); renderTrend(); renderPayments(data); renderRecent(data);
        root.querySelectorAll('[data-range]').forEach(function (button) {
            var selected = button.dataset.range === 'year' ? data.from_month === 1 && data.to_month === Number(root.dataset.currentMonth) : data.from_month === Number(root.dataset.currentMonth) && data.to_month === Number(root.dataset.currentMonth);
            button.classList.toggle('is-active', selected); button.setAttribute('aria-pressed', String(selected));
        });
    }
    function loadDashboard() {
        var from = Number(fields.from.val()), to = Number(fields.to.val());
        show('dd-error', false);
        if (from > to) { text('dd-error', 'Bulan awal tidak boleh melewati bulan akhir.'); show('dd-error', true); return; }
        var division = fields.division.val() || '';
        if (annualDivision !== division) loadAnnual(division);
        if (request) request.abort();
        show('dd-results', false); setBusy(true);
        request = $.ajax({ url: root.dataset.endpoint, data: { division: division, from_month: from, to_month: to, currency: fields.currency.val() || '' }, dataType: 'json', timeout: 30000 })
            .done(render).fail(function (xhr, status) {
                if (status === 'abort') return;
                text('dd-error', message(xhr, 'Data belum dapat dimuat. Tekan Terapkan untuk mencoba lagi; jika sesi berakhir, silakan login kembali.'));
                show('dd-error', true);
            }).always(function () { setBusy(false); });
    }
    $(form).on('submit', function (event) { event.preventDefault(); loadDashboard(); });
    fields.division.on('change', function () { fields.currency.empty().append(new Option('Otomatis', '')); });
    root.querySelectorAll('[data-range]').forEach(function (button) {
        button.addEventListener('click', function () { fields.from.val(button.dataset.range === 'month' ? root.dataset.currentMonth : '1'); fields.to.val(root.dataset.currentMonth); loadDashboard(); });
    });
    root.querySelectorAll('[data-metric]').forEach(function (button) {
        button.addEventListener('click', function () {
            metric = button.dataset.metric;
            root.querySelectorAll('[data-metric]').forEach(function (item) { item.classList.toggle('is-active', item === button); item.setAttribute('aria-pressed', String(item === button)); });
            renderTrend();
        });
    });
    document.getElementById('dd-year-retry').addEventListener('click', function () { loadAnnual(annualDivision); });
    loadDashboard();
})(window.jQuery);
