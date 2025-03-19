<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Register</title>
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <label for="cpassword">Confirm Password</label>
            <div class="password-container">
                <input type="password" id="password">
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <button class="add-btn">ADD</button>
        </div>
</body>
</html>