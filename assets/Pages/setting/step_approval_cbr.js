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

        // Hapus feedback sebelumnya (PENTING: agar tidak menumpuk)
        $input_group.find('.valid-feedback, .invalid-feedback').remove();

        $.ajax({
            dataType: "json",
            type: "POST",
            url: $('meta[name="base_url"]').attr('content') + "Set_StepApprovalCbr/ValidatePerson",
            data: {
                person: person,
                is_acc: is_acc,
                is_dir: is_dir
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
                    Toast.fire({
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
            element.closest('.fv-row').append(error);
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
                    Toast.fire({
                        icon: 'success',
                        title: response.msg
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