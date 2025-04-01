<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/admin/register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

    <!-- Kotak Kiri (Logo) -->
    <div class="logo-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" width="300" height="150" style="margin-left: 30px;">
    </div>

    <!-- Kotak Kanan (Form Register Admin) -->
    <div class="form-box">
        <h1>NEW ADMIN</h1>
        <div class="line"></div>

        <!-- Form Registrasi -->
        <form action="{{ route('admin.register.submit') }}" method="POST">
            @csrf

            <label for="full_name">Nama Lengkap</label>
            <input type="text" id="full_name" name="full_name" required>
            @error('full_name') <p class="error">{{ $message }}</p> @enderror

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i id="toggleEyePassword" class="fa-solid fa-eye-slash" onclick="togglePassword('password', 'toggleEyePassword')"></i>
            </div>
            @error('password') <p class="error">{{ $message }}</p> @enderror

            <label for="password_confirmation">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('password_confirmation', 'toggleEyeConfirm')"></i>
            </div>

            <button type="submit" class="add-btn">ADD</button>
        </form>
    </div>

    <script>
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
    </script>

</body>
</html>
