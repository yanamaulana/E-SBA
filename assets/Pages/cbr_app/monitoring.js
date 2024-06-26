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
            dom: 'lBfrtip',
            select: true,
            "lengthMenu": [
                [10, 30, 90, 1000],
                [10, 30, 90, 1000]
            ],
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "CbrMonitoring/DT_Monitoring",
                dataType: "json",
                type: "POST",
                data: {
                    from: $('#from').val(),
                    until: $('#until').val()
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
                            return `<span class="text-dark badge badge-warning">Not Paid</span>`
                        } else {
                            return `<span class="text-dark badge badge-success">Full Paid</span>`
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
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvStaff_Name}</span>` :
                            (data == 1 && (row.Status_AppvStaff == null || row.Status_AppvStaff == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvStaff_Name}</span>` :
                                (data == 1 && row.Status_AppvStaff == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvStaff_Name}</span>` :
                                    (data == 1 && row.Status_AppvStaff == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvStaff_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvStaff_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvChief",
                    name: "IsAppvChief",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvChief_Name}</span>` :
                            (data == 1 && (row.Status_AppvChief == null || row.Status_AppvChief == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvChief_Name}</span>` :
                                (data == 1 && row.Status_AppvChief == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvChief_Name}</span>` :
                                    (data == 1 && row.Status_AppvChief == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvChief_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvChief_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvAsstManager",
                    name: "IsAppvAsstManager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvAsstManager_Name}</span>` :
                            (data == 1 && (row.Status_AppvAsstManager == null || row.Status_AppvAsstManager == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvAsstManager_Name}</span>` :
                                (data == 1 && row.Status_AppvAsstManager == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvAsstManager_Name}</span>` :
                                    (data == 1 && row.Status_AppvAsstManager == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvAsstManager_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvAsstManager_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvManager",
                    name: "IsAppvManager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvManager_Name}</span>` :
                            (data == 1 && (row.Status_AppvManager == null || row.Status_AppvManager == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvManager_Name}</span>` :
                                (data == 1 && row.Status_AppvManager == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvManager_Name}</span>` :
                                    (data == 1 && row.Status_AppvManager == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvManager_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvManager_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvSeniorManager",
                    name: "IsAppvSeniorManager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvSeniorManager_Name}</span>` :
                            (data == 1 && (row.Status_AppvSeniorManager == null || row.Status_AppvSeniorManager == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvSeniorManager_Name}</span>` :
                                (data == 1 && row.Status_AppvSeniorManager == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvSeniorManager_Name}</span>` :
                                    (data == 1 && row.Status_AppvSeniorManager == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvSeniorManager_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvSeniorManager_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvGeneralManager",
                    name: "IsAppvGeneralManager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvGeneralManager_Name}</span>` :
                            (data == 1 && (row.Status_AppvGeneralManager == null || row.Status_AppvGeneralManager == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvGeneralManager_Name}</span>` :
                                (data == 1 && row.Status_AppvGeneralManager == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvGeneralManager_Name}</span>` :
                                    (data == 1 && row.Status_AppvGeneralManager == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvGeneralManager_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvGeneralManager_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvDirector",
                    name: "IsAppvDirector",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvDirector_Name}</span>` :
                            (data == 1 && (row.Status_AppvDirector == null || row.Status_AppvDirector == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvDirector_Name}</span>` :
                                (data == 1 && row.Status_AppvDirector == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvDirector_Name}</span>` :
                                    (data == 1 && row.Status_AppvDirector == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvDirector_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvDirector_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvPresidentDirector",
                    name: "IsAppvPresidentDirector",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvPresidentDirector_Name}</span>` :
                            (data == 1 && (row.Status_AppvPresidentDirector == null || row.Status_AppvPresidentDirector == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvPresidentDirector_Name}</span>` :
                                (data == 1 && row.Status_AppvPresidentDirector == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvPresidentDirector_Name}</span>` :
                                    (data == 1 && row.Status_AppvPresidentDirector == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvPresidentDirector_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvPresidentDirector_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvFinanceStaff",
                    name: "IsAppvFinanceStaff",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvFinanceStaff_Name}</span>` :
                            (data == 1 && (row.Status_AppvFinanceStaff == null || row.Status_AppvFinanceStaff == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvFinanceStaff_Name}</span>` :
                                (data == 1 && row.Status_AppvFinanceStaff == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvFinanceStaff_Name}</span>` :
                                    (data == 1 && row.Status_AppvFinanceStaff == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvFinanceStaff_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvFinanceStaff_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvFinanceManager",
                    name: "IsAppvFinanceManager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvFinanceManager_Name}</span>` :
                            (data == 1 && (row.Status_AppvFinanceManager == null || row.Status_AppvFinanceManager == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvFinanceManager_Name}</span>` :
                                (data == 1 && row.Status_AppvFinanceManager == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvFinanceManager_Name}</span>` :
                                    (data == 1 && row.Status_AppvFinanceManager == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvFinanceManager_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvFinanceManager_Name}</span>`;

                    }
                },
                {
                    data: "IsAppvFinanceDirector",
                    name: "IsAppvFinanceDirector",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return (data == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="No approval needed." class="badge btn-icon bg-secondary"><i class="fas fa-ban text-dark"></i> ${row.AppvFinanceDirector_Name}</span>` :
                            (data == 1 && (row.Status_AppvFinanceDirector == null || row.Status_AppvFinanceDirector == null)) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Approval In Progress" class="badge btn-icon bg-warning"><i class="bi bi-hourglass-split text-dark"></i> ${row.AppvFinanceDirector_Name}</span>` :
                                (data == 1 && row.Status_AppvFinanceDirector == 0) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="rejected" class="badge btn-icon bg-danger"><i class="fas fa-times text-white"></i> ${row.AppvFinanceDirector_Name}</span>` :
                                    (data == 1 && row.Status_AppvFinanceDirector == 1) ? `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="approved" class="badge btn-icon bg-success text-dark"><i class="fas fa-check-double text-white"></i> ${row.AppvFinanceDirector_Name}</span>` :
                                        `<span data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" title="Undefined" class="badge btn-icon bg-info"><i class="fas fa-question"></i> ${row.AppvFinanceDirector_Name}</span>`;

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
                targets: [0, 3, 4, 5, 6, 11, 12, 15, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29],
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
            getInsDetail(row.data().CBReq_No, row.data().Document_Number);
        }
    });
    function format(d) {
        let cbr_container = `<div class="row py-3" style="background-color: #CFE2FF;">
                                <div class="container-fluid">
                                    <div class="card shadow-sm">
                                        <div class="card-body">
                                            <div class="table-responsive overflow-auto">
                                                <table class="table-sm table-striped overflow-auto table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-dark" colspan="2">Cash Book Requisition Number : ${d.CBReq_No}</th>
                                                            <th class="text-dark text-center" colspan="2"><button type="button" value="${d.CBReq_No}" class="btn btn-sm btn-light-info btn-attachment"><i class="fas fa-paperclip"></i> List Attachment</button></th>
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
                                                            <table class="table-sm table-striped overflow-auto table-bordered">
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
                                                            <table class="table-sm table-striped overflow-auto table-bordered">
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
                                                                            <button type="button" value="${d.Document_Number}" class="btn btn-sm btn-light-danger rpt-vin"><i class="fas fa-search"></i> Purchase Invoice</button>
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
                                <td style="white-space: pre-line; max-width: 250px;">${item.PO_Number}</td>
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
                                <td style="white-space: pre-line; max-width: 250px;">${item.PO_NUMBER}</td>
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
