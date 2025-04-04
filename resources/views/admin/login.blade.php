<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/admin/styleLogin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="logo-box">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" width="300" height="150" style="margin-left: 30px;">
    </div>

    <div class="form-box">
        <h1>LOGIN</h1>
        <div class="line"></div>

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <label for="full_name">Nama Lengkap</label>
            <input type="text" id="full_name" name="full_name" required>

            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <i id="toggleEye" class="fa-solid fa-eye-slash" onclick="togglePassword()"></i>
                @error('password')
                    <div>{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-btn">LOGIN</button>
        </form>

        @if(session('error'))
            <div style="color: red;">{{ session('error') }}</div>
        @endif
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


    @if(session('console_log'))
        console.log("{{ session('console_log') }}");
    @endif

    </script>
</body>
</html>
