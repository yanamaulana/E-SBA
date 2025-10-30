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
                url: $('meta[name="base_url"]').attr('content') + "HistoryApproval/DT_List_History_Approval",
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
            columnDefs: [{
                width: 220,
                targets: 7
            }, {
                className: "text-center dt-nowrap",
                targets: [0, 3, 4, 5, 6, 11, 12, 15, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28],
            }, {
                className: "details-control pr-4 dt-nowrap",
                targets: [1]
            }],
            // orderCellsTop: true,
            // fixedColumns: true,
            scrollCollapse: true,
            scrollX: true,
            // scrollY: 410,
            // autoWidth: true,
            responsive: false,
            "rowCallback": function (row, data) {
                // if (data.is_active == "0") {
                // 	$('td', row).css('background-color', 'pink');
                // }
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
                text: `<i class="far fa-file-excel fs-2"></i>`,
                extend: 'excelHtml5',
                title: $('#table-title-history').text() + '~' + moment().format("YYYY-MM-DD"),
                className: "btn btn-light-success",
            }
            ],
        }).buttons().container().appendTo('TableDataHistory_wrapper .col-md-6:eq(0)');
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
            row.child(format(row.data())).show();
            tr.addClass('shown');
            // select element tr next var tr
            tr.next().addClass('bg-primary')
            getInsDetail(row.data().CBReq_No, row.data().Document_Number);
        }
    });

    function format(d) {
        let container = `<div class="row bg-primary">
                            <div class="col-md-6">
                                <div class="card my-3 px-1 py-1">
                                    <div class="table-responsive overflow-auto">
                                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-dark" colspan="4"><button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-light-info btn-cbr">🖨️ Cash Book Requisition Number : ${d.CBReq_No}</button></th>
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
                            <div class="col-md-6">
                                <div class="card my-3 px-1 py-1">
                                    <div class="table-responsive overflow-auto">
                                        <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;" >
                                            <thead>
                                                <tr>
                                                    <th class="text-center" colspan="4">
                                                        <button type="button" class="btn btn-sm btn-bg-light btn-color-dark">List Attachment : ${d.CBReq_No}</button>
                                                    </th>
                                                </tr>
                                                <tr class="bg-dark">
                                                    <th class="text-center">#</th>
                                                    <th class="text-center">File Name</th>
                                                    <th class="text-center">Doc Type</th>
                                                    <th class="text-center">Note</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody_attachment_${d.CBReq_No}">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row bg-primary">
                            <div class="col-md-12">
                        `;
        // detail ref cbr : ref_container
        if (d.Document_Number == null || d.Document_Number == '') {
            container = container + `<div class="card my-3 px-2 py-2">
                                        <div class="table-responsive overflow-auto">
                                            <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
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
                            </div>`;
        } else if (d.Document_Number.startsWith('PWU')) {
            container = container + `<div class="card my-3 px-2 py-2">
                                                <div class="table-responsive overflow-auto">
                                                    <table class="table-sm overflow-auto table-bordered rounded-sm" style="width:100%;">
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
                            </div>`;
        } else {
            container = container + `<div class="card my-3 px-2 py-2">
                                                <table class="table-sm table-bordered rounded-sm" style="width:100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-dark" colspan="7">
                                                                Purchase Invoice  : ${d.Document_Number}
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
                                                    <tbody id="tbody_vin_${d.CBReq_No}">
                                                    </tbody>
                                                </table>
                                        </div>
                                </div>
                            </div>`;
        }

        return container;

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


    $(document).on('click', '.btn-cbr', function () {
        let Cbr_no = $(this).val();

        window.open($('meta[name="base_url"]').attr('content') + `MyCbr/get_rpt_cbr/${Cbr_no}`, `RptCbr-${Cbr_no}`, 'width=854,height=480');
    })


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
