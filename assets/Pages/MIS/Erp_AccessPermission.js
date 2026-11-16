$(document).ready(function () {
    const site_url = $('meta[name="base_url"]').attr('content');

    // Inisialisasi Select2 pada dropdown filter
    $('.select2-filter').select2({
        theme: 'bootstrap5',
        width: '100%'
    });

    // --- 1. INISIALISASI DATATABLE ---
    var table = $('#table_erp_access').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 30,
        lengthMenu: [
            [30, 10, 25, 100, -1],
            ['30 Baris', '10', '25', '100', 'Semua']
        ],
        dom: '<"row mb-3"<"col-sm-12">><"row"<"col-sm-11"f><"col-sm-1"l>>rtip',
        ajax: {
            url: site_url + "Report/MIS/DT_ERP_AccessPermission",
            type: "POST",
            data: function (d) {
                d.usergroup_id = $('#filter_usergroup').val();
                d.function_id = $('#filter_function').val();
            }
        },
        columns: [
            {
                data: 'UserGroupFuncL_ID',
                name: 'UserGroupFuncL_ID',
                visible: false
            },
            {
                data: 'UserGroup_Name',
                name: 'UserGroup_Name'
            },
            {
                data: 'SF_UFUNC_NAME_EN',
                name: 'SF_UFUNC_NAME_EN'
            },
            {
                data: 'sf_ufunc_access',
                name: 'sf_ufunc_access',
                searchable: false,
                className: 'text-center',
                render: function (data, type, row) {
                    let checked = (data && data.toLowerCase() === 'delete') ? 'checked' : '';
                    return `<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input access-switch" type="checkbox" data-id="${row.UserGroupFuncL_ID}" ${checked}>
                            </div>`;
                }
            },
            {
                data: 'isLog',
                name: 'isLog',
                className: 'text-center',
                render: function (data, type, row) {
                    return (data == 1) ? '<span class="badge badge-light-success">Ya</span>' : '<span class="badge badge-light-danger">Tidak</span>';
                }
            }
        ],
        order: [[1, 'asc'], [2, 'asc']]
    });

    $('#btn_filter').on('click', function () {
        table.ajax.reload(); // Muat ulang data tabel dengan filter baru
    });

    // --- 3. EVENT SAAT SWITCH AKSES DIUBAH ---
    $('#table_erp_access tbody').on('change', '.access-switch', function () {
        const sw = $(this);
        const id = sw.data('id');
        const hasAccess = sw.is(':checked');

        // --- JIKA MEMBERIKAN AKSES (SWITCH ON) ---
        if (hasAccess) {
            // Diasumsikan variabel 'employees' berisi daftar karyawan dari controller.
            // Anda perlu menambahkan query di controller dan mengirimkannya ke view.
            // Contoh di view: <script>const employees = <?= json_encode($employees); ?>;</script>
            let employeeOptions = '<option value="">-- Pilih Karyawan --</option>';
            if (typeof employees !== 'undefined' && Array.isArray(employees)) {
                employees.forEach(emp => {
                    employeeOptions += `<option value="${emp.User_ID}">${emp.First_Name} (${emp.Emp_ID})</option>`;
                });
            } else {
                console.error("Variabel 'employees' tidak ditemukan. Harap teruskan dari controller.");
                sw.prop('checked', false);
                Swal.fire('Error', 'Data karyawan tidak ditemukan untuk konfirmasi.', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Pemberian Akses',
                html: `
                    <p>Anda akan memberikan akses.</p>
                    <p>Pilih karyawan yang bertanggung jawab atas pemberian akses ini:</p>
                    <select id="swal-employee" class="form-select">
                        ${employeeOptions}
                    </select>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Berikan Akses',
                cancelButtonText: 'Batal',
                didOpen: () => {
                    const confirmButton = Swal.getConfirmButton();
                    const employeeSelect = $('#swal-employee');
                    confirmButton.disabled = true; // Tombol konfirmasi nonaktif di awal

                    employeeSelect.select2({
                        theme: 'bootstrap5',
                        width: '100%',
                        dropdownParent: $(Swal.getPopup()),
                        placeholder: '-- Pilih Karyawan --',
                        allowClear: true,
                        minimumResultsForSearch: 0
                    });

                    // Select2 memicu event change melalui jQuery.
                    employeeSelect.on('change.employeeAccess', () => {
                        confirmButton.disabled = !employeeSelect.val();
                        Swal.resetValidationMessage();
                    });
                },
                willClose: () => {
                    const employeeSelect = $('#swal-employee');
                    employeeSelect.off('.employeeAccess');
                    if (employeeSelect.hasClass('select2-hidden-accessible')) {
                        employeeSelect.select2('destroy');
                    }
                },
                preConfirm: () => {
                    const employeeId = document.getElementById('swal-employee').value;
                    if (!employeeId) {
                        Swal.showValidationMessage('Harap pilih seorang karyawan');
                        return false;
                    }
                    return employeeId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil AJAX dengan menyertakan ID karyawan
                    ajaxUpdatePermission(id, hasAccess, sw, result.value);
                } else {
                    // Kembalikan switch jika dibatalkan
                    sw.prop('checked', false);
                }
            });
        }
        // --- JIKA MENCABUT AKSES (SWITCH OFF) ---
        else {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Akses akan dicabut untuk grup fungsi ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Cabut Akses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil AJAX tanpa ID karyawan
                    ajaxUpdatePermission(id, hasAccess, sw);
                } else {
                    // Kembalikan switch jika dibatalkan
                    sw.prop('checked', true);
                }
            });
        }
    });

    // Fungsi helper untuk AJAX agar tidak duplikat kode
    function ajaxUpdatePermission(id, hasAccess, switchElement, employeeId = null) {
        let ajaxData = {
            id: id,
            access: hasAccess
        };

        if (employeeId) {
            ajaxData.employee_id = employeeId;
        }

        $.ajax({
            url: site_url + 'Report/MIS/update_access_permission',
            type: 'POST',
            dataType: 'json',
            data: ajaxData,
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Gagal',
                        text: response.message
                    });
                    // Kembalikan posisi switch jika update gagal
                    switchElement.prop('checked', !hasAccess);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mengirim permintaan!'
                });
                // Kembalikan posisi switch jika error
                switchElement.prop('checked', !hasAccess);
            }
        });
    }

});
