<script>
    document.querySelectorAll('.reset-pwd-close').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            this.closest('.reset-pwd-modal').classList.remove('show');
            history.pushState("", document.title, window.location.pathname + window.location
            .search); // hapus hash dari URL
        });
    });

    // Fungsi untuk menutup modal saat overlay diklik
    const overlays = document.querySelectorAll('.reset-pwd-overlay');
    overlays.forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            closePasswordModal();
        });
    });

    // Fungsi untuk menangani submit pada form konfirmasi password
    const confirmForm = document.getElementById('confirmPasswordForm');
    if (confirmForm) {
        confirmForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(confirmForm);

            fetch('{{ route('password.confirm') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hideModal('confirmPasswordModal');
                        showModal('resetPasswordModal');
                    } else {
                        const errorElement = document.querySelector(
                            '#confirmPasswordForm .reset-pwd-error');
                        if (!errorElement) {
                            const errorSpan = document.createElement('span');
                            errorSpan.classList.add('reset-pwd-error');
                            errorSpan.textContent = data.message;
                            document.getElementById('current_password').parentNode.appendChild(
                                errorSpan);
                        } else {
                            errorElement.textContent = data.message;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    // Fungsi untuk menangani submit pada form reset password
    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(resetForm);

            fetch('{{ route('password.reset') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        hideAllModals(); // Pastikan SEMUA modal ditutup
                        showSuccessNotification('Password berhasil diubah!');
                    } else {
                        // Tampilkan pesan error
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const inputElement = document.getElementById(key);
                                if (inputElement) {
                                    const parentDiv = inputElement.parentNode;
                                    let errorElement = parentDiv.querySelector(
                                        '.reset-pwd-error');

                                    if (!errorElement) {
                                        errorElement = document.createElement('span');
                                        errorElement.classList.add('reset-pwd-error');
                                        parentDiv.appendChild(errorElement);
                                    }

                                    errorElement.textContent = data.errors[key][0];
                                }
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    }

    // Fungsi untuk menutup modal melalui AJAX
    function closePasswordModal() {
        fetch('{{ route('password.close-modal') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideAllModals();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                hideAllModals(); // Tutup modal bahkan jika terjadi error
            });
    }

    // Fungsi untuk menampilkan modal
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
        }
    }

    // Fungsi untuk menyembunyikan modal
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    }

    // Fungsi untuk menyembunyikan semua modal
    function hideAllModals() {
        const modals = document.querySelectorAll('.reset-pwd-modal');
        modals.forEach(function(modal) {
            modal.classList.remove('show');
        });
    }

    // Fungsi untuk menampilkan notifikasi sukses
    function showSuccessNotification(message) {
        // Buat elemen notifikasi
        const notification = document.createElement('div');
        notification.classList.add('reset-pwd-notification');
        notification.innerHTML = `
                <div class="reset-pwd-notification-content">
                    <i class="fas fa-check-circle"></i>
                    <span>${message}</span>
                </div>
            `;

        // Tambahkan ke body
        document.body.appendChild(notification);

        // Tampilkan dengan animasi
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Hilangkan setelah beberapa detik
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }

    // Tambahkan validasi realtime untuk password
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    if (passwordInput) {
        passwordInput.addEventListener('input', validatePassword);
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', validateConfirmPassword);
    }

    function validatePassword() {
        const password = passwordInput.value;
        const parentDiv = passwordInput.parentNode;
        let errorElement = parentDiv.querySelector('.reset-pwd-error');

        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.classList.add('reset-pwd-error');
            parentDiv.appendChild(errorElement);
        }

        if (password.length < 8) {
            errorElement.textContent = 'Password minimal harus 8 karakter';
            return false;
        } else if (!/[a-z]/.test(password) || !/[A-Z]/.test(password)) {
            errorElement.textContent = 'Password harus mengandung huruf besar dan kecil';
            return false;
        } else if (!/[0-9]/.test(password)) {
            errorElement.textContent = 'Password harus mengandung angka';
            return false;
        } else {
            errorElement.textContent = '';
            return true;
        }
    }

    function validateConfirmPassword() {
        const password = passwordInput.value;
        const confirm = confirmInput.value;
        const parentDiv = confirmInput.parentNode;
        let errorElement = parentDiv.querySelector('.reset-pwd-error');

        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.classList.add('reset-pwd-error');
            parentDiv.appendChild(errorElement);
        }

        if (password !== confirm) {
            errorElement.textContent = 'Konfirmasi password tidak cocok';
            return false;
        } else {
            errorElement.textContent = '';
            return true;
        }
    }

    // Jika ada pesan sukses, tampilkan notifikasi
    @if (session('success'))
        showSuccessNotification('{{ session('success') }}');
    @endif
</script>
