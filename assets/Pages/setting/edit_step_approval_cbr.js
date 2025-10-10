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


    $(document).on('click', '.validate-person', function () {
        let $button = $(this);
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
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
                    });
                    $input_group.append(`<span class="valid-feedback d-block">${response.name} (${response.position} - ${response.div})</span>`);
                    $input_group.find('input.validation').val('1');

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: response.msg
                    });
                    $input_group.append(`<span class="invalid-feedback d-block">${response.msg}</span>`);
                    $input_group.find('input.validation').val('0');
                }

                $button.prop("disabled", false);
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



    function validatePerson($button) {
        let column_input = $button.parent().parent().find('input.nik, select.nik'); // Menambahkan select.nik
        let $input_group = $button.parent().parent();

        // Ambil nilai dan trim
        person = column_input.val().trim();

        // PERUBAHAN: Jika person kosong, HENTIKAN TANPA MENAMPILKAN SWAL.
        if (person == '') {
            // Hapus feedback sebelumnya (jika ada)
            $input_group.find('.valid-feedback, .invalid-feedback').remove();
            // Reset nilai validasi menjadi 0 (jika ada input.validation)
            $input_group.find('input.validation').val('0');
            return;
        }

        // Ambil atribut data
        is_acc = column_input.attr('data-acc'); // Menggunakan data-acc (jika ada)
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
                $button.prop("disabled", true);
                // Opsional: Anda bisa menggunakan SweetAlert2 saat load, 
                // tetapi untuk auto-validasi saat load, lebih baik menggunakan visual loading di elemen terkait.
                // Swal.fire({...})
            },
            success: function (response) {
                Swal.close()
                if (response.code == 200) {
                    // ... (Toast.fire dan feedback berhasil)
                    $input_group.append(`<span class="valid-feedback d-block">${response.name} (${response.position} - ${response.div})</span>`);
                    $input_group.find('input.validation').val('1');

                } else {
                    // ... (Swal.fire error dan feedback gagal)
                    $input_group.append(`<span class="invalid-feedback d-block">${response.msg}</span>`);
                    $input_group.find('input.validation').val('0');
                }
                $button.prop("disabled", false);
            },
            error: function (xhr, status, error) {
                // ... (Penanganan error AJAX)
                $button.prop("disabled", false);
                Swal.close()
                var statusCode = xhr.status;
                var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "there is an error : " + error;
                $input_group.append(`<span class="invalid-feedback d-block">Error: ${statusCode} - ${errorMessage}</span>`);
                $input_group.find('input.validation').val('0');
            }
        });
    }

    // ----------------------------------------------------------------------
    // 1. EVENT: Saat Halaman Selesai di Load (Auto-Validasi)
    // ----------------------------------------------------------------------
    // Loop melalui SEMUA tombol validasi yang ada saat page load
    $('.validate-person').each(function () {
        // Panggil fungsi validasi untuk setiap tombol
        validatePerson($(this));
    });
})