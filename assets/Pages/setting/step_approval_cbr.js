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

    // $('[data-bs-toggle="tooltip"]').tooltip();

    // Atau jika Anda menggunakan Bootstrap 4:
    $('[data-bs-toggle="tooltip"]').tooltip({ html: true });

    $('#Setting_Approval_Code').on('keypress', function (event) {
        const validKeyRegex = /^[a-zA-Z0-9_]$/;

        const charCode = event.which;
        const char = String.fromCharCode(charCode);

        // Jika tombol yang ditekan (char) TIDAK cocok dengan pola yang diizinkan
        if (!validKeyRegex.test(char)) {
            // Cek tambahan untuk tombol kontrol (misalnya: Backspace=8, Tab=9)
            // Agar tombol-tombol ini tidak diblokir
            if (charCode !== 0 && charCode !== 8 && charCode !== 9) {
                event.preventDefault();
                return false;
            }
        }
    });

    $('#Setting_Approval_Code').on('keyup', function () {
        // Transformasi ke huruf kapital (uppercase) setiap kali tombol dilepas
        const upperCaseValue = $(this).val().toUpperCase();
        $(this).val(upperCaseValue);
    });

    /**
     * Fungsi untuk merender ikon Font Awesome berdasarkan nilai 1 atau 0.
     * @param {string|number} data - Nilai kolom (1 atau 0).
     * @returns {string} HTML untuk ikon.
     */
    function renderStatusIcon(data) {
        if (data == 1) {
            // Ikon Check (Hijau)
            return '<b class="text-success">✅</b>';
        } else if (data == 0) {
            // Ikon Times/Close (Merah)
            return '<b class="text-danger">❌</b>';
        }
        // Nilai selain 1 atau 0 (misalnya null, undefined)
        return '-';
    }


    function Fn_Initialized_DataTable() {
        $("#TableData").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: '<"row"<"col-md-12"B><"col-md-11"f><"col-md-1"l>>rtip',
            orderCellsTop: true,
            select: true,
            "lengthMenu": [
                [15, 30, 90, 100],
                [15, 30, 90, 100]
            ],
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "Set_StepApprovalCbr/DT_List_Template",
                dataType: "json",
                type: "POST",
            },
            columns: [
                {
                    data: "SysId",
                    name: "SysId",
                    title: "#",
                    orderable: false, // Biasanya nomor urut tidak perlu diurutkan
                    render: function (data, type, row, meta) {
                        // Kolom Nomor Urut (Nomor Baris)
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "Is_Active",
                    name: "Is_Active",
                    title: "Status",
                    orderable: false, // Biasanya nomor urut tidak perlu diurutkan
                    render: function (data, type, row, meta) {
                        return '<button class="text-dark status_btn btn ' + (data == 1 ? 'btn-light-success btn-sm' : 'btn-light-danger btn-sm') + '">' + (data == 1 ? 'Active' : 'Inactive') + '</button>';
                    }
                },
                {
                    data: "Setting_Approval_Code",
                    name: "Setting_Approval_Code",
                    title: "Step Name"
                },
                // --- KOLOM JABATAN (Flag 1/0 dengan Ikon) ---
                {
                    data: "Chief",
                    name: "Chief",
                    title: "Chief",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "Chief_Name",
                    name: "Chief_Name",
                    title: "Chief <i class='fas fa-user'></i>"
                },
                {
                    data: "AsstManager",
                    name: "AsstManager",
                    title: "Asst. Mgr",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "AsstManager_Name",
                    name: "AsstManager_Name",
                    title: "Asst. Mgr <i class='fas fa-user'></i>"
                },
                // --- Kolom Jabatan Lainnya ---
                {
                    data: "Manager",
                    name: "Manager",
                    title: "Manager",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "Manager_Name",
                    name: "Manager_Name",
                    title: "Manager <i class='fas fa-user'></i>"
                },
                {
                    data: "SeniorManager",
                    name: "SeniorManager",
                    title: "Sr. Mgr",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "SeniorManager_Name",
                    name: "SeniorManager_Name",
                    title: "Sr. Mgr <i class='fas fa-user'></i>"
                },
                {
                    data: "GeneralManager",
                    name: "GeneralManager",
                    title: "GM",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "GeneralManager_Name",
                    name: "GeneralManager_Name",
                    title: "GM <i class='fas fa-user'></i>"
                },
                {
                    data: "Additional",
                    name: "Additional",
                    title: "Additional",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "Additional_Name",
                    name: "Additional_Name",
                    title: "Additional <i class='fas fa-user'></i>"
                },
                {
                    data: "Director",
                    name: "Director",
                    title: "Director",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "Director_Name",
                    name: "Director_Name",
                    title: "Director <i class='fas fa-user'></i>"
                },
                {
                    data: "PresidentDirector",
                    name: "PresidentDirector",
                    title: "Pres. Dir",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "PresidentDirector_Name",
                    name: "PresidentDirector_Name",
                    title: "Pres. Dir <i class='fas fa-user'></i>"
                },
                {
                    data: "FinanceDirector",
                    name: "FinanceDirector",
                    title: "Fin. Dir",
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return renderStatusIcon(data);
                    }
                },
                {
                    data: "FinanceDirector_Name",
                    name: "FinanceDirector_Name",
                    title: "Fin. Dir <i class='fas fa-user'></i>"
                },
                // --- Kolom Status Akhir ---
                {
                    data: "Doc_Legitimate_Pos_On",
                    name: "Doc_Legitimate_Pos_On",
                    title: "Legitimate Pos",
                    render: function (data, type, row, meta) {
                        // Tampilkan string, atau bisa dikondisikan jika null
                        return data ? data : '-';
                    }
                }
            ],
            order: [
                [1, "ASC"]
            ],
            columnDefs: [{
                className: "text-center",
                targets: "_all"
            }, {
                className: "details-control pr-4 dt-nowrap",
                targets: [1]
            }, {
                className: "dt-nowrap",
                targets: []
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
                text: `<i class="fas fa-plus"></i> Add New`,
                className: "btn btn-info",
                action: function (e, dt, node, config) {
                    init_hide_table();
                    init_show_form();

                }
            },
            {
                text: `<i class="fas fa-edit"></i> Edit Data`,
                className: "btn btn-warning",
                action: function (e, dt, node, config) {
                    var RowData = dt.rows({
                        selected: true
                    }).data();
                    if (RowData.length == 0 || RowData[0].isCancel == 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Ooppss...',
                            text: 'Silahkan pilih data yang akan di cancel !',
                            footer: '<a href="javascript:void(0)" class="text-danger">Notifikasi System</a>'
                        });
                    } else {
                        Fn_Init_Edit_Form(RowData[0].SysId)
                    }
                }
            },
            {
                text: `Export to :`,
                className: "btn btn-default disabled",
            },
            {
                text: `<i class="far fa-file-excel fs-2"></i>`,
                extend: 'excelHtml5',
                title: $('.card-title').text() + '_' + moment().format("YYYY-MM-DD"),
                className: "btn btn-light-success",
            }],
        }).buttons().container().appendTo('#TableData_wrapper .col-md-6:eq(0)');
    }

    Fn_Initialized_DataTable();

    $('#back-button').click(function () {
        init_show_table();
        init_hide_form();
    });


    $(document).on('click', '.validate-person', function () {
        // 1. Simpan referensi ke tombol yang diklik (this)
        let $button = $(this);

        // 2. Gunakan $button untuk mencari input dan mendapatkan nilainya
        let column_input = $button.parent().parent().find('input.nik');
        let $input_group = $button.parent().parent(); // Referensi ke div input-group

        person = column_input.val().trim();
        if (person == '') {
            Swal.fire({
                icon: "warning",
                title: "Warning !",
                html: `You must fill the NIK/username column first !`,
            });
            return;
        }
        is_acc = column_input.attr('data-fin');
        is_dir = column_input.attr('data-dir');
        data_pos = column_input.attr('data-pos');

        // Hapus feedback sebelumnya (PENTING: agar tidak menumpuk)
        $input_group.find('.valid-feedback, .invalid-feedback').remove();

        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "Set_StepApprovalCbr/ValidatePerson",
            data: {
                person: person,
                is_acc: is_acc,
                is_dir: is_dir,
                pos_must: data_pos
            },
            beforeSend: function () {
                $button.prop("disabled", true); // Gunakan $button
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

                // 3. Gunakan referensi yang disimpan ($input_group) untuk append
                if (response.code == 200) {
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    // APPEND ke div input-group yang benar
                    $input_group.append(`<span class="valid-feedback d-block">${response.name} (${response.position} - ${response.div})</span>`);

                    // Tambahkan validasi sukses ke hidden input
                    $input_group.find('input.validation').val('1');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.msg
                    });
                    // APPEND ke div input-group yang benar
                    $input_group.append(`<span class="invalid-feedback d-block">${response.msg}</span>`);

                    // Reset validasi
                    $input_group.find('input.validation').val('0');
                }

                $button.prop("disabled", false); // Gunakan $button
            },
            error: function (xhr, status, error) {
                $button.prop("disabled", false);
                Swal.close()
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "there is an error : " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                });
            }
        });
    });



    // --------------------------- form validation section

    const main_form = $('#main-form')
    main_form.validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.fv-row, .input-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
    $.validator.setDefaults({
        debug: true,
        success: 'valid'
    });

    $('#submit-main-data').click(function (e) {
        e.preventDefault();
        if (main_form.valid()) {
            Swal.fire({
                title: 'System Message !',
                text: `Are you sure to save this record ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    Init_Submit_Form(main_form)
                }
            })
        } else {
            $('html, body').animate({
                scrollTop: ($('.error:visible').offset().top - 200)
            }, 400);
        }
    });

    function Init_Submit_Form(DataForm) {
        let BtnAction = $('#submit-main-data');
        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "Set_StepApprovalCbr/store",
            data: DataForm.serialize(),
            beforeSend: function () {
                BtnAction.prop("disabled", true);
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
                        title: response.msg,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: response.msg
                    });
                }
                BtnAction.prop("disabled", false);
            },
            error: function (xhr, status, error) {
                Swal.close()
                BtnAction.prop("disabled", false);
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "there is an error : " + error;
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                });
            }
        });
    }


    function init_show_form() {
        $('#main-form').show();
        $('#submit-main-data').show();
    }
    function init_hide_form() {
        $('#main-form').hide();
        $('#submit-main-data').hide();
    }

    init_hide_form()

    function init_hide_table() {
        $('#el-table').hide();
    }
    function init_show_table() {
        $('#el-table').show();
    }

    init_show_table()

    function Fn_Init_Edit_Form(SysId) {
        window.location.href = `${$('meta[name="base_url"]').attr('content')}Set_StepApprovalCbr/edit/${SysId}`
    }

    $(document).on('click', '.status_btn', function () {
        let $button = $(this);
        let $row = $button.closest('tr');
        let table = $('#TableData').DataTable();
        let rowData = table.row($row).data();
        let sysId = rowData.SysId;
        let currentStatus = rowData.Is_Active; // Asumsikan ada kolom Is_Active di data tabel
        let newStatus = currentStatus == 1 ? 0 : 1; // Toggle status (1 -> 0 atau 0 -> 1)

        Swal.fire({
            title: 'System Message !',
            text: `Are you sure to update this record status ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    dataType: "json",
                    type: "POST",
                    url: $('meta[name="base_url"]').attr('content') + "Set_StepApprovalCbr/UpdateStatus",
                    data: { SysId: sysId, newStatus: newStatus },
                    beforeSend: function () {
                        $button.prop("disabled", true);
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
                                title: response.msg,
                                showConfirmButton: true
                            }).then(() => {
                                window.location.href = window.location.href;
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.msg
                            });
                        }
                        $button.prop("disabled", false);
                    },
                    error: function (xhr, status, error) {
                        Swal.close()
                        $button.prop("disabled", false);
                        var statusCode = xhr.status;
                        var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "there is an error : " + error;
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                        });
                    }
                });
            }
        })
    });
})