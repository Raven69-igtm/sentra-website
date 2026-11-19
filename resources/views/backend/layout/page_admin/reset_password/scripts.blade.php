<script>
    // Dibungkus IIFE agar aman dieksekusi ulang oleh Livewire soft navigation:
    // deklarasi const/let di top-level akan bentrok saat <body> diganti.
    (function () {
        'use strict';

        const modalIds = ['confirmPasswordModal', 'resetPasswordModal'];

        // Fungsi untuk menampilkan modal
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                return;
            }

            modal.classList.add('show');

            const firstInput = modal.querySelector('input:not([type="hidden"])');
            if (firstInput) {
                setTimeout(function () {
                    firstInput.focus();
                }, 60);
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
            document.querySelectorAll('.reset-pwd-modal.show').forEach(function (modal) {
                modal.classList.remove('show');
            });
        }

        // Hapus hash (#confirmPasswordModal / #resetPasswordModal) dari URL.
        // replaceState dipakai agar halaman tidak melompat ke atas.
        function clearModalHash() {
            if (!window.location.hash) {
                return;
            }

            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
            } else {
                window.location.hash = '';
            }
        }

        // Tutup modal sekaligus lupakan status modal di sesi server.
        function closePasswordModal() {
            hideAllModals();
            clearModalHash();

            fetch('{{ route('password.close-modal') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(function (response) {
                    return response.ok ? response.json() : null;
                })
                .catch(function (error) {
                    console.error('Error:', error);
                });
        }

        // Tombol X
        document.querySelectorAll('.reset-pwd-close').forEach(function (btn) {
            btn.addEventListener('click', function (event) {
                event.preventDefault();
                closePasswordModal();
            });
        });

        // Klik overlay menutup modal
        document.querySelectorAll('.reset-pwd-overlay').forEach(function (overlay) {
            overlay.addEventListener('click', function (event) {
                event.preventDefault();
                closePasswordModal();
            });
        });

        // Link pemicu modal (dropdown profil) menggunakan href="#idModal".
        // Ditangani lewat class "show" agar modal tetap bisa ditutup walau
        // hash masih tertinggal di URL.
        document.querySelectorAll('a[href="#confirmPasswordModal"], a[href="#resetPasswordModal"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();

                const modalId = (this.getAttribute('href') || '').replace('#', '');
                hideAllModals();
                showModal(modalId);
                clearModalHash();
            });
        });

        // Tombol Escape menutup modal.
        if (window.__sentraResetPwdKeydown) {
            document.removeEventListener('keydown', window.__sentraResetPwdKeydown);
        }
        window.__sentraResetPwdKeydown = function (event) {
            if (event.key === 'Escape' && document.querySelector('.reset-pwd-modal.show')) {
                closePasswordModal();
            }
        };
        document.addEventListener('keydown', window.__sentraResetPwdKeydown);

        // Dukung URL lama yang masih membawa hash modal saat halaman dimuat.
        const initialHash = window.location.hash.replace('#', '');
        if (modalIds.indexOf(initialHash) !== -1) {
            showModal(initialHash);
            clearModalHash();
        }

        // Form konfirmasi password
        const confirmForm = document.getElementById('confirmPasswordForm');
        if (confirmForm) {
            confirmForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(confirmForm);

                fetch('{{ route('password.confirm') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            hideModal('confirmPasswordModal');
                            showModal('resetPasswordModal');
                            return;
                        }

                        const errorElement = document.querySelector('#confirmPasswordForm .reset-pwd-error');
                        if (!errorElement) {
                            const errorSpan = document.createElement('span');
                            errorSpan.classList.add('reset-pwd-error');
                            errorSpan.textContent = data.message;
                            document.getElementById('current_password').parentNode.appendChild(errorSpan);
                        } else {
                            errorElement.textContent = data.message;
                        }
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                    });
            });
        }

        // Form reset password
        const resetForm = document.getElementById('resetPasswordForm');
        if (resetForm) {
            resetForm.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(resetForm);

                fetch('{{ route('password.reset') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            hideAllModals();
                            clearModalHash();
                            showSuccessNotification('Password berhasil diubah!');
                            return;
                        }

                        if (data.errors) {
                            Object.keys(data.errors).forEach(function (key) {
                                const inputElement = document.getElementById(key);
                                if (!inputElement) {
                                    return;
                                }

                                const parentDiv = inputElement.parentNode;
                                let errorElement = parentDiv.querySelector('.reset-pwd-error');

                                if (!errorElement) {
                                    errorElement = document.createElement('span');
                                    errorElement.classList.add('reset-pwd-error');
                                    parentDiv.appendChild(errorElement);
                                }

                                errorElement.textContent = data.errors[key][0];
                            });
                        }
                    })
                    .catch(function (error) {
                        console.error('Error:', error);
                    });
            });
        }

        // Fungsi untuk menampilkan notifikasi sukses
        function showSuccessNotification(message) {
            const notification = document.createElement('div');
            notification.classList.add('reset-pwd-notification');
            notification.innerHTML = `
                <div class="reset-pwd-notification-content">
                    <i class="fas fa-check-circle"></i>
                    <span></span>
                </div>
            `;
            notification.querySelector('span').textContent = message;

            document.body.appendChild(notification);

            setTimeout(function () {
                notification.classList.add('show');
            }, 100);

            setTimeout(function () {
                notification.classList.remove('show');
                setTimeout(function () {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Validasi realtime password
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
            }

            errorElement.textContent = '';
            return true;
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
            }

            errorElement.textContent = '';
            return true;
        }

        // Jika ada pesan sukses, tampilkan notifikasi
        @if (session('success'))
            showSuccessNotification(@json(session('success')));
        @endif
    })();
</script>
