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

    function Fn_Initialized_DataTable(approval) {
        $("#TableData").DataTable({
            destroy: true,
            processing: true,
            serverSide: true,
            paging: true,
            dom: '<"row"<"col-md-11"f><"col-md-1"l>>rtip',
            orderCellsTop: true,
            select: true,
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
                        return `<button class="btn btn-sm btn-danger" onclick="Fn_Delete_Approval_Assignment('${row.UserName_Employee}')"><i class="fas fa-trash-alt"></i> Delete</button>`;
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
            }
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



});