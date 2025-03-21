<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kepegawaian</title>
    <link rel="stylesheet" href="{{ asset('css/data.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <div class="header">
        <h2>DATA PEGAWAI</h2>
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
    </div>
    <div class="divider"></div>

    <div class="container">
        <div class="employee-list">
            @foreach($employees as $employee)
                <div class="employee-card {{ $employee['status'] === 'resign' ? 'resign' : 'active' }}">
                   <img src="{{ asset('images/logo.png') }}" alt="employee-photo" class="employee-photo">
                   
                    <div class="employee-info">
                        <h2 class="employee-name">{{ strtoupper($employee['name']) }}</h2>
                        <p class="employee-role">{{ $employee['role'] }}</p>
                    </div>

                    <div class="status-box {{ $employee['status'] === 'active' ? 'status-active' : 'status-resign' }}">
                        {{ strtoupper($employee['status']) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Ikon Edit & Kunci -->
    <div class="action-icons">
        <img src="{{ asset('images/edit.png') }}" alt="Edit" class="icon-edit" onclick="openDialog()">
        <img src="{{ asset('images/lock.png') }}" alt="Lock" class="icon-lock" onclick="openDialog()">
    </div>

    <!-- Dialog Password -->
    <div id="dialog">
        <p>Enter Password:</p>
        <div class="password-container">
            <input type="password" id="password">
            <i id="toggleEye" class="fa-solid fa-eye-slash" onclick="togglePassword()"></i>
        </div>
        <div class="dialog-buttons">
            <button class="btn-done" onclick="handleSubmit()">DONE</button>
            <button class="btn-cancel" onclick="closeDialog()">CANCEL</button>
        </div>
    </div>

    <!-- Notifikasi -->
    <div id="notification">
        <img id="notifImage" src="" alt="Status">
        <p id="notifText"></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let dialog = document.getElementById("dialog");
            let passwordInput = document.getElementById("password");
            let notification = document.getElementById("notification");
            let notifImage = document.getElementById("notifImage");
            let notifText = document.getElementById("notifText");
            let eyeIcon = document.getElementById("toggleEye");

            // Simpan path gambar di data-attribute
            let successImage = "{{ asset('images/success.png') }}";
            let failedImage = "{{ asset('images/failed.png') }}";

            window.openDialog = function () {
                dialog.style.display = "block";
            };

            window.closeDialog = function () {
                dialog.style.display = "none";
            };

            window.togglePassword = function () {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    eyeIcon.classList.remove("fa-eye-slash");
                    eyeIcon.classList.add("fa-eye");
                } else {
                    passwordInput.type = "password";
                    eyeIcon.classList.remove("fa-eye");
                    eyeIcon.classList.add("fa-eye-slash");
                }
            };

            window.handleSubmit = function () {
                let password = passwordInput.value;
                dialog.style.display = "none";

                setTimeout(() => {
                    if (password === "1234") {
                        notifImage.src = successImage;
                        notifText.innerText = "BERHASIL!";
                    } else {
                        notifImage.src = failedImage;
                        notifText.innerText = "GAGAL!";
                    }

                    notification.style.display = "flex";

                    setTimeout(() => {
                        notification.style.display = "none";
                    }, 2000);
                }, 100);
            };
        });
    </script>

</body>
</html>
