<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/pegawai/loginpg.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<div class="container">
    <div class="logo-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan" class="logo-img">
    </div>

    <div class="form-box">
        <h1>LOGIN</h1>
        <div class="line"></div>

        <form id="loginForm" action="{{ route('pegawai.login.submit') }}" method="POST">
            @csrf
            <div class="input-container">
                <label class="label-text" for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="input-container">
                <label class="label-text" for="password">Password</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required>
                    <i id="toggleEyeLogin" class="fa-solid fa-eye-slash" onclick="togglePassword('password', 'toggleEyeLogin')"></i>
                </div>
            </div>

            @if(session('error'))
                <div class="error-message" style="color: red; text-align: center; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
                    {{ session('error') }}
                </div>
            @endif

            <button type="submit" class="login-btn">LOGIN</button>
        </form>

        <div class="password-link" id="myBtn">Lupa Password?</div>
    </div>
</div>

<!-- Popup untuk reset password -->
<div id="popup-ubah-password" class="popup">
    <div class="popup-content">
        <h3>RESET PASSWORD</h3>
        <form method="POST" id="resetPasswordForm">
            @csrf
            <!-- Kolom Username -->
            <div class="password-container">
                <input type="text" id="reset_username" name="reset_username" placeholder="Username" required>
            </div>

            <!-- Tombol Verifikasi dan Cancel (Awal) -->
            <div id="verificationButtons" class="button-group">
                <button type="button" class="btn-verify" id="verifyUserBtn">VERIFIKASI</button>
                <button type="button" class="btn-cancel" onclick="closePopup('popup-ubah-password')">CANCEL</button>
            </div>

            <!-- Form Password Baru -->
            <div id="passwordForm" style="display:none;">
                <input type="hidden" id="user_id" name="user_id" value="">

                <div class="password-container">
                    <input type="password" id="new_password" name="new_password" placeholder="Password Baru" required>
                    <i id="toggleEyeNew" class="fa-solid fa-eye-slash" onclick="togglePassword('new_password', 'toggleEyeNew')"></i>
                </div>

                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('confirm_password', 'toggleEyeConfirm')"></i>
                </div>

                <button type="submit" class="btn-done">DONE</button>
                <button type="submit" class="btn-cancel">CANCEL</button>

            </div>
        </form>
    </div>
</div>

<!-- Notifikasi -->
<div id="notifikasi" class="notifikasi">
    <img id="notifikasi-gambar" src="" alt="Notifikasi">
    <p id="notifikasi-text"></p>
</div>

