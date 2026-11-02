$(document).ready(function () {
    const misPanelLink = $('.mis-panel-link');
    const validationKey = 'misPanelValidatedTimestamp';
    const validationDuration = 2 * 60 * 60 * 1000; // 2 jam dalam milidetik

    // Target tautan untuk MIS Panel berdasarkan ID yang baru ditambahkan
    misPanelLink.on('click', function (e) {
        // Mencegah browser langsung pindah halaman
        e.preventDefault();
        const targetUrl = $(this).attr('href');

        const lastValidationTime = localStorage.getItem(validationKey);
        const currentTime = new Date().getTime();

        // Cek apakah validasi sebelumnya masih berlaku (kurang dari 2 jam)
        if (lastValidationTime && (currentTime - lastValidationTime < validationDuration)) {
            // Jika masih valid, langsung arahkan ke tujuan
            window.location.href = targetUrl;
            return; // Hentikan eksekusi lebih lanjut
        }

        // Jika validasi sudah kedaluwarsa atau belum ada, tampilkan prompt password
        showPasswordPrompt(targetUrl);
    });

    function showPasswordPrompt(targetUrl) {
        // Gunakan SweetAlert2 untuk memunculkan prompt password
        Swal.fire({
            title: 'Masukkan Password',
            input: 'password',
            inputLabel: 'Untuk mengakses menu ini, silakan masukkan password Anda.',
            inputPlaceholder: 'Masukkan password Anda',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                // Lakukan panggilan AJAX ke controller untuk validasi password
                return $.ajax({
                    url: $('meta[name="base_url"]').attr('content') + 'Report/Navigation/validate_mis_password',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        password: password
                    },
                })
                    .fail(function (xhr) {
                        let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.showValidationMessage(`Request Gagal: ${errorMsg}`);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status === 'success') {
                    // Jika password benar, simpan timestamp validasi
                    localStorage.setItem(validationKey, new Date().getTime());
                    // Arahkan ke URL tujuan
                    window.location.href = targetUrl;
                } else {
                    // Jika password salah, hapus timestamp lama dan tampilkan pesan error
                    localStorage.removeItem(validationKey);
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: result.value.message || 'Password yang Anda masukkan salah.'
                    });
                }
            }
        });
    }
});