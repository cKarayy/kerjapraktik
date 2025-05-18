<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai</title>
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

        <form action="{{ route('pegawai.login.submit') }}" method="POST">
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

            <button class="login-btn">LOGIN</button>
        </form>

        <!-- Link untuk membuka popup Ubah Password -->
        <div class="password-link" id="myBtn">RESET PASSWORD</div>
    </div>
</div>

<!-- Popup untuk ubah password -->
<div id="popup-ubah-password" class="popup">
    <div class="popup-content">
        <h3>RESET PASSWORD</h3>
        <form id="changePasswordForm">
            @csrf
            @method('PUT')
            <div class="password-container">
                <input type="password" id="new_password" name="new_password" placeholder="Password Baru" required>
                <i id="toggleEyeNew" class="fa-solid fa-eye-slash" onclick="togglePassword('new_password', 'toggleEyeNew')"></i>
            </div>
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Konfirmasi Password" required>
                <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('confirm_password', 'toggleEyeConfirm')"></i>
            </div>

            <div id="errorMessage" style="color: red;"></div>

            <div class="popup-buttons">
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

    // Menangani form pengubahan password menggunakan AJAX
     document.getElementById('changePasswordForm').onsubmit = function(e) {
        e.preventDefault();  // Menghindari pengiriman form secara normal

        var formData = new FormData(this);

        // Mengirim data form ke server menggunakan AJAX
        fetch("{{ route('pegawai.updatePassword') }}", {
            method: 'PUT',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan sukses dan tutup popup
                showNotification('Password berhasil diperbarui!', 'success');
                closePopup('popup-ubah-password');  // Tutup popup setelah berhasil
            } else {
                // Tampilkan pesan error jika ada
                document.getElementById('errorMessage').innerText = data.message;
                showNotification('Gagal memperbarui password.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan, coba lagi nanti.', 'error');
        });
    };

    // Menampilkan notifikasi
    function showNotification(message, type) {
        var notifikasi = document.getElementById("notifikasi");
        var notifikasiText = document.getElementById("notifikasi-text");
        var notifikasiImage = document.getElementById("notifikasi-gambar");

        notifikasiText.textContent = message;

        // Menentukan gambar dan warna berdasarkan tipe
        if (type === 'success') {
            notifikasiImage.src = "{{ asset('images/success.png') }}";
            notifikasi.style.backgroundColor = "#4CAF50";  
        } else {
            notifikasiImage.src = "{{ asset('images/failed.png') }}";
            notifikasi.style.backgroundColor = "#800000";
        }

        // Tampilkan notifikasi
        notifikasi.style.display = "block";

        // Sembunyikan setelah beberapa detik
        setTimeout(function() {
            notifikasi.style.display = "none";
        }, 3000);  // Notifikasi akan hilang setelah 3 detik
    }
</script>

</body>
</html>
