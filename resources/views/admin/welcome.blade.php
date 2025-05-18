<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Landing Page</title>
    <link rel="stylesheet" href="{{ asset('css/admin/styleLanding.css') }}">
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <a href="{{ route('pegawai.loginPg') }}" class="login-btn">LOGIN</a>
    </div>
</body>
</html>
