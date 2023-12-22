$(document).ready(function () {
    var today = new Date();
    var year = today.getFullYear();
    var month = ('0' + (today.getMonth() + 1)).slice(-2);
    var day = ('0' + today.getDate()).slice(-2);
    var formattedDate = year + '-' + month + '-' + day;

    $('.date-picker').flatpickr({
        maxDate: formattedDate
    });

    $('#Generate').on('click', function () {
        let SelLocation = $('#SelLocation').val();
        let DatePeriod = $('#DatePeriod').val();
        let selCatType = $('#selCatType').val();
        let item_code = $('#item_code').val();
        let Category = $('#Category').val();
        let Gudang = $('#Gudang').val();

        if (!SelLocation || SelLocation.trim() === '') {
            return Swal.fire({
                title: 'System Message !',
                text: `You need to select warehouse location !`,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        if (!DatePeriod || DatePeriod.trim() === '') {
            return Swal.fire({
                title: 'System Message !',
                text: `You need to choose date period !`,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        if (!selCatType || selCatType.trim() === '') {
            return Swal.fire({
                title: 'System Message !',
                text: `You need to choose date period !`,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        if (!Category || Category.trim() === '') {
            return Swal.fire({
                title: 'System Message !',
                text: `You need to choose item Category !`,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        if (!Gudang || Gudang.trim() === '') {
            return Swal.fire({
                title: 'System Message !',
                text: `You need to select BIN !`,
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            })
        }

        let urls = $('meta[name="base_url"]').attr('content') + `Opname/GenerateDataOpname?SelLocation=${SelLocation}&DatePeriod=${DatePeriod}&selCatType=${selCatType}&item_code=${item_code}&Category=${Category}&Gudang=${Gudang}`;


        Swal.fire({
            title: 'Apakah Anda Yakin ?',
            text: `Click confirm apabila anda sudah yakin dengan pilihan anda !`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm !'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(urls, 'WindowInsertCurrentStockToOpname', 'width=800,height=600');
            }
        });
    })
})

function Initialize_select2_category(el_selCatType) {
    $('#Category').val('ALL').trigger('change');

    $('#Category').select2({
        minimumInputLength: 0,
        allowClear: true,
        cache: true,
        ajax: {
            dataType: 'json',
            url: $('meta[name="base_url"]').attr('content') + `Opname/select2_category?selCatType=${el_selCatType}`,
            delay: 800,
            data: function (params) {
                return {
                    search: params.term
                }
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (obj) {
                        return {
                            id: obj.id,
                            text: obj.text
                        };
                    })
                };
            },
        }
    })
}

function Initialize_select2_bin(el_location) {
    if (el_location != 1) {
        $('#Gudang').val(null).trigger('change');
    } else {
        $('#Gudang').val(1).trigger('change');
    }

    $('#Gudang').select2({
        minimumInputLength: 0,
        allowClear: true,
        cache: true,
        ajax: {
            dataType: 'json',
            url: $('meta[name="base_url"]').attr('content') + `Opname/select2_bin?location=${el_location}`,
            delay: 800,
            data: function (params) {
                return {
                    search: params.term
                }
            },
            processResults: function (data, page) {
                return {
                    results: $.map(data, function (obj) {
                        return {
                            id: obj.id,
                            text: obj.text
                        };
                    })
                };
            },
        }
    })
}

Initialize_select2_category('SP')
Initialize_select2_bin(1)
