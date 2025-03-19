<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styleLogin.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="login-container">
        <!-- Kotak Kiri (Logo) -->
        <div class="logo-box">
            <img src="assets/images/logo.png" alt="Logo">
        </div>

        <!-- Kotak Kanan (Form Login) -->
        <div class="form-box">
            <h1>LOGIN</h1>
            <div class="line"></div>

            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Masukkan username">

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" placeholder="Masukkan password">
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

            <button class="login-btn">LOGIN</button>
        </div>
    </div>
</body>
</html>