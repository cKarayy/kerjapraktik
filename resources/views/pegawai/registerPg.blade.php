<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pegawai</title>
    <link rel="stylesheet" href="{{ asset('css/pegawai/registerpg.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<body>

    <div class="container">
        <div class="logo-box">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan" class="logo-img">
        </div>

        <div class="form-box">
            <h1>REGISTER</h1>
            <div class="line"></div>

            <form action="{{ route('pegawai.register') }}" method="POST">
                @csrf

                <div class="input-container">
                    <label class="label-text" for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama">
                </div>

                <div class="input-container">
                    <label class="label-text" for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password">
                        <i id="toggleEye" class="fa-solid fa-eye-slash toggle-eye" onclick="togglePassword()"></i>
                    </div>
                </div>

                <div class="input-container">
                    <label class="label-text" for="confirm_password">Confirm Password</label>
                    <div class="confirm_password-container">
                        <input type="password" id="confirm_password" name="confirm_password">
                        <i id="toggleConfirmEye" class="fa-solid fa-eye-slash toggle-eye" onclick="toggleConfirmPassword()"></i>
                    </div>
                </div>

                <button class="register-btn">REGISTER</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = document.getElementById("toggleEye");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }

        function toggleConfirmPassword() {
            const input = document.getElementById("confirm_password");
            const icon = document.getElementById("toggleConfirmEye");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        }
    </script>

</body>
</html>
