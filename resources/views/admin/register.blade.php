<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/admin/register.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"></head>
    <script src="script.js" defer></script>
</head>
<body>
        <!-- Kotak Kiri (Logo) -->
        <div class="logo-box">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="300" height="150" style="margin-left: 30px;">
        </div>

        <!-- Kotak Kanan (Form Login) -->
        <div class="form-box">
            <h1>NEW ADMIN</h1>
            <div class="line"></div>

            <label for="username">Username</label>
            <input type="text" id="username">

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password">
                <i id="toggleEyePassword" class="fa-solid fa-eye-slash" onclick="togglePassword('password', 'toggleEyePassword')"></i>
            </div>

            <label for="confirm-password">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="confirm-password">
                <i id="toggleEyeConfirm" class="fa-solid fa-eye-slash" onclick="togglePassword('confirm-password', 'toggleEyeConfirm')"></i>
            </div>


            <button class="add-btn">ADD</button>
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