<script>
    let verifiedUserId = '';

    // Fungsi toggle password
    function togglePassword(inputId, iconId) {
        let passwordInput = document.getElementById(inputId);
        let toggleIcon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        }
    }

    // Fungsi untuk membuka popup
    var modal = document.getElementById("popup-ubah-password");
    var btn = document.getElementById("myBtn");
    var span = document.getElementsByClassName("btn-cancel")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    // Fungsi untuk menutup popup
    function closePopup(popupId) {
        var popup = document.getElementById(popupId);
        popup.style.display = "none";

        // Reset semua elemen form ke state awal
        document.getElementById('resetPasswordForm').reset();
        document.getElementById('passwordForm').style.display = 'none';

        // Tampilkan kembali tombol verifikasi dan cancel
        document.getElementById('verificationButtons').style.display = 'flex'; // atau 'block' tergantung CSS

        // Aktifkan kembali input username
        document.getElementById('reset_username').disabled = false;

        // Tampilkan tombol verifikasi (jika sebelumnya dihide)
        document.getElementById('verifyUserBtn').style.display = 'block';
    }

    // Update event listener untuk tombol cancel
    document.querySelectorAll('.btn-cancel').forEach(button => {
        button.addEventListener('click', function() {
            closePopup('popup-ubah-password');
        });
    });

    // Menangani submit form login
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        fetch("/pegawai/login", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        }).then(response => response.json())
        .then(data => {
            console.log("Login Response:", data);  // Debugging response
            if (data.status === 'success') {
                // Redirect user to the page after login
                window.location.href = data.redirectUrl;  // This will redirect the user
            } else {
                showNotification(data.message, 'error');
            }
        }).catch(error => {
            console.error("Login Error:", error);
            showNotification('Terjadi kesalahan saat login.', 'error');
        });
    });


    // Verifikasi Nama Lengkap dan Tampilkan Form Password Baru
    document.getElementById('verifyUserBtn').addEventListener('click', function() {
        const resetUsername = document.getElementById('reset_username').value;

        // Log nama lengkap yang dikirim
        console.log("Username yang Dikirimkan: " + resetUsername);

        fetch("/pegawai/verify-user", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ username: resetUsername })
        })
        .then(response => response.json())
        .then(data => {
            // Log respons dari server
            console.log("Respons dari Server: ", data);

            if (data.status === 'success') {
                // Simpan user_id dari response untuk digunakan di form password
                verifiedUserId = data.user_id;  // Ambil user_id yang dikirim dari backend

                document.getElementById('user_id').value = verifiedUserId;
                document.getElementById('reset_username').disabled = true;
                document.getElementById('verificationButtons').style.display = 'none';
                document.getElementById('passwordForm').style.display = "block";  // Menampilkan form password setelah verifikasi

                console.log("User ID yang Ditemukan: ", verifiedUserId);
                showNotification;
            } else {
                showNotification;
            }
        })
        .catch(() => {
            showNotification('Terjadi kesalahan, coba lagi.', 'error');
        });
    });

    // Menangani submit form reset password
    document.querySelector('#popup-ubah-password form').addEventListener('submit', function(e) {
        e.preventDefault();

        // Ambil nilai dari form
        const formData = new FormData(this);
        const newPassword = formData.get('new_password');
        const confirmPassword = formData.get('confirm_password');
        const userId = formData.get('user_id');

        console.log("New Password: " + newPassword);
        console.log("Confirm Password: " + confirmPassword);
        console.log("User ID: " + userId);

        const passwordRegex = /^(?=.*[A-Z])(?=.*\d).{6,}$/;
        if (!passwordRegex.test(newPassword)) {
            showNotification('Password harus minimal 6 karakter, mengandung huruf kapital dan angka.', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            showNotification('Password dan konfirmasi password tidak cocok.', 'error');
            return;
        }

        console.log("Mengirim permintaan untuk reset password...");

        // Kirim data ke server dengan fetch
        fetch("/pegawai/reset-password", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json' // Penting untuk meminta response JSON
            },
            body: JSON.stringify({
                user_id: userId,
                new_password: newPassword,
                new_password_confirmation: confirmPassword // Sesuai dengan validasi Laravel
            })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');

            // Cek jika response bukan JSON
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Response bukan JSON:', text);
                throw new Error('Terjadi kesalahan pada server');
            }

            return response.json();
        })
        .then(data => {
            console.log("Respons Reset Password: ", data);

            if (data.status === 'success') {
                showNotification(data.message, 'success');
                closePopup('popup-ubah-password');
                // Reset form
                document.getElementById('resetPasswordForm').reset();
                document.getElementById('passwordForm').style.display = 'none';
                document.getElementById('reset_username').disabled = false;
                document.getElementById('verifyUserBtn').style.display = 'block';
            } else {
                showNotification(data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'Terjadi kesalahan saat menghubungi server.', 'error');
        });
    });

    // Verifikasi User
    document.getElementById('verifyUserBtn').addEventListener('click', function() {
        const resetUsername = document.getElementById('reset_username').value.trim();

        if (!resetUsername) {
            showNotification('Username harus diisi', 'error');
            return;
        }

        console.log("Username yang Dikirimkan: " + resetUsername);

        fetch("/pegawai/verify-user", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ username: resetUsername })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(text || 'Invalid response from server');
            }
            return response.json();
        })
        .then(data => {
            console.log("Respons dari Server: ", data);

            if (data.status === 'success') {
                document.getElementById('user_id').value = data.user_id;
                document.getElementById('reset_username').disabled = true;
                document.getElementById('verifyUserBtn').style.display = 'none';
                document.getElementById('passwordForm').style.display = "block";

                console.log("User ID yang Ditemukan: ", data.user_id);
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification(error.message || 'Terjadi kesalahan, coba lagi.', 'error');
        });
    });


    // Menampilkan notifikasi
    function showNotification(message, type) {
        var notifikasi = document.getElementById("notifikasi");
        var notifikasiText = document.getElementById("notifikasi-text");
        var notifikasiImage = document.getElementById("notifikasi-gambar");

        notifikasiText.textContent = message;

        if (type === 'success') {
            notifikasiImage.src = "{{ asset('images/success.png') }}";
            notifikasi.style.backgroundColor = "#4CAF50";
        } else {
            notifikasiImage.src = "{{ asset('images/failed.png') }}";
            notifikasi.style.backgroundColor = "#800000";
        }

        notifikasi.style.display = "block";

        setTimeout(function() {
            notifikasi.style.display = "none";
        }, 3000);
    }
</script>

</body>
</html>
