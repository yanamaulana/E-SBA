<div class="modal fade" id="ModalAttachment" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="ModalAttachmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="ModalAttachmentLabel">Upload Attachment For : <?= $CbrNo; ?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-attachment" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="CbrNo" id="CbrNo" value="<?= $CbrNo; ?>">

                    <div class="row mb-3">
                        <label for="attachment" class="col-sm-3 col-form-label"><b>Attachment Type :</b></label>
                        <div class="col-sm-5 fv-row">
                            <select class="form-select form-select-sm rounded-5" data-control="select2" data-placeholder="Select an option" name="Type" id="Type" required data-allow-clear="true">
                                <option selected disabled>-Choose Attachment Type-</option>
                                <?php foreach ($Types->result() as $type) : ?>
                                    <?= '<option value="' . $type->Att_Code . '">' . $type->Att_Name . ' (' . $type->Att_Code . ')</option>' ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="attachment" class="col-sm-3 col-form-label"><b>Choose Attachment :</b></label>
                        <div class="col-sm-9 fv-row">
                            <input type="file" accept="image/jpeg,image/png,application/pdf" required class="form-control form-control-sm" name="attachment" id="attachment" placeholder="Choose file attachment....">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="note" class="col-sm-3 col-form-label"><b>Note Attachment :</b></label>
                        <div class="col-sm-9 fv-row">
                            <textarea rows="2" required class="form-control form-control-sm" name="note" id="note" placeholder="Note attachment...."></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="note" class="col-sm-3 col-form-label">&nbsp;</label>
                        <div class="col-sm-9">
                            <button type="button" id="submit--data" class="btn btn-sm btn-primary"><i class="fas fa-folder"></i> Upload</button>
                        </div>
                    </div>
                    <div class="row">
                        <hr />
                    </div>
                    <div class="mt-3 table-responsive">
                        <table class="table-sm table-striped table-hover table-bordered w-100">
                            <thead>
                                <tr style="background-color: #CFE2FF;">
                                    <th class="text-center">#</th>
                                    <th class="text-center">File Name</th>
                                    <th class="text-center">Note</th>
                                    <th class="text-center"><i class="fas fa-cogs text-dark"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-attachment">
                                <?php if ($Attachments->num_rows() > 0) : ?>
                                    <?php $i = 1; ?>
                                    <?php foreach ($Attachments->result() as $li) : ?>
                                        <tr>
                                            <td class="text-center"><?= $i; ?></td>
                                            <td class="">
                                                <a target='_blank' href="<?= base_url() ?>assets/Files/AttachmentCbr/<?= $li->Attachment_FileName; ?>"><?= $li->Attachment_FileName; ?></a>
                                            </td>
                                            <td class=""><?= $li->Note; ?></td>
                                            <td class="text-center">
                                                <button type="button" value="<?= $li->SysId ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="tooltip-dark" title="Delete" class="btn btn-icon btn-danger btn-sm  btn-delete-attachment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3" class="text-center">This Cbr Doesnt Have Attachment</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
        $('#Type').select2();

        const main_form = $('#form-attachment')
        main_form.validate({
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.fv-row').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        $.validator.setDefaults({
            debug: true,
            success: 'valid'
        });


        $('#submit--data').click(function(e) {
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
                    if (result.isConfirmed == true) {
                        Init_Submit_Form(main_form)
                    }
                })
            } else {
                $('html, body').animate({
                    scrollTop: ($('.error:visible').offset().top - 200)
                }, 400);
            }
        });

        function Init_Submit_Form(main_form) {
            let BtnAction = $('#submit--data');
            $.ajax({
                dataType: "json",
                type: "POST",
                url: $('meta[name="base_url"]').attr('content') + "MyCbr/store_attachment",
                data: new FormData(main_form[0]),
                processData: false,
                contentType: false,
                beforeSend: function() {
                    BtnAction.prop("disabled", true);
                    Swal.fire({
                        title: 'Loading....',
                        html: '<div class="spinner-border text-primary"></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    })
                },
                success: function(response) {
                    Swal.close()
                    if (response.code == 200) {
                        Toast.fire({
                            icon: 'success',
                            title: response.msg
                        });
                        main_form.trigger('reset');
                        Fn_reload_Table_Attachment(response.data)
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.msg
                        });
                    }
                    BtnAction.prop("disabled", false);
                },
                error: function(xhr, status, error) {
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

        function Fn_reload_Table_Attachment(data) {
            // Mengambil elemen tbody dari tabel (ganti 'TableData' dengan ID tabel Anda)
            var tbody = document.getElementById('tbody-attachment');
            var tr = tbody.getElementsByTagName('tr');
            var td = tr[0].getElementsByTagName('td');
            var colspanValue = td[0].getAttribute('colspan');
            if (colspanValue === '3') {
                $('#tbody-attachment').empty()
            }
            var jumlahBaris = tbody.childElementCount;
            // Melakukan loop melalui data dan membuat elemen <tr> dan <td> untuk setiap item data
            var row = tbody.insertRow(tbody.rows.length); // Menambahkan baris ke dalam tbody
            // Menambahkan data ke dalam elemen <td>
            var cell1 = row.insertCell(0);
            var cell2 = row.insertCell(1);
            var cell3 = row.insertCell(2);
            var cell4 = row.insertCell(3);
            // Menetapkan nilai untuk masing-masing sel
            cell1.innerHTML = 1 + jumlahBaris;
            cell2.innerHTML = data.Attachment_FileName;
            cell3.innerHTML = data.Note;
            cell4.innerHTML = data.Action;
        }

        $('[data-bs-toggle="tooltip"]').tooltip();

        $(document).on('click', '.btn-delete-attachment', function() {
            let tr = $(this).closest('tr');
            let vall = $(this).val();

            Swal.fire({
                title: 'System Message !',
                text: `Are you sure to delete this record ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed == true) {
                    $.ajax({
                        dataType: "json",
                        type: "POST",
                        url: $('meta[name="base_url"]').attr('content') + "MyCbr/Delete_Attachment",
                        data: {
                            id: vall,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Loading....',
                                html: '<div class="spinner-border text-primary"></div>',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            })
                        },
                        success: function(response) {
                            Swal.close()
                            if (response.code == 200) {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.msg
                                });
                                tr.remove();
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.msg
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            var statusCode = xhr.status;
                            var errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.responseText ? xhr.responseText : "Terjadi kesalahan: " + error;
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                html: `Kode HTTP: ${statusCode}<br\>Pesan: ${errorMessage}`,
                            });
                        }
                    })
                }
            })
        });
    });
</script>