<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/loginpg.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"></head>
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
                    <input type="text" id="full_name" name="full_name">
                </div>

                <div class="input-container">
                    <label class="label-text" for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password">
                        <i id="toggleEye" class="fa-solid fa-eye-slash" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button class="login-btn">LOGIN</button>
            </form>

            <p style="margin-top: 5px; text-align: center;">
                Belum punya akun?
                <a href="{{ route('pegawai.registerPg') }}" style="text-decoration: underline; color: #007bff;">
                    Signup
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword() {
            let passwordInput = document.getElementById("password");
            let toggleIcon = document.getElementById("toggleEye");

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
    </script>

</body>
</html>
