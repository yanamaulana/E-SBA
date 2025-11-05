$(document).ready(function () {
    $('.date-picker').flatpickr();
    $(document).on('click', '.btn-report', function () {
        let type_report = $(this).val();
        var report_type = "";
        if (type_report == 'excel') {
            report_type = '&excel=1'
        } else {
            report_type = '&excel=0'
        }
        let start = $('#from').val();
        let until = $('#until').val();

        window.open($('meta[name="base_url"]').attr('content') + `Report/Sales/customer_transaction_report?start=${start}&until=${until}${report_type}`, `Rpt_HPP`, 'width=800,height=600');
    })
})