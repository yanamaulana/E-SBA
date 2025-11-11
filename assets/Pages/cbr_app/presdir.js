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
        dom: 'lBfrtip',
        orderCellsTop: true,
        select: false,
        "lengthMenu": [
            [15, 30, 90, 100],
            [15, 30, 90, 100]
        ],
        ajax: {
            url: $('meta[name="base_url"]').attr('content') + "CbrAppPresidentDirector/DT_List_To_Approve",
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
            targets: [0, 2, 3, 4, 5, 8, 9, 10, 11, 12, 13, 14, 15],
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
            text: `<i class="fas fa-check"></i> Approve`,
            className: "btn btn-success",
            action: function (e, dt, node, config) {
                Swal.fire({
                    title: 'System Message !',
                    text: `Are you sure to approve all checked submission ?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Fn_Approve_Submission();
                    }
                })
            }
        },
        {
            text: `-`,
            className: "btn btn-default btn-icon disabled",
        },
        {
            text: `<i class="fas fa-times text-white fs-3"></i> Reject`,
            className: "btn btn-danger",
            action: function (e, dt, node, config) {
                Swal.fire({
                    title: 'System Message',
                    text: 'Please provide the reason for rejecting the submission(s) below:',
                    // 🔥 Tambahkan input textarea
                    input: 'textarea',
                    inputLabel: 'Rejection Reason (Required)',
                    inputPlaceholder: 'Enter your justification here...',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33', // Merah untuk Reject
                    cancelButtonColor: '#6c757d', // Abu-abu
                    confirmButtonText: 'Yes, Reject',

                    // Validasi bahwa input tidak boleh kosong
                    inputValidator: (value) => {
                        if (!value || value.trim() === '') {
                            return 'You must enter a reason for rejection!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // result.value berisi alasan yang dimasukkan pengguna
                        Fn_Reject_Submission(result.value);
                    }
                });
            }
        }],
    }).buttons().container().appendTo('#TableData_wrapper .col-md-6:eq(0)');

    function Fn_Approve_Submission() {
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
            url: $('meta[name="base_url"]').attr('content') + "CbrAppPresidentDirector/approve_submission",
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
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    $('#CheckAll').removeAttr('checked');
                    $('#TableData').DataTable().ajax.reload(null, false);
                    $("#TableDataHistory").DataTable().ajax.reload(null, false);
                } else {
                    Toast.fire({
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

    function Fn_Reject_Submission(rejectionReason) {
        if ($('input[name="CBReq_No[]"]:checked').length == 0) {
            return Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'You need check the submission first !',
                footer: '<a href="javascript:void(0)">Notifikasi System</a>'
            });
        }

        var formData = $('#form-submission').serialize();
        formData += '&rejection_reason=' + encodeURIComponent(rejectionReason); // Tambahkan alasan

        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "CbrAppPresidentDirector/reject_submission",
            data: formData,
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
                    $('#CheckAll').removeAttr('checked');
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    $('#TableData').DataTable().ajax.reload(null, false);
                    $("#TableDataHistory").DataTable().ajax.reload(null, false);
                } else {
                    Toast.fire({
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
