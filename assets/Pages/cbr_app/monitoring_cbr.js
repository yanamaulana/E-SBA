$(document).ready(function () {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })

    $('.date-picker').flatpickr();

    function renderApprovalDateTime(value) {
        if (value == null) {
            return '<span class="approval-datetime">-</span>';
        }

        const dateTime = value.replace('T', ' ');
        const date = $('<span>').text(dateTime.slice(0, 10)).html();
        const time = $('<span>').text(dateTime.slice(11, 16)).html();
        return '<span class="approval-datetime">' + date + '<br/>' + time + '</span>';
    }

    let detailResizeObserver;

    function Fn_Initialized_DataTable() {
        $("#TableDataHistory").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: '<"row mb-3"<"col-sm-12"B>><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
            select: false,
            "lengthMenu": [
                [15, 10, 50, 100, 9999],
                [15, 10, 50, 100, 9999]
            ],
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "MonitoringCbr/DT_Monitoring_global",
                dataType: "json",
                type: "POST",
                data: {
                    from: $('#from').val(),
                    until: $('#until').val(),
                    column_range: $('#column_range').val(),
                    employee: $('#employee').val()
                }
            },
            columns: [
                {
                    data: "CBReq_No",
                    name: "CBReq_No",
                    visible: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "CBReq_No",
                    name: "CBReq_No",
                }, {
                    data: "Has_Submitted_Approval",
                    name: "Has_Submitted_Approval",
                    render: function (data) {
                        if (data == 0) {
                            return `<h5>❌</h5>`;
                        } else {
                            return `<h5>✅</h5>`;
                        }
                    }
                },
                {
                    data: "Type",
                    name: "Type",
                    visible: false
                },
                {
                    data: "Document_Date",
                    name: "Document_Date",
                    render: function (data) {
                        return data.substring(0, data.indexOf(' '));
                    }
                },
                {
                    data: "Rec_Created_At",
                    name: "Rec_Created_At",
                    render: function (data) {
                        if (data == null || data == '') {
                            return '-'
                        } else {
                            return data.substring(0, data.indexOf(' '));
                        }
                    }
                },
                {
                    data: "Currency_Id",
                    name: "Currency_Id",
                },
                {
                    data: "Amount",
                    name: "Amount",
                    render: function (data) {
                        return parseFloat(data).toLocaleString('en-US', {
                            minimumFractionDigits: 4,
                            maximumFractionDigits: 4
                        });
                    }
                },
                {
                    data: "Document_Number",
                    name: "Document_Number",
                },
                {
                    data: "Descript",
                    name: "Descript",
                },
                {
                    data: "baseamount",
                    name: "baseamount",
                    visible: false
                },
                {
                    data: "curr_rate",
                    name: "curr_rate",
                    visible: false
                },
                {
                    data: "Approval_Status",
                    name: "Approval_Status",
                    visible: false
                },
                {
                    data: "isClose",
                    name: "isClose",
                    render: function (data) {
                        if (data == 0 || data == '' || data == null) {
                            return `<span class="text-dark badge badge-success">Open</span>`;
                        } else {
                            return `<span class="text-dark badge badge-danger">VOID</span>`;
                        }
                    }
                },
                {
                    data: "Payment_Status", // <-- Ganti data dan name ke Payment_Status
                    name: "Payment_Status",
                    render: function (data, type, row, meta) {
                        if (row.isClose == 0 || row.isClose == '' || row.isClose == null) {
                            if (data == 0 || data == null || data == '') {
                                return `<span class="text-dark badge badge-warning">Not Paid</span>`;
                            } else if (data == 3) {
                                return `<span class="text-white badge badge-info" style="background-color: #17a2b8;">Partially Paid</span>`;
                            } else if (data == 1) {
                                return `<span class="text-white badge badge-success">Fully Paid</span>`;
                            } else if (data == 2) {
                                return `<span class="text-white badge badge-danger">Payment Rejected</span>`;
                            } else {
                                return `<span class="text-dark badge badge-light">-</span>`;
                            }
                        } else {
                            return `<span class="text-dark badge badge-warning">Not Paid</span>`;
                        }
                    }
                },
                {
                    data: "Creation_DateTime",
                    name: "Creation_DateTime",
                    visible: false
                },
                {
                    data: "UserDivision",
                    name: "UserDivision",
                    orderable: true
                },
                {
                    data: "First_Name",
                    name: "First_Name",
                    orderable: false,
                },
                {
                    data: "Last_Update",
                    name: "Last_Update",
                    visible: false
                },
                {
                    data: "Acc_ID",
                    name: "Acc_ID",
                    visible: false
                },
                {
                    data: "Approve_Date",
                    name: "Approve_Date",
                    visible: false
                },
                // =========================== SECTION APPROVAL
                {
                    data: "IsAppvStaff",
                    name: "IsAppvStaff",
                    visible: false,
                },
                {
                    data: "IsAppvChief",
                    name: "IsAppvChief",
                    visible: false,
                },
                {
                    data: "IsAppvAsstManager",
                    name: "IsAppvAsstManager",
                    orderable: false,
                    visible: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvAsstManager) + ' <br/> ' + renderApprovalDateTime(row.AppvAsstManager_At);
                    }
                },
                {
                    data: "IsAppvManager",
                    name: "IsAppvManager",
                    visible: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvManager) + ' <br/> ' + renderApprovalDateTime(row.AppvManager_At);
                    }
                },
                {
                    data: "IsAppvSeniorManager",
                    name: "IsAppvSeniorManager",
                    visible: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvSeniorManager) + ' <br/> ' + renderApprovalDateTime(row.AppvSeniorManager_At);
                    }
                },
                {
                    data: "IsAppvGeneralManager",
                    name: "IsAppvGeneralManager",
                    visible: false,
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvGeneralManager) + ' <br/> ' + renderApprovalDateTime(row.AppvGeneralManager_At);
                    }
                },
                {
                    data: "IsAppvAdditional",
                    name: "IsAppvAdditional",
                    visible: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvAdditional) + ' <br/> ' + renderApprovalDateTime(row.AppvAdditional_At);
                    }
                },
                {
                    data: "IsAppvFinancePerson",
                    name: "IsAppvFinancePerson",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvFinancePerson) + ' <br/> ' + renderApprovalDateTime(row.AppvFinancePerson_At);
                    }
                },
                {
                    data: "IsAppvDirector",
                    name: "IsAppvDirector",
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvDirector) + ' <br/> ' + renderApprovalDateTime(row.AppvDirector_At);
                    }
                },
                {
                    data: "IsAppvFinanceDirector",
                    name: "IsAppvFinanceDirector",
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvFinanceDirector) + ' <br/> ' + renderApprovalDateTime(row.AppvFinanceDirector_At);
                    }
                },
                {
                    data: "IsAppvPresidentDirector",
                    name: "IsAppvPresidentDirector",
                    orderable: false,
                    render: function (data, type, row) {
                        return renderApprovalStatusWithName(row.Has_Submitted_Approval, data, row.Status_AppvPresidentDirector) + ' <br/> ' + renderApprovalDateTime(row.AppvPresidentDirector_At);
                    }
                },
                {
                    data: "Payment_Plan_Date",
                    name: "Payment_Plan_Date",
                    className: "payment-plan-date",
                }
            ],
            order: [
                [3, "DESC"]
            ],
            columnDefs: [{
                className: "text-center",
                targets: [0, 2, 4, 5, 6, 12, 13, 16, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
            }, {
                className: "details-control pr-4 dt-nowrap",
                targets: [1]
            }, {
                className: "text-end",
                targets: [7, 11]
            }],
            // orderCellsTop: true,
            // fixedColumns: true,
            scrollCollapse: true,
            scrollX: true,
            // scrollY: 410,
            // autoWidth: true,
            responsive: false,
            "rowCallback": function (row, data) {
                if (data.isClose == '1') {
                    $('td', row).css('background-color', '#e97781');
                } else {
                    if (data.Status_AppvManager == '2' ||
                        data.Status_AppvSeniorManager == '2' ||
                        data.Status_AppvGeneralManager == '2' ||
                        data.Status_AppvAdditional == '2' ||
                        data.Status_AppvFinancePerson == '2' ||
                        data.Status_AppvDirector == '2' ||
                        data.Status_AppvPresidentDirector == '2' ||
                        data.Status_AppvFinanceDirector == '2') {
                        $('td', row).css('background-color', '#F8D7DA');
                    }

                    if (data.Legitimate == '1') {
                        $('td', row).css('background-color', '#D4EDDA');
                    }
                }
            },
            preDrawCallback: function () {
                $("TableDataHistory tbody td").addClass("blurry");
            },
            language: {
                processing: '<i style="color:#4a4a4a" class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span><p><span style="color:#4a4a4a" style="text-align:center" class="loading-text"></span> ',
                searchPlaceholder: "Search..."
            },
            drawCallback: function () {
                $("TableDataHistory tbody td").addClass("blurry");
                setTimeout(function () {
                    $("TableDataHistory tbody td").removeClass("blurry");
                });
                $('[data-bs-toggle="tooltip"]').tooltip();
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            },
            "buttons": [{
                text: `Export to :`,
                className: "btn disabled text-dark bg-white",
            }, {
                text: `<i class="far fa-copy fs-2"></i>`,
                extend: 'copy',
                className: "btn btn-light-warning",
            }, {
                text: `<i class="far fa-file-excel fs-2"></i> Export All`,
                className: "btn btn-light-success",
                action: function (e, dt, node, config) {
                    var from = $('#from').val();
                    var until = $('#until').val();
                    var column_range = $('#column_range').val();
                    var employee = $('#employee').val();
                    var url = $('meta[name="base_url"]').attr('content') + 'MonitoringCbr/export_excel?from=' + from + '&until=' + until + '&column_range=' + column_range + '&employee=' + employee;
                    window.open(url, '_blank');
                }
            }, {
                extend: 'colvis',
                text: '<i class="fas fa-columns fs-2"></i> Pengaturan Kolom',
                titleAttr: 'Tampilkan atau sembunyikan kolom',
                className: 'btn btn-light-primary',
                columns: ':not(:first-child)'
            },
                // {
                //     text: `<i class="far fa-file-pdf fs-2"></i>`,
                //     extend: 'pdfHtml5',
                //     title: $('#table-title-history').text() + '~' + moment().format("YYYY-MM-DD"),
                //     className: "btn btn-light-danger",
                //     orientation: "landscape"
                // }, {
                //     text: `<i class="fas fa-print fs-2"></i>`,
                //     extend: 'print',
                //     className: "btn btn-light-dark",
                // }
            ],
        }).buttons().container().appendTo('TableDataHistory_wrapper .col-md-6:eq(0)');

        const table = $('#TableDataHistory').DataTable();
        const scrollBody = table.settings()[0].nScrollBody;
        const detailContainer = document.querySelector('.cbr-monitoring-table');
        if (detailResizeObserver) {
            detailResizeObserver.disconnect();
        }
        if (scrollBody && detailContainer) {
            const updateDetailWidth = function () {
                detailContainer.style.setProperty('--cbr-detail-width', Math.max(0, scrollBody.clientWidth - 8) + 'px');
            };
            updateDetailWidth();
            detailResizeObserver = new ResizeObserver(updateDetailWidth);
            detailResizeObserver.observe(scrollBody);
        }
    }

    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((el) => {
        el.addEventListener('shown.bs.tab', () => {
            DataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });

    $('#do--filter').on('click', function () {
        $("#TableDataHistory").DataTable().clear().destroy(), Fn_Initialized_DataTable(), DataTable.tables({ visible: true, api: true }).columns.adjust();
    })

    Fn_Initialized_DataTable()


    $(document).on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr'); // Menggunakan closest() untuk mendapatkan elemen tr terdekat
        var row = tr.closest('table').DataTable().row(tr); // Mendapatkan instance DataTable dari tabel terdekat

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row (the format() function would return the data to be shown)
            const detail = $('<div class="cbr-child-panel"><div class="cbr-child-scroll" tabindex="0" role="region" aria-label="Detail CBR, geser untuk melihat seluruh kolom"><div class="cbr-child-content"></div></div></div>');
            detail.find('.cbr-child-content').html(format(row.data()));
            row.child(detail).show();
            tr.addClass('shown');
            getInsDetail(row.data().CBReq_No, row.data().Document_Number);
        }
    });

    function format(d) {
        let container = `
        <div class="row bg-primary">
            <div class="col-md-6">
                <div class="card my-3 px-1 py-1">
                    <div class="table-responsive overflow-auto">
                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-dark" colspan="4">
                                        <button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-light-info btn-cbr">
                                            🖨️ Cash Book Requisition Number : ${d.CBReq_No}
                                        </button>
                                    </th>
                                </tr>
                                <tr class="bg-dark">
                                    <th class="text-center">Account</th>
                                    <th class="text-center">Description</th>
                                    <th class="text-center">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_${d.CBReq_No}"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card my-3 px-1 py-1">
                    <div class="table-responsive overflow-auto">
                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" colspan="4">
                                        <button type="button" class="btn btn-sm btn-bg-light btn-color-dark">
                                            List Attachment : ${d.CBReq_No}
                                        </button>
                                    </th>
                                </tr>
                                <tr class="bg-dark">
                                    <th class="text-center">#</th>
                                    <th class="text-center">File Name</th>
                                    <th class="text-center">Doc Type</th>
                                    <th class="text-center">Note</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_attachment_${d.CBReq_No}"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row bg-primary">
            <div class="col-md-6">
    `;

        // Detail Ref CBR : Ref Container
        if (d.Document_Number == null || d.Document_Number == '') {
            container += `
                <div class="card my-3 px-2 py-2">
                    <div class="table-responsive overflow-auto">
                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-dark" colspan="11">Purchase Invoice : -N/A-</th>
                                </tr>
                                <tr class="bg-dark">
                                    <th class="text-center">Invoice No</th>
                                    <th class="text-center">Vendor Invoice Number</th>
                                    <th class="text-center">Invoice Date</th>
                                    <th class="text-center">Due Date</th>
                                    <th class="text-center">Purchase Order Number</th>
                                    <th class="text-center">Vendor Name</th>
                                    <th class="text-center">Payment Status</th>
                                    <th class="text-center">Is Void</th>
                                    <th class="text-center">Document Status</th>
                                    <th class="text-center">Receipt Date</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_vin_${d.CBReq_No}"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        } else if (d.Document_Number.startsWith('PWU')) {
            container += `
                <div class="card my-3 px-2 py-2">
                    <div class="table-responsive overflow-auto">
                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-dark" colspan="11">Purchase Order : ${d.Document_Number}</th>
                                </tr>
                                <tr class="bg-dark">
                                    <th class="text-center">PO Number</th>
                                    <th class="text-center">Vendor</th>
                                    <th class="text-center">PO Date</th>
                                    <th class="text-center">Pick Up Date</th>
                                    <th class="text-center">Vendor SO Number</th>
                                    <th class="text-center">Document Status</th>
                                    <th class="text-center">PO Status</th>
                                    <th class="text-center">Approval</th>
                                    <th class="text-center">Invoiced</th>
                                    <th class="text-center">Active</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_vin_${d.CBReq_No}"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        } else {
            container += `
                <div class="card my-3 px-2 py-2">
                    <div class="table-responsive overflow-auto">
                        <table class="table-sm table-bordered rounded-sm" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-dark" colspan="7">
                                        Purchase Invoice : ${d.Document_Number}
                                    </th>
                                    <th style="text-align: center;" colspan="3">
                                        <button type="button" value="${d.Document_Number}" class="btn btn-sm btn-light-danger rpt-vin">🔍 Purchase Invoice</button>
                                    </th>
                                </tr>
                                <tr class="bg-dark">
                                    <th class="text-center">Invoice No</th>
                                    <th class="text-center">Vendor Invoice Number</th>
                                    <th class="text-center">Invoice Date</th>
                                    <th class="text-center">Due Date</th>
                                    <th class="text-center" style="white-space: pre-line; max-width: 200px;">Purchase Order Number</th>
                                    <th class="text-center">Vendor Name</th>
                                    <th class="text-center">Payment Status</th>
                                    <th class="text-center">Is Void</th>
                                    <th class="text-center">Document Status</th>
                                    <th class="text-center">Receipt Date</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_vin_${d.CBReq_No}"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        }
        container += `
                    </div>
                        <div class="row bg-primary">
                            <div class="col-md-6">
                                <div class="card my-3 px-2 py-2">
                                    <div class="table-responsive overflow-auto">
                                        <table class="table-sm table-bordered rounded-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th colspan="6">
                                                        <button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-warning">LIst BDJ - Cashbook</button>
                                                    </th>
                                                </tr>
                                                <tr class="bg-dark">
                                                    <th class="text-center">Doc Numb</th>
                                                    <th class="text-center">Payee</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Account</th>
                                                    <th class="text-center">Bank Payment</th>
                                                    <th class="text-center">Memo</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_bdj_${d.CBReq_No}"></tbody>
                                        </table>
                                    </div>
                                    <h6 class="mt-3 mb-2">List Upload BDJ</h6>
                                    <div class="table-responsive overflow-auto">
                                        <table class="table-sm table-bordered rounded-sm" style="width:100%;">
                                            <thead>
                                                <tr class="bg-dark">
                                                    <th class="text-center">BDJ</th>
                                                    <th class="text-center">File</th>
                                                    <th class="text-center">Ukuran</th>
                                                    <th class="text-center">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_bdj_upload_${d.CBReq_No}"><tr><td colspan="4">Memuat lampiran BDJ...</td></tr></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>`;

        return container;
    }

    $(document).on('click', '.rpt-vin', function () {
        let vin = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_detail_purchase_invoice/${vin}`, `RptVin-${vin}`, 'width=800,height=600');
    })

    $(document).on('click', '.rpt-bdj', function () {
        let bdj = $(this).data('bdj');

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_detail_bdj/${bdj}`, `RptBdj-${bdj}`, 'width=800,height=600');
    })

    function loadBdjUploads(requestNo, journals) {
        const body = $(document.getElementById('tbody_bdj_upload_' + requestNo)).empty();
        const codes = [...new Set((journals || []).map(item => item.JournalH_Code).filter(Boolean))];
        if (!codes.length) {
            body.append('<tr><td colspan="4">Belum ada BDJ untuk CBR ini.</td></tr>');
            return;
        }
        codes.forEach(function (journal) {
            const loading = $('<tr></tr>').append($('<td colspan="4"></td>').text(journal + ': memuat lampiran...')).appendTo(body);
            $.ajax({
                url: $('meta[name="base_url"]').attr('content') + 'MonitoringCbr/bdj_attachments',
                data: { journal: journal }, dataType: 'json'
            }).done(function (response) {
                if (!response.data.length) {
                    loading.find('td').text(journal + ': belum ada lampiran.');
                    return;
                }
                response.data.forEach(function (file) {
                    const row = $('<tr></tr>');
                    $('<td class="text-start text-nowrap"></td>').text(journal).appendTo(row);
                    $('<td class="text-start text-break"></td>').append(
                        $('<a target="_blank" rel="noopener"></a>').attr('href', file.url).text(file.Original_Name)
                    ).appendTo(row);
                    $('<td class="text-end text-nowrap"></td>').text((Number(file.File_Size) / 1024).toFixed(1) + ' KB').appendTo(row);
                    $('<td class="text-start text-break"></td>').css('white-space', 'pre-wrap').text(file.Notes || '-').appendTo(row);
                    row.insertBefore(loading);
                });
                loading.remove();
            }).fail(function () {
                loading.find('td').text(journal + ': lampiran gagal dimuat. Tutup dan buka kembali detail untuk mencoba lagi.');
            });
        });
    }

    function getInsDetail(Req_No, Ref_no) {
        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/get_detail_cbr",
            data: {
                Req_No: Req_No,
                Ref_no: Ref_no
            }, success: function (response) {
                var lastitem = '0';
                var i;
                var tr = $("#tbody_" + Req_No);
                tr.empty();
                if (response.code == 200) {
                    $.each(response.data, function (index, item) {
                        console.log(item.Account_Name);
                        tr.append(
                            `<tr>
                            <td class="text-center">${item.Account_Name}</td>
                            <td class="text-center">${item.Description}</td>
                            <td>${item.Amount_Detail}</td>
                            </tr>`);
                    });
                } else {
                    tr.append(`<tr><td colspan="3">Detail Cash Book Requisition Not found !</td></tr>`);
                }

                var tr = $("#tbody_vin_" + Req_No);
                if (Ref_no == null || Ref_no == '') {
                    tr.append(`<tr><td colspan="11">This Cash Book Requisition doesnt have a Purchase Invoice !</td></tr>`);
                }
                else if (Ref_no.startsWith('PWU')) {
                    if (response.code_vin == 200) {
                        $.each(response.dataVins, function (index, item) {
                            tr.append(
                                `<tr>
                                <td style="white-space: pre-line; max-width: 200px;">${item.PO_Number}</td>
                                <td class="text-center">${item.Account_Name}</td>
                                <td class="text-center">${item.PO_Date}</td>
                                <td class="text-center">${item.ETD}</td>
                                <td class="text-center">${item.SO_NumCustomer}</td>
                                <td class="text-center">${item.Doc_Status}</td>
                                <td class="text-center">${item.PO_Status}</td>
                                <td class="text-center">${item.Approval_Status}</td>
                                <td class="text-center">${item.Invoice_Status}</td>
                                <td class="text-center">${item.isNotActive}</td>
                            </tr>`);
                        });
                    } else {
                        tr.append(`<tr><td colspan="11">This Cash Book Requisition doesnt have a Purchase Order !</td></tr>`);
                    }
                } else {
                    if (response.code_vin == 200) {
                        $.each(response.dataVins, function (index, item) {
                            tr.append(
                                `<tr>
                                <td>${item.Invoice_Number}</td>
                                <td>${item.VenInvoice_Number}</td>
                                <td class="text-center">${item.Invoice_Date}</td>
                                <td class="text-center">${item.Due_Date}</td>
                                <td style="white-space: pre-line; max-width: 200px;">${item.PO_NUMBER}</td>
                                <td class="text-center">${item.Account_Name}</td>
                                <td class="text-center">${item.Invoice_Status}</td>
                                <td class="text-center">${item.isVoid}</td>
                                <td class="text-center">${item.is_document_received}</td>
                                <td class="text-center">${item.document_received_date}</td>
                            </tr>`);
                        });
                    } else {
                        tr.append(`<tr><td colspan="11">This Cash Book Requisition doesnt have a Purchase Invoice !</td></tr>`);
                    }
                }

                var tbody_attachment = $("#tbody_attachment_" + Req_No);
                if (response.data_Attachments.length > 0) {
                    $.each(response.data_Attachments, function (index, att) {
                        tbody_attachment.append(
                            `<tr>
                                <td class="text-center">${att.iteration}</td>
                                <td>${att.attachment}</td>
                                <td class="text-center">${att.AttachmentType}</td>
                                <td>${att.Note}</td>
                            </tr>`);
                    });
                } else {
                    tbody_attachment.append(`<tr><td colspan="4">This Cash Book Requisition doesnt have attachment file !</td></tr>`);
                }

                loadBdjUploads(Req_No, response.code_bdj == 200 ? response.dataBdjs : []);
                var tbody_bdj = $("#tbody_bdj_" + Req_No);
                if (response.code_bdj == 200) {
                    $.each(response.dataBdjs, function (index, bdj) {
                        tbody_bdj.append(
                            `<tr>
                                <td><a href="#" class="rpt-bdj" class="btn rpt-bdj" data-bdj="${bdj.JournalH_Code}">🖨️ ${bdj.JournalH_Code}</a></td>
                                <td>${bdj.Payor_Payee}</td>
                                <td>${bdj.CashBookDate}</td>
                                <td>${bdj.Account_Name}</td>
                                <td>${bdj.Currency_ID} ${bdj.Total_Amount}</td>
                                <td>${bdj.Memo}</td>
                            </tr>`);
                    });
                } else {
                    tbody_bdj.append(`<tr><td colspan="6">This Cash Book Requisition doesnt have BDJ !</td></tr>`);
                }



            }, error: function (xhr, status, error) {
                $(document.getElementById('tbody_bdj_upload_' + Req_No)).empty()
                    .append('<tr><td colspan="4">Lampiran BDJ gagal dimuat. Buka kembali detail untuk mencoba lagi.</td></tr>');
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "Terjadi kesalahan: " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>message: ${errorMessage}`,
                });
            }
        });
    }

    $(document).on('click', '.btn-cbr', function () {
        let Cbr_no = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_rpt_cbr/${Cbr_no}`, `RptCbr-${Cbr_no}`, 'width=854,height=480');
    })

    $(document).on('click', '.btn-attachment', function () {
        $('#txt-cbr').text($(this).val());
        $.ajax({
            // dataType: "json",
            type: "GET",
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/m_list_cbr_attachment",
            data: {
                CbrNo: $(this).val(),
            }, beforeSend: function () {
                Swal.fire({
                    title: 'Loading....',
                    html: '<div class="spinner-border text-primary"></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                })
            },
            success: function (ajaxData) {
                Swal.close()
                $("#location").html(ajaxData);
                $("#ModalAttachment").modal('show');
            }, error: function (xhr, status, error) {
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "Terjadi kesalahan: " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>message: ${errorMessage}`,
                });
            }
        });
    })


})
