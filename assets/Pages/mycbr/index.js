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

    var TableData = $("#TableData").DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        paging: true,
        // dom: 'lBfrtip',
        dom: '<"row mb-3"<"col-sm-12"B>><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
        orderCellsTop: true,
        select: false,
        "lengthMenu": [
            [15, 30, 90, 100],
            [15, 30, 90, 100]
        ],
        ajax: {
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/DT_List_To_Approve",
            dataType: "json",
            type: "POST",
        },
        columns: [{
            data: 'CBReq_No',
            name: "CheckBox",
            orderable: false,
            render: function (data, type, row, meta) {
                return `<div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${row.CBReq_No}" id="${row.CBReq_No}" name="CBReq_No[]">
                  </div>`
            }
        },
        {
            data: "CBReq_No",
            name: "CBReq_No",
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
            data: "CBReq_Status",
            name: "CBReq_Status",
            render: function (data) {
                if (data == 3) {
                    return `<a hreff="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Close" class="badge badge-success btn-icon"><i class="text-white fas fa-file-archive"></i></a>`
                } else if (data == 2) {
                    return `<a hreff="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Open" class="badge badge-info btn-icon"><i class="text-white fas fa-folder-open"></i></a></button>`
                } else {
                    return `<a hreff="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="New" class="badge badge-warning btn-icon"><i class="text-white fas fa-file"></i></a></button>`
                }
            }
        },
        {
            data: "Paid_Status",
            name: "Paid_Status",
            render: function (data) {
                if (data == 'NP') {
                    return `<span class="text-dark badge badge-warning">Not Paid</span>`;
                } else if (data == 'HP') {
                    return `<span class="text-dark badge badge-primary">Half Paid</span>`;
                } else {
                    return `<span class="text-dark badge badge-success">Full Paid</span>`;
                }
            }
        },
        {
            data: "Creation_DateTime",
            name: "Creation_DateTime",
            visible: false
        },
        {
            data: "Created_By",
            name: "Created_By",
            visible: false
        },
        {
            data: "First_Name",
            name: "First_Name",
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
        }
        ],
        order: [
            [3, "DESC"]
        ],
        columnDefs: [{
            className: "text-center",
            targets: [0, 2, 3, 4, 5, 8, 9, 10, 11, 12, 13],
        }, {
            className: "details-control pr-4 dt-nowrap",
            targets: [1]
        }, {
            className: "dt-nowrap",
            targets: [6]
        }],
        autoWidth: false,
        responsive: false,
        "rowCallback": function (row, data) {
            // if (data.is_active == "0") {
            // 	$('td', row).css('background-color', 'pink');
            // }
        },
        preDrawCallback: function () {
            $("#TableData tbody td").addClass("blurry");
        },
        language: {
            processing: '<i style="color:#4a4a4a" class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span><p><span style="color:#4a4a4a" style="text-align:center" class="loading-text"></span> ',
            searchPlaceholder: "Search..."
        },
        drawCallback: function () {
            $("#TableData tbody td").addClass("blurry");
            setTimeout(function () {
                $("#TableData tbody td").removeClass("blurry");
            });
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        "buttons": [{
            text: `<i class="fas fa-external-link-alt"></i> Send Submission`,
            className: "btn btn-success",
            action: function (e, dt, node, config) {
                Swal.fire({
                    title: 'System Message !',
                    text: `Are you sure to submit all checked submission CBR ?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Fn_Send_Submission();
                    }
                })
            }
        },
        {
            text: `-`,
            className: "btn btn-default btn-icon disabled",
        },
            // {
            //     text: `<i class="fas fa-times text-white fs-3"></i> Reject (Not Send)`,
            //     className: "btn btn-danger",
            //     action: function (e, dt, node, config) {
            //         Swal.fire({
            //             title: 'System Message !',
            //             text: `Are you sure to reject all checked submission ?`,
            //             icon: 'warning',
            //             showCancelButton: true,
            //             confirmButtonColor: '#3085d6',
            //             cancelButtonColor: '#d33',
            //             confirmButtonText: 'Yes'
            //         }).then((result) => {
            //             if (result.isConfirmed) {
            //                 Fn_Reject_Submission();
            //             }
            //         })
            //     }
            // },
            // {
            // 	text: `Export to :`,
            // 	className: "btn disabled text-dark bg-white",
            // }, {
            // 	text: `<i class="far fa-copy fs-2"></i>`,
            // 	extend: 'copy',
            // 	className: "btn btn-light-warning",
            // }, {
            // 	text: `<i class="far fa-file-excel fs-2"></i>`,
            // 	extend: 'excelHtml5',
            // 	title: $('#table-title').text() + '~' + moment().format("YYYY-MM-DD"),
            // 	className: "btn btn-light-success",
            // }, {
            // 	text: `<i class="far fa-file-pdf fs-2"></i>`,
            // 	extend: 'pdfHtml5',
            // 	title: $('#table-title').text() + '~' + moment().format("YYYY-MM-DD"),
            // 	className: "btn btn-light-danger",
            // 	orientation: "landscape"
            // }, {
            // 	text: `<i class="fas fa-print fs-2"></i>`,
            // 	extend: 'print',
            // 	className: "btn btn-light-dark",
            // }
        ],
    }).buttons().container().appendTo('#TableData_wrapper .col-md-6:eq(0)');


    function Fn_Initialized_DataTable() {
        $("#TableDataHistory").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: '<"row mb-3"<"col-sm-12"B>><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
            select: true,
            "lengthMenu": [
                [10, 30, 90, 1000],
                [10, 30, 90, 1000]
            ],
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "MyCbr/DT_List_History_Submission",
                dataType: "json",
                type: "POST",
                data: {
                    from: $('#from').val(),
                    until: $('#until').val(),
                    column_range: $('#column_range').val(),
                }
            },
            columns: [
                {
                    data: "CBReq_No", name: "CBReq_No", orderable: false, render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: "CBReq_No", name: "CBReq_No", },
                { data: "Type", name: "Type", visible: false },
                {
                    data: "Document_Date", name: "Document_Date", render: function (data) {
                        return data ? data.substring(0, data.indexOf(' ')) : '-'; // Tambah cek NULL
                    }
                },
                {
                    data: "Rec_Created_At", name: "Rec_Created_At", render: function (data) {
                        return data ? data.substring(0, data.indexOf(' ')) : '-'; // Tambah cek NULL
                    }
                },
                { data: "Currency_Id", name: "Currency_Id" },
                {
                    data: "Amount", name: "Amount", render: function (data) {
                        return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
                    }
                },
                { data: "Document_Number", name: "Document_Number" },
                { data: "Descript", name: "Descript" },
                { data: "baseamount", name: "baseamount", visible: false },
                { data: "curr_rate", name: "curr_rate", visible: false },
                { data: "Approval_Status", name: "Approval_Status", visible: false },
                {
                    data: "CBReq_Status", name: "CBReq_Status", render: function (data) {
                        if (data == 3) {
                            return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Close" class="badge badge-success btn-icon"><i class="text-white fas fa-file-archive"></i></a>`;
                        } else if (data == 2) {
                            return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Open" class="badge badge-info btn-icon"><i class="text-white fas fa-folder-open"></i></a>`;
                        } else {
                            return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="New" class="badge badge-warning btn-icon"><i class="text-white fas fa-file"></i></a>`;
                        }
                    }
                },
                {
                    data: "Paid_Status", name: "Paid_Status", render: function (data) {
                        if (data == 'NP') {
                            return `<span class="text-dark badge badge-warning">Not Paid</span>`;
                        } else if (data == 'HP') {
                            return `<span class="text-dark badge badge-info">Half Paid</span>`;
                        } else {
                            return `<span class="text-dark badge badge-success">Full Paid</span>`;
                        }
                    }
                },
                { data: "Creation_DateTime", name: "Creation_DateTime", visible: false },
                { data: "Created_By", name: "Created_By", visible: false },
                { data: "First_Name", name: "First_Name", orderable: false },
                { data: "Last_Update", name: "Last_Update", visible: false },
                { data: "Acc_ID", name: "Acc_ID", visible: false },
                { data: "Approve_Date", name: "Approve_Date", visible: false },
                { data: "IsAppvStaff", name: "IsAppvStaff", visible: false },
                { data: "IsAppvChief", name: "IsAppvChief", visible: false },
                { data: "IsAppvAsstManager", name: "IsAppvAsstManager", visible: false },
                {
                    data: "IsAppvManager", name: "IsAppvManager", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvManager);
                    }
                },
                {
                    data: "IsAppvSeniorManager", name: "IsAppvSeniorManager", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvSeniorManager);
                    }
                },
                {
                    data: "IsAppvGeneralManager", name: "IsAppvGeneralManager", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvGeneralManager);
                    }
                },
                {
                    data: "IsAppvAdditional", name: "IsAppvAdditional", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvAdditional);
                    }
                },
                {
                    data: "IsAppvDirector", name: "IsAppvDirector", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvDirector);
                    }
                },
                {
                    data: "IsAppvPresidentDirector", name: "IsAppvPresidentDirector", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvPresidentDirector);
                    }
                },
                {
                    data: "IsAppvFinanceDirector", name: "IsAppvFinanceDirector", orderable: false, render: function (data, type, row, meta) {
                        return renderApprovalStatus(data, row.Status_AppvFinanceDirector);
                    }
                }
            ],
            order: [
                [3, "DESC"]
            ],
            columnDefs: [
                { width: 220, targets: 7 },
                {
                    className: "text-center dt-nowrap",
                    // Hati-hati dengan indeks. Ini disesuaikan dengan daftar kolom di atas.
                    targets: [0, 3, 4, 5, 6, 11, 12, 15, 22, 23, 24, 25, 26, 27, 28],
                }, {
                    className: "details-control pr-4 dt-nowrap",
                    targets: [1]
                }
            ],
            scrollCollapse: true,
            scrollX: true,
            scrollY: 410,
            responsive: false,
            preDrawCallback: function () {
                $("#TableDataHistory tbody td").addClass("blurry"); // Poin 1: Tambahkan #
            },
            language: {
                processing: '<i style="color:#4a4a4a" class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span><p><span style="color:#4a4a4a" style="text-align:center" class="loading-text"></span> ',
                searchPlaceholder: "Search..."
            },
            drawCallback: function () {
                $("#TableDataHistory tbody td").removeClass("blurry"); // Poin 1: Tambahkan #
                $('[data-bs-toggle="tooltip"]').tooltip();
                DataTable.tables({ visible: true, api: true }).columns.adjust();
            },
            "rowCallback": function (row, data) {
                // console.log(data.Legitimate)
                if (data.Status_AppvManager == '2' ||
                    data.Status_AppvSeniorManager == '2' ||
                    data.Status_AppvGeneralManager == '2' ||
                    data.Status_AppvAdditional == '2' ||
                    data.Status_AppvDirector == '2' ||
                    data.Status_AppvPresidentDirector == '2' ||
                    data.Status_AppvFinanceDirector == '2') {
                    $('td', row).css('background-color', '#F8D7DA');
                }

                if (data.Legitimate == '1') {
                    $('td', row).css('background-color', '#D4EDDA');
                }
            },
            "buttons": [
                { text: `Export to :`, className: "btn disabled text-dark bg-white" },
                { text: `<i class="far fa-copy fs-2"></i>`, extend: 'copy', className: "btn btn-light-warning" },
                { text: `<i class="far fa-file-excel fs-2"></i>`, extend: 'excelHtml5', title: $('#table-title-history').text() + '~' + moment().format("YYYY-MM-DD"), className: "btn btn-light-success" }
            ],
        }).buttons().container().appendTo('#TableDataHistory_wrapper .col-md-6:eq(0)'); // Poin 5: Tambahkan #
    }



    $('#do--filter').on('click', function () {
        $("#TableDataHistory").DataTable().clear().destroy(), Fn_Initialized_DataTable(), DataTable.tables({ visible: true, api: true }).columns.adjust();
    })


    $(document).on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tr.closest('table').DataTable().row(tr);
        var tableId = tr.closest('table').attr('id');

        // Perhitungan is_hst_table
        var is_hst_table = (tableId == 'TableDataHistory') ? 1 : 0;

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // 🔥 KOREKSI: Panggil format() DENGAN DUA ARGUMEN, 
            // lalu berikan hasilnya ke row.child()
            var detailContent = format(row.data(), is_hst_table);

            row.child(detailContent).show();
            tr.addClass('shown');
            getInsDetail(row.data().CBReq_No, row.data().Document_Number);
        }
    });

    // document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((el) => {
    //     el.addEventListener('shown.bs.tab', () => {
    //         DataTable.tables({ visible: true, api: true }).columns.adjust();
    //     });
    // });

    function format(d, is_hst_table) {
        console.log(is_hst_table)
        var attachmentButton = (is_hst_table == 1)
            ? `<button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-light-primary btn-list-attachment"><i class="fas fa-paperclip"></i> List Attachment</button>`
            : `<button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-primary btn-attachment"><i class="fas fa-paperclip"></i> Upload Attachment</button>`;
        let cbr_container = `<div class="row py-3" style="background-color: #CFE2FF;">
                                <div class="container-fluid">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table-sm table-striped overflow-auto table-bordered" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-dark" colspan="2">Cash Book Requisition Number : ${d.CBReq_No}</th>
                                                            <th class="text-dark text-center">
                                                            <button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-info btn-cbr"><i class="fas fa-print"></i> Cash Book Requisition</button> 
                                                            ${attachmentButton}
                                                        </tr>
                                                        <tr class="bg-dark">
                                                            <th class="text-center">Account</th>
                                                            <th class="text-center">Description</th>
                                                            <th class="text-center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody_${d.CBReq_No}">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
        if (d.Document_Number == null || d.Document_Number == '') {
            let container = cbr_container + `<div class="container-fluid">
                                                <div class="card shadow-sm mt-5">
                                                    <div class="card-body">
                                                        <div class="table-responsive overflow-auto">
                                                            <table class="table-sm table-striped overflow-auto table-bordered" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-dark" colspan="11">Purchase Invoice  : -N/A-</th>
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
                                                                <tbody id="tbody_vin_${d.CBReq_No}">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
            return container;
        } else if (d.Document_Number.startsWith('PWU')) {
            let container = cbr_container + `<div class="container-fluid">
                                                <div class="card shadow-sm mt-5">
                                                    <div class="card-body">
                                                        <div class="table-responsive overflow-auto">
                                                            <table class="table-sm table-striped overflow-auto table-bordered" style="width:100%;">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-dark" colspan="11">Purchase Order  : ${d.Document_Number}</th>
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
                                                                <tbody id="tbody_vin_${d.CBReq_No}">
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
            return container;
        } else {
            let container = cbr_container + `<div class="container-fluid">
                                                <div class="card shadow-sm mt-5">
                                                    <div class="card-body">
                                                            <table class="table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-dark" colspan="7">
                                                                            Purchase Invoice  : ${d.Document_Number}
                                                                        </th>
                                                                        <th style="text-align: center;" colspan="3">
                                                                            <button type="button" value="${d.Document_Number}" class="btn btn-sm btn-info rpt-vin"><i class="fas fa-search"></i> Purchase Invoice</button>
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
                                                                <tbody id="tbody_vin_${d.CBReq_No}">
                                                                </tbody>
                                                            </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
            return container;
        }

    }
    $(document).on('click', '.rpt-vin', function () {
        let vin = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_detail_purchase_invoice/${vin}`, `RptVin-${vin}`, 'width=800,height=600');
    })

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
                                <td class="dt-nowrap">${item.PO_Number}</td>
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
                                <td class="dt-nowrap">${item.Invoice_Number}</td>
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
    }

    $(document).on('click', '.btn-attachment', function () {
        $('#txt-cbr').text($(this).val());
        $.ajax({
            // dataType: "json",
            type: "GET",
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/m_f_cbr_attachment",
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

    $(document).on('click', '.btn-list-attachment', function () {
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


    function Fn_Send_Submission() {
        if ($('input[name="CBReq_No[]"]:checked').length == 0) {
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You need check the submission first !',
                footer: '<a href="javascript:void(0)">Notifikasi System</a>'
            });
        }

        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/approve_submission",
            data: $('#form-submission').serialize(),
            beforeSend: function () {
                Swal.fire({
                    title: 'Loading....',
                    html: '<div class="spinner-border text-primary"></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                })
            },
            success: function (response) {
                Swal.close()
                if (response.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    $('#CheckAll').removeAttr('checked');
                    $('#TableData').DataTable().ajax.reload(null, false);
                    $("#TableDataHistory").DataTable().clear().destroy(), Fn_Initialized_DataTable(), DataTable.tables({ visible: true, api: true }).columns.adjust();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.msg
                    });
                }
            },
            error: function (xhr, status, error) {
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "Terjadi kesalahan: " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                });
            }
        });
    }

    $(document).on('click', '.rpt-vin', function () {
        let vin = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_detail_purchase_invoice/${vin}`, `RptVin-${vin}`, 'width=800,height=600');
    })

    $(document).on('click', '.btn-cbr', function () {
        let Cbr_no = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_rpt_cbr/${Cbr_no}`, `RptCbr-${Cbr_no}`, 'width=854,height=480');
    })

    var TableDataResubmission = $("#TableDataResubmission").DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
        paging: true,
        dom: '<"row mb-3"<"col-sm-12"B>><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
        select: false,
        "lengthMenu": [
            [10, 30, 90, 1000],
            [10, 30, 90, 1000]
        ],
        ajax: {
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/DT_List_Resubmission",
            dataType: "json",
            type: "POST"
        },

        columns: [
            {
                data: "CBReq_No", name: "CBReq_No", orderable: false, render: function (data, type, row, meta) {
                    return `<div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${row.CBReq_No}" id="${row.CBReq_No}" name="CBReq_No_Resubmission[]">
                  </div>`
                }
            },
            { data: "CBReq_No", name: "CBReq_No", },
            { data: "Type", name: "Type", visible: false },
            {
                data: "Document_Date", name: "Document_Date", render: function (data) {
                    return data ? data.substring(0, data.indexOf(' ')) : '-'; // Tambah cek NULL
                }
            },
            {
                data: "Rec_Created_At", name: "Rec_Created_At", render: function (data) {
                    return data ? data.substring(0, data.indexOf(' ')) : '-'; // Tambah cek NULL
                }
            },
            { data: "Currency_Id", name: "Currency_Id" },
            {
                data: "Amount", name: "Amount", render: function (data) {
                    return parseFloat(data).toLocaleString('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 });
                }
            },
            { data: "Document_Number", name: "Document_Number" },
            { data: "Descript", name: "Descript" },
            { data: "baseamount", name: "baseamount", visible: false },
            { data: "curr_rate", name: "curr_rate", visible: false },
            { data: "Approval_Status", name: "Approval_Status", visible: false },
            {
                data: "CBReq_Status", name: "CBReq_Status", render: function (data) {
                    if (data == 3) {
                        return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Close" class="badge badge-success btn-icon"><i class="text-white fas fa-file-archive"></i></a>`;
                    } else if (data == 2) {
                        return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="Open" class="badge badge-info btn-icon"><i class="text-white fas fa-folder-open"></i></a>`;
                    } else {
                        return `<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" data-bs-custom-class="tooltip-dark" title="New" class="badge badge-warning btn-icon"><i class="text-white fas fa-file"></i></a>`;
                    }
                }
            },
            {
                data: "Paid_Status", name: "Paid_Status", render: function (data) {
                    if (data == 'NP') {
                        return `<span class="text-dark badge badge-warning">Not Paid</span>`;
                    } else if (data == 'HP') {
                        return `<span class="text-dark badge badge-info">Half Paid</span>`;
                    } else {
                        return `<span class="text-dark badge badge-success">Full Paid</span>`;
                    }
                }
            },
            { data: "Creation_DateTime", name: "Creation_DateTime", visible: false },
            { data: "Created_By", name: "Created_By", visible: false },
            { data: "First_Name", name: "First_Name", orderable: false },
            { data: "Last_Update", name: "Last_Update", visible: false },
            { data: "Acc_ID", name: "Acc_ID", visible: false },
            { data: "Approve_Date", name: "Approve_Date", visible: false },
            { data: "IsAppvStaff", name: "IsAppvStaff", visible: false },
            { data: "IsAppvChief", name: "IsAppvChief", visible: false },
            { data: "IsAppvAsstManager", name: "IsAppvAsstManager", visible: false },
            {
                data: "IsAppvManager", name: "IsAppvManager", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvManager);
                }
            },
            {
                data: "IsAppvSeniorManager", name: "IsAppvSeniorManager", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvSeniorManager);
                }
            },
            {
                data: "IsAppvGeneralManager", name: "IsAppvGeneralManager", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvGeneralManager);
                }
            },
            {
                data: "IsAppvAdditional", name: "IsAppvAdditional", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvAdditional);
                }
            },
            {
                data: "IsAppvDirector", name: "IsAppvDirector", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvDirector);
                }
            },
            {
                data: "IsAppvPresidentDirector", name: "IsAppvPresidentDirector", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvPresidentDirector);
                }
            },
            {
                data: "IsAppvFinanceDirector", name: "IsAppvFinanceDirector", orderable: false, render: function (data, type, row, meta) {
                    return renderApprovalStatus(data, row.Status_AppvFinanceDirector);
                }
            }
        ],
        order: [
            [3, "DESC"]
        ],
        columnDefs: [
            { width: 220, targets: 7 },
            {
                className: "text-center dt-nowrap",
                // Hati-hati dengan indeks. Ini disesuaikan dengan daftar kolom di atas.
                targets: [0, 3, 4, 5, 6, 11, 12, 15, 22, 23, 24, 25, 26, 27, 28],
            }, {
                className: "details-control pr-4 dt-nowrap",
                targets: [1]
            }
        ],
        scrollCollapse: true,
        scrollX: true,
        scrollY: 410,
        responsive: false,
        autoWidth: true,
        preDrawCallback: function () {
            $("#TableDataResubmission tbody td").addClass("blurry"); // Poin 1: Tambahkan #
        },
        language: {
            processing: '<i style="color:#4a4a4a" class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span><p><span style="color:#4a4a4a" style="text-align:center" class="loading-text"></span> ',
            searchPlaceholder: "Search..."
        },
        drawCallback: function () {
            $("#TableDataResubmission tbody td").removeClass("blurry"); // Poin 1: Tambahkan #
            $('[data-bs-toggle="tooltip"]').tooltip();
            DataTable.tables({ visible: true, api: true }).columns.adjust();
        },
        "buttons": [{
            text: `<i class="fas fa-external-link-alt"></i> Send Submission`,
            className: "btn btn-success",
            action: function (e, dt, node, config) {
                Swal.fire({
                    title: 'System Message !',
                    text: `Are you sure to Resubmission all checked CBR ?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Fn_Send_reSubmission();
                    }
                })
            }
        },
        { text: `Export to :`, className: "btn disabled text-dark bg-white" },
        { text: `<i class="far fa-copy fs-2"></i>`, extend: 'copy', className: "btn btn-light-warning" },
        { text: `<i class="far fa-file-excel fs-2"></i>`, extend: 'excelHtml5', title: $('#table-title-history').text() + '~' + moment().format("YYYY-MM-DD"), className: "btn btn-light-success" }
        ],
    }).buttons().container().appendTo('#TableData_wrapper .col-md-6:eq(0)');

    function Fn_Send_reSubmission() {
        if ($('input[name="CBReq_No_Resubmission[]"]:checked').length == 0) {
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You need check the submission first !',
                footer: '<a href="javascript:void(0)">Notifikasi System</a>'
            });
        }

        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "MyCbr/approve_resubmission",
            data: $('#form-resubmission').serialize(),
            beforeSend: function () {
                Swal.fire({
                    title: 'Loading....',
                    html: '<div class="spinner-border text-primary"></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                })
            },
            success: function (response) {
                Swal.close()
                if (response.code == 200) {
                    Swal.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    $('#CheckAll').removeAttr('checked');
                    $('#TableDataResubmission').DataTable().ajax.reload(null, false);
                    $("#TableDataHistory").DataTable().clear().destroy(), Fn_Initialized_DataTable(), DataTable.tables({ visible: true, api: true }).columns.adjust();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.msg
                    });
                }
            },
            error: function (xhr, status, error) {
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "Terjadi kesalahan: " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                });
            }
        });
    }


    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach((el) => {
        el.addEventListener('shown.bs.tab', () => {
            // Panggil penyesuaian kolom untuk SEMUA tabel yang terlihat
            DataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
})

function check_uncheck_checkbox(isChecked) {
    if (isChecked) {
        $('input[name="CBReq_No[]"]').each(function () {
            this.checked = true;
        });
    } else {
        $('input[name="CBReq_No[]"]').each(function () {
            this.checked = false;
        });
    }
}
function check_uncheck_checkbox_resubmission(isChecked) {
    if (isChecked) {
        $('input[name="CBReq_No_Resubmission[]"]').each(function () {
            this.checked = true;
        });
    } else {
        $('input[name="CBReq_No_Resubmission[]"]').each(function () {
            this.checked = false;
        });
    }
}
