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
                <label class="label-text" for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" required>
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
        <form method="POST" action="{{ route('pegawai.resetPassword') }}">
            @csrf
            <!-- Kolom Nama Lengkap -->
            <div class="password-container">
                <input type="text" id="reset_full_name" name="reset_full_name" placeholder="Nama Lengkap" required>
            </div>

            <button type="button" class="btn-verify" id="verifyUserBtn">VERIFIKASI</button>

            <!-- Form Password Baru (Tersembunyi Awalnya) -->
            <div id="passwordForm" style="display:none;">
                <input type="hidden" id="user_id" name="user_id" value="">

                <div class="password-container">
                    <input type="password" id="new_password" name="new_password" placeholder="Password Baru" required>
                    <i id="toggleEyeNew" class="fa-solid fa-eye-slash" onclick="togglePassword('new_password', 'toggleEyeNew')"></i>

                    @error('new_password')
                        <div class="error-message" style="color: red; text-align: center; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
                            {{ $message }} <!-- Menampilkan pesan error jika ada -->
                        </div>
                    @enderror
                </div>

                <div class="password-container">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                    <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('confirm_password', 'toggleEyeConfirm')"></i>

                    @error('confirm_password')
                        <div class="error-message" style="color: red; text-align: center; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
                            {{ $message }} <!-- Menampilkan pesan error jika ada -->
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn-done">DONE</button>
                <button type="button" class="btn-cancel" onclick="closePopup('popup-ubah-password')">CANCEL</button>
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
    }

    // Menangani submit form login
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const full_name = document.getElementById('full_name').value;
        const password = document.getElementById('password').value;

        fetch("/pegawai/login", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                full_name: full_name,
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
        const resetFullName = document.getElementById('reset_full_name').value;

        // Log nama lengkap yang dikirim
        console.log("Nama Lengkap yang Dikirimkan: " + resetFullName);

        fetch("/pegawai/verify-user", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ full_name: resetFullName })
        })
        .then(response => response.json())
        .then(data => {
            // Log respons dari server
            console.log("Respons dari Server: ", data);

            if (data.status === 'success') {
                // Simpan user_id dari response untuk digunakan di form password
                verifiedUserId = data.user_id;  // Ambil user_id yang dikirim dari backend

                document.getElementById('reset_full_name').disabled = true;
                document.getElementById('verifyUserBtn').style.display = 'none';
                document.getElementById('passwordForm').style.display = "block";  // Menampilkan form password setelah verifikasi

                console.log("User ID yang Ditemukan: ", verifiedUserId);
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
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
        const newPassword = document.getElementById('new_password').value.trim();
        const confirmPassword = document.getElementById('confirm_password').value.trim();
        const fullName = document.getElementById('reset_full_name').value;
        const userId = document.getElementById('user_id').value;

        console.log("New Password: " + newPassword);
        console.log("Confirm Password: " + confirmPassword);

        // Cek apakah password baru dan konfirmasi password cocok
        if (newPassword !== confirmPassword) {
            console.log('Password dan konfirmasi password tidak cocok.');
            showNotification('Password dan konfirmasi password tidak cocok.', 'error');
            return;
        }

        console.log("Mengirim permintaan untuk reset password...");

        // Kirim data ke server dengan fetch
        fetch("/pegawai/reset-password", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                user_id: userId,
                new_password: newPassword,
                confirm_password: confirmPassword,
            })
        })
        .then(response => response.json())  // Mengubah respons menjadi JSON
        .then(data => {
            console.log("Respons Reset Password: ", data);

            if (data.status === 'success') {
                showNotification(data.message, 'success');
                closePopup('popup-ubah-password');
            } else {
                console.log("Error response from server:", data.message);  // Log error message dari server
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);  // Log error yang terjadi di fetch
            showNotification('Terjadi kesalahan saat menghubungi server.', 'error');
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
