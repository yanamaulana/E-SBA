$(function () {
    'use strict';
    const options = JSON.parse(document.getElementById('cashbook-options').textContent);
    const form = document.getElementById('cashbook-filters');
    const dateDefaults = $('#date_from, #date_to').toArray().map(input => ({ input, value: input.value }));
    $('#date_from, #date_to').flatpickr({
        dateFormat: 'Y-m-d',
        allowInput: true,
        monthSelectorType: 'dropdown'
    });
    const types = { BR: 'Bank Receipt', CR: 'Cash Receipt', BD: 'Bank Payment', CD: 'Cash Payment' };
    const text = $.fn.dataTable.render.text();
    const escape = value => text.display(value == null ? '' : String(value));
    const fields = {
        document: 'Document No.', description: 'Description', date: 'Date', memo: 'Memo',
        payee: 'Payer', currency: 'Currency', account: 'Account Name', amount: 'Bank Receipt'
    };
    const comparisons = { eq: 'Equals', ne: 'Not equal', gt: 'Greater than', gte: 'Greater or equal', lt: 'Less than', lte: 'Less or equal' };
    const stringOperators = { contains: 'Any part of field', eq: 'Whole word only', starts: 'Start of field', not_contains: 'Not contain' };
    let table;
    let appliedFilters;

    function error(message) { $('#cashbook-error').text(message || '').toggleClass('d-none', !message); }
    function fillSelect(select, items) {
        select.empty();
        Object.keys(items).forEach(key => select.append(new Option(items[key], key)));
    }
    function updateCriterion(row) {
        const field = row.find('.criterion-field').val();
        fillSelect(row.find('.criterion-operator'), field === 'date' ? comparisons :
            (field === 'amount' ? Object.assign({}, stringOperators, comparisons) : stringOperators));
        const holder = row.find('.criterion-input').empty();
        if (field === 'currency') {
            const select = $('<select class="form-select form-select-sm criterion-value" aria-label="Search expression"></select>');
            select.append(new Option('Select currency', ''));
            options.currencies.forEach(currency => select.append(new Option(currency, currency)));
            holder.append(select);
        } else {
            holder.append($('<input class="form-control form-control-sm criterion-value" aria-label="Search expression">')
                .attr('type', field === 'date' ? 'date' : 'text').attr('placeholder', 'Search expression'));
        }
    }
    function updateConditionButtons() {
        const rows = $('#cashbook-criteria .criterion');
        rows.find('.criterion-join').prop('disabled', false).removeClass('invisible');
        rows.first().find('.criterion-join').prop('disabled', true).addClass('invisible');
        $('#add-criterion').prop('disabled', rows.length >= 5);
    }
    function addCriterion() {
        if ($('#cashbook-criteria .criterion').length >= 5) return;
        const row = $('<div class="criterion row g-2 mb-2">' +
            // '<div class="col-md-1"><select class="criterion-join form-select form-select-sm" aria-label="Logical operator"><option>AND</option><option>OR</option></select></div>' +
            '<div class="col-md-2"><select class="criterion-field form-select form-select-sm" aria-label="Field / Operand"></select></div>' +
            '<div class="col-md-1"><select class="criterion-operator form-select form-select-sm" aria-label="Conditional operator"></select></div>' +
            '<div class="col-md-5 criterion-input"></div>' +
            '<div class="col-md-1"><button type="button" class="remove-criterion btn btn-sm btn-light-danger" aria-label="Remove condition"><i class="fas fa-times"></i></button></div></div>');
        fillSelect(row.find('.criterion-field'), fields);
        $('#cashbook-criteria').append(row);
        updateCriterion(row);
        updateConditionButtons();
    }
    function updateType() {
        const type = $('#entry_type').val();
        const bank = type === 'BR' || type === 'BD';
        fields.payee = type === 'BD' || type === 'CD' ? 'Payee' : 'Payer';
        fields.amount = types[type];
        $('#account-label').text(bank ? 'Filter Bank' : 'Filter Cash');
        const selected = $('#account').val();
        const account = $('#account').empty().append(new Option('All', ''));
        options.accounts.filter(item => bank ? [3, 5].includes(Number(item.AccountClass_ID)) : Number(item.AccountClass_ID) === 4)
            .forEach(item => account.append(new Option('[' + item.Account_Number + '] ' + item.Account_Name, item.Acc_ID)));
        account.val(account.find('option').toArray().some(item => item.value === selected) ? selected : '');
        $('.criterion-field option[value="payee"]').text(fields.payee);
        $('.criterion-field option[value="amount"]').text(fields.amount);
    }
    function collectFilters() {
        const data = {};
        ['entry_type', 'posted', 'void', 'account', 'date_from', 'date_to'].forEach(key => { data[key] = $('#' + key).val(); });
        ['document_status', 'approval_status'].forEach(key => {
            data[key] = $(form).find('input[name="' + key + '[]"]:checked').map(function () { return this.value; }).get();
        });
        data.criteria = $('#cashbook-criteria .criterion').map(function () {
            const row = $(this);
            return {
                field: row.find('.criterion-field').val(), operator: row.find('.criterion-operator').val(),
                value: row.find('.criterion-value').val(), join: row.find('.criterion-join').val()
            };
        }).get();
        return data;
    }
    function applyFilters() {
        if (!form.reportValidity()) return;
        if ($('#date_from').val() > $('#date_to').val()) { error('Tanggal akhir harus sama atau setelah tanggal awal.'); return; }
        error('');
        appliedFilters = collectFilters();
        const payer = ['BD', 'CD'].includes(appliedFilters.entry_type) ? 'Payee' : 'Payer';
        // Address original column indexes even when Columns hides a column.
        // DataTables refreshes its scrollX header copy on the following draw.
        $(table.column(3).header()).text(payer);
        $(table.column(7).header()).text(types[appliedFilters.entry_type]);
        table.ajax.reload();
    }
    function badge(group) {
        return function (value, type) {
            const label = options.statuses[group][value] || String(value == null ? '-' : value);
            if (type !== 'display') return label;
            const color = /approved|closed/i.test(label) ? 'success' : /reject/i.test(label) ? 'danger' : 'info';
            return '<span class="badge badge-light-' + color + '">' + escape(label) + '</span>';
        };
    }
    function formatAmount(value) {
        const decimals = Math.max(2, Math.min(20, (String(value).split('.')[1] || '').replace(/0+$/, '').length));
        return Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
    }
    function detailColumn(field, numeric) {
        return {
            data: 'Details', orderable: false,
            className: 'cashbook-detail-cell' + (numeric ? ' text-end' : ''),
            render: function (details, type) {
                const lines = Array.isArray(details) && details.length ? details : [{}];
                const values = lines.map(detail => {
                    const value = detail[field];
                    return value == null || value === '' ? '-' : (numeric ? formatAmount(value) : String(value));
                });
                if (type !== 'display') return values.join(' | ');
                return values.map(value => '<div class="cashbook-detail-line">' + escape(value) + '</div>').join('');
            }
        };
    }
    const exportOptions = { columns: ':visible:not(.no-export)', modifier: { page: 'current' }, orthogonal: 'export' };
    function exportExcel(dt, node) {
        const button = dt.button(node);
        const filters = Object.assign({}, appliedFilters, {
            search: { value: dt.search() },
            order: dt.order().map(item => ({ column: item[0], dir: item[1] })),
            visible_columns: dt.columns(':visible:not(.no-export)').indexes().toArray()
        });
        button.enable(false).text('<i class="fas fa-spinner fa-spin"></i> Exporting...');
        error('');
        $.ajax({
            url: $('meta[name="base_url"]').attr('content') + 'CashbookEntry/export_excel',
            type: 'POST', data: { filters: JSON.stringify(filters) }, xhrFields: { responseType: 'blob' }
        }).done(function (blob, status, xhr) {
            if (!(xhr.getResponseHeader('Content-Type') || '').includes('spreadsheetml.sheet')) {
                error('File export tidak dapat diunduh. Periksa sesi login lalu coba kembali.'); return;
            }
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            const filename = /filename="([^"]+)"/.exec(xhr.getResponseHeader('Content-Disposition') || '');
            link.href = url; link.download = filename ? filename[1] : 'Cashbook_Entry.xlsx';
            document.body.appendChild(link); link.click(); link.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        }).fail(function (xhr) {
            const fallback = 'Export gagal. Coba kembali atau persempit rentang tanggal.';
            if (xhr.response instanceof Blob) {
                xhr.response.text().then(function (body) {
                    try { error(JSON.parse(body).error || fallback); } catch (e) { error(fallback); }
                }).catch(() => error(fallback));
            } else { error(fallback); }
        }).always(function () {
            button.enable(true).text('<i class="far fa-file-excel fs-2"></i> Export All');
        });
    }
    updateType();
    addCriterion();
    appliedFilters = collectFilters();
    table = $('#TableData').DataTable({
        processing: true, serverSide: true, paging: true,
        dom: '<"row mb-3"<"col-sm-12"B>><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
        scrollX: true, autoWidth: false, responsive: false, searchDelay: 400,
        pageLength: 15, lengthMenu: [15, 30, 90, 100], order: [[4, 'desc'], [2, 'desc']],
        ajax: function (request, callback) {
            $.ajax({
                url: $('meta[name="base_url"]').attr('content') + 'CashbookEntry/DT_List',
                type: 'POST', dataType: 'json', data: Object.assign({}, request, appliedFilters)
            }).done(function (response) {
                error(''); callback(response);
            }).fail(function (xhr) {
                error(xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Data gagal dimuat. Periksa koneksi atau login kembali.');
                callback({ draw: request.draw, recordsTotal: 0, recordsFiltered: 0, data: [] });
            });
        },
        columns: [
            {
                data: 'IsVoid', visible: false, orderable: false, searchable: false, className: 'no-export text-center', render: function (value, type) {
                    return type === 'display' ? '<input type="checkbox" class="form-check-input cashbook-print" aria-label="Select entry to print"' + (Number(value) === 1 ? ' disabled' : '') + '>' : '';
                }
            },
            { data: null, orderable: false, searchable: false, render: (value, type, row, meta) => meta.settings._iDisplayStart + meta.row + 1 },
            { data: 'JournalH_Code', render: function (value, type) {
                if (type !== 'display') return value;
                return '<div>' + escape(value) + '</div><button type="button" class="btn btn-sm btn-light-info cashbook-attachments mt-2"' +
                    ' title="Lampiran cashbook"><i class="fas fa-paperclip"></i> Attachments</button>';
            } }, { data: 'Payor_Payee', render: text },
            {
                data: 'CashBookDate', render: function (value, type) {
                    const date = String(value || '').substring(0, 10);
                    return type === 'display' || type === 'export' ? escape(date) : date;
                }
            },
            {
                data: 'Account_Name', render: function (value, type, row) {
                    return type === 'display' ? escape(value) + (row.Clearing_Account ? '<br><small>(' + escape(row.Clearing_Account) + ')</small>' : '') :
                        (value || '') + (row.Clearing_Account ? ' (' + row.Clearing_Account + ')' : '');
                }
            },
            { data: 'Currency_ID', render: text },
            {
                data: 'Total_Amount', className: 'text-end', render: function (value, type) {
                    if (type !== 'display') return value;
                    return formatAmount(value);
                }
            },
            { data: 'Memo', visible: false, render: text }, { data: 'Status', render: badge('DOC') },
            { data: 'Approval_Status', render: badge('APPROVAL') },
            { data: 'IsVoid', render: value => Number(value) === 1 ? 'Yes' : 'No' },
            {
                data: 'CashFlow_Name', visible: false, render: function (value, type, row) {
                    const label = Number(row.CashFlow_Type) === 0 ? 'None' : [row.Type_NameEn, value].filter(Boolean).join(' / ');
                    return type === 'display' ? escape(label) : label;
                }
            },
            detailColumn('Account_Name', false),
            detailColumn('DebCred', false),
            detailColumn('Amount', true),
            detailColumn('Description', false),
            detailColumn('CBR_No', false)
        ],
        columnDefs: [{ targets: '_all', defaultContent: '' }, { targets: [2, 4, 6, 7], className: 'dt-nowrap' }],
        rowCallback: function (row, data) {
            $(row).toggleClass('cashbook-void', Number(data.IsVoid) === 1);
        },
        buttons: [
            {
                text: 'Export to :',
                className: 'btn disabled text-dark bg-white',
                enabled: false
            },
            {
                extend: 'copy',
                text: '<i class="far fa-copy fs-2"></i>',
                titleAttr: 'Copy data halaman ini',
                className: 'btn btn-light-warning',
                title: 'List Of Cahbook Entry',
                exportOptions: exportOptions
            },
            {
                text: '<i class="far fa-file-excel fs-2"></i> Export All',
                titleAttr: 'Export seluruh data sesuai filter dan kolom yang ditampilkan',
                className: 'btn btn-light-success',
                action: function (event, dt, node) { exportExcel(dt, node); }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns fs-2"></i> Columns',
                className: 'btn btn-light-primary',
                columns: ':not(.no-export)'
            },
        ],
        language: {
            searchPlaceholder: 'Search...', emptyTable: 'No cashbook entries found.',
            lengthMenu: '_MENU_ headers',
            info: 'Showing _START_ to _END_ of _TOTAL_ headers',
            infoEmpty: 'Showing 0 headers',
            infoFiltered: '(filtered from _MAX_ total headers)'
        }
    });
    $('#add-criterion').on('click', addCriterion);
    $('#cashbook-criteria').on('change', '.criterion-field', function () { updateCriterion($(this).closest('.criterion')); });
    $('#cashbook-criteria').on('click', '.remove-criterion', function () { $(this).closest('.criterion').remove(); updateConditionButtons(); });
    $('#entry_type').on('change', function () { updateType(); applyFilters(); });
    $(form).on('submit', function (event) { event.preventDefault(); applyFilters(); });
    $('#reset-filters').on('click', function () {
        form.reset();
        dateDefaults.forEach(({ input, value }) => input._flatpickr.setDate(value, false));
        $('#cashbook-criteria').empty(); updateType(); addCriterion(); table.search(''); applyFilters();
    });

    const attachmentModal = document.getElementById('cashbook-attachment-modal');
    const attachmentForm = document.getElementById('cashbook-attachment-form');
    const uploadLimits = options.uploadLimits;
    let uploadBusy = false;
    let listRequest;
    function attachmentMessage(message, success) {
        $('#attachment-message').text(message || '').toggleClass('d-none', !message)
            .toggleClass('alert-success', !!success).toggleClass('alert-danger', !success);
    }
    function updateUploadControls() {
        $('#attachment-add').prop('disabled', uploadBusy || $('#attachment-upload-rows .attachment-upload-row').length >= uploadLimits.max_files);
        $('#attachment-upload-rows input, #attachment-upload-rows textarea, #attachment-upload-rows button, #attachment-save').prop('disabled', uploadBusy);
        $(attachmentModal).find('[data-bs-dismiss="modal"]').prop('disabled', uploadBusy);
    }
    function addUploadRow() {
        if (uploadBusy || $('#attachment-upload-rows .attachment-upload-row').length >= uploadLimits.max_files) return;
        $('#attachment-upload-rows').append(
            '<div class="attachment-upload-row row g-3 align-items-start border rounded p-3 mb-3">' +
            '<div class="col-md-5"><label class="form-label w-100">File<input type="file" name="files[]" class="form-control form-control-sm mt-2" accept=".pdf,.jpg,.jpeg,.png,.gif,.webp" required></label></div>' +
            '<div class="col-md-6"><label class="form-label w-100">Notes<textarea name="notes[]" rows="2" maxlength="2000" class="form-control form-control-sm mt-2" placeholder="Notes untuk file ini"></textarea></label></div>' +
            '<div class="col-md-1 pt-md-7"><button type="button" class="btn btn-sm btn-light-danger attachment-remove" aria-label="Remove file row"><i class="fas fa-minus"></i></button></div></div>');
        updateUploadControls();
    }
    function loadAttachments(journal) {
        if (listRequest) listRequest.abort();
        $('#attachment-list').text('Memuat lampiran...');
        listRequest = $.ajax({
            url: $('meta[name="base_url"]').attr('content') + 'CashbookEntry/attachments',
            data: { journal: journal }, dataType: 'json'
        }).done(function (response) {
            if ($('#attachment-journal').val() !== journal) return;
            const list = $('#attachment-list').empty();
            if (!response.data.length) { list.text('Belum ada lampiran.'); return; }
            response.data.forEach(function (file) {
                const item = $('<div class="border rounded p-3 mb-2"></div>');
                $('<a target="_blank" rel="noopener" class="fw-bold"></a>').attr('href', file.url).text(file.Original_Name).appendTo(item);
                $('<div class="text-muted fs-8 mt-1"></div>').text((Number(file.File_Size) / 1024).toFixed(1) + ' KB | ' + file.Created_By + ' | ' + file.Created_At).appendTo(item);
                $('<div class="mt-2 text-break"></div>').css('white-space', 'pre-wrap').text(file.Notes || '-').appendTo(item);
                list.append(item);
            });
        }).fail(function (xhr, status) {
            if (status !== 'abort' && $('#attachment-journal').val() === journal) {
                $('#attachment-list').text('Daftar lampiran belum dapat dimuat.');
                attachmentMessage(xhr.responseJSON && xhr.responseJSON.error || 'Gagal memuat lampiran. Buka ulang modal untuk mencoba kembali.');
            }
        });
    }
    $('#TableData tbody').on('click', '.cashbook-attachments', function () {
        if (uploadBusy) return;
        const header = table.row($(this).closest('tr')).data();
        attachmentForm.reset();
        $('#attachment-journal').val(header.JournalH_Code);
        $('#attachment-journal-label').text(header.JournalH_Code);
        $('#attachment-header-info').text(types[String(header.CashBookType).trim().toUpperCase()] + ' | ' + String(header.CashBookDate).substring(0, 10));
        $('#attachment-upload-rows').empty();
        attachmentMessage('');
        addUploadRow();
        bootstrap.Modal.getOrCreateInstance(attachmentModal).show();
        loadAttachments(header.JournalH_Code);
    });
    $('#attachment-add').on('click', addUploadRow);
    $('#attachment-upload-rows').on('click', '.attachment-remove', function () {
        if (uploadBusy) return;
        $(this).closest('.attachment-upload-row').remove();
        if (!$('#attachment-upload-rows .attachment-upload-row').length) addUploadRow();
        updateUploadControls();
    });
    $(attachmentModal).on('hide.bs.modal', function (event) { if (uploadBusy) event.preventDefault(); });
    $(attachmentForm).on('submit', function (event) {
        event.preventDefault();
        if (uploadBusy || !attachmentForm.reportValidity()) return;
        const files = $(attachmentForm).find('input[type="file"]').toArray().map(input => input.files[0]);
        if (files.some(file => !file || !/\.(pdf|jpe?g|png|gif|webp)$/i.test(file.name) || file.size === 0 || file.size > uploadLimits.max_file_bytes) ||
            files.reduce((total, file) => total + (file ? file.size : 0), 0) > uploadLimits.max_total_bytes) {
            attachmentMessage('Periksa format dan ukuran file sesuai batas upload.'); return;
        }
        const data = new FormData(attachmentForm);
        const journal = $('#attachment-journal').val();
        uploadBusy = true;
        updateUploadControls();
        attachmentMessage('');
        $('#attachment-save').text('Menyimpan...');
        $.ajax({
            url: $('meta[name="base_url"]').attr('content') + 'CashbookEntry/upload_attachments',
            type: 'POST', data: data, dataType: 'json', processData: false, contentType: false
        }).done(function (response) {
            $('#attachment-upload-rows').empty();
            attachmentMessage(response.message, true);
            loadAttachments(journal);
        }).fail(function (xhr) {
            attachmentMessage(xhr.responseJSON && xhr.responseJSON.error || 'Upload gagal. Periksa daftar lampiran sebelum mencoba kembali.');
        }).always(function () {
            uploadBusy = false;
            if (!$('#attachment-upload-rows .attachment-upload-row').length) addUploadRow();
            $('#attachment-save').html('<i class="fas fa-save"></i> Save Attachments');
            updateUploadControls();
        });
    });
});
