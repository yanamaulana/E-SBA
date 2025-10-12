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

    function Fn_Initialized_DataTable(approval) {
        $("#TableData").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: '<"row"<"col-md-11"B><"col-md-11"f><"col-md-1"l>>rtip',
            orderCellsTop: true,
            select: false,
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/DT_List_Approval_Assignment",
                dataType: "json",
                type: "POST",
                data: { Approval: approval }
            },
            columns: [
                {
                    data: "SysId",
                    name: "SysId",
                    orderable: false, // Biasanya nomor urut tidak perlu diurutkan
                    render: function (data, type, row, meta) {
                        // Kolom Nomor Urut (Nomor Baris)
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "UserName_Employee",
                    name: "UserName_Employee",
                },
                {
                    data: "First_Name",
                    name: "First_Name",
                },
                {
                    data: null,
                    name: "Action",
                    orderable: false, // Biasanya nomor urut tidak perlu diurutkan
                    render: function (data, type, row, meta) {
                        return `<button class="btn btn-sm btn-danger btn-delete" data-emp="${row.UserName_Employee}" type="button" ><i class="fas fa-trash-alt"></i> Delete</button>`;
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
                targets: []
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
            }, "buttons": [
                {
                    text: `Export to :`,
                    className: "btn btn-default disabled",
                },
                {
                    text: `<i class="far fa-file-excel fs-2"></i>`,
                    extend: 'excelHtml5',
                    title: 'List Assignment ' + $('#Approval option:selected').text() + ' date_' + moment().format("YYYY-MM-DD"),
                    className: "btn btn-success",
                }
            ],
        })
    }

    $('#Approval').on('change', function (e) {
        e.preventDefault();
        var approval = $('#Approval').val();
        if (approval == null || approval == "") {
            Swal.fire({
                icon: "warning",
                title: "Warning !",
                html: `Please select Step Approval first.`,
            });
            return false;
        }
        $("#TableData").DataTable().destroy();
        Fn_Initialized_DataTable(approval);
    });

    // pembuatan ajax untuk select 2, mengambil data user dengan loading 800
    $('#Employee').select2({
        searching: true,
        allowClear: true,
        minimumInputLength: 3,
        placeholder: 'Select User',
        multiple: true, // PASTIKAN ini diaktifkan jika menggunakan versi Select2 lama
        ajax: {
            url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/Select2_User_Employee",
            dataType: 'json',
            minimumInputLength: 3,
            delay: 800,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            // KOREKSI: Gunakan item.id dan item.text dari data PHP
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }
        }
    });


    $('#preview').on('click', function (e) {
        e.preventDefault();
        var approval = $('#Approval').val();
        var approvalText = $('#Approval option:selected').text();
        if (approval == null || approval == "") {
            Swal.fire({
                icon: "warning",
                title: "Warning !",
                html: `Please select Step Approval first.`,
            });
            return false;
        }
        $("#TableData").DataTable().destroy();
        Fn_Initialized_DataTable_Preview(approval);

        $('#modal_preview').modal('show');
        $('#modal_title').html(`Preview Step Approval: <b>${approvalText}</b>`);

    });

    function Fn_Initialized_DataTable_Preview(approval) {
        $("#TableData_Preview").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: 'lrti',
            orderCellsTop: true,
            select: false,
            ajax: {
                url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/DT_Preview_Step_Approval",
                dataType: "json",
                type: "POST",
                data: { Approval: approval }
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
                    visible: false
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
                $("#TableData_Preview tbody td").addClass("blurry");
            },
            language: {
                processing: '<i style="color:#4a4a4a" class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span><p><span style="color:#4a4a4a" style="text-align:center" class="loading-text"></span> ',
                searchPlaceholder: "Search..."
            },
            drawCallback: function () {
                $("#TableData_Preview tbody td").addClass("blurry");
                setTimeout(function () {
                    $("#TableData_Preview tbody td").removeClass("blurry");
                });
            }
        })
    }


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
                text: `Are you sure to Assign this step approval to selected employees ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    Init_Submit_Form({
                        Approval: $('#Approval').val(),
                        Employee: $('#Employee').val()
                    })
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
            url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/store",
            data: DataForm,
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
                        $("#TableData").DataTable().destroy();
                        Fn_Initialized_DataTable(DataForm.Approval);

                        // $('#Approval').val(null).trigger('change');
                        $('#Employee').val([]).trigger('change');
                    });
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Warning !",
                        html: response.msg,
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

    $(document).on('click', '.btn-delete', function () {
        var employee = $(this).attr('data-emp');
        var approval = $('#Approval').val();
        Swal.fire({
            title: 'System Message !',
            text: `Are you sure to remove this employee from this step approval ?`,
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
                    url: $('meta[name="base_url"]').attr('content') + "Approval_Assignment/delete",
                    data: {
                        Employee: employee
                    },
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
                                title: response.msg,
                                showConfirmButton: true
                            }).then(() => {
                                $("#TableData").DataTable().destroy();
                                Fn_Initialized_DataTable(approval);
                            });
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Warning !",
                                html: response.msg,
                            });
                        }
                    },
                    error: function (xhr, status, error) {
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
            }
        })
    });




});